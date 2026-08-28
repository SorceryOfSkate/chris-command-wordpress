<?php
/**
 * News feed normalization.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Converts SimplePie feed items into the public News schema.
 */
final class Feed_Normalizer {
	public const ARTICLE_LIMIT = 20;

	/**
	 * Normalizes one feed.
	 *
	 * @param object                       $feed     SimplePie-compatible feed.
	 * @param array{label: string, query: string} $category Category configuration.
	 * @return list<array{id: string, category: string, title: string, source: string, sourceDomain: string, url: string, publishedAt: string}>
	 */
	public function normalize( object $feed, array $category ): array {
		if ( ! is_callable( array( $feed, 'get_items' ) ) ) {
			throw new \UnexpectedValueException( 'Malformed News feed.' );
		}

		$items = $feed->get_items( 0, self::ARTICLE_LIMIT );
		if ( ! is_array( $items ) ) {
			throw new \UnexpectedValueException( 'Malformed News feed items.' );
		}

		$articles = array();
		$seen     = array();

		foreach ( $items as $item ) {
			try {
				$article = $this->normalize_item( $item, $category['label'] );
			} catch ( \Throwable ) {
				continue;
			}

			if ( null === $article || isset( $seen[ $article['id'] ] ) ) {
				continue;
			}

			$seen[ $article['id'] ] = true;
			$articles[]             = $article;
		}

		usort(
			$articles,
			static fn ( array $left, array $right ): int => strcmp( $right['publishedAt'], $left['publishedAt'] )
		);

		return $articles;
	}

	/**
	 * Normalizes one feed item.
	 *
	 * @param mixed  $item           SimplePie-compatible item.
	 * @param string $category_label Public category label.
	 * @return array{id: string, category: string, title: string, source: string, sourceDomain: string, url: string, publishedAt: string}|null
	 */
	private function normalize_item( mixed $item, string $category_label ): ?array {
		if ( ! is_object( $item ) || ! is_callable( array( $item, 'get_permalink' ) ) ) {
			return null;
		}

		$url = $this->safe_url( (string) $item->get_permalink() );
		if ( '' === $url ) {
			return null;
		}

		$source_metadata = $this->source_metadata( $item );
		$source_name     = $source_metadata['name'];
		$source_url      = $source_metadata['url'];
		$source_domain   = '';
		$title           = is_callable( array( $item, 'get_title' ) ) ? $this->clean_text( (string) $item->get_title() ) : '';

		if ( '' === $source_name ) {
			$source_parts = $this->source_from_title( $title );
			$title        = $source_parts['title'];
			$source_name  = $source_parts['source'];
		} else {
			$title = $this->remove_source_suffix( $title, $source_name );
		}

		$source_name = '' !== $source_name ? $source_name : 'Unknown source';

		if ( '' === $title ) {
			return null;
		}

		$source_domain = $this->domain_from_url( $source_url );
		if ( '' === $source_domain ) {
			$source_domain = $this->domain_from_url( $url );
		}
		if ( '' === $source_domain ) {
			$source_domain = trim( (string) preg_replace( '/[^a-z0-9.-]+/', '-', strtolower( $source_name ) ), '-' );
		}

		$guid = is_callable( array( $item, 'get_id' ) ) ? $this->clean_text( (string) $item->get_id() ) : '';
		$date = is_callable( array( $item, 'get_date' ) ) ? (string) $item->get_date( DATE_ATOM ) : '';

		return array(
			'id'           => substr( hash( 'sha256', '' !== $guid ? $guid : $url ), 0, 20 ),
			'category'     => $category_label,
			'title'        => $title,
			'source'       => $source_name,
			'sourceDomain' => $source_domain,
			'url'          => $url,
			'publishedAt'  => $this->normalize_date( $date ),
		);
	}

	/**
	 * Reads publisher metadata from Atom-style or RSS-style source elements.
	 *
	 * @param object $item SimplePie-compatible item.
	 * @return array{name: string, url: string}
	 */
	private function source_metadata( object $item ): array {
		$name   = '';
		$url    = '';
		$source = is_callable( array( $item, 'get_source' ) ) ? $item->get_source() : null;

		if ( is_object( $source ) ) {
			if ( is_callable( array( $source, 'get_title' ) ) ) {
				$name = $this->clean_text( (string) $source->get_title() );
			}
			if ( is_callable( array( $source, 'get_permalink' ) ) ) {
				$url = $this->safe_url( (string) $source->get_permalink() );
			}
		}

		if ( '' !== $name || ! is_callable( array( $item, 'get_item_tags' ) ) ) {
			return array(
				'name' => $name,
				'url'  => $url,
			);
		}

		$namespaces = array( '' );
		if ( defined( 'SIMPLEPIE_NAMESPACE_RSS_20' ) ) {
			$namespaces[] = SIMPLEPIE_NAMESPACE_RSS_20;
		}

		foreach ( array_unique( $namespaces ) as $namespace ) {
			$tags = $item->get_item_tags( $namespace, 'source' );
			if ( ! is_array( $tags ) || ! isset( $tags[0] ) || ! is_array( $tags[0] ) ) {
				continue;
			}

			$name       = $this->clean_text( (string) ( $tags[0]['data'] ?? '' ) );
			$attributes = $tags[0]['attribs'][''] ?? array();
			if ( is_array( $attributes ) ) {
				$url = $this->safe_url( (string) ( $attributes['url'] ?? '' ) );
			}

			if ( '' !== $name ) {
				break;
			}
		}

		return array(
			'name' => $name,
			'url'  => $url,
		);
	}

	/**
	 * Uses Google News' publisher suffix when source metadata is unavailable.
	 *
	 * @param string $title Remote title.
	 * @return array{title: string, source: string}
	 */
	private function source_from_title( string $title ): array {
		$parts = preg_split( '/\s(?:-|–|—)\s/u', $title );
		if ( ! is_array( $parts ) || count( $parts ) < 2 ) {
			return array(
				'title'  => $title,
				'source' => '',
			);
		}

		$source = $this->clean_text( (string) array_pop( $parts ) );
		$clean  = $this->clean_text( implode( ' - ', $parts ) );

		if ( '' === $clean || '' === $source || strlen( $source ) > 120 ) {
			return array(
				'title'  => $title,
				'source' => '',
			);
		}

		return array(
			'title'  => $clean,
			'source' => $source,
		);
	}

	/**
	 * Cleans remote text without retaining markup.
	 *
	 * @param string $value Remote text.
	 * @return string
	 */
	private function clean_text( string $value ): string {
		$value = html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$value = preg_replace( '/\s+/u', ' ', $value );

		return trim( is_string( $value ) ? $value : '' );
	}

	/**
	 * Removes Google News' repeated publisher suffix.
	 *
	 * @param string $title  Article title.
	 * @param string $source Publisher name.
	 * @return string
	 */
	private function remove_source_suffix( string $title, string $source ): string {
		if ( 'Unknown source' === $source ) {
			return $title;
		}

		$suffix = ' - ' . $source;
		if ( strlen( $title ) >= strlen( $suffix ) && 0 === strcasecmp( substr( $title, -strlen( $suffix ) ), $suffix ) ) {
			return rtrim( substr( $title, 0, -strlen( $suffix ) ) );
		}

		return $title;
	}

	/**
	 * Validates a public HTTP(S) article URL.
	 *
	 * @param string $url Candidate URL.
	 * @return string
	 */
	private function safe_url( string $url ): string {
		$url   = trim( $url );
		$parts = wp_parse_url( $url );

		if (
			! is_array( $parts ) ||
			empty( $parts['host'] ) ||
			empty( $parts['scheme'] ) ||
			! in_array( strtolower( (string) $parts['scheme'] ), array( 'http', 'https' ), true )
		) {
			return '';
		}

		return esc_url_raw( $url, array( 'http', 'https' ) );
	}

	/**
	 * Extracts a normalized host name.
	 *
	 * @param string $url Validated URL.
	 * @return string
	 */
	private function domain_from_url( string $url ): string {
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( ! is_string( $host ) ) {
			return '';
		}

		return preg_replace( '/^www\./i', '', strtolower( $host ) ) ?? '';
	}

	/**
	 * Converts a date to a stable UTC ISO value.
	 *
	 * @param string $value Remote date.
	 * @return string
	 */
	private function normalize_date( string $value ): string {
		try {
			$date = new \DateTimeImmutable( $value );
			return $date->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'Y-m-d\TH:i:s\Z' );
		} catch ( \Throwable ) {
			return '1970-01-01T00:00:00Z';
		}
	}
}
