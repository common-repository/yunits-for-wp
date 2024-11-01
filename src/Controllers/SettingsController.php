<?php
/**
 * Settings controller.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Settings controller.
 *
 * @since 1.0.0
 */
class SettingsController extends BaseController
{
	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_page(): void
	{
		$this->render( 'admin/settings-page.php' );
	}
}
