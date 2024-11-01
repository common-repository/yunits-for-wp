<?php
/**
 * Register agenda model.
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
 * Register agenda model.
 *
 * @since 1.0.0
 */
class Agenda
{
	public const POST_TYPE = 'yfw_agenda';

	protected array $agenda;

	public function __construct( array $agenda )
	{
		$this->agenda = $agenda;
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
	 * Converts an agenda item array to a WordPress post array.
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
	public static function to_post_array( array $agenda ): array
	{
		$published_date = self::validate_and_format_date( $agenda['published'] );

		return array(
			'post_type'      => self::POST_TYPE,
			'post_title'     => sanitize_text_field( $agenda['title'] ),
			'post_excerpt'   => sanitize_text_field( $agenda['teaser'] ),
			'post_content'   => htmlspecialchars_decode( esc_html( $agenda['content'] ) ),
			'post_date'      => $published_date,
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'meta_input'     => self::add_metadata( $agenda ),
		);
	}

	public static function add_metadata( array $agenda ): array
	{
		$start_date = self::validate_and_format_date( $agenda['startDate'] );
		$end_date   = self::validate_and_format_date( $agenda['endDate'] );

		$sanitized_themes = array_map(
			function ( $theme ) {
				return array(
					'id'    => absint( $theme['id'] ),
					'title' => sanitize_text_field( $theme['title'] ),
				);
			},
			$agenda['themes']
		);

		$data = array(
			self::POST_TYPE . '_item_id'                  => sanitize_text_field( $agenda['id'] ),
			self::POST_TYPE . '_item_image'               => sanitize_text_field( $agenda['image'] ),
			self::POST_TYPE . '_item_web_detail'          => sanitize_text_field( $agenda['links']['web-detail'] ),
			self::POST_TYPE . '_item_web_detail_comments' => sanitize_text_field( $agenda['links']['web-detail-comments'] ),
			self::POST_TYPE . '_item_role'                => sanitize_text_field( $agenda['role'] ),
			self::POST_TYPE . '_item_start_date'          => $start_date,
			self::POST_TYPE . '_item_end_date'            => $end_date,
			self::POST_TYPE . '_item_start_time'          => sanitize_text_field( $agenda['startTime'] ),
			self::POST_TYPE . '_item_end_time'            => sanitize_text_field( $agenda['endTime'] ),
			self::POST_TYPE . '_item_allow_participants'  => sanitize_text_field( $agenda['allowParticipants'] ),
			self::POST_TYPE . '_item_type_name'           => sanitize_text_field( $agenda['typeName'] ),
			self::POST_TYPE . '_item_themes'              => $sanitized_themes,
		);

		if ( isset( $agenda['venue'] ) ) {
			$venue = $agenda['venue'];

			$sanitized_venue = array(
				'id'      => intval( $venue['id'] ),
				'title'   => sanitize_text_field( $venue['title'] ),
				'content' => sanitize_textarea_field( $venue['content'] ),
				'website' => esc_url( $venue['website'] ),
				'phone'   => sanitize_text_field( $venue['phone'] ),
				'address' => array(
					'street'     => sanitize_text_field( $venue['address']['street'] ),
					'postalCode' => sanitize_text_field( $venue['address']['postalCode'] ),
					'city'       => sanitize_text_field( $venue['address']['city'] ),
					'province'   => sanitize_text_field( $venue['address']['province'] ),
					'country'    => sanitize_text_field( $venue['address']['country'] ),
				),
			);

			$data[ self::POST_TYPE . '_item_venue' ] = $sanitized_venue;
		}

		return $data;
	}
}
