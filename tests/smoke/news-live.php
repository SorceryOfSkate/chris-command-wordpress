<?php
/**
 * Live News end-to-end smoke test for WordPress Playground.
 *
 * @package ChrisCommand
 */

use ChrisCommand\Modules\News\Categories;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( 7 !== count( Categories::all() ) ) {
	throw new RuntimeException( 'The seven-category News allowlist is incomplete.' );
}

$request = new WP_REST_Request( 'GET', '/chris-command/v1/news' );
$request->set_param( 'category', 'tech' );
$response = rest_do_request( $request );

if ( 200 !== $response->get_status() ) {
	throw new RuntimeException( 'The News REST endpoint did not return HTTP 200.' );
}

$data = $response->get_data();
if ( ! is_array( $data ) || 'available' !== $data['status'] || empty( $data['articles'] ) ) {
	throw new RuntimeException( 'The live Tech feed did not return usable articles.' );
}

$expected_fields = array( 'id', 'category', 'title', 'source', 'sourceDomain', 'url', 'publishedAt' );
if ( $expected_fields !== array_keys( $data['articles'][0] ) ) {
	throw new RuntimeException( 'The live article schema is incorrect.' );
}

$shortcode = do_shortcode( '[chris_command_news category="tech" limit="3"]' );
if ( ! str_contains( $shortcode, 'class="chris-command-news"' ) ) {
	throw new RuntimeException( 'The News shortcode did not use the shared renderer.' );
}

$block = do_blocks( '<!-- wp:chris-command/news {"category":"tech","limit":3} /-->' );
if ( ! str_contains( $block, 'class="chris-command-news"' ) ) {
	throw new RuntimeException( 'The dynamic News block did not render.' );
}

$dashboard_shortcode = do_shortcode( '[chris_command_dashboard]' );
if ( ! str_contains( $dashboard_shortcode, 'data-cc-dashboard' ) || ! str_contains( $dashboard_shortcode, 'data-news-endpoint' ) ) {
	throw new RuntimeException( 'The dashboard shortcode did not render the complete public shell.' );
}

$dashboard_block = do_blocks( '<!-- wp:chris-command/dashboard /-->' );
if ( ! str_contains( $dashboard_block, 'class="cc-dashboard"' ) ) {
	throw new RuntimeException( 'The primary dashboard block did not render.' );
}

$forbidden_public_payloads = array( 'data-cc-module="work"', '>Finances<', '>MTG<', '>Notes<', 'client_id', 'api_key', 'YAWA Build', 'Cassie', 'chatgpt.com' );
foreach ( $forbidden_public_payloads as $forbidden_payload ) {
	if ( str_contains( $dashboard_block, $forbidden_payload ) ) {
		throw new RuntimeException( 'The dashboard contains a forbidden private payload.' );
	}
}

WP_CLI::success( 'Live News REST plus dashboard shortcode and block rendering passed.' );
