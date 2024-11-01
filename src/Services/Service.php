<?php
/**
 * Register base service.
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

use YunitsForWP\Interfaces\Services\ServiceInterface;

/**
 * Register base service.
 */
class Service implements ServiceInterface
{
	/**
	 * Register the service.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
	}

	/**
	 * Called when all services are registered.
	 *
	 * @since 1.0.0
	 */
	public function boot(): void
	{
	}
}
