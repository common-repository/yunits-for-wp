<?php
/**
 * PHP DI.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

/**
 * Exit when accessed directly.
 */

if ( ! defined( 'ABSPATH' )) {
	exit;
}

use YunitsForWP\Commands\GetAgendasCommand;
use YunitsForWP\Commands\GetNewsCommand;
use YunitsForWP\Commands\GetThemesCommand;
use YunitsForWP\Controllers\ApiController;
use YunitsForWP\Controllers\BaseController;
use YunitsForWP\Controllers\SettingsController;
use YunitsForWP\Interfaces\Commands\GetAgendasCommandInterface;
use YunitsForWP\Interfaces\Commands\GetNewsCommandInterface;
use YunitsForWP\Interfaces\Commands\GetThemesCommandInterface;
use YunitsForWP\Interfaces\Controllers\ApiControllerInterface;
use YunitsForWP\Interfaces\Controllers\BaseControllerInterface;
use YunitsForWP\Interfaces\Controllers\SettingsControllerInterface;
use YunitsForWP\Interfaces\Providers\ApiServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\AppServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\AgendaServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\NewsServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\OIDCServiceProviderInterface;
use YunitsForWP\Interfaces\Providers\SettingsServiceProviderInterface;
use YunitsForWP\Interfaces\Services\EventServiceInterface;
use YunitsForWP\Interfaces\Services\LifeCycleServiceInterface;
use YunitsForWP\Interfaces\Services\ResourceServiceInterface;
use YunitsForWP\Interfaces\Services\TokenServiceInterface;
use YunitsForWP\Interfaces\Services\YunitsServiceInterface;
use YunitsForWP\Providers\ApiServiceProvider;
use YunitsForWP\Providers\AppServiceProvider;
use YunitsForWP\Providers\AgendaServiceProvider;
use YunitsForWP\Providers\NewsServiceProvider;
use YunitsForWP\Providers\OIDCServiceProvider;
use YunitsForWP\Providers\SettingsServiceProvider;
use YunitsForWP\Services\EventService;
use YunitsForWP\Services\LifeCycleService;
use YunitsForWP\Services\ResourceService;
use YunitsForWP\Services\TokenService;
use YunitsForWP\Services\YunitsService;

return array(
	// Commands.
	GetAgendasCommandInterface::class       => YunitsForWP\Vendor_Prefixed\DI\autowire( GetAgendasCommand::class ),
	GetNewsCommandInterface::class          => YunitsForWP\Vendor_Prefixed\DI\autowire( GetNewsCommand::class ),
	GetThemesCommandInterface::class        => YunitsForWP\Vendor_Prefixed\DI\autowire( GetThemesCommand::class ),

	// Controllers.
	ApiControllerInterface::class           => YunitsForWP\Vendor_Prefixed\DI\autowire( ApiController::class ),
	BaseControllerInterface::class          => YunitsForWP\Vendor_Prefixed\DI\autowire( BaseController::class ),
	SettingsControllerInterface::class      => YunitsForWP\Vendor_Prefixed\DI\autowire( SettingsController::class ),

	// Providers.
	ApiServiceProviderInterface::class      => YunitsForWP\Vendor_Prefixed\DI\autowire( ApiServiceProvider::class ),
	AppServiceProviderInterface::class      => YunitsForWP\Vendor_Prefixed\DI\autowire( AppServiceProvider::class ),
	AgendaServiceProviderInterface::class   => YunitsForWP\Vendor_Prefixed\DI\autowire( AgendaServiceProvider::class ),
	NewsServiceProviderInterface::class     => YunitsForWP\Vendor_Prefixed\DI\autowire( NewsServiceProvider::class ),
	OIDCServiceProviderInterface::class     => YunitsForWP\Vendor_Prefixed\DI\autowire( OIDCServiceProvider::class ),
	SettingsServiceProviderInterface::class => YunitsForWP\Vendor_Prefixed\DI\autowire( SettingsServiceProvider::class ),

	// Services.
	EventServiceInterface::class            => YunitsForWP\Vendor_Prefixed\DI\autowire( EventService::class ),
	LifeCycleServiceInterface::class        => YunitsForWP\Vendor_Prefixed\DI\autowire( LifeCycleService::class ),
	ResourceServiceInterface::class         => YunitsForWP\Vendor_Prefixed\DI\autowire( ResourceService::class ),
	TokenServiceInterface::class            => YunitsForWP\Vendor_Prefixed\DI\autowire( TokenService::class ),
	YunitsServiceInterface::class           => YunitsForWP\Vendor_Prefixed\DI\autowire( YunitsService::class ),
);
