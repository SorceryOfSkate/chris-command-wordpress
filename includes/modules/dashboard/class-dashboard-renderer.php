<?php
/**
 * Public dashboard renderer.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the public-safe Chris Command application shell.
 */
final class Dashboard_Renderer {
	/**
	 * Dynamic block callback.
	 *
	 * @return string
	 */
	public function render_block(): string {
		return $this->render();
	}

	/**
	 * Shortcode callback.
	 *
	 * @return string
	 */
	public function render_shortcode(): string {
		return $this->render();
	}

	/**
	 * Renders the complete dashboard shell.
	 *
	 * @return string
	 */
	public function render(): string {
		wp_enqueue_style( 'chris-command-dashboard-fonts' );
		wp_enqueue_style( 'chris-command-dashboard' );
		wp_enqueue_script( 'chris-command-dashboard' );

		$instance_id = wp_unique_id( 'chris-command-dashboard-' );
		$modules     = array(
			array(
				'slug'  => 'overview',
				'icon'  => '⌁',
				'label' => __( 'Overview', 'chris-command' ),
			),
			array(
				'slug'  => 'news',
				'icon'  => '▤',
				'label' => __( 'News', 'chris-command' ),
			),
			array(
				'slug'  => 'projects',
				'icon'  => '⌬',
				'label' => __( 'Projects', 'chris-command' ),
			),
			array(
				'slug'  => 'about',
				'icon'  => '◇',
				'label' => __( 'About', 'chris-command' ),
			),
			array(
				'slug'  => 'contact',
				'icon'  => '◫',
				'label' => __( 'Contact', 'chris-command' ),
			),
			array(
				'slug'  => 'settings',
				'icon'  => '⚙',
				'label' => __( 'Settings', 'chris-command' ),
			),
		);

		ob_start();
		?>
		<div class="cc-dashboard-host">
			<section
				class="cc-dashboard"
				id="<?php echo esc_attr( $instance_id ); ?>"
				data-cc-dashboard
				data-news-endpoint="<?php echo esc_url( rest_url( 'chris-command/v1/news' ) ); ?>"
				data-version="<?php echo esc_attr( CHRIS_COMMAND_VERSION ); ?>"
				aria-label="<?php esc_attr_e( 'Chris Command public dashboard', 'chris-command' ); ?>"
			>
				<div class="cc-dashboard__grid" aria-hidden="true"></div>
				<span class="cc-corner cc-corner--tl" aria-hidden="true"></span>
				<span class="cc-corner cc-corner--tr" aria-hidden="true"></span>
				<span class="cc-corner cc-corner--bl" aria-hidden="true"></span>
				<span class="cc-corner cc-corner--br" aria-hidden="true"></span>

				<header class="cc-hero">
					<div class="cc-brand">
						<span class="cc-version">v<?php echo esc_html( CHRIS_COMMAND_VERSION ); ?></span>
						<h1>Chris Command</h1>
						<p><?php esc_html_e( 'Public operations dashboard', 'chris-command' ); ?></p>
						<button type="button" class="cc-fold-toggle" data-cc-selector-toggle aria-expanded="true">
							<span aria-hidden="true">◀</span>
							<?php esc_html_e( 'Fold selector', 'chris-command' ); ?>
						</button>
					</div>
					<section class="cc-time-core" aria-label="<?php esc_attr_e( 'Current time', 'chris-command' ); ?>">
						<time data-cc-clock datetime="">00:00</time>
						<span data-cc-weekday>---</span>
						<span data-cc-date>---</span>
					</section>
					<div class="cc-live-indicator" aria-label="<?php esc_attr_e( 'Public system online', 'chris-command' ); ?>">
						<span aria-hidden="true"></span>
						<strong><?php esc_html_e( 'Public Link', 'chris-command' ); ?></strong>
						<small><?php esc_html_e( 'WordPress Online', 'chris-command' ); ?></small>
					</div>
				</header>

				<div class="cc-layout">
					<aside class="cc-selector" data-cc-selector>
						<div class="cc-wheel-controls" aria-hidden="true"><span>▲</span><span>▼</span></div>
						<nav class="cc-module-wheel" data-cc-wheel aria-label="<?php esc_attr_e( 'Dashboard modules', 'chris-command' ); ?>" tabindex="0">
							<?php foreach ( $modules as $index => $module ) : ?>
								<button
									type="button"
									class="cc-module-button<?php echo 0 === $index ? ' is-active' : ''; ?>"
									data-cc-module="<?php echo esc_attr( $module['slug'] ); ?>"
									aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
								>
									<span aria-hidden="true"><?php echo esc_html( $module['icon'] ); ?></span>
									<strong><?php echo esc_html( $module['label'] ); ?></strong>
								</button>
							<?php endforeach; ?>
						</nav>
						<p class="cc-wheel-hint"><?php esc_html_e( 'Scroll · Arrow keys · Enter to select', 'chris-command' ); ?></p>

						<section class="cc-panel cc-audio-panel" aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-audio-title">
							<h2 id="<?php echo esc_attr( $instance_id ); ?>-audio-title"><?php esc_html_e( 'Audio Channel', 'chris-command' ); ?></h2>
							<div class="cc-audio-display" data-cc-audio-display>
								<span class="cc-audio-orbit" aria-hidden="true"></span>
								<div><strong><?php esc_html_e( 'Channel Standby', 'chris-command' ); ?></strong><small><?php esc_html_e( 'Paste a Spotify link to connect locally.', 'chris-command' ); ?></small></div>
							</div>
							<div class="cc-spotify-frame" data-cc-spotify-frame hidden></div>
							<form class="cc-audio-form" data-cc-audio-form>
								<label>
									<span><?php esc_html_e( 'Spotify URL', 'chris-command' ); ?></span>
									<input type="url" data-cc-spotify-input placeholder="https://open.spotify.com/..." autocomplete="off" />
								</label>
								<div><button type="submit"><?php esc_html_e( 'Load', 'chris-command' ); ?></button><button type="button" data-cc-audio-reset><?php esc_html_e( 'Reset', 'chris-command' ); ?></button></div>
							</form>
							<p class="cc-audio-status" data-cc-audio-status aria-live="polite"><?php esc_html_e( 'No public audio link is preloaded.', 'chris-command' ); ?></p>
						</section>
					</aside>

					<main class="cc-main-display cc-panel" data-cc-main>
						<header class="cc-display-header">
							<span><?php esc_html_e( 'Active Module', 'chris-command' ); ?></span>
							<strong data-cc-active-title><?php esc_html_e( 'Overview', 'chris-command' ); ?></strong>
						</header>
						<p class="cc-display-copy" data-cc-active-copy><?php esc_html_e( 'Public interface online. Live signals and approved modules are ready.', 'chris-command' ); ?></p>
						<section class="cc-module-stage" data-cc-stage aria-live="polite"></section>
					</main>

					<aside class="cc-right-rail" data-cc-right-rail>
						<section class="cc-panel cc-status-panel">
							<header><h2><?php esc_html_e( 'System Status', 'chris-command' ); ?></h2><button type="button" data-cc-rail-toggle aria-expanded="true" aria-label="<?php esc_attr_e( 'Hide status rail', 'chris-command' ); ?>">✦</button></header>
							<ul>
								<li><span></span><strong><?php esc_html_e( 'Dashboard', 'chris-command' ); ?></strong><small><?php esc_html_e( 'Online', 'chris-command' ); ?></small></li>
								<li><span></span><strong><?php esc_html_e( 'News Feed', 'chris-command' ); ?></strong><small data-cc-news-status><?php esc_html_e( 'Ready', 'chris-command' ); ?></small></li>
								<li><span></span><strong><?php esc_html_e( 'Public API', 'chris-command' ); ?></strong><small><?php esc_html_e( 'Connected', 'chris-command' ); ?></small></li>
							</ul>
						</section>

						<section class="cc-panel cc-overview-panel">
							<h2><?php esc_html_e( 'Today Overview', 'chris-command' ); ?></h2>
							<div class="cc-overview-reflection">
								<span><?php esc_html_e( 'Current Lane', 'chris-command' ); ?></span>
								<strong data-cc-current-lane><?php esc_html_e( 'Public Overview', 'chris-command' ); ?></strong>
								<p data-cc-current-detail><?php esc_html_e( 'Only approved public modules are visible in this interface.', 'chris-command' ); ?></p>
							</div>
							<div class="cc-focus-timer">
								<h3><?php esc_html_e( 'Focus Timer', 'chris-command' ); ?></h3>
								<strong data-cc-timer>25:00</strong>
								<div><button type="button" data-cc-timer-toggle><?php esc_html_e( 'Start Focus', 'chris-command' ); ?> <span aria-hidden="true">▶</span></button><button type="button" data-cc-timer-reset aria-label="<?php esc_attr_e( 'Reset focus timer', 'chris-command' ); ?>">↺</button></div>
							</div>
						</section>

						<section class="cc-panel cc-listings-panel">
							<h2><?php esc_html_e( 'Module Status', 'chris-command' ); ?></h2>
							<ul>
								<li><span>&gt;</span><strong><?php esc_html_e( 'News', 'chris-command' ); ?></strong><small><?php esc_html_e( 'Live', 'chris-command' ); ?></small></li>
								<li><span>&gt;</span><strong><?php esc_html_e( 'Public Shell', 'chris-command' ); ?></strong><small><?php esc_html_e( 'Review', 'chris-command' ); ?></small></li>
								<li><span>&gt;</span><strong><?php esc_html_e( 'Publishing', 'chris-command' ); ?></strong><small><?php esc_html_e( 'Standby', 'chris-command' ); ?></small></li>
							</ul>
						</section>
					</aside>

					<button type="button" class="cc-rail-reopen" data-cc-rail-reopen aria-label="<?php esc_attr_e( 'Show status rail', 'chris-command' ); ?>"><span aria-hidden="true">◀</span><strong>✦</strong></button>
				</div>

				<footer class="cc-footer">
					<span>CMD OS v<?php echo esc_html( CHRIS_COMMAND_VERSION ); ?></span>
					<strong><?php esc_html_e( 'Stay focused. Keep building. Make an impact.', 'chris-command' ); ?></strong>
					<span><?php esc_html_e( 'Online', 'chris-command' ); ?></span>
				</footer>
			</section>
		</div>
		<?php

		return (string) ob_get_clean();
	}
}
