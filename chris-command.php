<?php
/**
 * Plugin Name:       Chris Command
 * Plugin URI:        https://github.com/SorceryOfSkate/chris-command-wordpress
 * Description:       Public WordPress frontend for approved Chris Command modules.
 * Version:           0.3.1
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

define( 'CHRIS_COMMAND_VERSION', '0.3.1' );
define( 'CHRIS_COMMAND_FILE', __FILE__ );
define( 'CHRIS_COMMAND_PATH', plugin_dir_path( __FILE__ ) );
define( 'CHRIS_COMMAND_URL', plugin_dir_url( __FILE__ ) );

require_once CHRIS_COMMAND_PATH . 'includes/contracts/interface-module.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/interface-feed-client.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/interface-cache-store.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-categories.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-transient-cache-store.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-wordpress-feed-client.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-feed-normalizer.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-news-service.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-news-renderer.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-news-rest-controller.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/news/class-news-module.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/dashboard/class-dashboard-renderer.php';
require_once CHRIS_COMMAND_PATH . 'includes/modules/dashboard/class-dashboard-module.php';
require_once CHRIS_COMMAND_PATH . 'includes/core/class-module-registry.php';
require_once CHRIS_COMMAND_PATH . 'includes/core/class-plugin.php';

Plugin::boot();
