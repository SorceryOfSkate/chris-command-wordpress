<?php
/**
 * WordPress CLI activation smoke test.
 *
 * @package ChrisCommand
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! is_plugin_active( 'chris-command/chris-command.php' ) ) {
	throw new RuntimeException( 'Chris Command is not active.' );
}

if ( ! defined( 'CHRIS_COMMAND_VERSION' ) || '0.3.1' !== CHRIS_COMMAND_VERSION ) {
	throw new RuntimeException( 'Chris Command version constant is unavailable or incorrect.' );
}

global $wpdb;

$option_pattern = $wpdb->esc_like( 'chris_command_' ) . '%';
$option_count   = (int) $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
		$option_pattern
	)
);

if ( 0 !== $option_count ) {
	throw new RuntimeException( 'Activation unexpectedly created Chris Command options.' );
}

$routes = rest_get_server()->get_routes();
if ( ! isset( $routes['/chris-command/v1/news'] ) ) {
	throw new RuntimeException( 'The public News REST route is not registered.' );
}

if ( ! shortcode_exists( 'chris_command_news' ) ) {
	throw new RuntimeException( 'The News shortcode is not registered.' );
}

if ( ! shortcode_exists( 'chris_command_dashboard' ) ) {
	throw new RuntimeException( 'The dashboard shortcode is not registered.' );
}

$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
if ( ! isset( $block_types['chris-command/news'] ) ) {
	throw new RuntimeException( 'The Chris Command News block is not registered.' );
}

if ( ! isset( $block_types['chris-command/dashboard'] ) ) {
	throw new RuntimeException( 'The primary Chris Command Dashboard block is not registered.' );
}

$page_templates = apply_filters( 'theme_page_templates', array() );
if ( ! isset( $page_templates['chris-command-dashboard.php'] ) ) {
	throw new RuntimeException( 'The standalone Chris Command Dashboard page template is not registered.' );
}

WP_CLI::success( 'Chris Command 0.3.1 activated with the public dashboard shell and News service.' );
