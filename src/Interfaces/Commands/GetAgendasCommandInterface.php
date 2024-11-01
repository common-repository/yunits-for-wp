<?php
/**
 * Get agendas command interface.
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
 * Get agendas command interface.
 *
 * @since 1.0.0
 */
interface GetAgendasCommandInterface
{
	public function import( $args, $assoc_args );
}
