<?php
/**
 * App service provider (registers general plugins functionality).
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

use YunitsForWP\Interfaces\Providers\AppServiceProviderInterface;
use YunitsForWP\Interfaces\Services\EventServiceInterface;
use YunitsForWP\Interfaces\Services\ResourceServiceInterface;
use YunitsForWP\Interfaces\Services\LifeCycleServiceInterface;

/**
 * App service provider (registers general plugins functionality).
 *
 * @since 1.0.0
 */
class AppServiceProvider extends ServiceProvider implements AppServiceProviderInterface
{
	public function __construct(
		EventServiceInterface $event_service,
		LifeCycleServiceInterface $life_cycle_service,
		ResourceServiceInterface $resource_service
	) {
		$this->services = array(
			'event'      => $event_service,
			'life_cycle' => $life_cycle_service,
			'resource'   => $resource_service,
		);
	}
}
