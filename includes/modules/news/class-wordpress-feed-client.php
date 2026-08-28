<?php
/**
 * WordPress News feed client.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches allowlisted RSS feeds through WordPress fetch_feed().
 */
final class WordPress_Feed_Client implements Feed_Client {
	private const TIMEOUT_SECONDS = 10;
	private const MAX_BYTES       = 2000000;
	private const FEED_CACHE_TTL  = 900;

	/**
	 * URL active during a scoped request.
	 *
	 * @var string
	 */
	private string $active_url = '';

	/**
	 * Fetches one allowlisted feed.
	 *
	 * @param string $url Allowlisted feed URL.
	 * @return object
	 */
	public function fetch( string $url ): object {
		if ( ! in_array( $url, Categories::feed_urls(), true ) ) {
			throw new \InvalidArgumentException( 'News feed URL is not allowlisted.' );
		}

		require_once ABSPATH . WPINC . '/feed.php';

		$this->active_url = $url;
		add_filter( 'wp_feed_cache_transient_lifetime', array( $this, 'filter_feed_cache_lifetime' ), 10, 2 );
		add_filter( 'http_request_args', array( $this, 'filter_http_request_args' ), 10, 2 );

		try {
			$feed = fetch_feed( $url );
		} finally {
			remove_filter( 'wp_feed_cache_transient_lifetime', array( $this, 'filter_feed_cache_lifetime' ), 10 );
			remove_filter( 'http_request_args', array( $this, 'filter_http_request_args' ), 10 );
			$this->active_url = '';
		}

		if ( is_wp_error( $feed ) ) {
			throw new \RuntimeException( 'The News provider did not return a usable feed.' );
		}

		return $feed;
	}

	/**
	 * Sets a short cache lifetime only for the active allowlisted feed.
	 *
	 * @param int          $lifetime Existing lifetime.
	 * @param string|array $url      Feed URL or URLs.
	 * @return int
	 */
	public function filter_feed_cache_lifetime( int $lifetime, string|array $url ): int {
		return $this->active_url === $url ? self::FEED_CACHE_TTL : $lifetime;
	}

	/**
	 * Applies bounded HTTP settings only to the active allowlisted request.
	 *
	 * @param array<string, mixed> $args Request arguments.
	 * @param string               $url  Request URL.
	 * @return array<string, mixed>
	 */
	public function filter_http_request_args( array $args, string $url ): array {
		if ( $this->active_url !== $url ) {
			return $args;
		}

		$args['timeout']             = self::TIMEOUT_SECONDS;
		$args['limit_response_size'] = self::MAX_BYTES;
		$args['reject_unsafe_urls']  = true;
		$args['user-agent']          = 'Chris Command/' . CHRIS_COMMAND_VERSION . '; WordPress RSS reader';

		return $args;
	}
}
