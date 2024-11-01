<?php
/**
 * Base Controller Interface.
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
 * Base Controller Interface.
 *
 * @since 1.0.0
 */
interface BaseControllerInterface
{
	public function register();
	public function render( $file_path, $data, $buffer );
}
