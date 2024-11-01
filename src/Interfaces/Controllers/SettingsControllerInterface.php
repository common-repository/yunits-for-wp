<?php
/**
 * Settings Controller Interface.
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
 * Settings Controller Interface.
 *
 * @since 1.0.0
 */
interface SettingsControllerInterface extends BaseControllerInterface
{
	public function render_page();
}
