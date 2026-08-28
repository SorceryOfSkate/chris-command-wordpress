<?php
/**
 * Plugin Name:       Chris Command
 * Plugin URI:        https://github.com/SorceryOfSkate/chris-command-wordpress
 * Description:       Public WordPress foundation for approved Chris Command modules.
 * Version:           0.1.0
 * Requires at least: 7.1
 * Requires PHP:      8.2
 * Author:            Chris Siennick
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/SorceryOfSkate/chris-command-wordpress
 * Text Domain:       chris-command
 * Domain Path:       /languages
 *
 * @package ChrisCommand
 */

namespace ChrisCommand;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHRIS_COMMAND_VERSION', '0.1.0' );
define( 'CHRIS_COMMAND_FILE', __FILE__ );
define( 'CHRIS_COMMAND_PATH', plugin_dir_path( __FILE__ ) );

require_once CHRIS_COMMAND_PATH . 'includes/contracts/interface-module.php';
require_once CHRIS_COMMAND_PATH . 'includes/core/class-module-registry.php';
require_once CHRIS_COMMAND_PATH . 'includes/core/class-plugin.php';

Plugin::boot();
