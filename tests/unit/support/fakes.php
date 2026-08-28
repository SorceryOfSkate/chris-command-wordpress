<?php
/**
 * News unit test doubles.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Tests\Unit\Support;

use ChrisCommand\Modules\News\Cache_Store;
use ChrisCommand\Modules\News\Feed_Client;

/**
 * In-memory cache store.
 */
final class Array_Cache_Store implements Cache_Store {
	/**
	 * Stored values.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	public array $values = array();

	/**
	 * Gets one value.
	 *
	 * @param string $key Cache key.
	 * @return mixed
	 */
	public function get( string $key ): mixed {
		return $this->values[ $key ] ?? false;
	}

	/**
	 * Stores one value.
	 *
	 * @param string               $key        Cache key.
	 * @param array<string, mixed> $value      Cache value.
	 * @param int                  $expiration Ignored expiration.
	 * @return bool
	 */
	public function set( string $key, array $value, int $expiration ): bool {
		unset( $expiration );
		$this->values[ $key ] = $value;
		return true;
	}
}

/**
 * Callback-driven feed client.
 */
final class Fake_Feed_Client implements Feed_Client {
	/**
	 * Fetch callback.
	 *
	 * @var \Closure(string): object
	 */
	private \Closure $callback;

	/**
	 * Constructs the fake.
	 *
	 * @param callable(string): object $callback Fetch callback.
	 */
	public function __construct( callable $callback ) {
		$this->callback = \Closure::fromCallable( $callback );
	}

	/**
	 * Fetches a fake feed.
	 *
	 * @param string $url Feed URL.
	 * @return object
	 */
	public function fetch( string $url ): object {
		return ( $this->callback )( $url );
	}
}

/**
 * SimplePie-compatible source.
 */
final class Fake_Source {
	public function __construct( private string $title, private string $url ) {}

	public function get_title(): string {
		return $this->title;
	}

	public function get_permalink(): string {
		return $this->url;
	}
}

/**
 * SimplePie-compatible item.
 */
final class Fake_Item {
	public function __construct(
		private string $title,
		private string $url,
		private string $guid,
		private string $date,
		private ?Fake_Source $source = null
	) {}

	public function get_title(): string {
		return $this->title;
	}

	public function get_permalink(): string {
		return $this->url;
	}

	public function get_id(): string {
		return $this->guid;
	}

	public function get_date( string $format ): string {
		unset( $format );
		return $this->date;
	}

	public function get_source(): ?Fake_Source {
		return $this->source;
	}

	/**
	 * Returns no raw RSS tags for the default fake.
	 *
	 * @return array<mixed>
	 */
	public function get_item_tags(): array {
		return array();
	}
}

/**
 * SimplePie-compatible feed.
 */
final class Fake_Feed {
	/**
	 * Feed items.
	 *
	 * @param list<Fake_Item> $items Feed items.
	 */
	public function __construct( private array $items ) {}

	/**
	 * Gets feed items.
	 *
	 * @param int $offset Offset.
	 * @param int $limit  Limit.
	 * @return list<Fake_Item>
	 */
	public function get_items( int $offset, int $limit ): array {
		return array_slice( $this->items, $offset, $limit );
	}
}

/**
 * Creates one valid feed.
 *
	 * @param string $suffix Unique suffix.
	 * @param string $date   Article date.
 * @return Fake_Feed
 */
function valid_feed( string $suffix = 'one', string $date = '2026-08-28T12:00:00+00:00' ): Fake_Feed {
	return new Fake_Feed(
		array(
			new Fake_Item(
				'Signal &amp; Response - Example News',
				'https://news.google.com/rss/articles/' . $suffix,
				'guid-' . $suffix,
				$date,
				new Fake_Source( 'Example News', 'https://www.example.com' )
			),
		)
	);
}
