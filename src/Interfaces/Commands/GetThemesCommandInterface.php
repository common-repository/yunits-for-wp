<?php
/**
 * Get themes command interface.
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
 * Get themes command interface.
 *
 * @since 1.0.0
 */
interface GetThemesCommandInterface
{
	public function import( $args, $assoc_args );
}
