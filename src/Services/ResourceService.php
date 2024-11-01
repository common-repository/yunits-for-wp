<?php
/**
 * Register resource service.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Interfaces\Services\ResourceServiceInterface;

/**
 * Register resource service.
 *
 * @since 1.0.0
 */
class ResourceService extends Service implements ResourceServiceInterface
{
	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
	}

	/**
	 * Register the admin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix
	 * @throws \Error Run npm build;
	 * @return void;
	 */
	public function register_admin_scripts( $hook_suffix )
	{
		// only load the scripts on the plugin settings page
		if ('settings_page_yunits-for-wp' !== $hook_suffix) {
			return;
		}

		$script_asset_path = YUNITS_FOR_WP_DIR_PATH . 'dist/admin.asset.php';

		if ( ! file_exists( $script_asset_path )) {
			throw new \Error(
				'You need to run `npm run watch` or `npm run build` to be able to use this plugin first.'
			);
		}

		$script_asset = require $script_asset_path;

		wp_enqueue_style(
			yunits_for_wordpress_prefix( 'admin-css' ),
			yunits_for_wordpress_asset_url( 'admin.css' ),
			array( 'wp-components' ),
			$script_asset['version']
		);

		wp_register_script(
			yunits_for_wordpress_prefix( 'admin-js' ),
			yunits_for_wordpress_asset_url( 'admin.js' ),
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_localize_script(
			yunits_for_wordpress_prefix( 'admin-js' ),
			'yfwSettings',
			array(
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'yfw_ajax_base' => esc_url_raw( rest_url( 'yfw/v1' ) ),
			)
		);

		wp_enqueue_script( yunits_for_wordpress_prefix( 'admin-js' ) );
	}
}
