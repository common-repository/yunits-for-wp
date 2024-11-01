<?php
/**
 * Register importer.
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

/**
 * Register importer.
 *
 * @since 1.0.0
 */
class Importer
{
	/**
	 * Recursively converts stdClass objects to arrays.
	 */
	protected function toArray(mixed $data ): mixed
	{
		if (is_object( $data )) {
			$data = (array) $data;
		}

		if (is_array( $data )) {
			return array_map( array( $this, 'toArray' ), $data );
		}

		return $data;
	}

	/**
	 * Filters the given array or object to include only the specified keys.
	 */
	protected function only(array $collection, array $keys ): array
	{
		return array_filter(
			$collection,
			function ($key ) use ($keys ) {
				return in_array( $key, $keys, true );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Recursively processes the array, if needed.
	 */
	protected function recursive(array $collection ): array
	{
		// Implement any recursive processing if needed. If not, simply return the array.
		// For the sake of this example, we'll assume no additional processing is required.
		return $collection;
	}

	/**
	 * Pause the indexers while importing content and optimize queries.
	 */
	protected function pause_indexers(): void
	{
		// Disable FacetWP indexer
		// (https://facetwp.com/help-center/developers/hooks/indexing-hooks/facetwp_indexer_is_enabled/#disable-automatic-indexing-for-a-file)
		\add_filter( 'facetwp_indexer_is_enabled', '__return_false' );

		// Pause SearchWP indexer
		// (https://searchwp.com/documentation/knowledge-base/content-imports-migrations/)
		if (class_exists( 'SearchWP' )) {
			\SearchWP::$indexer->pause();
		}

		// https://developer.wordpress.org/reference/classes/wp_importer/stop_the_insanity/
		if ( ! defined( 'WP_IMPORTING' )) {
			define( 'WP_IMPORTING', true );
		}

		\wp_defer_term_counting( true );
		\wp_defer_comment_counting( true );
	}

	/**
	 * Resume the indexers and queries if there are no more scheduled actions.
	 */
	protected function resume_indexers(): void
	{
		// In theory multiple importers can be running simultaneously, we don't want to resume unless we know none are running.
		$scheduled_actions = as_get_scheduled_actions(
			array(
				'group'  => 'yunits-for-wp',
				'status' => 'ActionScheduler_Store::STATUS_PENDING',
			)
		);

		if ( ! empty( $scheduled_actions )) {
			return;
		}

		\add_filter( 'facetwp_indexer_is_enabled', '__return_true' );

		if (class_exists( 'SearchWP' )) {
			\SearchWP::$indexer->unpause();
		}

		\wp_defer_term_counting( false );
		\wp_defer_comment_counting( false );

		// Trigger a new index.
		if ( function_exists( 'FWP' ) ) {
			FWP()->indexer->index();
		}
	}
}
