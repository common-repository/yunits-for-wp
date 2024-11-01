<?php
/**
 * Base controller.
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
 * Base controller.
 *
 * @since 1.0.0
 */
class BaseController
{
	/**
	 * Register hooks callback
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Render view file and pass data to the file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file_path
	 * @param  array  $data
	 * @param  bool   $buffer
	 */
	public function render($file_path, $data = array(), $buffer = false ): mixed
	{
		if ( ! $buffer) {
			return yunits_for_wordpress_render_view_template( $file_path, $data );
		}
		ob_start();
		yunits_for_wordpress_render_view_template( $file_path, $data );
		return ob_get_clean();
	}
}
