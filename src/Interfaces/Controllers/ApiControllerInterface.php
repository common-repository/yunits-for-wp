<?php
/**
 * Api controller interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Interfaces\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Api controller interface.
 *
 * @since 1.0.0
 */
interface ApiControllerInterface
{
	public function register();
	public function register_routes();
	public function get_settings();
	public function update_settings( \WP_REST_Request $request );
	public function get_options_permission();
}
