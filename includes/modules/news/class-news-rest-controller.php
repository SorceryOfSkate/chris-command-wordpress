<?php
/**
 * Public News REST endpoint.
 *
 * @package ChrisCommand
 */

namespace ChrisCommand\Modules\News;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the read-only News endpoint.
 */
final class News_REST_Controller {
	private const NAMESPACE = 'chris-command/v1';
	private const ROUTE     = '/news';

	/**
	 * News service.
	 *
	 * @var News_Service
	 */
	private News_Service $service;

	/**
	 * Constructs the controller.
	 *
	 * @param News_Service $service News service.
	 */
	public function __construct( News_Service $service ) {
		$this->service = $service;
	}

	/**
	 * Registers the public route.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_news' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'category' => array(
						'description'       => __( 'Optional approved News category slug.', 'chris-command' ),
						'type'              => 'string',
						'enum'              => array_keys( Categories::all() ),
						'sanitize_callback' => 'sanitize_key',
						'required'          => false,
					),
				),
				'schema'              => array( $this, 'get_schema' ),
			)
		);
	}

	/**
	 * Returns one category or all seven categories.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_news( \WP_REST_Request $request ): \WP_REST_Response {
		$slug     = sanitize_key( (string) $request->get_param( 'category' ) );
		$data     = '' !== $slug ? $this->service->get_category( $slug ) : $this->service->get_all();
		$response = rest_ensure_response( $data );
		$response->header( 'Cache-Control', 'public, max-age=300' );

		return $response;
	}

	/**
	 * Returns a minimal route schema.
	 *
	 * @return array<string, mixed>
	 */
	public function get_schema(): array {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'chris-command-news',
			'type'       => 'object',
			'properties' => array(
				'articles'   => array(
					'type' => 'array',
				),
				'categories' => array(
					'type' => 'array',
				),
			),
		);
	}
}
