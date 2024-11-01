<?php
/**
 * Register life cycle service.
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

use YunitsForWP\Interfaces\Services\LifeCycleServiceInterface;

/**
 * Register life cycle service.
 *
 * @since 1.0.0
 */
class LifeCycleService extends Service implements LifeCycleServiceInterface
{
	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		register_deactivation_hook(
			YUNITS_FOR_WP_FILE,
			array( $this, 'deactivate' )
		);

		register_uninstall_hook(
			YUNITS_FOR_WP_FILE,
			array( __CLASS__, 'uninstall' )
		);
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * @since 1.2.0
	 */
	public function deactivate(): void
	{
		EventService::unschedule( 'yfw_import_agendas_cron' );
		EventService::unschedule( 'yfw_import_news_cron' );
		EventService::unschedule( 'yfw_import_themes_cron' );
	}

	/**
	 * Plugin uninstall callback.
	 *
	 * @since 1.0.0
	 */
	public static function uninstall(): void
	{
	}
}
