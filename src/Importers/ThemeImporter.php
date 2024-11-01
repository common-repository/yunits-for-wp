<?php
/**
 * Register theme importer.
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

use YunitsForWP\Models\Theme;
use YunitsForWP\Services\YunitsService;
use YunitsForWP\Traits\Exists;

/**
 * Register theme importer.
 *
 * @since 1.0.0
 */
class ThemeImporter extends Importer
{
	use Exists;

	private $container;

	// The `pagination-limit` per api call.
	private const MAX_BATCH_SIZE = 100;

	public function __construct(
		YunitsService $container
	) {
		$this->container = $container;
	}

	/**
	 * Insert a theme as taxonomy item.
	 *
	 * @since 1.0.0
	 */
	public function insert( array $theme ): array|\WP_Error
	{
		$existing_term_id = $this->exists_term_id( $theme['id'], Theme::TAXONOMY );

		if ($existing_term_id) {
			return Theme::update_term_with_meta(
				$theme['title'],
				$theme,
				$existing_term_id,
			);
		}

		return Theme::insert_term_with_meta(
			$theme['title'],
			$theme
		);
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
				'yfw_import_themes',
				array( $next_page ),
				'yunits-for-wp'
			);
		}
	}

	/**
	 * Make api call and import the themes.
	 *
	 * @since 1.0.0
	 */
	public function import_themes( string $page ): void
	{
		$themes = $this->container->get_all_themes( $page, self::MAX_BATCH_SIZE );

		if ( ! empty( $themes )) {
			foreach ($themes as $term) {
				$term_array = $this->toArray( $term );

				// Community themes are not actual theme's so omit them.
				if (true === $term_array['community']) {
					continue;
				}

				$this->insert(
					$this->only(
						$term_array,
						array(
							'id',
							'title',
							'online',
							'community',
							'themeCategory',
							'type',
						)
					)
				);
			}

			// Schedule the next page import if there are more items
			if (count( $themes ) === self::MAX_BATCH_SIZE) {
				$this->schedule_next_import( $page + 1 );
			}
		}
	}
}
