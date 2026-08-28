<?php
/**
 * Feed normalizer tests.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Tests\Unit;

use ChrisCommand\Modules\News\Categories;
use ChrisCommand\Modules\News\Feed_Normalizer;
use ChrisCommand\Tests\Unit\Support\Fake_Feed;
use ChrisCommand\Tests\Unit\Support\Fake_Item;
use PHPUnit\Framework\TestCase;

use function ChrisCommand\Tests\Unit\Support\valid_feed;

/**
 * Covers the public article schema and malformed feeds.
 */
final class FeedNormalizerTest extends TestCase {
	public function test_contains_exactly_the_seven_approved_categories(): void {
		self::assertSame(
			array( 'russia', 'china', 'north-korea', 'tech', 'economics', 'united-states', 'philippines' ),
			array_keys( Categories::all() )
		);
	}

	public function test_normalizes_article_into_public_schema(): void {
		$articles = ( new Feed_Normalizer() )->normalize( valid_feed(), Categories::get( 'tech' ) );

		self::assertCount( 1, $articles );
		self::assertSame(
			array( 'id', 'category', 'title', 'source', 'sourceDomain', 'url', 'publishedAt' ),
			array_keys( $articles[0] )
		);
		self::assertMatchesRegularExpression( '/^[a-f0-9]{20}$/', $articles[0]['id'] );
		self::assertSame( 'Tech', $articles[0]['category'] );
		self::assertSame( 'Signal & Response', $articles[0]['title'] );
		self::assertSame( 'Example News', $articles[0]['source'] );
		self::assertSame( 'example.com', $articles[0]['sourceDomain'] );
		self::assertSame( '2026-08-28T12:00:00Z', $articles[0]['publishedAt'] );
	}

	public function test_rejects_unsafe_article_urls(): void {
		$feed = new Fake_Feed(
			array(
				new Fake_Item( 'Unsafe', 'javascript:alert(1)', 'unsafe', '2026-08-28T12:00:00Z' ),
			)
		);

		self::assertSame( array(), ( new Feed_Normalizer() )->normalize( $feed, Categories::get( 'tech' ) ) );
	}

	public function test_uses_title_suffix_when_source_metadata_is_missing(): void {
		$feed = new Fake_Feed(
			array(
				new Fake_Item(
					'Signal & Response - Example News',
					'https://news.google.com/rss/articles/example',
					'fallback-source',
					'2026-08-28T12:00:00Z'
				),
			)
		);

		$articles = ( new Feed_Normalizer() )->normalize( $feed, Categories::get( 'tech' ) );

		self::assertSame( 'Signal & Response', $articles[0]['title'] );
		self::assertSame( 'Example News', $articles[0]['source'] );
	}

	public function test_malformed_feed_is_rejected(): void {
		$this->expectException( \UnexpectedValueException::class );
		( new Feed_Normalizer() )->normalize( new \stdClass(), Categories::get( 'tech' ) );
	}
}
