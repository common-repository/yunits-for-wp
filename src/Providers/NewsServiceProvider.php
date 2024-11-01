<?php
/**
 * Register news service provider.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Models\News;
use YunitsForWP\Interfaces\Providers\NewsServiceProviderInterface;

/**
 * Register news service provider.
 *
 * @since 1.0.0
 */
class NewsServiceProvider extends ServiceProvider implements NewsServiceProviderInterface
{
	/**
	 * @inheritDoc
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_news_cpt' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function register_news_cpt(): void {
		$settings = get_option( 'yfw_settings_news' );

		if ( ! $settings) return;

		$enabled  = $settings['isEnabled'];
		$plural   = $settings['labelPlural'] ?? __( 'News', 'yunits-for-wp' );
		$singular = $settings['labelSingular'] ?? __( 'News', 'yunits-for-wp' );

		if ($enabled) {
			register_post_type(
				News::POST_TYPE,
				array(
					'has_archive'  => false,
					'labels'       => array(
						'name'               => $plural,
						'singular_name'      => $singular,
						// translators: %s is the singular label
						'add_new'            => sprintf( __( 'New %s', 'yunits-for-wp' ), $singular ),
						// translators: %s is the singular label
						'add_new_item'       => sprintf( __( 'Add New %s', 'yunits-for-wp' ), $singular ),
						// translators: %s is the singular label
						'edit_item'          => sprintf( __( 'Edit %s', 'yunits-for-wp' ), $singular ),
						// translators: %s is the singular label
						'new_item'           => sprintf( __( 'New %s', 'yunits-for-wp' ), $singular ),
						// translators: %s is the singular label
						'view_item'          => sprintf( __( 'View %s', 'yunits-for-wp' ), $singular ),
						// translators: %s is the plural label
						'search_items'       => sprintf( __( 'Search %s', 'yunits-for-wp' ), $plural ),
						// translators: %s is the plural label
						'not_found'          => sprintf( __( 'No %s Found', 'yunits-for-wp' ), $plural ),
						// translators: %s is the plural label
						'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'yunits-for-wp' ), $plural ),
					),
					'menu_icon'    => 'dashicons-book',
					'show_in_menu' => true,
					'public'       => true,
					'rewrite'      => array(
						'slug' => sanitize_title_with_dashes( $plural ),
					),
					'show_in_rest' => true,
					'capabilities' => array(
						'edit_post'          => 'yfw_edit_news_item',
						'read_post'          => 'yfw_read_news_item',
						'delete_post'        => 'yfw_delete_news_item',
						'edit_posts'         => 'yfw_edit_news_items',
						'edit_others_posts'  => 'yfw_edit_others_news_items',
						'publish_posts'      => 'yfw_publish_news_items',
						'read_private_posts' => 'yfw_read_private_news_Items',
						'create_posts'       => 'yfw_edit_news_items',
					),
					'map_meta_cap' => true,
				)
			);
		}
	}
}
