<?php
/**
 * Register news model.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Models;

use DateTime;
use Exception;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Register news model.
 *
 * @since 1.0.0
 */
class News
{
	public const POST_TYPE = 'yfw_news';

	protected array $news;

	public function __construct( array $news )
	{
		$this->news = $news;
	}

	private static function validate_and_format_date( $date ): string
	{
		try {
			$datetime = new DateTime( $date );
			return $datetime->format( 'Y-m-d H:i:s' );
		} catch (Exception $e) {
			return current_time( 'mysql' );
		}
	}

	/**
	 * Converts a news item array to a WordPress post array.
	 *
	 * This method prepares a news item for insertion as a WordPress post by mapping the necessary fields
	 * and applying the required sanitization and formatting functions.
	 *
	 * Note: The `htmlspecialchars_decode()` and `esc_html()` functions are applied to the post content
	 * because `wp_kses_post()` removes data attributes, which are necessary for handling base64-encoded
	 * images. In some cases, images may be base64-encoded due to authorization requirements in Yunits.
	 * The `kses_allowed_protocols` filter cannot be utilized here because it would require enabling data
	 * URLs globally, which is not desirable.
	 */
	public static function to_post_array( array $news ): array
	{
		$published_date = self::validate_and_format_date( $news['published'] );

		return array(
			'post_type'      => self::POST_TYPE,
			'post_title'     => sanitize_text_field( $news['title'] ),
			'post_excerpt'   => sanitize_text_field( $news['teaser'] ),
			'post_content'   => htmlspecialchars_decode( esc_html( $news['content'] ) ),
			'post_date'      => $published_date,
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'meta_input'     => self::add_metadata( $news ),
		);
	}

	public static function add_metadata( array $news ): array
	{
		$sanitized_themes = array_map(
			function ( $theme ) {
				return array(
					'id'    => absint( $theme['id'] ),
					'title' => sanitize_text_field( $theme['title'] ),
				);
			},
			$news['themes']
		);

		return array(
			self::POST_TYPE . '_item_id'                  => sanitize_text_field( $news['id'] ),
			self::POST_TYPE . '_item_image'               => sanitize_text_field( $news['image'] ),
			self::POST_TYPE . '_item_web_detail'          => sanitize_text_field( $news['links']['web-detail'] ),
			self::POST_TYPE . '_item_web_detail_comments' => sanitize_text_field( $news['links']['web-detail-comments'] ),
			self::POST_TYPE . '_item_lead'                => sanitize_text_field( $news['contentFields']['lead'] ),
			self::POST_TYPE . '_item_role'                => sanitize_text_field( $news['role'] ),
			self::POST_TYPE . '_item_themes'              => $sanitized_themes,
		);
	}
}
