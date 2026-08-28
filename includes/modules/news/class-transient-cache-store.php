<?php
/**
 * WordPress transient-backed News cache.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adapts the Transients API to the News cache contract.
 */
final class Transient_Cache_Store implements Cache_Store {
	/**
	 * Gets one transient.
	 *
	 * @param string $key Cache key.
	 * @return mixed
	 */
	public function get( string $key ): mixed {
		return get_transient( $key );
	}

	/**
	 * Stores one transient.
	 *
	 * @param string               $key        Cache key.
	 * @param array<string, mixed> $value      Cache value.
	 * @param int                  $expiration Maximum lifetime in seconds.
	 * @return bool
	 */
	public function set( string $key, array $value, int $expiration ): bool {
		return set_transient( $key, $value, $expiration );
	}
}
