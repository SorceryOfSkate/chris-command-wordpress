<?php
/**
 * Approved public News categories.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides the fixed News category allowlist and feed URLs.
 */
final class Categories {
	/**
	 * Approved category configuration.
	 *
	 * @var array<string, array{label: string, query: string}>
	 */
	private const ITEMS = array(
		'russia'        => array(
			'label' => 'Russia',
			'query' => '(Russia OR Kremlin OR Ukraine) when:2d',
		),
		'china'         => array(
			'label' => 'China',
			'query' => '(China OR Beijing OR Taiwan) when:2d',
		),
		'north-korea'   => array(
			'label' => 'North Korea',
			'query' => '("North Korea" OR DPRK OR Pyongyang) when:7d',
		),
		'tech'          => array(
			'label' => 'Tech',
			'query' => '(technology OR AI OR cybersecurity OR semiconductor) when:2d',
		),
		'economics'     => array(
			'label' => 'Economics',
			'query' => '(economy OR inflation OR interest rates OR jobs OR markets) when:2d',
		),
		'united-states' => array(
			'label' => 'United States',
			'query' => '("United States" OR Congress OR "White House") when:2d',
		),
		'philippines'   => array(
			'label' => 'Philippines',
			'query' => '(Philippines OR Manila OR "South China Sea") when:3d',
		),
	);

	/**
	 * Returns every approved category.
	 *
	 * @return array<string, array{label: string, query: string}>
	 */
	public static function all(): array {
		return self::ITEMS;
	}

	/**
	 * Determines whether a category slug is approved.
	 *
	 * @param string $slug Category slug.
	 * @return bool
	 */
	public static function has( string $slug ): bool {
		return isset( self::ITEMS[ $slug ] );
	}

	/**
	 * Returns one category.
	 *
	 * @param string $slug Category slug.
	 * @return array{label: string, query: string}
	 */
	public static function get( string $slug ): array {
		if ( ! self::has( $slug ) ) {
			throw new \InvalidArgumentException( 'Unknown News category.' );
		}

		return self::ITEMS[ $slug ];
	}

	/**
	 * Builds the fixed Google News RSS URL for one category.
	 *
	 * @param string $slug Category slug.
	 * @return string
	 */
	public static function feed_url( string $slug ): string {
		$category = self::get( $slug );
		$query    = http_build_query(
			array(
				'q'    => $category['query'],
				'hl'   => 'en-US',
				'gl'   => 'US',
				'ceid' => 'US:en',
			),
			'',
			'&',
			PHP_QUERY_RFC3986
		);

		return 'https://news.google.com/rss/search?' . $query;
	}

	/**
	 * Returns every allowlisted feed URL.
	 *
	 * @return list<string>
	 */
	public static function feed_urls(): array {
		$urls = array();

		foreach ( array_keys( self::ITEMS ) as $slug ) {
			$urls[] = self::feed_url( $slug );
		}

		return $urls;
	}
}
