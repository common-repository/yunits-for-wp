<?php
/**
 * Service provider interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Interfaces\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Service provider interface.
 *
 * @since 1.0.0
 */
interface AgendaServiceProviderInterface extends ServiceProviderInterface
{
	/**
	 * Register provider.
	 *
	 * @since 1.0.0
	 */
	public function register();

	/**
	 * Register agenda CPT.
	 *
	 * @since 1.0.0
	 */
	public function register_agenda_cpt(): void;

	/**
	 * Change the admin columns for the custom post type.
	 *
	 * @since 1.0.0
	 */
	public function add_agenda_columns( array $columns ): array;

	/**
	 * Populate the newly added admin columns.
	 *
	 * @since 1.0.0
	 */
	public function populate_agenda_column( string $column, int $post_id ): void;

	/**
	 * Set a custom sort order for the wp-admin agenda cpt posts.
	 *
	 * @since 1.0.0
	 */
	public function set_custom_post_order( \WP_Query $query ): void;

	/**
	 * Set a custom sort order for the REST api agenda cpt endpoint.
	 */
	public function modify_cpt_rest_query_order( array $args, \WP_REST_Request $request ): array;
}
