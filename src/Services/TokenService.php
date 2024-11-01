<?php
/**
 * Register token service provider.
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

use YunitsForWP\Interfaces\Services\TokenServiceInterface;

/**
 * Register token service provider.
 *
 * @since 1.0.0
 */
class TokenService extends Service implements TokenServiceInterface
{
	/**
	 * Token object
	 *
	 * @var object
	 */
	private $token_object;

	/**
	 * Token constructor.
	 *
	 * @since 1.0.0
	 * @param object $token_object
	 * @throws \RuntimeException Invalid token.
	 */
	public function __construct( $token_object ) {
		if ( is_object( $token_object ) && ! isset( $token_object->created ) ) {
			$token_object->created = time();
		}

		if ( ! $this->is_valid( $token_object ) ) {
			throw new \RuntimeException( 'Invalid token.' );
		}

		$this->token_object = $token_object;
	}

	/**
	 * Creates a new token from a JSON string.
	 *
	 * @since 1.0.0
	 * @param string $json
	 * @return TokenService
	 */
	public static function from_json( $json ) {
		return new TokenService( json_decode( $json ) );
	}

	/**
	 * Returns the token object as a JSON string.
	 *
	 * @since 1.0.0
	 * @return string|false
	 */
	public function to_json() {
		return wp_json_encode( $this->token_object );
	}

	/**
	 * Returns the access token.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function token() {
		return $this->token_object->access_token;
	}


	/**
	 * Returns the token expiration time.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function expires_in() {
		return $this->token_object->expires_in ?? '';
	}

	/**
	 * Determines whether the token has expired.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_expired() {
		// Regenerate tokens 10 minutes early, just in case.
		$expires_in = $this->token_object->expires_in - ( 10 * MINUTE_IN_SECONDS );

		return time() > $this->token_object->created + $expires_in;
	}

	/**
	 * Determines whether or not the token is a valid token object.
	 *
	 * @param [type] $token_object
	 * @return boolean
	 */
	private function is_valid( $token_object ) {
		$required_properties = array(
			'created',
			'access_token',
			'expires_in',
		);

		foreach ( $required_properties as $property ) {
			if ( ! isset( $token_object->{$property} ) ) {
				return false;
			}
		}

		return true;
	}
}
