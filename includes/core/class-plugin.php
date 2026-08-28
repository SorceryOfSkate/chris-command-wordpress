<?php
/**
 * Plugin bootstrap.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand;

use ChrisCommand\Core\Module_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Boots the inert Phase 0 foundation.
 */
final class Plugin {
	/**
	 * Boots the plugin and its explicitly approved modules.
	 *
	 * @return void
	 */
	public static function boot(): void {
		$registry = new Module_Registry();
		$registry->register_all();
	}
}
