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

if ( ! defined( 'CHRIS_COMMAND_VERSION' ) || '0.1.0' !== CHRIS_COMMAND_VERSION ) {
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
	throw new RuntimeException( 'Phase 0 unexpectedly created WordPress options.' );
}

$routes = rest_get_server()->get_routes();

foreach ( array_keys( $routes ) as $route ) {
	if ( str_starts_with( $route, '/chris-command/' ) ) {
		throw new RuntimeException( 'Phase 0 unexpectedly registered a REST route.' );
	}
}

if ( shortcode_exists( 'chris_command_news' ) ) {
	throw new RuntimeException( 'Phase 0 unexpectedly registered the News shortcode.' );
}

$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();

foreach ( array_keys( $block_types ) as $block_name ) {
	if ( str_starts_with( $block_name, 'chris-command/' ) ) {
		throw new RuntimeException( 'Phase 0 unexpectedly registered a block.' );
	}
}

WP_CLI::success( 'Chris Command 0.1.0 activated without persistent data or public modules.' );
