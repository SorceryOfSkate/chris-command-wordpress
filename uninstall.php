<?php
/**
 * Plugin uninstall cleanup.
 *
 * @package ChrisCommand
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$chris_command_news_categories = array(
	'russia',
	'china',
	'north-korea',
	'tech',
	'economics',
	'united-states',
	'philippines',
);

foreach ( $chris_command_news_categories as $chris_command_news_category ) {
	delete_transient( 'chris_command_news_fresh_v1_' . $chris_command_news_category );
	delete_transient( 'chris_command_news_stale_v1_' . $chris_command_news_category );
}
