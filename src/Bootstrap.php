<?php
/**
 * Bootstrap providers and containers.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Interfaces\Providers\ApiServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\AppServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\AgendaServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\NewsServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\OIDCServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\SettingsServiceProviderInterface;
use YunitsForWP\Vendor_Prefixed\DI\ContainerBuilder;
use YunitsForWP\Vendor_Prefixed\Psr\Container\ContainerInterface;

require_once __DIR__ . '/helpers.php';

/**
 * Bootstrap providers and containers.
 */
final class Bootstrap
{
	/**
	 * Dependency Injection container.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * Dependency providers.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $providers;

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->container = $this->build_container();
		$this->providers = $this->get_providers();
		$this->register_providers();
		$this->boot_providers();
	}

	/**
	 * Gets all providers
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_providers(): array {
		$providers = array(
			AgendaServiceProviderInterface::class,
			ApiServiceProviderInterface::class,
			AppServiceProviderInterface::class,
			NewsServiceProviderInterface::class,
			OIDCServiceProviderInterface::class,
			SettingsServiceProviderInterface::class,
		);
		foreach ( $providers as &$provider ) {
			$provider = $this->container->get( $provider );
		}
		return $providers;
	}

	/**
	 * Registers all providers.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_providers(): void {
		foreach ( $this->providers as $provider ) {
			$provider->register();
		}
	}

	/**
	 * Boots all providers.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function boot_providers(): void {
		foreach ( $this->providers as $provider ) {
			$provider->boot();
		}
	}

	/**
	 * Builds the container.
	 *
	 * @since 1.0.0
	 *
	 * @return ContainerInterface
	 */
	protected function build_container(): ContainerInterface {
		$builder = new ContainerBuilder();
		$builder->addDefinitions( __DIR__ . './../config/php-di.php' );
		$builder->useAnnotations( true );
		$container = $builder->build();
		return $container;
	}
}
