<?php
/**
 * Lightweight unit test bootstrap.
 *
 * @package ChrisCommand
 */

define( 'ABSPATH', __DIR__ . '/wordpress/' );

if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * Test substitute for wp_parse_url().
	 *
	 * @param string $url       URL.
	 * @param int    $component Component.
	 * @return array<string, mixed>|string|int|null|false
	 */
	function wp_parse_url( string $url, int $component = -1 ): array|string|int|null|false {
		return parse_url( $url, $component );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * Test substitute for esc_url_raw().
	 *
	 * @param string      $url       URL.
	 * @param list<string> $protocols Allowed protocols.
	 * @return string
	 */
	function esc_url_raw( string $url, array $protocols = array( 'http', 'https' ) ): string {
		$scheme = parse_url( $url, PHP_URL_SCHEME );
		return is_string( $scheme ) && in_array( strtolower( $scheme ), $protocols, true ) ? $url : '';
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * Test substitute for wp_strip_all_tags().
	 *
	 * @param string $value Input text.
	 * @return string
	 */
	function wp_strip_all_tags( string $value ): string {
		return strip_tags( $value );
	}
}

$root = dirname( __DIR__, 2 );
require_once $root . '/includes/modules/news/interface-feed-client.php';
require_once $root . '/includes/modules/news/interface-cache-store.php';
require_once $root . '/includes/modules/news/class-categories.php';
require_once $root . '/includes/modules/news/class-feed-normalizer.php';
require_once $root . '/includes/modules/news/class-news-service.php';
require_once __DIR__ . '/support/fakes.php';
