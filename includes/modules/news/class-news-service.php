<?php
/**
 * News aggregation and cache behavior.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieves, normalizes, and caches approved News feeds.
 */
final class News_Service {
	public const FRESH_TTL = 900;
	public const STALE_TTL = 86400;

	/**
	 * Feed client.
	 *
	 * @var Feed_Client
	 */
	private Feed_Client $feed_client;

	/**
	 * Feed normalizer.
	 *
	 * @var Feed_Normalizer
	 */
	private Feed_Normalizer $normalizer;

	/**
	 * Cache store.
	 *
	 * @var Cache_Store
	 */
	private Cache_Store $cache;

	/**
	 * Clock returning a Unix timestamp.
	 *
	 * @var \Closure(): int
	 */
	private \Closure $clock;

	/**
	 * Constructs the service.
	 *
	 * @param Feed_Client           $feed_client Feed client.
	 * @param Feed_Normalizer       $normalizer  Feed normalizer.
	 * @param Cache_Store           $cache       Cache store.
	 * @param callable(): int|null  $clock       Optional test clock.
	 */
	public function __construct( Feed_Client $feed_client, Feed_Normalizer $normalizer, Cache_Store $cache, ?callable $clock = null ) {
		$this->feed_client = $feed_client;
		$this->normalizer  = $normalizer;
		$this->cache       = $cache;
		$this->clock       = \Closure::fromCallable( $clock ?? 'time' );
	}

	/**
	 * Returns one category, using a bounded stale cache on failure.
	 *
	 * @param string $slug  Approved category slug.
	 * @param bool   $force Whether to skip the fresh cache.
	 * @return array<string, mixed>
	 */
	public function get_category( string $slug, bool $force = false ): array {
		$category  = Categories::get( $slug );
		$fresh_key = $this->cache_key( 'fresh', $slug );
		$stale_key = $this->cache_key( 'stale', $slug );

		if ( ! $force ) {
			$fresh = $this->cache->get( $fresh_key );
			if ( $this->is_cache_record( $fresh, $slug ) ) {
				return $fresh;
			}
		}

		try {
			$feed     = $this->feed_client->fetch( Categories::feed_url( $slug ) );
			$articles = $this->normalizer->normalize( $feed, $category );

			if ( array() === $articles ) {
				throw new \UnexpectedValueException( 'The News feed contained no usable articles.' );
			}

			$record = array(
				'slug'      => $slug,
				'label'     => $category['label'],
				'status'    => 'available',
				'stale'     => false,
				'fetchedAt' => $this->now_iso(),
				'articles'  => $articles,
			);

			$this->cache->set( $fresh_key, $record, self::FRESH_TTL );
			$this->cache->set( $stale_key, $record, self::STALE_TTL );

			return $record;
		} catch ( \Throwable ) {
			$stale = $this->cache->get( $stale_key );
			if ( $this->is_bounded_stale_record( $stale, $slug ) ) {
				$stale['status']  = 'stale';
				$stale['stale']   = true;
				$stale['warning'] = 'Live feed unavailable; showing a recent cached copy.';

				return $stale;
			}

			return array(
				'slug'      => $slug,
				'label'     => $category['label'],
				'status'    => 'unavailable',
				'stale'     => false,
				'fetchedAt' => null,
				'warning'   => 'Live feed is temporarily unavailable.',
				'articles'  => array(),
			);
		}
	}

	/**
	 * Returns all approved categories and a merged chronological list.
	 *
	 * @param bool $force Whether to skip fresh caches.
	 * @return array<string, mixed>
	 */
	public function get_all( bool $force = false ): array {
		$lanes                  = array();
		$articles_by_id         = array();
		$stale_categories       = array();
		$unavailable_categories = array();

		foreach ( array_keys( Categories::all() ) as $slug ) {
			$lane    = $this->get_category( $slug, $force );
			$lanes[] = $lane;

			if ( 'stale' === $lane['status'] ) {
				$stale_categories[] = $slug;
			} elseif ( 'unavailable' === $lane['status'] ) {
				$unavailable_categories[] = $slug;
			}

			foreach ( $lane['articles'] as $article ) {
				if ( ! isset( $articles_by_id[ $article['id'] ] ) ) {
					$articles_by_id[ $article['id'] ] = $article;
				}
			}
		}

		$articles = array_values( $articles_by_id );
		usort(
			$articles,
			static fn ( array $left, array $right ): int => strcmp( $right['publishedAt'], $left['publishedAt'] )
		);

		return array(
			'generatedAt'           => $this->now_iso(),
			'partial'               => array() !== $stale_categories || array() !== $unavailable_categories,
			'stale'                 => array() !== $stale_categories,
			'staleCategories'       => $stale_categories,
			'unavailableCategories' => $unavailable_categories,
			'categories'            => $lanes,
			'articles'              => $articles,
		);
	}

	/**
	 * Builds a versioned transient key.
	 *
	 * @param string $kind Cache kind.
	 * @param string $slug Category slug.
	 * @return string
	 */
	private function cache_key( string $kind, string $slug ): string {
		return 'chris_command_news_' . $kind . '_v1_' . $slug;
	}

	/**
	 * Validates the shape of a cached category.
	 *
	 * @param mixed  $record Candidate cache record.
	 * @param string $slug   Expected category slug.
	 * @return bool
	 */
	private function is_cache_record( mixed $record, string $slug ): bool {
		return is_array( $record ) &&
			isset( $record['slug'], $record['fetchedAt'], $record['articles'] ) &&
			$slug === $record['slug'] &&
			is_string( $record['fetchedAt'] ) &&
			is_array( $record['articles'] ) &&
			array() !== $record['articles'];
	}

	/**
	 * Checks both record shape and the explicit stale age bound.
	 *
	 * @param mixed  $record Candidate stale record.
	 * @param string $slug   Expected category slug.
	 * @return bool
	 */
	private function is_bounded_stale_record( mixed $record, string $slug ): bool {
		if ( ! $this->is_cache_record( $record, $slug ) ) {
			return false;
		}

		$fetched_timestamp = strtotime( $record['fetchedAt'] );
		if ( false === $fetched_timestamp ) {
			return false;
		}

		$age = max( 0, ( $this->clock )() - $fetched_timestamp );

		return $age <= self::STALE_TTL;
	}

	/**
	 * Returns the current clock time in UTC ISO format.
	 *
	 * @return string
	 */
	private function now_iso(): string {
		return gmdate( 'Y-m-d\TH:i:s\Z', ( $this->clock )() );
	}
}
