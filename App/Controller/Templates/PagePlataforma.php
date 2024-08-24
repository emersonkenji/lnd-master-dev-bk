<?php

namespace App\Controller\Templates;

use App\View\Pages\Page_Plataforma;

/**
 * Class LND_Controller_Downloads
 */

if (!defined('ABSPATH')) {
    exit();
}

class PagePlataforma
{
    public static function init()
    {
        add_action('init', [__CLASS__, 'lnd_url_plugin_init']);
        add_action('wp_loaded', [__CLASS__, 'lnd_maybe_flush_rewrite_rules']);
        add_filter('query_vars',  [__CLASS__, 'lnd_url_plugin_query_vars']);
        add_action('template_include', [__CLASS__, 'lnd_url_downloads'], 9999);
        
    }

    public static function lnd_url_downloads($template)
    {
        if (empty(get_query_var('lnd_plataforma'))) {
            return $template;
        }

        add_action('wp_enqueue_scripts', array(__CLASS__, 'load_plugin_styles'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'load_plugin_scripts'));

        get_header();

        new Page_Plataforma();

        get_footer();
        
    }

    

    public static function lnd_url_plugin_init()
    {
        // add_rewrite_rule(
        //     'lnd-downloads/([^/]+)/([^/]+)/([^/]+)/?$',
        //     'index.php?lnd_downloads=pro&lnd_params[license_key]=$matches[1]&lnd_params[slug]=$matches[2]&lnd_params[version]=$matches[3]',
        //     'top'
        // );

        // add_rewrite_rule(
        //     '^downloads-lnd/([^/]+)(/([^/]+))?$',
        //     'index.php?lnd_downloads=free&lnd_params[slug]=$matches[1]&lnd_params[version]=$matches[3]',
        //     'top'
        // );
        add_rewrite_rule(
            '^lnd-plataforma/?$',
            'index.php?lnd_plataforma=lnd_plataforma',
            'top'
        );
    }

    public static function lnd_url_plugin_query_vars($vars)
    {
        $vars[] = 'lnd_plataforma';
        return $vars;
    }

    public static function lnd_url_plugin_template_redirect()
    {
        if (!empty($_GET['lnd_plataforma'])) {
            wp_redirect("lnd-plataforma/");
            exit;
        }
    }

    /**
     * Flush the rewrite rules if needed.
     *
     * @since 0.3.0
     */
    public static function lnd_maybe_flush_rewrite_rules()
    {
        if (is_network_admin() || 'no' === get_option('lnd_flush_rewrite_rules')) {
            return;
        }
        update_option('lnd_flush_rewrite_rules', 'no');
        flush_rewrite_rules();
        return;
    }

    public static function load_plugin_styles()
    {
        // global $parent_file;
        // if ('lnd-master-dev' != $parent_file || get_query_var('lnd_plataforma') ) {
        //     return;
        // }
        
        wp_register_style(
            'lnd-plugin-styles-boot', 
            plugins_url('/assets/css/bootstrap.css', MASTER_LND_BASE_FILE), 
            array(), 
            '5.2.0', 
            'all'
        );
        wp_register_style(
            'lnd-plugin-styles-font-awesome', 
            plugins_url('/assets/css/font-awesome.css', MASTER_LND_BASE_FILE), 
            array(), 
            '6.1.2', 
            'all'
        );
        wp_register_style(
            'lnd-plugin-styles-style', 
            plugins_url('/assets/css/plataforma.style.css', MASTER_LND_BASE_FILE), 
            array(), 
            MASTER_LND_VERSION, 
            'all'
        );
        wp_enqueue_style('lnd-plugin-styles-boot');
        wp_enqueue_style('lnd-plugin-styles-font-awesome');
        wp_enqueue_style('lnd-plugin-styles-style');

    }

    public static function load_scripts_plataforma()
    {
        add_action('wp_enqueue_scripts', [__CLASS__, 'load_plugin_scripts']);
    }

    public static function load_plugin_scripts()
    {
        // global $parent_file;
        // if ('lnd-master-dev' != $parent_file || get_query_var('lnd_plataforma') ) {
        //     return;
        // }
        // wp_deregister_script('jquery');
        // wp_enqueue_script(
        //     'lnd-plugin-app',
        //     plugins_url('assets/js/jquery.js', MASTER_LND_BASE_FILE),
        //     array(),
        //     '3.7.0',
        //     true
        // );
        // wp_enqueue_script(
        //     'lnd-plugin-boot-app',
        //     plugins_url('assets/js/bootstrap.js', MASTER_LND_BASE_FILE),
        //     [],
        //     '5.2.0',
        //     false
        // );
        wp_enqueue_script(
            'lnd-plugin-jquery-app',
            plugins_url('assets/js/plataforma.min.js', MASTER_LND_BASE_FILE),
            [],
            MASTER_LND_VERSION,
            true
        );
        wp_localize_script('lnd-plugin-jquery-app', 'ajax_var', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce')
        ));

    }
}
