<?php
/**
 * Plugin helpers.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Add prefix for the given string.
 *
 * @param string $name - plugin name.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */
if ( ! function_exists( 'yunits_for_wordpress_prefix' )) {
	function yunits_for_wordpress_prefix($name )
	{
		return 'yunits-for-wp-' . $name;
	}
}

/**
 * Enforce correct plugin path.
 *
 * @param  string $path
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */
if ( ! function_exists( 'yunits_for_wordpress_url' )) {
	function yunits_for_wordpress_url($path )
	{
		return YUNITS_FOR_WP_PLUGIN_URL . $path;
	}
}

/**
 * Create asset url.
 *
 * @param   string $path
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */
if ( ! function_exists( 'yunits_for_wordpress_asset_url' )) {
	function yunits_for_wordpress_asset_url($path )
	{
		return yunits_for_wordpress_url( 'dist/' . $path );
	}
}

/**
 * Require a template file.
 *
 * @param   string $file_path
 * @param   array  $data
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 *
 * @throws \Exception - if file not found throw exception
 * @throws \Exception - if data is not array throw exception
 */
if ( ! function_exists( 'yunits_for_wordpress_render_template' )) {
	function yunits_for_wordpress_render_template( $file_path, $data = array() )
	{
		$file = YUNITS_FOR_WP_DIR_PATH . 'src/' . $file_path;
		if ( ! file_exists( $file )) {
			throw new \Exception( 'File not found' );
		}
		if ( ! is_array( $data )) {
			throw new \Exception( 'Expected array as data' );
		}
        extract($data, EXTR_PREFIX_SAME, 'todo');	// @phpcs:ignore

		return require_once $file;
	}
}

/**
 * Require a view template file.
 *
 * @param   string $file_path
 * @param   array  $data
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */
if ( ! function_exists( 'yunits_for_wordpress_render_view_template' )) {
	function yunits_for_wordpress_render_view_template($file_path, $data = array() )
	{
		return yunits_for_wordpress_render_template( 'Views/' . $file_path, $data );
	}
}
