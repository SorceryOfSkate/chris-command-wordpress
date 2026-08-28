<?php
/**
 * Shared News renderer.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the block and shortcode through one escaped PHP view.
 */
final class News_Renderer {
	/**
	 * News service.
	 *
	 * @var News_Service
	 */
	private News_Service $service;

	/**
	 * Constructs the renderer.
	 *
	 * @param News_Service $service News service.
	 */
	public function __construct( News_Service $service ) {
		$this->service = $service;
	}

	/**
	 * Dynamic block callback.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string
	 */
	public function render_block( array $attributes ): string {
		return $this->render( $attributes );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array<string, mixed>|string $attributes Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( array|string $attributes = array() ): string {
		$attributes = shortcode_atts(
			array(
				'category' => 'tech',
				'limit'    => 5,
			),
			is_array( $attributes ) ? $attributes : array(),
			'chris_command_news'
		);

		return $this->render( $attributes );
	}

	/**
	 * Renders one selected category.
	 *
	 * @param array<string, mixed> $attributes Render attributes.
	 * @return string
	 */
	public function render( array $attributes ): string {
		$slug  = isset( $attributes['category'] ) ? sanitize_key( (string) $attributes['category'] ) : 'tech';
		$slug  = Categories::has( $slug ) ? $slug : 'tech';
		$limit = isset( $attributes['limit'] ) ? absint( $attributes['limit'] ) : 5;
		$limit = min( 10, max( 1, $limit ) );
		$lane  = $this->service->get_category( $slug );

		wp_enqueue_style( 'chris-command-news-style' );

		$heading_id = wp_unique_id( 'chris-command-news-' );
		$articles   = array_slice( $lane['articles'], 0, $limit );
		$status     = 'available' === $lane['status'] ? __( 'Live', 'chris-command' ) : __( 'Cached', 'chris-command' );

		if ( 'unavailable' === $lane['status'] ) {
			$status = __( 'Unavailable', 'chris-command' );
		}

		ob_start();
		?>
		<section class="chris-command-news" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>" data-category="<?php echo esc_attr( $slug ); ?>">
			<header class="chris-command-news__header">
				<p class="chris-command-news__eyebrow"><?php esc_html_e( 'Chris Command News', 'chris-command' ); ?></p>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $lane['label'] ); ?></h2>
				<p class="chris-command-news__status">
					<?php echo esc_html( $status ); ?>
					<?php if ( is_string( $lane['fetchedAt'] ) && '' !== $lane['fetchedAt'] ) : ?>
						<span aria-hidden="true">&middot;</span>
						<?php
						printf(
							/* translators: %s: date and time of the News fetch. */
							esc_html__( 'Updated %s', 'chris-command' ),
							esc_html( $this->format_date( $lane['fetchedAt'] ) )
						);
						?>
					<?php endif; ?>
				</p>
			</header>

			<?php if ( isset( $lane['warning'] ) ) : ?>
				<p class="chris-command-news__notice" role="status"><?php echo esc_html( $lane['warning'] ); ?></p>
			<?php endif; ?>

			<?php if ( array() === $articles ) : ?>
				<p class="chris-command-news__empty"><?php esc_html_e( 'No News stories are available for this category right now.', 'chris-command' ); ?></p>
			<?php else : ?>
				<ol class="chris-command-news__list">
					<?php foreach ( $articles as $article ) : ?>
						<li class="chris-command-news__item">
							<a href="<?php echo esc_url( $article['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $article['title'] ); ?>
							</a>
							<p>
								<span><?php echo esc_html( $article['source'] ); ?></span>
								<span aria-hidden="true">&middot;</span>
								<time datetime="<?php echo esc_attr( $article['publishedAt'] ); ?>">
									<?php echo esc_html( $this->format_date( $article['publishedAt'] ) ); ?>
								</time>
							</p>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</section>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Formats an ISO date using the site timezone and format.
	 *
	 * @param string $value ISO date.
	 * @return string
	 */
	private function format_date( string $value ): string {
		$timestamp = strtotime( $value );
		if ( false === $timestamp ) {
			return __( 'Unknown time', 'chris-command' );
		}

		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
	}
}
