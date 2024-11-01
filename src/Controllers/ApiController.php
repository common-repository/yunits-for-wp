<?php
/**
 * Api controller.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WP_REST_Controller;
use YunitsForWP\Interfaces\Controllers\ApiControllerInterface;
use YunitsForWP\Providers\SettingsServiceProvider;
use YunitsForWP\Services\EventService;

/**
 * Api controller.
 *
 * @since 1.0.0
 */
class ApiController extends WP_REST_Controller implements ApiControllerInterface
{
	/**
	 * Namespace to prefix REST calls.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $context = 'yfw/';

	/**
	 * The current version of the REST calls.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = 'v1';

	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register api routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes(): void
	{
		$context = $this->context . $this->version;

		register_rest_route(
			$context,
			'/settings',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_options_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'get_options_permission' ),
				),
			)
		);

		register_rest_route(
			$context,
			'/trigger-import',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'trigger_import' ),
					'args'                => array(
						'type' => array(
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param );
							},
						),
					),
					'permission_callback' => array( $this, 'get_options_permission' ),
				),
			)
		);

		register_rest_route(
			$context,
			'/assigned-knowledge-bases',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_assigned_knowledge_bases' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * Get admin settings.
	 *
	 * @since 1.0.0
	 */
	public function get_settings(): \WP_REST_Response
	{
		$option_agendas           = get_option( 'yfw_settings_agendas' );
		$option_general           = get_option( 'yfw_settings_general' );
		$option_knowledge_bases   = get_option( 'yfw_settings_knowledge_bases' );
		$option_news              = get_option( 'yfw_settings_news' );
		$all_available_post_types = get_post_types();

		$schedules = wp_get_schedules();

		// Show only the schedules defined for this plugin
		$schedules = array_filter(
			$schedules,
			static fn ( $key ) => str_starts_with( $key, 'yfw_' ),
			ARRAY_FILTER_USE_KEY
		);
		uasort(
			$schedules,
			static fn ( array $a, array $b ): int => (int) $b['interval'] - (int) $a['interval']
		);
		$schedules = array_map(
			static fn ( array $schedule ): string => $schedule['display'],
			$schedules
		);

		return new \WP_REST_Response(
			array(
				'success' => true,
				'value'   => array(
					'agendas'         => $option_agendas,
					'general'         => $option_general,
					'knowledge_bases' => $option_knowledge_bases,
					'news'            => $option_news,
					'post_types'      => $all_available_post_types,
					'schedules'       => $schedules,
				),
			),
			200
		);
	}

	/**
	 * Update admin settings.
	 *
	 * @since 1.0.0
	 */
	public function update_settings( \WP_REST_Request $request ): \WP_REST_Response
	{
		$agendas         = $request->get_param( 'agendas' );
		$general         = $request->get_param( 'general' );
		$knowledge_bases = $request->get_param( 'knowledgeBases' );
		$news            = $request->get_param( 'news' );

		$general_old      = get_option( 'yfw_settings_general' );
		$schedule_old     = ( $general_old['schedule'] ?? null ) ?: SettingsServiceProvider::DEFAULT_CRON_SCHEDULE;
		$schedule         = ( $general['schedule'] ?? null ) ?: SettingsServiceProvider::DEFAULT_CRON_SCHEDULE;
		$schedule_changed = $schedule !== $schedule_old;

		update_option( 'yfw_settings_agendas', $agendas );
		update_option( 'yfw_settings_general', $general );
		update_option( 'yfw_settings_knowledge_bases', $knowledge_bases );
		update_option( 'yfw_settings_news', $news );

		if ($schedule_changed || false === $agendas['isEnabled']) {
			EventService::unschedule( 'yfw_import_agendas_cron' );
		}

		if ($schedule_changed || false === $news['isEnabled']) {
			EventService::unschedule( 'yfw_import_news_cron' );
		}

		if ($schedule_changed || false === $knowledge_bases['isEnabled']) {
			EventService::unschedule( 'yfw_import_themes_cron' );
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
			),
			200
		);
	}

	/**
	 * Trigger an import this api is used to trigger an import manually for example.
	 *
	 * @since 1.0.0
	 */
	public function trigger_import( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error
	{
		$type = $request->get_param( 'type' );

		if ( ! $type ) {
			return new \WP_Error( 'missing_type', 'Type parameter is required', array( 'status' => 400 ) );
		}

		if ('agenda' === $type) {
			do_action( 'yfw_import_agendas', 1 );
			return new \WP_REST_Response( 'Success', 200 );
		}

		if ('news' === $type) {
			do_action( 'yfw_import_news', 1 );
			return new \WP_REST_Response( 'Success', 200 );
		}

		if ('theme' === $type) {
			do_action( 'yfw_import_themes', 1 );
			return new \WP_REST_Response( 'Success', 200 );
		}

		return new \WP_REST_Response( 'Invalid type', 400 );
	}

	/**
	 * Get assigned knowledge bases.
	 *
	 * @since 1.0.0
	 */
	public function get_assigned_knowledge_bases(): \WP_REST_Response
	{
		$option_knowledge_bases = get_option( 'yfw_settings_knowledge_bases' );

		if ( ! $option_knowledge_bases) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'value'   => array(),
				),
				404
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'value'   => $option_knowledge_bases,
			),
			200
		);
	}

	/**
	 * Check if user may access the endpoints.
	 *
	 * @since 1.0.0
	 */
	public function get_options_permission(): \WP_Error|bool
	{
		if ( ! current_user_can( SettingsServiceProvider::CAP_MANAGE_SETTINGS )) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to manage options.', 'yunits-for-wp' ), array( 'status' => 401 ) );
		}

		return true;
	}
}
