<?php
/**
 * Register settings service provider.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Controllers\SettingsController;
use YunitsForWP\Interfaces\Providers\SettingsServiceProviderInterface;

/**
 * Register settings service provider.
 *
 * @since 1.0.0
 */
class SettingsServiceProvider extends ServiceProvider implements SettingsServiceProviderInterface
{
	private SettingsController $controller;

	public const CAP_MANAGE_SETTINGS = 'yfw_manage_settings';

	public const DEFAULT_CRON_SCHEDULE = 'yfw_12_hour';

	public function __construct( SettingsController $controller )
	{
		$this->controller = $controller;
	}

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings_options' ) );
		add_filter( 'cron_schedules', array( $this, 'register_cron_schedules' ) );
		add_action( 'admin_init', array( $this, 'add_manage_settings_cap_to_admins' ) );
	}

	/**
	 * Add a settings page to the wp-admin.
	 *
	 * @since 1.0.0
	 */
	public function register_settings_page(): void
	{
		add_options_page(
			__( 'Yunits for WP', 'yunits-for-wp' ),
			__( 'Yunits for WP', 'yunits-for-wp' ),
			self::CAP_MANAGE_SETTINGS,
			'yunits-for-wp',
			array( $this->controller, 'render_page' )
		);
	}

	/**
	 * Initialize the options for the settings page.
	 *
	 * @since 1.0.0
	 */
	public function register_settings_options(): void
	{
		add_option( 'yfw_settings_agendas', array() );
		add_option( 'yfw_settings_general', array() );
		add_option( 'yfw_settings_knowledge_bases', array() );
		add_option( 'yfw_settings_news', array() );
	}

	/**
	 * Register custom cron schedule intervals.
	 *
	 * @since 1.10.0
	 */
	public static function register_cron_schedules(array $schedules ): array
	{
		$custom_schedules = self::get_cron_schedules();

		return array_merge( $schedules, $custom_schedules );
	}

	/**
	 * Define custom cron schedule intervals.
	 *
	 * @since 1.10.0
	 */
	public static function get_cron_schedules(): array
	{
		return array(
			'yfw_1_day'   => array(
				'interval' => 86400,
				'display'  => __( 'Every day', 'yunits-for-wp' ),
			),
			'yfw_12_hour' => array(
				'interval' => 43200,
				'display'  => __( 'Every 12 hours', 'yunits-for-wp' ),
			),
			'yfw_6_hour'  => array(
				'interval' => 21600,
				'display'  => __( 'Every 6 hours', 'yunits-for-wp' ),
			),
			'yfw_4_hour'  => array(
				'interval' => 14400,
				'display'  => __( 'Every 4 hours', 'yunits-for-wp' ),
			),
			'yfw_2_hour'  => array(
				'interval' => 7200,
				'display'  => __( 'Every 2 hours', 'yunits-for-wp' ),
			),
			'yfw_1_hour'  => array(
				'interval' => 3600,
				'display'  => __( 'Every hour', 'yunits-for-wp' ),
			),
		);
	}

	/**
	 * Add the manage settings capability to all administrators.
	 *
	 * @since 1.10.0
	 */
	public function add_manage_settings_cap_to_admins(): void {
		$role = get_role( 'administrator' );

		if ( $role && ! $role->has_cap( self::CAP_MANAGE_SETTINGS ) ) {
			$role->add_cap( self::CAP_MANAGE_SETTINGS );
		}
	}
}
