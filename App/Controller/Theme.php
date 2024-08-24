<?php

namespace App\Controller;

use Theme_Upgrader;
use WP_Upgrader;
use WP_Ajax_Upgrader_Skin;

class Theme
{
    public static function init()
    {
        // add_action('init', array('App\Theme', 'lnd_update_themes'));
        // add_action('init', array('App\Theme', 'lnd_get_actions'));
    }

    public static function lnd_theme_install( $package, $args = array() ) {
        require_once ABSPATH .  'wp-admin/includes/class-wp-upgrader.php';
        $skin  = new WP_Ajax_Upgrader_Skin();
        $theme_install = new Theme_Upgrader($skin);
        $defaults    = array(
            'clear_update_cache' => true,
            'overwrite_package'  => false, // Do not overwrite files.
        );
        $parsed_args = wp_parse_args( $args, $defaults );
     
        add_filter( 'upgrader_source_selection', array( $theme_install, 'check_package' ) );
        add_filter( 'upgrader_post_install', array( $theme_install, 'check_parent_theme_filter' ), 10, 3 );
     
        if ( $parsed_args['clear_update_cache'] ) {
            // Clear cache so wp_update_themes() knows about the new theme.
            add_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9, 0 );
        }
     
        $theme_install->run(
            array(
                'package'           => $package,
                'destination'       => get_theme_root(),
                'clear_destination' => $parsed_args['overwrite_package'],
                'clear_working'     => true,
                'hook_extra'        => array(
                    'type'   => 'theme',
                    'action' => 'install',
                ),
            )
        );
     
        remove_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9 );
        remove_filter( 'upgrader_source_selection', array( $theme_install, 'check_package' ) );
        remove_filter( 'upgrader_post_install', array( $theme_install, 'check_parent_theme_filter' ) );
     
        if ( ! $theme_install->result || is_wp_error( $theme_install->result ) ) {
            return $theme_install->result;
        }
     
        // Refresh the Theme Update information.
        wp_clean_themes_cache( $parsed_args['clear_update_cache'] );
     
        if ( $parsed_args['overwrite_package'] ) {
            /** theme_install action is documented in wp-admin/includes/class-plugin-upgrader.php */
            do_action( 'upgrader_overwrote_package', $package, $theme_install->new_theme_data, 'theme' );
        }
     
        return true;
    }

    public static function installTheme($theme)
    {
        if (!function_exists('install')) {
            $callback = Self::lnd_theme_install($theme);
            wp_clean_plugins_cache();
            return $callback;

        } 

        return false;
    }

    public static function upgradeTheme($lnd_name, $lnd_themes)
    {
        if (!function_exists('upgrade')) {
            wp_clean_themes_cache($clear_update_cache = true);
            $skin  = new WP_Ajax_Upgrader_Skin();
            $obj = new Theme_Upgrader($skin);
            $obj->upgrade($lnd_name, $lnd_themes);
        }
    }

    public static function is_theme_installed($slug)
    {
        $installed_themes = wp_get_themes();
        return (!empty($installed_themes[$slug]));
    }

    public static function is_theme_active($slug)
    {
        $current_theme = wp_get_theme();
        if ($current_theme && self::is_theme_installed($slug)) {
            if ($current_theme->stylesheet == $slug) {
                return true;
            }
        }
        return false;
    }

    public static function  activateTheme($slug)
    {
        if (self::is_theme_installed($slug) && !self::is_theme_active($slug)) {

            $activate = switch_theme($slug);
            if (is_wp_error($activate) ) {
                return false;
            } 
            return true;
        }

        return false;
    }

    public static function update_theme($theme)
    {
        if (!function_exists('run')) {
            require_once ABSPATH .  'wp-admin/includes/class-wp-upgrader.php';
            $skin  = new WP_Ajax_Upgrader_Skin();

            $lnd_update = new WP_Upgrader($skin);

            $lnd_update->run($theme);

            wp_clean_plugins_cache(true);
            return true;

        } 
        return false;
    }

    public static function get_version_compare($info, $valor)
    {
        if ($info) {
            if (version_compare($valor, $info, '>')) {
                $version = '<spam class="border border-white badge bg-light text-danger rounded-1" style="border-radius: 3px;">'. __('Update to: ', 'lnd-master-dev') . $valor . '</spam>';
            } else {
                $version = '<spam class="border border-white badge bg-light text-primary rounded-1" style="border-radius: 3px;">'. __('Installed: ', 'lnd-master-dev') . $info . '</spam>';
            }
        } else {
            $version = '<spam class="border border-white badge bg-light text-success rounded-1" style="border-radius: 3px;">'. __('Version: ', 'lnd-master-dev') . $valor . '</spam>';
        }

        return  $version;
    }
    public static function get_new_plugins($data_inicial){
        $data_final = date('d-m-Y');
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));
        if ($dias <= 30){
            $new_product = '<span class="badge bg-success">New</span>';
        }else{
            $new_product = '';
        }
        return $new_product;
    }

}
