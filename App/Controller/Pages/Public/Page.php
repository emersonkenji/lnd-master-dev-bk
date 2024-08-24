<?php

namespace App\Controller\Pages\Public;

use App\Utils\View;

class Page
{    
    // Store styles and scripts
    private static $styles = [];
    private static $scripts = [];
    private static $scripts_extra = [];
    /**
     * Método responsável por retornar os header padrão da aplicação (view) da nossa pagina admin
     * @return string
     */
    public static function getHeader()
    {
        return View::render('module/header', [
            'btn-update-catalog-header' =>  __('Update Catalog', 'lnd-auto-update'),
            'btn-my-account' => __('My Account', 'lnd-auto-update')
        ]);
    }



    // Method to register styles
    public static function register_style($handle, $src, $version = null)
    {
        self::$styles[$handle] = plugins_url() . '/' . MASTER_LND_SLUG . $src .( $version != null ? '?ver=' . $version : '');
    }

    // Method to register scripts
    public static function register_script($handle, $src, $version = null)
    {

        self::$scripts[$handle] = plugins_url() . '/' . MASTER_LND_SLUG . $src .( $version != null ? '?ver=' . $version : '') ;
    }

    public static function localize_script($handle, $object_name, $l10n)
    {
        $script = sprintf( '<script id="%s-js-extra">', esc_attr( $handle ) );
		$script .= sprintf( "var %s = %s;", esc_js( $object_name ), wp_json_encode( $l10n ) );
		$script .= "</script>\n";

        self::$scripts_extra[$handle] = $script;
    }

    // Method to enqueue styles and scripts
    public static function enqueue_assets()
    {
        $styles = '';
        $scripts = '';
        // $scripts_extra = '';
        // Generate style tags
        foreach (self::$styles as $handle => $src) {
            $styles .= "<link rel='stylesheet' id='{$handle}-css' href='{$src}' type='text/css' media='all' />";
        }
        foreach (self::$scripts_extra as $script) {
            $scripts .= $script;
        }
        // Generate script tags
        foreach (self::$scripts as $handle => $src) {
            $scripts .= "<script type='text/javascript' id='{$handle}-js' src='{$src}'></script>";
            
        }
        
        return [
            'styles' => $styles,
            'scripts' => $scripts,
        ];
    }

    /**
     * Método responsável por retornar as pages (view) da nossa pagina admin
     * @param mixed $content
     * @param mixed $header
     * @return string
     */
    public static function getPage($content)
    {
        // Self::register_script('teste', 'https://lojanegociosdigital.local/wp-content/plugins/lnd-master-dev/assets/js/dashboard.min.js');
        $assets = Self::enqueue_assets();
        
        return print_r(View::render('page', [
            'styles' => $assets['styles'],
            'scripts' => $assets['scripts'],
            'dashboard-header-title' => 'Dashboard Downloads',
            'dashboard-content' => $content
        ]));
    }
}
