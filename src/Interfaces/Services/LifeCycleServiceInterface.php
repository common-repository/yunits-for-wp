<?php
/**
 * Life cycle service interface.
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
 * Life cycle service interface.
 *
 * @since 1.0.0
 */
interface LifeCycleServiceInterface extends ServiceInterface
{
	public static function uninstall();
}
