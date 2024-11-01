<?php
/**
 * Register Yunits service provider.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Interfaces\Services\YunitsServiceInterface;

/**
 * Register api service provider.
 *
 * @since 1.0.0
 */
class YunitsService extends ApiService implements YunitsServiceInterface
{
	/**
	 * @inheritDoc
	 */
	public function get_agenda_items( $page, $limit ) {
		$url      = '/partner-api/v1/agenda-items';
		$response = $this->make_request(
			$url,
			array(
				'page'  => $page,
				'limit' => $limit,

			),
			array(),
			'GET'
		);
		return $response;
	}

	/**
	 * @inheritDoc
	 */
	public function get_news_items( $page, $limit ) {
		$url      = '/partner-api/v1/news-items';
		$response = $this->make_request(
			$url,
			array(
				'page'  => $page,
				'limit' => $limit,

			),
			array(),
			'GET'
		);

		return isset( $response ) ? $response : array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_all_themes( $page, $limit ) {
		$url      = '/partner-api/v1/themes';
		$response = $this->make_request(
			$url,
			array(
				'page'  => $page,
				'limit' => $limit,

			),
			array(),
			'GET'
		);

		return isset( $response ) ? $response : array();
	}
}
