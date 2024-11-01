<?php
/**
 * Service interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Interfaces\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Service interface.
 *
 * @since 1.0.0
 */
interface ServiceInterface
{
	public function register();
	public function boot();
}
