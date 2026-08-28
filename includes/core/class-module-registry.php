<?php
/**
 * Explicit registry for approved public modules.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Core;

use ChrisCommand\Contracts\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores and registers explicitly approved public modules.
 */
final class Module_Registry {
	/**
	 * Registered modules keyed by public slug.
	 *
	 * @var array<string, Module>
	 */
	private array $modules = array();

	/**
	 * Adds an approved module to the registry.
	 *
	 * @param Module $module Module instance.
	 * @return void
	 */
	public function add( Module $module ): void {
		$this->modules[ $module->get_slug() ] = $module;
	}

	/**
	 * Registers every approved module.
	 *
	 * @return void
	 */
	public function register_all(): void {
		foreach ( $this->modules as $module ) {
			$module->register();
		}
	}

	/**
	 * Returns registered module slugs.
	 *
	 * @return list<string>
	 */
	public function get_slugs(): array {
		return array_keys( $this->modules );
	}
}
