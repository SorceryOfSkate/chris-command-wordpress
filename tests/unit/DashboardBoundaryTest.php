<?php
/**
 * Dashboard public-boundary tests.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Prevents source-dashboard private payloads from entering the public shell.
 */
final class DashboardBoundaryTest extends TestCase {
	public function test_dashboard_assets_exclude_private_source_payloads(): void {
		$root  = dirname( __DIR__, 2 );
		$files = array(
			$root . '/includes/modules/dashboard/class-dashboard-renderer.php',
			$root . '/includes/modules/dashboard/class-dashboard-module.php',
			$root . '/assets/js/dashboard.js',
			$root . '/assets/css/dashboard.css',
			$root . '/templates/chris-command-dashboard.php',
		);
		$code  = '';

		foreach ( $files as $file ) {
			self::assertFileExists( $file );
			$contents = file_get_contents( $file );
			self::assertIsString( $contents );
			$code .= $contents;
		}

		$forbidden = array(
			'data-cc-module="work"',
			'>Finances<',
			'>MTG<',
			'>Notes<',
			'googleCalendar',
			'clientId',
			'apiKey',
			'YAWA Build',
			'Cassie',
			'chatgpt.com',
			'mtga.untapped.gg',
		);

		foreach ( $forbidden as $needle ) {
			self::assertStringNotContainsString( $needle, $code );
		}
	}

	public function test_dashboard_block_is_the_single_primary_shell(): void {
		$root     = dirname( __DIR__, 2 );
		$metadata = json_decode( (string) file_get_contents( $root . '/blocks/dashboard/block.json' ), true );

		self::assertSame( 3, $metadata['apiVersion'] );
		self::assertSame( 'chris-command/dashboard', $metadata['name'] );
		self::assertFalse( $metadata['supports']['multiple'] );
	}
}
