<?php
/**
 * Plugin bootstrap.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand;

use ChrisCommand\Core\Module_Registry;
use ChrisCommand\Modules\News\News_Module;

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
		$registry->add( new News_Module() );
		$registry->register_all();
	}
}
