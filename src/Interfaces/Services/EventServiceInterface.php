<?php
/**
 * Event service interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.2.0
 */

namespace YunitsForWP\Interfaces\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Event service interface.
 *
 * @since 1.2.0
 */
interface EventServiceInterface extends ServiceInterface
{
	/**
	 * Schedule recurring import events.
	 *
	 * @since 1.2.0
	 */
	public static function schedule();

	/**
	 * Clear scheduled import events.
	 *
	 * @since 1.2.0
	 * @param string $hook The cron hook name.
	 */
	public static function unschedule( string $hook ): void;
}
