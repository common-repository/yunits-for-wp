<?php
/**
 * Register service provider.
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

use YunitsForWP\Interfaces\Providers\ServiceProviderInterface;
use YunitsForWP\Interfaces\Services\ServiceInterface;

/**
 * Register service provider.
 *
 * @since 1.0.0
 */
class ServiceProvider implements ServiceProviderInterface
{
	protected $services = array();

	/**
	 * Registers the services.
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( $this->services as $service ) {
			$service->register();
		}
	}

	/**
	 * Boots the services.
	 *
	 * @return void
	 */
	public function boot(): void {
		foreach ( $this->services as $service ) {
			if ( false === $service instanceof ServiceInterface ) {
				continue;
			}
			$service->boot();
		}
	}
}
