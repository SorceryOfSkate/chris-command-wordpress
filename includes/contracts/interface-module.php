<?php
/**
 * Public module contract.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Contracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defines the minimum contract for an approved public module.
 */
interface Module {
	/**
	 * Returns the stable public module slug.
	 *
	 * @return string
	 */
	public function get_slug(): string;

	/**
	 * Registers the module with WordPress.
	 *
	 * @return void
	 */
	public function register(): void;
}
