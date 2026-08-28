<?php
/**
 * Public dashboard module.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\Dashboard;

use ChrisCommand\Contracts\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the dashboard block, shortcode, assets, and optional page template.
 */
final class Dashboard_Module implements Module {
	private const BLOCK_NAME = 'chris-command/dashboard';
	private const SHORTCODE  = 'chris_command_dashboard';
	private const TEMPLATE   = 'chris-command-dashboard.php';

	/**
	 * Dashboard renderer.
	 *
	 * @var Dashboard_Renderer
	 */
	private Dashboard_Renderer $renderer;

	/**
	 * Constructs the module.
	 */
	public function __construct() {
		$this->renderer = new Dashboard_Renderer();
	}

	/**
	 * Returns the module slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return 'dashboard';
	}

	/**
	 * Registers public hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_template_assets' ) );
		add_filter( 'theme_page_templates', array( $this, 'register_page_template' ) );
		add_filter( 'template_include', array( $this, 'select_page_template' ) );
		add_shortcode( self::SHORTCODE, array( $this->renderer, 'render_shortcode' ) );
	}

	/**
	 * Registers assets and the primary dynamic block.
	 *
	 * @return void
	 */
	public function register_block(): void {
		wp_register_style(
			'chris-command-dashboard-fonts',
			'https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;800&family=Rajdhani:wght@500;600;700&family=Work+Sans:wght@400;500;600&display=swap',
			array(),
			CHRIS_COMMAND_VERSION
		);
		wp_register_style(
			'chris-command-dashboard',
			CHRIS_COMMAND_URL . 'assets/css/dashboard.css',
			array( 'chris-command-dashboard-fonts' ),
			CHRIS_COMMAND_VERSION
		);
		wp_register_script(
			'chris-command-dashboard',
			CHRIS_COMMAND_URL . 'assets/js/dashboard.js',
			array(),
			CHRIS_COMMAND_VERSION,
			true
		);

		register_block_type(
			CHRIS_COMMAND_PATH . 'blocks/dashboard',
			array(
				'render_callback' => array( $this->renderer, 'render_block' ),
			)
		);
	}

	/**
	 * Adds the standalone public dashboard template.
	 *
	 * @param array<string, string> $templates Registered templates.
	 * @return array<string, string>
	 */
	public function register_page_template( array $templates ): array {
		$templates[ self::TEMPLATE ] = __( 'Chris Command Dashboard', 'chris-command' );
		return $templates;
	}

	/**
	 * Uses the plugin-owned standalone template when selected.
	 *
	 * @param string $template Current template path.
	 * @return string
	 */
	public function select_page_template( string $template ): string {
		if ( is_singular( 'page' ) && self::TEMPLATE === get_page_template_slug() ) {
			return CHRIS_COMMAND_PATH . 'templates/chris-command-dashboard.php';
		}

		return $template;
	}

	/**
	 * Enqueues assets early for the template, block, or shortcode entry point.
	 *
	 * @return void
	 */
	public function enqueue_template_assets(): void {
		if ( ! is_singular( 'page' ) ) {
			return;
		}

		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$uses_dashboard = self::TEMPLATE === get_page_template_slug()
			|| has_block( self::BLOCK_NAME, $post )
			|| has_shortcode( $post->post_content, self::SHORTCODE );

		if ( ! $uses_dashboard ) {
			return;
		}

		wp_enqueue_style( 'chris-command-dashboard-fonts' );
		wp_enqueue_style( 'chris-command-dashboard' );
		wp_enqueue_script( 'chris-command-dashboard' );
	}

	/**
	 * Renders the shell for the plugin-owned page template.
	 *
	 * @return string
	 */
	public function render_template_dashboard(): string {
		return $this->renderer->render();
	}
}
