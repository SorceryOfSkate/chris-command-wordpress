<?php
/**
 * News feed client contract.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches one allowlisted feed.
 */
interface Feed_Client {
	/**
	 * Fetches a feed object.
	 *
	 * @param string $url Allowlisted feed URL.
	 * @return object
	 */
	public function fetch( string $url ): object;
}
