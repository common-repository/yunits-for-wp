<?php
/**
 * Exists trait.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Traits;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Exists trait.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */
trait Exists
{
	public function exists_id( array $model, string $post_type ): int
	{
		$args = array(
			'post_type'   => $post_type,
			'post_status' => array( 'publish', 'draft' ),
			'meta_query'  => array( // phpcs:ignore
				array(
					'key'     => $post_type . '_item_id',
					'value'   => $model['id'],
					'compare' => '=',
				),
			),
		);

		$query = new \WP_Query( $args );

		if (empty( $query->post )) {
			return 0;
		}

		return $query->post->ID;
	}

	/**
	 * Check if a term exists in a given taxonomy by ID.
	 */
	public function exists_term_id(int $theme_id, string $taxonomy ): bool|int
	{
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'meta_query' => array( // phpcs:ignore
					array(
						'key'     => '_yfw_theme_id',
						'value'   => sanitize_text_field( $theme_id ),
						'compare' => '=',
					),
				),
			)
		);

		if ( ! empty( $terms ) && ! is_wp_error( $terms )) {
			return $terms[0]->term_id;
		}

		return false;
	}
}
