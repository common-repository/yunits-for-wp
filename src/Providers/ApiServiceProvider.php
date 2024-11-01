<?php
/**
 * Register api service provider.
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

use YunitsForWP\Interfaces\Controllers\ApiControllerInterface;
use YunitsForWP\Interfaces\Providers\ApiServiceProviderInterface;
use YunitsForWP\Interfaces\Services\YunitsServiceInterface;

/**
 * Register api service provider.
 *
 * @since 1.0.0
 */
class ApiServiceProvider extends ServiceProvider implements ApiServiceProviderInterface
{
	public function __construct(
		ApiControllerInterface $api,
		YunitsServiceInterface $yunits_service
	) {
		$this->services = array(
			'api'            => $api,
			'yunits_service' => $yunits_service,
		);
	}
}
