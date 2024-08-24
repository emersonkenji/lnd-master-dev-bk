<?php

namespace App\Controller;
/*
 * Class LND_Core
*/
use App\Request\Downloads;
use App\Controller\Pages\admin\Configuration;
use App\Http\RemoteRequestHandler;
use App\Model\Dashboard\TemplatesModel;
use App\Request\CatalogManager;
use App\Utils\WooCommerce\User;
use App\View\Configuration\Page_Master_Downloads;
use App\View\Configuration\PageLndMasterView;
use App\View\LND_View_Settings;
use App\View\Pages\Page_Plataforma;

if (!defined('ABSPATH')) {
    exit();
}

class Core
{
    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'admin_menu_callback'));
    }

    /**
     * Enqueue scripts for all admin pages.
     *
     * @param string $hook_suffix The current admin page.
     */
    public function action_admin_enqueue_scripts(string $hook_suffix) : void {
    }  
    
    public static function lnd_filter_plugin_updates( $value ) {
        $packages = get_option('_lnd_plugins_remove_datajson');
          foreach( $packages as $plugin ) {
            if ( isset( $value->response[$plugin] ) ) {
              unset( $value->response[$plugin] );
            }
          }
          return $value;
    }

    public static function lnd_shortcode_plataforma()
    {      
        $lnd_page = new Page_Plataforma();
    }


    public static function admin_menu_callback()
    {
        global $lnd_master_downloads;
        global $lnd_master_conf;
        global $lnd_master_menu;

        $lnd_master_menu = add_menu_page(
            __('LND Master', 'lnd-master-dev'),
            __('Lnd Master', 'lnd-master-dev'),
            'manage_options',
            MASTER_LND_SLUG,
            array(
                __CLASS__,
                'admin_page_html_callback'
            ),
            Self::$menu_icon,
            55.5
        );

        $lnd_master_conf = add_submenu_page(
            MASTER_LND_SLUG, 
            'LND Master Config', 
            'Configurações', 
            'manage_options', 
            MASTER_LND_SLUG . '-conf', 
            array(__CLASS__, 
            'admin_submenu_config')
        );

        $lnd_master_downloads = add_submenu_page(
            MASTER_LND_SLUG, 
            'LND Master Downloads', 
            'Downloads', 
            'manage_options', 
            MASTER_LND_SLUG . '-downloads', 
            array(__CLASS__, 'admin_submenu_downloads')
        );

        add_action("load-$lnd_master_downloads", array(__CLASS__, "lnd_master_sample_screen_options"));
        add_action ( 'admin_head' , array ( __CLASS__ , 'admin_header' ) );
    }

    public static function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if ('lnd-master-dev-downloads' != $page) {
            return;
        }
      
        echo '<style type="text/css">';
        echo '.wp-list-table .column-cb { width: 3%; }';
        echo '.wp-list-table .column-type { width: 6%; }';
        echo '.wp-list-table .column-version { width: 6%; }';
        echo '.wp-list-table .column-status { width: 7%; }';
        echo '.wp-list-table .column-update_date { width: 12%; }';
        echo '.wp-list-table .column-data { width: 10%; }';
        echo '</style>';
      }

    public static function admin_submenu_config()
    {
        // $current_user_id = get_current_user_id();
        // echo '<pre>';
        // print_r(User::get_customer_downloads($current_user_id));
        // echo '</pre>';exit;

        // $a = TemplatesModel::get_instance()->get_templates();
        $a = TemplatesModel::get_instance()->get_templates_categories();
        // $b = RemoteRequestHandler::getValidToken();
        echo '<pre>';
        print_r($a);
        echo '</pre>';exit;
        // global $wpdb;

        // $sql = "SELECT * FROM `wpdsxb_lnd_master_catalog` WHERE category_id = '12'";
        // echo '<pre>';
        // print_r($wpdb->get_results($sql));
        // echo '</pre>';exit;
        // $data = CatalogManager::get_categories();
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
        Configuration::settings_page();
    }

    // add screen options
    public static function lnd_master_sample_screen_options()
    {
        global $lnd_master_downloads;
        global $table;
        
        $screen = get_current_screen();
        //get out of here if we are not on our settings page
        if (!is_object($screen) || $screen->id != $lnd_master_downloads) {
            return;
        }

        $args = array(
            'label' => __('Número de itens por página:', 'lnd-master-dev'),
            'default' => 30,
            'option' => 'elements_per_page'
        );
        add_screen_option('per_page', $args);
        $table = new Page_Master_Downloads();
    }

    public static function admin_submenu_downloads()
    {
        if (!class_exists('Page_Master_Downloads')) {
            require_once MASTER_LND_MASTER_DEV . 'App/View/Configuration/Page_Master_Downloads.php';
        }

        LND_View_Settings::get_header_pages();
        
        $table = new Page_Master_Downloads();
        echo '<div class="wrap"><h2>Tabela de lista de Downloads</h2>';        

        echo '<form method="post">';
        // Prepara a mesa
        $table->prepare_items();
        // Search Boxs
        $table->search_box('search', 'search_id');
        // Exibe a tabela
        $table->display();
        echo '</div></form>'; 
    }
 
    public static function admin_page_html_callback()
    {

        new PageLndMasterView();
    }

    public static function verifica_shortcode($shortcode)
    {
        global $post;
        if ( has_shortcode( $post->post_content, $shortcode ) ) {
          return true;
        } else {
          return false;
        }
    }

    public static $menu_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMHB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPiAgPGltYWdlIGlkPSJpbWFnZTAiIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgeD0iMCIgeT0iMCIKICAgIGhyZWY9ImRhdGE6aW1hZ2UvcG5nO2Jhc2U2NCxpVkJPUncwS0dnb0FBQUFOU1VoRVVnQUFBQlFBQUFBVUNBWUFBQUNOaVIwTkFBQUFCR2RCVFVFQUFMR1BDL3hoQlFBQUFDQmpTRkpOCkFBQjZKZ0FBZ0lRQUFQb0FBQUNBNkFBQWRUQUFBT3BnQUFBNm1BQUFGM0NjdWxFOEFBQUFCbUpMUjBRQS93RC9BUCtndmFlVEFBQUEKQjNSSlRVVUg1Z01TQ3hBaUlvNExyd0FBQS9SSlJFRlVPTXR0azhsdUhGVVVocjg3VlhXM1hlNTBiR3dIVDRsakVSd1JRcVFJS1NDbQpQWThBUzhnV3ljOUFkdVlCSXBZZ3NXU0hCTmtnRmdRd1NHQ1JpVXpPNE1TSjUzWjNWOVc5ZFErTGhpaE9jdGJuZnZmb0g5UVhYMzZkCkhwMmQrM1JnWUdEQk9UZXRsVks3WmNWbmYxbisyVE9Bc0c4RVBwNnBPRHNyVktJUWllSzlYK2wyTzR1WExsOCtiMmRuajM3U2FyVSsKbHlqWnJWdTMyZHJhWWpkQUp6MER3eTNVTTBCQnNkcSt3ZEx2VjBBYnhrWkgxZFRVNU9FMFNjL056ODlqbTFtMjRLek52dnYrQWt0TApmN0M3K1poQ08rb2Z2Y24wV0FZeDdyOVFheDc4Y3B0dmZ2Z0tXNnZUR2hubGcvZmY1WjIzejJSRFZiWmdHNDM2eklPSGoxaisreXFiCkQrN2llMTNVWUpPcHBrYTNBTkg3ZUVwRG5sVGszUTRoNzdIVzYvTHJVc1liSjAvUXFOZG1iSm80MWVubDdPMXVFOHVDTkVrSk5xSHQKRGJIM3ZJUm8wRGlzYzFqcjhMNWthM09EdkNob05UTmxFMmN4MWxLRmdMVVdxdzJsdHR6WjFwVHF4Y0JtenpGcERFWWI1S20zaWJNOApBU3BqTVZwanRFSXBRN3NkS2FoQTltc28ycExrNnNtdWlFRWJpN1VXNXd6V1dZUFdCbVVzb2ZFU3ZqWkU0VEppTDBmWVJza3pKeXBOCkpRbjVnVm1zMGRqT0drb2J0REU0YTdEV0tMVFdTTkxnL3ZFUEtWdEg4VlZGVlhxVWY4U0xwbE1mNC9hcHN4aG5PSFR0VzdMaUxsb3AKck5iWXZQQkVFWXhVcE52WGVlaGFCRUNpdkJDbUFLOWdRMmxHMmh0a25YdW8xQUFRWThUdWRYTktYNkdKSEZuN0RhcUttOWt4UktubgpEZmxmUjZWb2xsdk1iMXlrMGR1QTJoaTl3ck96MThPR0VBZ2hBQXBONU9YVmkzU3lIUjZPbjBLVWZ0NW1wYWwxMXBtKyt5TjFWNkJ0CkFrRHBQYVV2c1ZYd2ZhQUlMcWxoblRDNS9pYytWbXdjZVF2UlQvVlphZHplT3VNM0x0Qk1BbWsyVFBRRkZSQ0M3OGZIZTA4VlBDQjkKcDJvRGpEV0dVQnNyeUdxZHpWZmVRNHdEcGJCN0c0emUvSW1KNWlBSFdpMk1lS1RxSHhPOEozaVBMb3FDRVB4L2VWT0F3aVoxUmc0ZAo1bWo1bUxIVlplclpDQTFYWi96V3owdzNNN0xSR2JTMi9YM1ZUMy93bnJJc3NXVlpJckhDdVlSUWRFbUlxQml3eGpKeWNKZzBYMlh0CnpoSXEzMkVzRlJvRHc5aXFoNDRCTFpFcWVGd2pJMWFCb2lpd1plbWxPVkJUSStPSHVITmxIZFZwWTVJNmxha1FKOVJyRFNaMnJ3UGcKYWpVbzJram9VVlVGdnV5Uis4RGN4QVNOeEZLV1hxd1BZZVZnMWpoOCt1UnhyRFU4dW4rUEVEeWlOV0lkcEhXc1RVRUV5aTdFSHBHSQpLSENEUXh5Ym51SDA2Ni9TcUNYc2RYc3J0cHZuaTRPTitya1RjNVBaM05RNHZhSkVSRUJwUkJ0RUdVU3B2cm9TMFJKUlJKQ0lWb3A2CkxTRzFobTVldEx0NXNXaVhMMTA3Zi9ya2ExaHJGckpHT2pNMFdGUDlSaWlVN3RkU0s5VVB0QWd4UnFKSS8xT0JLQ0o1VWE3c3REdUwKeTVldm52OFhSa0hyM0Z1NXNDOEFBQUFsZEVWWWRHUmhkR1U2WTNKbFlYUmxBREl3TWpJdE1EVXRNamRVTURNNk5URTZNREFyTURNNgpNREFFYVU0bUFBQUFKWFJGV0hSa1lYUmxPbTF2WkdsbWVRQXlNREl5TFRBMUxUSTNWREF6T2pVeE9qQXdLekF6T2pBd2RUVDJtZ0FBCkFBQkpSVTVFcmtKZ2dnPT0iIC8+Cjwvc3ZnPgo=';

}
