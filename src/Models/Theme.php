<?php
/**
 * Register theme model.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Models;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Register theme model.
 *
 * @since 1.0.0
 */
class Theme
{
	public const TAXONOMY = 'yfw_knowledge_base_theme';

	protected array $theme;

	public function __construct( array $theme ) {
		$this->theme = $theme;
	}

	/**
	 * Insert terms and their meta.
	 *
	 * @since 1.0.0
	 */
	public static function insert_term_with_meta( $term, $theme ) {
		$meta      = self::add_metadata( $theme );
		$term_data = wp_insert_term( $term, self::TAXONOMY );

		if ( ! is_wp_error( $term_data ) && ! empty( $meta )) {
			$term_id = $term_data['term_id'];
			foreach ($meta as $key => $value) {
				add_term_meta( $term_id, $key, $value, true );
			}
		}

		return $term_data;
	}

	/**
	 * Update existing terms and their meta.
	 *
	 * @since 1.4.0
	 */
	public static function update_term_with_meta( $term, $theme, $term_id ) {
		$term_data = array(
			'name' => $term,
		);

		$updated_term = wp_update_term( $term_id, self::TAXONOMY, $term_data );

		if (is_wp_error( $updated_term )) {
			return $updated_term;
		}

		$meta = self::add_metadata( $theme );

		if ( ! empty( $meta )) {
			foreach ($meta as $key => $value) {
				update_term_meta( $term_id, $key, $value );
			}
		}

		return $updated_term;
	}

	public static function add_metadata( array $theme )
	{
		return array(
			'_yfw_theme_id'        => sanitize_text_field( $theme['id'] ),
			'_yfw_theme_online'    => sanitize_text_field( $theme['online'] ),
			'_yfw_theme_community' => sanitize_text_field( $theme['community'] ),
			'_yfw_theme_category'  => sanitize_text_field( $theme['themeCategory'] ),
			'_yfw_theme_type'      => sanitize_text_field( $theme['type'] ),
		);
	}
}
