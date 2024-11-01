<?php
/**
 * Yunits service interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Interfaces\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Yunits service interface.
 *
 * @since 1.0.0
 */
interface YunitsServiceInterface extends ServiceInterface
{
	/**
	 * Fetch the agenda items.
	 *
	 * @since 1.0.0
	 * @param string $page
	 * @param string $limit
	 */
	public function get_agenda_items( $page, $limit );

	/**
	 * Fetch the news items.
	 *
	 * @since 1.0.0
	 * @param string $page
	 * @param string $limit
	 */
	public function get_news_items( $page, $limit );

	/**
	 * Fetch all themes.
	 *
	 * @since 1.0.0
	 * @param string $page
	 * @param string $limit
	 */
	public function get_all_themes( $page, $limit );
}
