<?php
/**
 * News cache contract.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores bounded News cache records.
 */
interface Cache_Store {
	/**
	 * Gets one cache value.
	 *
	 * @param string $key Cache key.
	 * @return mixed
	 */
	public function get( string $key ): mixed;

	/**
	 * Stores one cache value.
	 *
	 * @param string               $key        Cache key.
	 * @param array<string, mixed> $value      Cache value.
	 * @param int                  $expiration Maximum lifetime in seconds.
	 * @return bool
	 */
	public function set( string $key, array $value, int $expiration ): bool;
}
