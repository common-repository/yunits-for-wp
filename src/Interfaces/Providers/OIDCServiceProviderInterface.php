<?php
/**
 * Service provider interface.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.12.0
 */

namespace YunitsForWP\Interfaces\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Service provider interface.
 *
 * @since 1.12.0
 */
interface OIDCServiceProviderInterface extends ServiceProviderInterface
{
	/**
	 * Register provider.
	 *
	 * @since 1.12.0
	 */
	public function register();

	/**
	 * Update user meta with claim information.
	 *
	 * @since 1.12.0
	 */
	public function update_user_using_current_claim( \WP_User $user, array $user_claim ): void;
}
