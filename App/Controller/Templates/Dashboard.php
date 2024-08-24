<?php

namespace App\Controller\Templates;

use App\Controller\Pages\Public\Dashboard as PagesDashboards;

/**
 * Class LND_Master_Plataforma
 */

if (!defined('ABSPATH')) {
    exit();
}

class Dashboard
{
    public static function init()
    {
        add_action('init', [__CLASS__, 'url_rewrite'], 99);
        add_filter('query_vars',  [__CLASS__, 'url_rewrite_query_vars']);
        add_action('template_include', [__CLASS__, 'url_dashboard'], 9999);
    }

    public static function url_dashboard($template)
    {
        if (empty(get_query_var('dashboard'))) {
            return $template;
        }

        // Assegura que a barra de administração seja mostrada
        // show_admin_bar(true);

        // Inicia o buffer de saída
        ob_start();

        // Carrega apenas os scripts e estilos necessários
        // wp_deregister_style('dashicons');
        // wp_deregister_style('admin-bar');
        // wp_deregister_script('admin-bar');
        // wp_deregister_script('jquery-core');

        // Imprime os scripts e estilos no cabeçalho
        // wp_print_styles();
        // wp_print_head_scripts();

        // // Imprime a barra de administração
        // wp_body_open();
        // wp_admin_bar_render();
        // echo '<div class="mb-6">teste</div>';
        // Seu conteúdo personalizado aqui
        PagesDashboards::getDashboard();

        // Imprime os scripts do rodapé
        // wp_print_footer_scripts();

        // Finaliza o buffer de saída e imprime o conteúdo
        echo ob_get_clean();

        exit;
    }

    public static function url_rewrite()
    {
        add_rewrite_rule(
            '^dashboard/?$',
            'index.php?dashboard=dashboard',
            'top'
        );
    }

    public static function url_rewrite_query_vars($vars)
    {
        $vars[] = 'dashboard';
        return $vars;
    }

    
}