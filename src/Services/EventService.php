<?php
/**
 * Register wp_schedule_event service.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.2.0
 */

namespace YunitsForWP\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Interfaces\Services\EventServiceInterface;
use YunitsForWP\Importers\AgendaImporter;
use YunitsForWP\Importers\NewsImporter;
use YunitsForWP\Importers\ThemeImporter;
use YunitsForWP\Providers\SettingsServiceProvider;

/**
 * Register wp_schedule_event service.
 *
 * @since 1.2.0
 */
class EventService extends Service implements EventServiceInterface
{
	private const CRON_AGENDAS = 'yfw_import_agendas_cron';
	private const CRON_NEWS    = 'yfw_import_news_cron';
	private const CRON_THEMES  = 'yfw_import_themes_cron';

	private YunitsService $service;

	/**
	 * Register import events and hooks.
	 */
	public function register(): void
	{
		$this->service = new YunitsService();

		// Register import hooks.
		$this->register_import_hooks();

		// Register WP-Cron hooks.
		$this->register_cron_hooks();

		// Schedule cron events immediately.
		self::schedule();
	}

	/**
	 * Register importer hooks for agendas, news, and themes.
	 */
	private function register_import_hooks(): void
	{
		$agenda_importer = new AgendaImporter( $this->service );
		$news_importer   = new NewsImporter( $this->service );
		$theme_importer  = new ThemeImporter( $this->service );

		// Hook the import methods to WordPress actions
		add_action( 'yfw_import_agendas', array( $agenda_importer, 'import_agendas' ), 10, 1 );
		add_action( 'yfw_import_news', array( $news_importer, 'import_news' ), 10, 1 );
		add_action( 'yfw_import_themes', array( $theme_importer, 'import_themes' ), 10, 1 );
	}

	/**
	 * Register WP-Cron hooks.
	 *
	 * @since 1.12.0
	 */
	private function register_cron_hooks(): void
	{
		$this->register_single_cron_hook( self::CRON_AGENDAS, 'yfw_import_agendas' );
		$this->register_single_cron_hook( self::CRON_NEWS, 'yfw_import_news' );
		$this->register_single_cron_hook( self::CRON_THEMES, 'yfw_import_themes' );
	}

	/**
	 * Register a single cron hook to trigger an event.
	 *
	 * @since 1.12.0
	 * @param string $cron_hook The cron hook name.
	 * @param string $event The event to trigger.
	 */
	private function register_single_cron_hook( string $cron_hook, string $event ): void
	{
		add_action( $cron_hook, fn() => do_action( $event, 1 ) );
	}

	/**
	 * @inheritDoc
	 */
	public static function schedule(): void
	{
		// The binding of this same filter in `SettingsServiceProvider::register` apparently occurs *too late*
		add_filter( 'cron_schedules', array( SettingsServiceProvider::class, 'register_cron_schedules' ) );

		// Fetch general and individual settings
		$general_settings        = get_option( 'yfw_settings_general' );
		$agenda_settings         = get_option( 'yfw_settings_agendas' );
		$news_settings           = get_option( 'yfw_settings_news' );
		$knowledge_base_settings = get_option( 'yfw_settings_knowledge_bases' );

		// Determine recurrence schedule or use default
		$recurrence = $general_settings['schedule'] ?? SettingsServiceProvider::DEFAULT_CRON_SCHEDULE;
		$schedules  = SettingsServiceProvider::get_cron_schedules();

		// Ensure the recurrence is valid
		if ( ! isset( $schedules[ $recurrence ] ) ) {
			$recurrence = SettingsServiceProvider::DEFAULT_CRON_SCHEDULE;
		}

		// Schedule events for enabled imports
		self::maybe_schedule_event( self::CRON_AGENDAS, $recurrence, $agenda_settings );
		self::maybe_schedule_event( self::CRON_NEWS, $recurrence, $news_settings );
		self::maybe_schedule_event( self::CRON_THEMES, $recurrence, $knowledge_base_settings );
	}

	/**
	 * Conditionally schedule a WordPress event if the setting is enabled.
	 *
	 * @param string     $hook The cron hook.
	 * @param string     $recurrence The recurrence interval.
	 * @param array|null $settings The individual settings array.
	 */
	private static function maybe_schedule_event( string $hook, string $recurrence, ?array $settings ): void
	{
		// TODO: could this benefit in performance when only registered after a settings update? See APIController > update_settings
		if ( self::is_enabled( $settings ) ) {
			self::schedule_event_if_not_exists( $hook, $recurrence );
		}
	}

	/**
	 * Check if a setting is enabled.
	 *
	 * @since 1.12.0
	 * @param array|null $settings The settings to check.
	 * @return bool True if the setting is enabled, false otherwise.
	 */
	private static function is_enabled( ?array $settings ): bool
	{
		return isset( $settings['isEnabled'] ) && $settings['isEnabled'];
	}

	/**
	 * Schedule a WordPress event if it's not already scheduled.
	 *
	 * @since 1.2.0
	 * @param string $hook The cron hook name.
	 * @param string $recurrence The recurrence schedule.
	 */
	private static function schedule_event_if_not_exists( string $hook, string $recurrence ): void
	{
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event( time(), $recurrence, $hook );
		}
	}

	/**
	 * @inheritDoc
	 */
	public static function unschedule( string $hook ): void
	{
		$timestamp = wp_next_scheduled( $hook );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $hook );
		}
	}
}
