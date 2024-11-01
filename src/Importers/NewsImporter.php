<?php
/**
 * Register news importer.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Importers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Models\News;
use YunitsForWP\Models\Theme;
use YunitsForWP\Services\YunitsService;
use YunitsForWP\Traits\Exists;

/**
 * Register news importer.
 *
 * @since 1.0.0
 */
class NewsImporter extends Importer
{
	use Exists;

	private YunitsService $container;

	private $yfw_settings_cache;

	// The `pagination-limit` per api call.
	private const MAX_BATCH_SIZE = 100;

	public function __construct(
		YunitsService $container
	) {
		$this->container = $container;

		$this->yfw_settings_cache = get_option( 'yfw_settings_knowledge_bases' );
	}

	/**
	 * See: https://developer.wordpress.org/reference/functions/wp_insert_post/#comment-6514
	 *
	 * @since 1.0.0
	 */
	public function with_post_modified( array $data, array $post_array ): array {
		$data['post_modified']     = $post_array['post_modified'] ?? null;
		$data['post_modified_gmt'] = $post_array['post_modified_gmt'] ?? get_gmt_from_date( $data['post_modified'] );
		$data['post_modified']     = $data['post_modified'] ?? get_date_from_gmt( $data['post_modified_gmt'] );

		return $data;
	}

	/**
	 * Insert a news item as post.
	 *
	 * @since 1.0.0
	 */
	public function insert( array $news ): int|\WP_Error
	{
		$post_array        = News::to_post_array( $news );
		$id_already_exists = $this->exists_id( $news, News::POST_TYPE );

		if ($id_already_exists) {
			return $this->update( $post_array, $id_already_exists );
		}

		add_filter( 'wp_insert_post_data', array( $this, 'with_post_modified' ), PHP_INT_MAX, 2 );
		$post_id = wp_insert_post( $post_array, true );
		remove_filter( 'wp_insert_post_data', array( $this, 'with_post_modified' ), PHP_INT_MAX );

		$this->add_the_terms( $post_array, $post_id );

		return $post_id;
	}

	/**
	 * Update a news item as post.
	 *
	 * @since 1.0.0
	 */
	public function update( array $post_array, int $post_id ): int|\WP_Error {
		$post_array['ID'] = $post_id;

		$post_id = wp_update_post( $post_array, true );

		$this->add_the_terms( $post_array, $post_id );

		return $post_id;
	}

	/**
	 * Delete a news item as post.
	 *
	 * @since 1.0.0
	 */
	public function delete( array $news ): void
	{
		$id_already_exists = $this->exists_id( $news, News::POST_TYPE );

		if ($id_already_exists) {
			wp_delete_post( $id_already_exists, true );
		}
	}

	/**
	 * Add the terms to the post if the Post Type contains the taxonomy.
	 *
	 * @since 1.6.0
	 */
	public function add_the_terms( array $post_array, int $post_id ): void
	{
		// Possibly add the terms if the Post Type is set as a Knowledge Base, so it retrieves the Yunits Theme's.
		$yfw_settings = $this->yfw_settings_cache;

		if ($yfw_settings && ! empty( $yfw_settings['assignedPostTypes'] ) && is_array( $yfw_settings['assignedPostTypes'] )) {
			if (in_array( News::POST_TYPE, $yfw_settings['assignedPostTypes'], true )) {
				$themes = $post_array['meta_input'][ News::POST_TYPE . '_item_themes' ] ?? array();

				if (is_array( $themes ) && ! empty( $themes )) {
					$term_slugs = array();

					foreach ($themes as $theme) {
						$existing_term_id = $this->exists_term_id( $theme['id'], Theme::TAXONOMY );

						if ($existing_term_id) {
							$term_obj = get_term_by( 'id', $existing_term_id, Theme::TAXONOMY );

							if ($term_obj) {
								$term_slugs[] = $term_obj->slug; // Collect slugs
							}
						}
					}

					// Batch assign terms in a single call.
					if ( ! empty( $term_slugs )) {
						wp_set_object_terms( $post_id, $term_slugs, Theme::TAXONOMY, true );
					}
				}
			}
		}
	}

	/**
	 * Schedule a next import batch.
	 *
	 * @since 1.0.0
	 */
	public function schedule_next_import( string $next_page ): void
	{
		if (class_exists( 'ActionScheduler' )) {
			\as_schedule_single_action(
				time() + 10, // Schedule 10 seconds later.
				'yfw_import_news',
				array( $next_page ),
				'yunits-for-wp'
			);
		}
	}

	/**
	 * Make api call and import the news.
	 *
	 * @since 1.0.0
	 */
	public function import_news( string $page ): void
	{
		// Extend timeout to 5 minutes.
		set_time_limit( 300 );

		$news_items = $this->container->get_news_items( $page, self::MAX_BATCH_SIZE );

		$this->pause_indexers();

		if ( ! empty( $news_items )) {
			foreach ($news_items as $post) {
				$post_array = $this->toArray( $post );

				// activeCommon on Yunits tells us if a post may or may not be displayed on WordPress.
				if (isset( $post_array['activeCommon'] ) && false === $post_array['activeCommon']) {
					$this->delete( $post_array );
					continue;
				}

				$this->insert(
					$this->recursive(
						$this->only(
							$post_array,
							array(
								'id',
								'image',
								'title',
								'content',
								'teaser',
								'published',
								'links',
								'contentFields',
								'role',
								'themes',
							)
						)
					)
				);

				unset( $post_array );
			}

			// Schedule the next page import if there are more items
			if (count( $news_items ) === self::MAX_BATCH_SIZE) {
				$this->schedule_next_import( $page + 1 );
			}

			unset( $news_items );
		}

		$this->resume_indexers();
	}
}
