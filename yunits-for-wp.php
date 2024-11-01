<?php
/**
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 *
 * Plugin Name: Yunits for WP
 * Description: Integrate a Yunits Community with WordPress and vice versa.
 * Version: 1.12.0
 * Author: Yard | Digital Agency
 * Author URI: https://www.yard.nl
 * License: GPLv2 or later
 * Text Domain: yunits-for-wp
 * Domain Path: /languages
 * Requires at least: 6.0
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

define( 'YUNITS_FOR_WP_VERSION', '1.12.0' );
define( 'YUNITS_FOR_WP_REQUIRED_WP_VERSION', '6.0' );
define( 'YUNITS_FOR_WP_FILE', __FILE__ );
define( 'YUNITS_FOR_WP_DIR_PATH', plugin_dir_path( YUNITS_FOR_WP_FILE ) );
define( 'YUNITS_FOR_WP_PLUGIN_URL', plugins_url( '/', YUNITS_FOR_WP_FILE ) );

// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor-prefixed/autoload.php' ) ) {
	require_once __DIR__ . '/vendor-prefixed/autoload.php';
}

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/Bootstrap.php';

require_once __DIR__ . '/vendor/woocommerce/action-scheduler/action-scheduler.php';

$init = new YunitsForWP\Bootstrap();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'yfw:get-agendas', '\YunitsForWP\Commands\GetAgendasCommand' );
	WP_CLI::add_command( 'yfw:get-news', '\YunitsForWP\Commands\GetNewsCommand' );
	WP_CLI::add_command( 'yfw:get-themes', '\YunitsForWP\Commands\GetThemesCommand' );
}
