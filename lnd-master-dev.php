<?php
/*
---------------------------------------------------------
Plugin Name: LND Master Plugin
Plugin URI: https://lojanegociosdigital.com.br/
Author: Emerson Takada
Author URI: https://ricol.com.br/
Description: Controle e ultilitarios do site create produtcts with downloads LND 
Version: 2.0.10
Requires at least: 3.5.0
Tested up to: 6.1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html 
------------------------------------------------------------
*/

if (!defined('ABSPATH')) {
    exit();
}

include_once( ABSPATH . 'wp-includes/functions.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once dirname(__FILE__) . '/vendor/autoload.php';

define('MASTER_LND_VERSION', '2.0.11');
define('MASTER_LND_BASE_FILE', plugin_basename(__FILE__));
define('MASTER_LND_BASE', __FILE__);
define('LND_MASTER_DOWNLOADS_URL', 'https://planos.lojanegociosdigital.com.br/downloads/');
define('LND_MASTER_DOWNLOADS_URL_API', 'https://api.lojanegociosdigital.com.br/');
define('MASTER_LND_SLUG', 'lnd-master-dev');
define('MASTER_LND_ABSPATH', dirname(dirname(__FILE__)) . '/');
define('MASTER_LND_MASTER_DEV', dirname(dirname(__FILE__)) . '/lnd-master-dev/' );
// define('LND_AU_URL', esc_url('http://planos.lojanegociosdigital.com.br/'));
// define('MASTER_LND_URL_DOWNLOADS', esc_url('https://api.lojanegociosdigital.com.br/downloads-lnd/'));

App\Registers::init();
App\Instance::get_instance()->init();

// function add_cors_http_header(){
//     header("Access-Control-Allow-Origin: *");
//     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//     header("Access-Control-Allow-Headers: Content-Type, Authorization");
// }
// add_action('init', 'add_cors_http_header');


// require_once dirname(__FILE__) . '/App/License/LndAutoUpdate.php';

