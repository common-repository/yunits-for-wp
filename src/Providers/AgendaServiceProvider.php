<?php
/**
 * Register agenda service provider.
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

use YunitsForWP\Models\Agenda;
use YunitsForWP\Interfaces\Providers\AgendaServiceProviderInterface;

/**
 * Register agenda service provider.
 *
 * @since 1.0.0
 */
class AgendaServiceProvider extends ServiceProvider implements AgendaServiceProviderInterface
{
	/**
	 * @inheritDoc
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_agenda_cpt' ) );
		add_filter( 'manage_' . Agenda::POST_TYPE . '_posts_columns', array( $this, 'add_agenda_columns' ) );
		add_action( 'manage_' . Agenda::POST_TYPE . '_posts_custom_column', array( $this, 'populate_agenda_column' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'set_custom_post_order' ) );
		add_filter( 'rest_' . Agenda::POST_TYPE . '_query', array( $this, 'modify_cpt_rest_query_order' ), 10, 2 );
	}

	/**
	 * @inheritDoc
	 */
	public function register_agenda_cpt(): void {
		$settings = get_option( 'yfw_settings_agendas' );

		if ( ! $settings ) return;

		$enabled  = $settings['isEnabled'];
		$plural   = $settings['labelPlural'] ?? __( 'Agendas', 'yunits-for-wp' );
		$singular = $settings['labelSingular'] ?? __( 'Agenda', 'yunits-for-wp' );

		if ($enabled) {
			register_post_type(
				Agenda::POST_TYPE,
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
						'edit_post'          => 'yfw_edit_agenda',
						'read_post'          => 'yfw_read_agenda',
						'delete_post'        => 'yfw_delete_agenda',
						'edit_posts'         => 'yfw_edit_agendas',
						'edit_others_posts'  => 'yfw_edit_others_agendas',
						'publish_posts'      => 'yfw_publish_agendas',
						'read_private_posts' => 'yfw_read_private_agendas',
						'create_posts'       => 'yfw_edit_agendas',
					),
					'map_meta_cap' => true,
				)
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function add_agenda_columns( array $columns ): array
	{
		return array(
			'cb'                              => $columns['cb'],
			'title'                           => __( 'Title' ),
			Agenda::POST_TYPE . '_start_date' => __( 'Start Date', 'yunits-for-wp' ),
			Agenda::POST_TYPE . '_end_date'   => __( 'End Date', 'yunits-for-wp' ),
			'date'                            => __( 'Date' ),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function populate_agenda_column( string $column, int $post_id ): void
	{
		if ( Agenda::POST_TYPE . '_start_date' === $column ) {
			$start_date = get_post_meta( $post_id, Agenda::POST_TYPE . '_item_start_date', true );

			echo esc_html( date_i18n( 'j F Y', strtotime( $start_date ) ) );
		}

		if ( Agenda::POST_TYPE . '_end_date' === $column ) {
			$end_date = get_post_meta( $post_id, Agenda::POST_TYPE . '_item_start_date', true );

			echo esc_html( date_i18n( 'j F Y', strtotime( $end_date ) ) );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function set_custom_post_order( \WP_Query $query ): void
	{
		if ( $query->is_main_query() && $query->get( 'post_type' ) === Agenda::POST_TYPE) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', Agenda::POST_TYPE . '_item_start_date' );
			$query->set( 'meta_type', 'DATETIME' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function modify_cpt_rest_query_order( array $args, \WP_REST_Request $request ): array
	{
		if ( isset( $args['post_type'] ) && Agenda::POST_TYPE === $args['post_type'] ) {
			$args['orderby']   = 'meta_value';
			$args['meta_key']  = Agenda::POST_TYPE . '_item_start_date';  // phpcs:ignore
			$args['meta_type'] = 'DATETIME';
			$args['order']     = 'ASC';
		}

		return $args;
	}
}
