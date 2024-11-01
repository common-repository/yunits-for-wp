<?php
/**
 * Get news command interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Interfaces\Commands;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Get news command interface.
 *
 * @since 1.0.0
 */
interface GetNewsCommandInterface
{
	public function import( $args, $assoc_args );
}
