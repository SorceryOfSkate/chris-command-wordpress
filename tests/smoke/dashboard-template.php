<?php
/**
 * Dashboard template, block, and shortcode regression smoke test.
 *
 * @package ChrisCommand
 */

use ChrisCommand\Modules\Dashboard\Dashboard_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$reflection  = new ReflectionClass( Dashboard_Module::class );
$identifiers = $reflection->getConstants();

if ( 'chris-command/dashboard' !== $identifiers['BLOCK_NAME'] ) {
	throw new RuntimeException( 'The canonical dashboard block identifier is incorrect.' );
}

if ( 'chris_command_dashboard' !== $identifiers['SHORTCODE'] ) {
	throw new RuntimeException( 'The canonical dashboard shortcode identifier is incorrect.' );
}

if ( 'chris-command-dashboard.php' !== $identifiers['TEMPLATE'] ) {
	throw new RuntimeException( 'The canonical dashboard page template identifier is incorrect.' );
}

global $post, $wp_query;

$original_post  = $post;
$original_query = $wp_query;
$page_ids       = array();

try {
	$template_page_id = wp_insert_post(
		array(
			'post_title'  => 'Chris Command Template Smoke',
			'post_type'   => 'page',
			'post_status' => 'publish',
		),
		true
	);

	if ( is_wp_error( $template_page_id ) ) {
		throw new RuntimeException( 'Unable to create the dashboard template smoke page.' );
	}

	$page_ids[] = $template_page_id;
	update_post_meta( $template_page_id, '_wp_page_template', $identifiers['TEMPLATE'] );

	$wp_query = new WP_Query(
		array(
			'page_id'   => $template_page_id,
			'post_type' => 'page',
		)
	);
	$wp_query->the_post();
	do_action( 'wp_enqueue_scripts' );

	$selected_template = apply_filters( 'template_include', get_page_template() );
	if ( CHRIS_COMMAND_PATH . 'templates/chris-command-dashboard.php' !== $selected_template ) {
		throw new RuntimeException( 'WordPress did not select the packaged dashboard page template.' );
	}

	ob_start();
	require $selected_template;
	$template_html = (string) ob_get_clean();

	if ( ! str_contains( $template_html, 'data-cc-dashboard' ) ) {
		throw new RuntimeException( 'The selected dashboard page template did not render the shell.' );
	}

	$block_page_id = wp_insert_post(
		array(
			'post_title'   => 'Chris Command Block Smoke',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '<!-- wp:chris-command/dashboard /-->',
		),
		true
	);

	if ( is_wp_error( $block_page_id ) ) {
		throw new RuntimeException( 'Unable to create the dashboard block smoke page.' );
	}

	$page_ids[] = $block_page_id;
	$wp_query   = new WP_Query(
		array(
			'page_id'   => $block_page_id,
			'post_type' => 'page',
		)
	);
	$wp_query->the_post();
	do_action( 'wp_enqueue_scripts' );

	$block_html = do_blocks( '<!-- wp:chris-command/dashboard /-->' );
	if ( ! str_contains( $block_html, 'data-cc-dashboard' ) ) {
		throw new RuntimeException( 'The canonical dashboard block did not render the shell.' );
	}

	$shortcode_page_id = wp_insert_post(
		array(
			'post_title'   => 'Chris Command Shortcode Smoke',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '[chris_command_dashboard]',
		),
		true
	);

	if ( is_wp_error( $shortcode_page_id ) ) {
		throw new RuntimeException( 'Unable to create the dashboard shortcode smoke page.' );
	}

	$page_ids[] = $shortcode_page_id;
	$wp_query   = new WP_Query(
		array(
			'page_id'   => $shortcode_page_id,
			'post_type' => 'page',
		)
	);
	$wp_query->the_post();
	do_action( 'wp_enqueue_scripts' );

	$shortcode_html = do_shortcode( '[chris_command_dashboard]' );
	if ( ! str_contains( $shortcode_html, 'data-cc-dashboard' ) ) {
		throw new RuntimeException( 'The canonical dashboard shortcode did not render the shell.' );
	}

	if ( ! wp_style_is( 'chris-command-dashboard', 'enqueued' ) || ! wp_script_is( 'chris-command-dashboard', 'enqueued' ) ) {
		throw new RuntimeException( 'Dashboard assets were not enqueued for the public entry points.' );
	}
} finally {
	foreach ( $page_ids as $page_id ) {
		wp_delete_post( $page_id, true );
	}

	$post     = $original_post;
	$wp_query = $original_query;
	wp_reset_postdata();
}

WP_CLI::success( 'Dashboard template, block, and shortcode regression smoke test passed.' );
