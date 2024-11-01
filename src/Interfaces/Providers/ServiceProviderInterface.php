<?php
/**
 * Service provider interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Interfaces\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Service provider interface.
 *
 * @since 1.0.0
 */
interface ServiceProviderInterface
{
	/**
	 * Register provider.
	 *
	 * @since 1.0.0
	 */
	public function register();
}
