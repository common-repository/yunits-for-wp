<?php
/**
 * Register api service.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Interfaces\Services\ApiServiceInterface;

/**
 * Register api service.
 *
 * @since 1.0.0
 */
class ApiService extends Service implements ApiServiceInterface
{
	private const DEFAULT_TIMEOUT = 15;

	/**
	 * The endpoint URL to send data to.
	 *
	 * @var string
	 */
	private $api_base_url;

	/**
	 * The oAuth clientID credentials.
	 *
	 * @var string
	 */
	protected $client_id;

	/**
	 * The oAuth clientSecret credentials.
	 *
	 * @var string
	 */
	protected $client_secret;

	/**
	 * The timeout in seconds for the API request.
	 *
	 * @var int
	 * @since 1.9.0
	 */
	protected $timeout;

	/**
	 * Cache key to use for the token.
	 *
	 * @var string
	 */
	private $token_cache_key = 'yfw_token_cache_key';

	public function __construct() {
		$settings          = get_option( 'yfw_settings_general' );
		$required_settings = array( 'apiBaseUrl', 'clientID', 'clientSecret' );

		if ( ! is_array( $settings )) return;

		foreach ($required_settings as $setting) {
			if ( ! array_key_exists( $setting, $settings )) {
				return;
			}
		}

		$this->api_base_url  = $settings['apiBaseUrl'];
		$this->client_id     = $settings['clientID'];
		$this->client_secret = $settings['clientSecret'];

		$timeout       = (int) ( $settings['timeout'] ?? null );
		$this->timeout = $timeout ? $timeout : self::DEFAULT_TIMEOUT;

		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks(): void
	{
		$type = get_option( 'yfw_settings_knowledge_bases' );

		if ($type && $type['isEnabled'] && isset( $type['assignedPostTypes'] )) {
			add_action( 'init', array( $this, 'register_knowledge_base_taxonomies' ) );
			add_filter( 'facetwp_api_can_access', array( $this, 'allow_facetwp_api_access' ) );

			foreach ($type['assignedPostTypes'] as $post_type) {
				add_filter( "rest_prepare_{$post_type}", array( $this, 'add_knowledge_base_data_to_default_cpt_endpoint' ), 10, 3 );
			}
		}

		// Register shortcode with the Yunits platform URL
		add_shortcode( 'yfw_show_api_base_url', array( $this, 'display_api_base_url_shortcode' ) );
	}

	/**
	 * Register Yunits taxonomies.
	 *
	 * @since 1.0.0
	 */
	public function register_knowledge_base_taxonomies(): void
	{
		$types = get_option( 'yfw_settings_knowledge_bases' );

		if (is_array( $types['assignedPostTypes'] )) {
			register_taxonomy(
				'yfw_knowledge_base_theme',
				$types['assignedPostTypes'],
				array(
					'label'             => __( 'Yunits Theme\'s', 'yunits-for-wp' ),
					'hierarchical'      => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					// this is because we don't want users to tinker with theme's coming from Yunits.
					'capabilities'      => array(
						'manage_terms' => 'manage_options',
						'edit_terms'   => 'manage_options',
						'delete_terms' => 'manage_options',
					),
					'labels'            => array(
						'singular_name'              => __( 'Yunits theme', 'yunits-for-wp' ),
						'all_items'                  => __( 'All Yunits theme\'s', 'yunits-for-wp' ),
						'edit_item'                  => __( 'Edit Yunits theme', 'yunits-for-wp' ),
						'view_item'                  => __( 'View Yunits theme', 'yunits-for-wp' ),
						'update_item'                => __( 'Update Yunits theme', 'yunits-for-wp' ),
						'add_new_item'               => __( 'Add New Yunits theme', 'yunits-for-wp' ),
						'new_item_name'              => __( 'New Yunits theme Name', 'yunits-for-wp' ),
						'search_items'               => __( 'Search Yunits theme\'s', 'yunits-for-wp' ),
						'popular_items'              => __( 'Popular Yunits theme\'s', 'yunits-for-wp' ),
						'separate_items_with_commas' => __( 'Separate Yunits theme\'s with comma', 'yunits-for-wp' ),
						'choose_from_most_used'      => __( 'Choose from most used Yunits theme\'s', 'yunits-for-wp' ),
						'not_found'                  => __( 'No Yunits theme\'s found', 'yunits-for-wp' ),
					),
				)
			);
		}
	}

	/**
	 * Register a shortcode to display the API Base URL as set in the settings.
	 *
	 * @since 1.8.0
	 */
	public function display_api_base_url_shortcode(): string
	{
		return esc_url( $this->api_base_url );
	}

	/**
	 * Allow access to FacetWP api.
	 *
	 * @since 1.0.0
	 */
	public function allow_facetwp_api_access(): bool
	{
		return true;
	}

	/**
	 * Add extra data to the default CPT endpoints.
	 *
	 * @since 1.0.0
	 */
	public function add_knowledge_base_data_to_default_cpt_endpoint(\WP_REST_Response $response, \WP_Post $post ): \WP_REST_Response
	{
		$author_id    = $post->post_author;
		$author_data  = get_userdata( $author_id );
		$author_email = $author_data->user_email;

		$terms = array();
		foreach ($response->data['yfw_knowledge_base_theme'] as $theme_id) {
			$terms[] = get_term_by( 'id', $theme_id, 'yfw_knowledge_base_theme' )->name;
		}

		$response->data['yfw_author_email']               = $author_email;
		$response->data['yfw_knowledge_base_theme_names'] = $terms;

		// This was added so that the Yunits platform does not need to call the separate `wp:featuredmedia` to retrieve the featured image.
		$thumbnail_url                        = get_the_post_thumbnail_url( $post->ID, 'large' );
		$response->data['yfw_featured_image'] = false !== $thumbnail_url ? $thumbnail_url : null;

		return $response;
	}

	/**
	 * Generates a new access token and caches it.
	 *
	 * @since 1.0.0
	 */
	private function generate_access_token(): TokenService
	{
		$response = wp_remote_post(
			$this->api_base_url . '/oauth/v2/token',
			array(
				'headers'    => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => sprintf( 'Basic %s', base64_encode( sprintf( '%s:%s', $this->client_id, $this->client_secret ) ) ),
					'timeout'       => $this->timeout,
				),
				'body'       => array(
					'grant_type' => 'client_credentials',
					'scope'      => 'email profile partner_api_agenda partner_api_news partner_api_theme',
				),
				'user-agent' => 'Yunits for WP/' . YUNITS_FOR_WP_VERSION . ';',
			)
		);

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$code = intval( wp_remote_retrieve_response_code( $response ) );

		$token = new TokenService( $body );

		set_transient( $this->token_cache_key, $token->to_json(), $token->expires_in() );

		return $token;
	}

	/**
	 * Retrieves the access token. This checks cache first, and if the cached token isn't valid then
	 * a new one is generated from the API.
	 *
	 * @since 1.0.0
	 */
	public function get_access_token(): TokenService
	{
		try {
			$token = TokenService::from_json( (string) get_transient( $this->token_cache_key ) );
			return ! $token->is_expired() ? $token : $this->generate_access_token();
		} catch ( \RuntimeException $e ) {
			return $this->generate_access_token();
		}
	}

	/**
	 * Make API request.
	 *
	 * @param string $endpoint API endpoint.
	 * @param array  $body     Array of data to send in the request.
	 * @param array  $headers  Array of headers.
	 * @param string $method   HTTP method.
	 *
	 * @since 1.0.0
	 */
	protected function make_request( $endpoint, $body = array(), $headers = array(), $method = 'POST' ): mixed
	{
		$headers = wp_parse_args(
			$headers,
			array(
				'Content-Type'  => 'application/json',
				'Authorization' => sprintf( 'Bearer %s', $this->get_access_token()->token() ),
			)
		);

		$request_args = array(
			'method'     => $method,
			'timeout'    => $this->timeout,
			'headers'    => $headers,
			'user-agent' => 'Yunits for WP/' . YUNITS_FOR_WP_VERSION . ';',
		);

		if ( ! empty( $body ) ) {
			$request_args['body'] = $body;
		}

		$response = wp_remote_request( $this->api_base_url . $endpoint, $request_args );

		return json_decode( wp_remote_retrieve_body( $response ) );
	}
}
