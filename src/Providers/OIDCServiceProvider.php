<?php
/**
 * Register news service provider.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.12.0
 */

namespace YunitsForWP\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Models\News;
use YunitsForWP\Interfaces\Providers\OIDCServiceProviderInterface;

/**
 * Register OIDC service provider.
 *
 * @since 1.12.0
 */
class OIDCServiceProvider extends ServiceProvider implements OIDCServiceProviderInterface
{
	/**
	 * @inheritDoc
	 */
	public function register(): void {
		add_action( 'openid-connect-generic-update-user-using-current-claim', array( $this, 'update_user_using_current_claim' ), 10, 2 );
	}

	/**
	 * @inheritDoc
	 */
	public function update_user_using_current_claim( \WP_User $user, array $user_claim ): void {
		if ( ! empty( $user_claim['roles'] ) ) {
			update_user_meta( $user->ID, 'yfw_user_roles', $user_claim['roles'] );
		}

		if ( ! empty( $user_claim['item_roles'] ) ) {
			update_user_meta( $user->ID, 'yfw_user_item_roles', $user_claim['item_roles'] );
		}
	}
}
