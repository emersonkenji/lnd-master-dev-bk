<?php

namespace App\Controller\Ajax;

use App\Request\CatalogManager;
use App\Utils\Module\Pagination;
use App\Controller\Plugin;
use App\Controller\Theme;
use App\View\LND_Get_Itens;
use App\Request\Downloads;
use App\Model\CatalogoModel; 

if (!defined('ABSPATH')) {
    exit();
}

class AdminPageCatalog
{

    public static function init()
    {
        add_action('wp_ajax_lnd_get_catalog_itens', array(__CLASS__, 'lnd_get_catalog_itens'));
        add_action('wp_ajax_nopriv_lnd_get_catalog_itens', array(__CLASS__, 'lnd_get_catalog_itens'));

        add_action('wp_ajax_lnd_get_catalog_itens_plataforma', array(__CLASS__, 'lnd_get_catalog_itens_plataforma'));
        add_action('wp_ajax_nopriv_lnd_get_catalog_itens_plataforma', array(__CLASS__, 'lnd_get_catalog_itens_plataforma'));
        // add_action('wp_ajax_lnd_get_catalog_itens_plataforma', array(__CLASS__, 'lnd_get_catalog_itens'));
        // add_action('wp_ajax_nopriv_lnd_get_catalog_itens_plataforma', array(__CLASS__, 'lnd_get_catalog_itens'));
        
        add_action('wp_ajax_lnd_update_catalog_ajax', array(__CLASS__, 'lnd_update_catalog_ajax'));
        add_action('wp_ajax_nopriv_lnd_update_catalog_ajax', array(__CLASS__, 'lnd_update_catalog_ajax'));
        
        add_action('wp_ajax_lnd_get_actions_plugins', array(__CLASS__, 'lnd_get_actions_plugins'));
        add_action('wp_ajax_nopriv_lnd_get_actions_plugins', array(__CLASS__, 'lnd_get_actions_plugins'));

        add_action('wp_ajax_lnd_install_itens', array(__CLASS__, 'lnd_install_itens'));
        add_action('wp_ajax_nopriv_lnd_install_itens', array(__CLASS__, 'lnd_install_itens'));

        add_action('wp_ajax_lnd_update_itens', array(__CLASS__, 'lnd_update_itens'));
        add_action('wp_ajax_nopriv_lnd_update_itens', array(__CLASS__, 'lnd_update_itens'));

        add_action('wp_ajax_lnd_activate_itens', array(__CLASS__, 'lnd_activate_itens'));
        add_action('wp_ajax_nopriv_lnd_activate_itens', array(__CLASS__, 'lnd_activate_itens'));

        add_action('wp_ajax_lnd_wp_ajax_install_plugin', array(__CLASS__, 'lnd_wp_ajax_install_plugin'));
        add_action('wp_ajax_nopriv_lnd_wp_ajax_install_plugin', array(__CLASS__, 'lnd_wp_ajax_install_plugin'));

        add_action('wp_ajax_lnd_downloads_now', array(__CLASS__, 'lnd_downloads_now'));
        add_action('wp_ajax_nopriv_lnd_downloads_now', array(__CLASS__, 'lnd_downloads_now'));

    }

    public static function lnd_get_catalog_itens()
    {
        check_ajax_referer( 'ajax-nonce' );

        $limit = $_POST['limit'] != '' ? $_POST['limit'] : 30;
        $page = $_POST['page'] > 1 ? $_POST['page'] : 1;
        $order = $_POST['order'] != '' ? $_POST['order'] :'update_date';
        $order_by = $_POST['order_by'] != '' ? $_POST['order_by'] :'DESC';

        $queryBuilder  = new CatalogoModel;
        $queryBuilder->setLimit($limit);
        $queryBuilder->setPage($page);
        $queryBuilder->setOrder($order);
        $queryBuilder->setOrderBy($order_by);
        $queryBuilder->setType(isset($_POST['type']) && $_POST['type'] !='' ? $_POST['type'] : null);
        $queryBuilder->setQuery(isset($_POST['query']) && $_POST['query'] != '' ? $_POST['query'] : null);
        $queryBuilder->setCategory(isset($_POST['category']) && $_POST['category']!= '' ? $_POST['category'] : null);
        $queryBuilder->setFilter(!empty($_POST['filter']) ? $_POST['filter'] : null);
        $result =  $queryBuilder->executeQuery();

        $itens = '';

        if ($result['total'] > 0) {
            $itens .= Pagination::lnd_pagination_ajax($result['page'], $result['total'], $result['limit']);
            $itens .= '<div class="row row-cols-md-5 gx-1 gy-4" id="lnd-post-grid">';
            // $itens .= '<div class="grid !grid-cols-4 gap-4" id="lnd-post-grid">';
            $itens .= LND_Get_Itens::lnd_get_plugins($result['result']);
            $itens .= '</div>';
            $itens .= Pagination::lnd_pagination_ajax($result['page'], $result['total'], $result['limit']);
        } else {
            $itens .= Pagination::lnd_pagination_ajax(1, 1, $result['limit']);
            $itens .= '<div class="alert alert-danger" role="alert">Nenhum resultado encontrado!</div>';
            $itens .= Pagination::lnd_pagination_ajax(1, 1, $result['limit']);
        }

        // wp_send_json($result);
        wp_send_json($itens);
    }

    public static function lnd_get_catalog_itens_plataforma()
    {
        check_ajax_referer( 'ajax-nonce' );

        $limit = $_POST['limit'] != '' ? $_POST['limit'] : 30;
        $page = $_POST['page'] > 1 ? $_POST['page'] : 1;
        $order = $_POST['order'] != '' ? $_POST['order'] :'update_date';
        $order_by = $_POST['order_by'] != '' ? $_POST['order_by'] :'DESC';

        $queryBuilder  = new CatalogoModel;
        $queryBuilder->setLimit($limit);
        $queryBuilder->setPage($page);
        $queryBuilder->setOrder($order);
        $queryBuilder->setOrderBy($order_by);
        $queryBuilder->setType(isset($_POST['type']) && $_POST['type'] !='' ? $_POST['type'] : null);
        $queryBuilder->setQuery(isset($_POST['query']) && $_POST['query'] != '' ? $_POST['query'] : null);
        $queryBuilder->setCategory(isset($_POST['category']) && $_POST['category']!= '' ? $_POST['category'] : null);
        $queryBuilder->setFilter(!empty($_POST['filter']) ? $_POST['filter'] : null);
        $queryBuilder->setPlans(!empty($_POST['plans']) ? $_POST['plans'] : null);
        $result =  $queryBuilder->executeQuery();

        $itens = '';
        if ($result['total'] > 0) {
            $itens .= Pagination::lnd_pagination_ajax($result['page'], $result['total'], $result['limit']);
            $itens .= '<div class="row row-cols-md-5 gx-1 gy-4" id="lnd-post-grid">';
            $itens .= LND_Get_Itens::lnd_get_plugins_plataforma($result['result']);
            $itens .= '</div>';
            $itens .= Pagination::lnd_pagination_ajax($result['page'], $result['total'], $result['limit']);
        } else {
            $itens .= Pagination::lnd_pagination_ajax(1, 1, $result['limit']);
            $itens .= '<div class="alert alert-danger" role="alert">Nenhum resultado encontrado!</div>';
            $itens .= Pagination::lnd_pagination_ajax(1, 1, $result['limit']);
        }

        // wp_send_json($itens);
        wp_send_json($itens);
    }

    public static function lnd_install_itens()
    {
        // check_ajax_referer( 'ajax-nonce' );
        $type = sanitize_text_field($_POST['type']);
        $item_name = sanitize_text_field($_POST['item_name']);
        $version = sanitize_text_field($_POST['version']);
        $path = sanitize_text_field($_POST['name']);
        $itens = sanitize_text_field($_POST['itens']);
        $links = Downloads::control_links($path, $version, $itens);
        $msg_success = __("Instalação concluida $type: $item_name - $version", 'lnd-auto-update') ;
        $msg_erro = __("Erro ao instalar  $type: $item_name - $version", 'lnd-auto-update');
        
        if ( isset($type) && $type === 'plugin') {

            if (!empty($path) && !empty($version)) {
                $callback = Plugin::installPlugin($links);
                $msg = $callback == true ? $msg_success : $msg_erro;
                Self::lnd_return_data($callback, $msg, $callback );
            }
        }

        if (isset($type) && $type === 'theme') {
            if (!empty($path) && !empty($version)) {
                $callback = Theme::installTheme($links);
                $msg = $callback == true ? $msg_success : $msg_erro;
                Self::lnd_return_data($callback, $msg , $links );
            } 
        } 

        Self::lnd_return_data('wait', __('Não temos as caracteristicas para fazer a instalação desse pacote', 'lnd-auto-update'), 'error');
    
    }

    public static function lnd_update_itens()
    {
        // check_ajax_referer( 'ajax-nonce' );
        $type = sanitize_text_field($_POST['type']);
        $item_name = sanitize_text_field($_POST['item_name']);
        $version = sanitize_text_field($_POST['version']);
        $path = sanitize_text_field($_POST['name']);
        $itens = sanitize_text_field($_POST['itens']);
        $success = __("Atualização concluida $type: $item_name - $version", 'lnd-auto-update');
        $error = __("Erro ao atualizar  $type: $item_name - $version", 'lnd-auto-update');
        $links = Downloads::control_links($path, $version, $itens);

        if (isset($type) && $type === 'plugin') {
            if (!empty($path) && !empty($version)) {
                $location = (ABSPATH . 'wp-content/plugins/' . $path);
                $args = array('package' =>  $links, 'destination' => $location, 'clear_destination' => true, 'clear_working' => false, 'abort_if_destination_exists' => false, 'is_multi' => false, 'hook_extra' => false);
                $callback = Plugin::updatePlugin($args);
                $msg = $callback == true ? $success : $error;
                Self::lnd_return_data(true, $msg, $callback );
            }
        }

        if (isset($type) && $type === 'theme') {

            if (!empty($path) && !empty($version)) {
                $location = WP_CONTENT_DIR . '/' . 'themes/' . $path;
                $args = array('package' =>  $links, 'destination' => $location, 'clear_update_cache' => true, 'clear_destination' => true, 'clear_working' => false, 'abort_if_destination_exists' => false, 'is_multi' => false, 'hook_extra' => false);
                $callback = Theme::update_theme($args);
                $msg = $callback == true ? $success : $error;
                Self::lnd_return_data(true,  $msg, $callback );
            }

        }

        Self::lnd_return_data('wait', __('Não temos as caracteristicas para fazer a instalação desse pacote', 'lnd-auto-update'), 'error');
    
    }

    public static function lnd_activate_itens()
    {
        // check_ajax_referer( 'ajax-nonce' );
        $type = sanitize_text_field($_POST['type']);
        $filepath = sanitize_text_field($_POST['filepath']);
        $item_name = sanitize_text_field($_POST['item_name']);
        $version = sanitize_text_field($_POST['version']);
        $path = sanitize_text_field($_POST['name']);
        $error = __("Erro ao ativar $type: $item_name - $version", 'lnd-auto-update');
        $success = __("$type: $item_name - $version Ativado com sucesso", 'lnd-auto-update');

        if ( $type === 'plugin') {
            if (isset($filepath)) {
                $callback = Plugin::activatePlugin($filepath);
                $msg = $callback == true ? $success : $error;
                Self::lnd_return_data(true, $msg, $callback );
            }
        }

        if ( $type === 'theme') {
            if (isset($path)) {
                $callback = Theme::activateTheme($path);
                $msg = $callback == true ? $success : $error;
                Self::lnd_return_data(true, $msg, $callback );
    
            }  
        }
        Self::lnd_return_data(false, __('failure!', 'lnd-auto-update'), false );

    }

    public static function lnd_update_catalog_ajax()
    {
        // check_ajax_referer('ajax-nonce');
        
        // $refresh = get_transient('lnd_last_refresh_catalog');

        // if (false === $refresh) {
        //     set_transient('lnd_last_refresh_catalog', true, 5 * MINUTE_IN_SECONDS);
            $response_ins = CatalogManager::lnd_insert_update_catalog();
            $response_temp = CatalogManager::lnd_insert_update_templates();

            if ($response_ins == null) {
                Self::lnd_return_data(false, __('Erro ao atualizar catalogo', 'lnd-auto-update'));
            }
            if ($response_temp == null) {
                Self::lnd_return_data(false, __('Erro ao atualizar templates', 'lnd-auto-update'));
            }
            Self::lnd_return_data(true, __('Catalogo atualizado', 'lnd-auto-update'));
        // }
        // Self::lnd_return_data('wait', __('Aguarde 5 min para atualizar novamente', 'lnd-auto-update'));
    }

    private static function lnd_return_data($status, $msg= '', $data= '')
    {
        $data = ['status' => $status, 'msg' => $msg, 'dados' => $data];

        wp_send_json($data);
    }
    
}
