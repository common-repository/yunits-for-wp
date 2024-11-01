<?php
/**
 * Get agendas command.
 *
 * @package Yunits_For_WP
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace YunitsForWP\Commands;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WP_CLI;
use WP_CLI_Command;
use YunitsForWP\Interfaces\Commands\GetAgendasCommandInterface;

/**
 * Get agendas command.
 *
 * @since 1.0.0
 */
class GetAgendasCommand extends WP_CLI_Command implements GetAgendasCommandInterface
{
	public function import( $args, $assoc_args ) {
		do_action( 'yfw_import_agendas', 1 );
		WP_CLI::success( 'Started importing agendas' );
	}
}
