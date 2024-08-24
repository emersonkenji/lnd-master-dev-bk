<?php

namespace App\Controller\Enqueue;

class Enqueue
{
    /**
     * Método responsável por instanciar a classe
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_enqueue_scripts', [__CLASS__, 'load_scripts_admin_catalogo_page']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'load_scripts_admin_page_downloads']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'load_scripts_admin_page_configuration']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'load_scripts_shortcode'], 20);
        // add_action('wp_enqueue_scripts', [__CLASS__, 'remove_unwanted_styles', 20]);
        // Adicione a verificação do shortcode se necessário
        // add_action('wp', [__CLASS__, 'check_shortcode_and_load_scripts']);
    }

    public static function check_shortcode_and_load_scripts()
    {
        if (is_singular() && has_shortcode(get_post()->post_content, 'lnd_master_plataforma')) {
            // Carregue os scripts necessários aqui
        }
    }

    public static function remove_unwanted_styles()
    {
        wp_dequeue_style('bootstrap-css'); // Remove o estilo com o ID 'bootstrap-css'
        wp_deregister_style('bootstrap-css'); // Remove o registro do estilo, opcional
    }

    /**
     * Enqueue scripts para a página admin de catálogo
     *
     * @return void
     */
    public static function load_scripts_admin_catalogo_page()
    {
        if (self::is_page('lnd-master-dev')) {
            wp_enqueue_script('react_plugin_js', plugins_url('/assets/js/plataforma.min.js', MASTER_LND_BASE_FILE), ['wp-element'], '0.1.0', true);
            wp_localize_script('react_plugin_js', 'appLocalizer', [
                'apiUrl' => admin_url('admin-ajax.php'),
                // 'nonce' => wp_create_nonce('wp_rest_react_plugin')
            ]);
            // wp_enqueue_style('react_plugin_global_css', plugins_url('/assets/css/global.min.css', MASTER_LND_BASE_FILE), [], MASTER_LND_VERSION, 'all');
            wp_enqueue_style('react_plugin_css', plugins_url('/assets/css/adminMaster.min.css', MASTER_LND_BASE_FILE), [], MASTER_LND_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts para a página admin de catálogo
     *
     * @return void
     */
    public static function load_scripts_admin_page_configuration()
    {
        if (self::is_page('lnd-master-dev-conf')) {
            wp_register_style('configuration_plugin_css', plugins_url('assets/css/configuration.css', MASTER_LND_BASE_FILE), array(), '1.0.1', 'all');
            wp_enqueue_style( "configuration_plugin_css" );
        }
    }

    public static function load_scripts_admin_page_downloads()
    {      
        if (self::is_page('lnd-master-dev-downloads')) {
            wp_enqueue_script(
                'lnd-plugin-jquery-app-downloads',
                plugins_url('assets/js/script_downloads.js', MASTER_LND_BASE_FILE),
                [],
                MASTER_LND_VERSION,
                true
            );
        }
        
    }

    public static function load_scripts_shortcode()
    {

        if (is_singular() && has_shortcode(get_post()->post_content, 'lnd_master_plataforma')) {
             // Método 1: Desregistrar o estilo
        // Desregistra o estilo do Bootstrap
        wp_deregister_style('bootstrap');
        
        // Remove o estilo do Bootstrap da fila
        wp_dequeue_style('bootstrap');
        
        // Remove o estilo usando o ID específico
        wp_dequeue_style('bootstrap-css');
        
        // Caso o tema use wp_enqueue_style para carregar o Bootstrap, podemos tentar removê-lo assim
        wp_dequeue_style('woodmart-bootstrap-light');
        
        // Adiciona um filtro para remover o link do Bootstrap do HTML final
        add_action('wp_head', [__CLASS__, 'remove_bootstrap_link'], 0);
        add_filter('style_loader_src', [__CLASS__, 'prevent_woodmart_bootstrap_loading'], 10, 2);
        // add_action('wp_enqueue_scripts', 'remove_woodmart_bootstrap', 100);


            wp_enqueue_style('react_plugin_global_css', plugins_url('/assets/css/global.min.css', MASTER_LND_BASE_FILE), [], MASTER_LND_VERSION, 'all');
            wp_enqueue_style('lnd-plugin-styles-boot', plugins_url('/assets/css/bootstrap.css', MASTER_LND_BASE_FILE), [], '5.3.0', 'all');
            wp_enqueue_style('lnd-plugin-styles-font-awesome', plugins_url('/assets/css/font-awesome.css', MASTER_LND_BASE_FILE), [], '6.1.2', 'all');
            wp_enqueue_style('lnd-plugin-styles-style', plugins_url('/assets/css/lndCuston.css', MASTER_LND_BASE_FILE), [], MASTER_LND_VERSION, 'all');

            wp_enqueue_script('lnd-plugin-boot-app', plugins_url('/assets/js/bootstrap.js', MASTER_LND_BASE_FILE), [], '5.3.0', true);
            wp_enqueue_script('lnd-plugin-jquery-app', plugins_url('/assets/js/ScriptPagePlatarfoma.min.js', MASTER_LND_BASE_FILE), [], MASTER_LND_VERSION, true);
            wp_localize_script('lnd-plugin-jquery-app', 'ajax_var', [
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce')
            ]);
        }
    }

    // function remove_woodmart_bootstrap_light() {
       
    // }
    // add_action('wp_enqueue_scripts', 'remove_woodmart_bootstrap_light', 100);
    
    public static function remove_bootstrap_link() {
        ob_start(function($html) {
            // Remove a tag link específica do Bootstrap
            $html = preg_replace('/<link[^>]*bootstrap-light\.min\.css[^>]*>/i', '', $html);
            return $html;
        });
    }
    
    // Adiciona um filtro para prevenir que o WordPress carregue o arquivo do Bootstrap
    public static function prevent_woodmart_bootstrap_loading($src, $handle) {
        if (strpos($src, 'bootstrap-light.min.css') !== false) {
            return false; // Retorna false para prevenir o carregamento
        }
        return $src; // Retorna a fonte original para outros arquivos
    }
    

    /**
     * Valida se está na página admin correta para instance enqueue
     *
     * @return boolean
     */
    private static function is_page($page)
    {
        return isset($_GET['page']) && $_GET['page'] === $page;
    }
}
