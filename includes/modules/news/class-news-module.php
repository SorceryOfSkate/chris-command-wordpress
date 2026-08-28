<?php
/**
 * Public News module.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

use ChrisCommand\Contracts\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wires News services to WordPress.
 */
final class News_Module implements Module {
	/**
	 * Shared renderer.
	 *
	 * @var News_Renderer
	 */
	private News_Renderer $renderer;

	/**
	 * REST controller.
	 *
	 * @var News_REST_Controller
	 */
	private News_REST_Controller $rest_controller;

	/**
	 * Constructs the module's local dependency graph.
	 */
	public function __construct() {
		$service = new News_Service(
			new WordPress_Feed_Client(),
			new Feed_Normalizer(),
			new Transient_Cache_Store()
		);

		$this->renderer        = new News_Renderer( $service );
		$this->rest_controller = new News_REST_Controller( $service );
	}

	/**
	 * Returns the public module slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return 'news';
	}

	/**
	 * Registers News hooks without fetching remote data.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'rest_api_init', array( $this->rest_controller, 'register_routes' ) );
		add_shortcode( 'chris_command_news', array( $this->renderer, 'render_shortcode' ) );
	}

	/**
	 * Registers the dynamic Block API v3 block.
	 *
	 * @return void
	 */
	public function register_block(): void {
		register_block_type(
			CHRIS_COMMAND_PATH . 'blocks/news',
			array(
				'render_callback' => array( $this->renderer, 'render_block' ),
			)
		);
	}
}
