<?php
/**
 * News service tests.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Tests\Unit;

use ChrisCommand\Modules\News\Categories;
use ChrisCommand\Modules\News\Feed_Normalizer;
use ChrisCommand\Modules\News\News_Service;
use ChrisCommand\Tests\Unit\Support\Array_Cache_Store;
use ChrisCommand\Tests\Unit\Support\Fake_Feed_Client;
use PHPUnit\Framework\TestCase;

use function ChrisCommand\Tests\Unit\Support\valid_feed;

/**
 * Covers cache and partial failure behavior.
 */
final class NewsServiceTest extends TestCase {
	public function test_malformed_feed_returns_unavailable_lane(): void {
		$service = new News_Service(
			new Fake_Feed_Client( static fn (): object => new \stdClass() ),
			new Feed_Normalizer(),
			new Array_Cache_Store(),
			static fn (): int => 1787918400
		);

		$result = $service->get_category( 'tech', true );

		self::assertSame( 'unavailable', $result['status'] );
		self::assertSame( array(), $result['articles'] );
	}

	public function test_feed_failure_returns_recent_stale_cache(): void {
		$now       = 1787918400;
		$cache     = new Array_Cache_Store();
		$available = new News_Service(
			new Fake_Feed_Client( static fn (): object => valid_feed() ),
			new Feed_Normalizer(),
			$cache,
			static function () use ( &$now ): int {
				return $now;
			}
		);
		$available->get_category( 'tech', true );

		$now += News_Service::FRESH_TTL + 1;
		$failing = new News_Service(
			new Fake_Feed_Client(
				static function (): object {
					throw new \RuntimeException( 'Offline.' );
				}
			),
			new Feed_Normalizer(),
			$cache,
			static function () use ( &$now ): int {
				return $now;
			}
		);

		$result = $failing->get_category( 'tech', true );

		self::assertSame( 'stale', $result['status'] );
		self::assertTrue( $result['stale'] );
		self::assertCount( 1, $result['articles'] );
	}

	public function test_stale_cache_is_rejected_after_bound(): void {
		$now   = 1787918400;
		$cache = new Array_Cache_Store();
		$seed  = new News_Service(
			new Fake_Feed_Client( static fn (): object => valid_feed() ),
			new Feed_Normalizer(),
			$cache,
			static function () use ( &$now ): int {
				return $now;
			}
		);
		$seed->get_category( 'tech', true );

		$now += News_Service::STALE_TTL + 1;
		$service = new News_Service(
			new Fake_Feed_Client(
				static function (): object {
					throw new \RuntimeException( 'Offline.' );
				}
			),
			new Feed_Normalizer(),
			$cache,
			static function () use ( &$now ): int {
				return $now;
			}
		);

		self::assertSame( 'unavailable', $service->get_category( 'tech', true )['status'] );
	}

	public function test_one_failed_category_keeps_other_lanes_available(): void {
		$client = new Fake_Feed_Client(
			static function ( string $url ): object {
				if ( Categories::feed_url( 'north-korea' ) === $url ) {
					throw new \RuntimeException( 'Malformed feed.' );
				}

				return valid_feed( substr( hash( 'sha256', $url ), 0, 8 ) );
			}
		);
		$service = new News_Service(
			$client,
			new Feed_Normalizer(),
			new Array_Cache_Store(),
			static fn (): int => 1787918400
		);

		$result = $service->get_all( true );

		self::assertTrue( $result['partial'] );
		self::assertSame( array( 'north-korea' ), $result['unavailableCategories'] );
		self::assertCount( 7, $result['categories'] );
		self::assertCount( 6, $result['articles'] );
	}
}
