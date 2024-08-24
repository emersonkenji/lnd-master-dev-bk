<?php

namespace App\Request;

use App\Controller\Templates\PageDownloads;
use App\Http\RemoteRequestHandler;
use App\Model\InserterDB;
use stdClass;
use WP_Error;

class CatalogManager
{
    private const TEMPLATES_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/files';
    private const TEMPLATES_CATEGORIES_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/categories';
    private const CATALOG_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/catalog/v2/files';
    private const CATEGORIES_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/catalog/v2/categories';
    private const TRANSIENT_CATALOG = 'lnd_catalog_data';
    private const TRANSIENT_CATALOG_CATEGORIES = 'lnd_catalog_categories';
    private const TRANSIENT_TEMPLATES = 'lnd_templates_data';
    private const TRANSIENT_TEMPLATES_CATEGORIES = 'lnd_templates_categories';

    private static $inserter;

    public static function init()
    {
        self::$inserter = new InserterDB();
    }

    public static function update_all_data()
    {
        self::lnd_insert_update_catalog();
        self::lnd_insert_update_templates();
    }

    private static function get_data($url, $transient, $refresh = false)
    {
        if (!$refresh) {
            $cached_data = get_transient($transient);
            if ($cached_data !== false) {
                return $cached_data;
            }
        }

        $data = RemoteRequestHandler::makeRequest($url, 'POST', null, true);

        if (isset($data->is_request_error) || is_wp_error($data)) {
            self::log_message('Request failure! Error: ' . (is_wp_error($data) ? $data->get_error_message() : $data->msg), 'error');
            return new WP_Error('request_failed', 'Request failed', array('status' => 401));
        }

        if (is_object($data) && $data instanceof stdClass) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            return new WP_Error('unexpected_response', 'Expected array but received something else', array('status' => 500));
        }

        set_transient($transient, $data, HOUR_IN_SECONDS);
        return $data;
    }

    public static function get_catalogo($refresh = false)
    {
        return self::get_data(self::CATALOG_URL, self::TRANSIENT_CATALOG, $refresh);
    }

    public static function get_categories($refresh = false)
    {
        return self::get_data(self::CATEGORIES_URL, self::TRANSIENT_CATALOG_CATEGORIES, $refresh);
    }

    public static function get_templates($refresh = false)
    {
        return self::get_data(self::TEMPLATES_URL, self::TRANSIENT_TEMPLATES, $refresh);
    }

    public static function get_templates_categories($refresh = false)
    {
        return self::get_data(self::TEMPLATES_CATEGORIES_URL, self::TRANSIENT_TEMPLATES_CATEGORIES, $refresh);
    }

    public static function lnd_insert_update_catalog()
    {
        self::init();
        $catalogo = self::get_catalogo(true);
        $categories = self::get_categories(true);

        if (is_wp_error($catalogo) || is_wp_error($categories)) {
            self::log_message("Error updating catalog or categories");
            return null;
        }

        foreach ($categories as $category) {
            $data = self::prepare_insert_categories($category);
            self::$inserter->insert_catalog_category($data);
        }

        foreach ($catalogo as $plugin) {
            $data = self::prepare_plugin_data($plugin);
            self::$inserter->insert_catalog($data);
        }

        self::log_message("Catalog and categories updated successfully");
        return true;
    }

    public static function lnd_insert_update_templates()
    {
        $templates = self::get_templates(true);
        $templates_categories = self::get_templates_categories(true);

        if (is_wp_error($templates) || is_wp_error($templates_categories)) {
            self::log_message("Error updating templates or template categories");
            return null;
        }

        foreach ($templates_categories as $category) {
            $data = self::prepare_insert_categories_templates($category);
            self::$inserter->insert_templates_categories($data);
        }

        foreach ($templates as $template) {
            $data = self::prepare_insert_templates($template);
            self::$inserter->insert_templates_files($data);
        }

        self::log_message("Templates and template categories updated successfully");
        return true;
    }

    private static function log_message($message, $type = 'info')
    {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            error_log("CatalogManager $type: $message");
        }
    }

    private static function prepare_plugin_data(stdClass $plugin)
    {
        $filepath = $plugin->filepath;
        $exploded = explode('/', $filepath);
        $instance = empty($plugin->instance) ? 'lnd-library' : $plugin->instance;

        return [
            'id' => $plugin->id,
            'status' => sanitize_text_field($plugin->status),
            'item_name' => sanitize_text_field($plugin->item_name),
            'type' => sanitize_text_field($plugin->type),
            'is_free' => $plugin->is_free,
            'version' => sanitize_text_field($plugin->version),
            'filepath' => sanitize_text_field($filepath),
            'image' => isset($plugin->image) && $plugin->image != null ? esc_url_raw($plugin->image) : '',
            'description' => isset($plugin->description) && $plugin->description != null ? wp_kses_post($plugin->description) : '',
            'demo' => isset($plugin->demo) && $plugin->demo != null ? esc_url_raw($plugin->demo) : '',
            'update_date' => sanitize_text_field($plugin->update_date),
            'data' => sanitize_text_field($plugin->created),
            'downloads' => esc_url_raw(PageDownloads::generate_download_link($instance, $exploded[0], $plugin->version)),
            'internal_downloads' => esc_url_raw(PageDownloads::generate_download_link('lnd-internal-downloads', $exploded[0], 'latest')),
            'instance' => intval($plugin->instance),
            'count' => intval($plugin->count),
            'category_id' => sanitize_text_field($plugin->category)
        ];
    }

    private static function prepare_insert_categories($categories)
    {
        return [
            'id' => intval($categories->id),
            'name' => sanitize_text_field($categories->name),
            'parent_id' => $categories->parent_id == 0 ?  null : intval($categories->parent_id),
            'created' => sanitize_text_field($categories->created)
        ];
    }

    private static function prepare_insert_templates($templates)
    {
        return [
            'id' => intval($templates->id),
            'filename' => sanitize_text_field($templates->filename),
            'category_id' => $templates->category_id == 0 ?  null : intval($templates->category_id),
            'img' => sanitize_text_field($templates->img),
        ];
    }

    private static function prepare_insert_categories_templates($templates_categories)
    {
        return [
            'id' => intval($templates_categories->id),
            'name' => sanitize_text_field($templates_categories->name),
            'parent_id' => $templates_categories->parent_id == 0 ?  null : intval($templates_categories->parent_id)
        ];
    }
}


// namespace App\Request;

// use App\Controller\Templates\PageDownloads;
// use App\Http\RemoteRequestHandler;
// use App\Request\Downloads;
// use stdClass;
// use WP_Error;

// class CatalogManager
// {
//     // private const CATALOG_URL = 'https://planos.lojanegociosdigital.com.br/wp-json/lnd_catalog/v1/plugins.json';
//     //https://api.lojanegociosdigital.com.br/wp-json/catalog/v2/files
//     // private const CATALOG_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/lnd_catalog /v1/plugins.json';
//     private const TEMPLATES_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/files';
//     private const TEMPLATES_CATEGORIES_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/files';
//     private const CATALOG_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/catalog/v2/files';
//     private const CATEGORIES_URL = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/catalog/v2/categories';
//     private const TRANSIENT_CATALOG = 'lnd_catalog_data';
//     private const TRANSIENT_CATALOG_CATEGORIES = 'lnd_catalog_categories';
    

//     public static function init()
//     {
//         // add_action('init', [self::class, 'get_catalogo']);
//         // add_filter('transient_update_plugins', [self::class, 'lnd_get_plugin_update'], 99);
//         // add_filter('pre_set_site_transient_update_themes', [self::class, 'lnd_get_theme_update']);
//     }

//     public static function get_catalogo( $refresh = false ) :array|WP_Error
//     {
//         $cached_catalog = get_transient(self::TRANSIENT_CATALOG);
//         if ($cached_catalog !== false && $refresh === false) {
//             return $cached_catalog;
//         }
        
//         $data = RemoteRequestHandler::makeRequest(self::CATALOG_URL, 'POST', null, true);
       
//         if (isset($data->is_request_error)) {
//             self::log_message('Catalog request failure! Error: ' . $data->msg . 'status' . $data->status_code, 'error');
//             return new WP_Error('auth_failed', 'authentication failed', array('status' => 401));
//         }

//         if (is_wp_error($data)) {
//             self::log_message('Catalog request failure! Error: ' . $data->msg, 'error');
//             return $data;
//         }
        
//         if (is_object($data) && $data instanceof stdClass) {
//             $data = (array) $data;
//         }
        
//         if (!is_array($data)) {
//             // Se $data não for um array, retorna um WP_Error
//             return new WP_Error('unexpected_response', 'Expected array but received something else', array('status' => 500));
//         }

//         set_transient(self::TRANSIENT_CATALOG, $data, HOUR_IN_SECONDS);
//         // update_option(self::OPTION_CATALOG, $data);
//         return  $data;
//     }

//     public static function get_categories( $refresh = false ) :array|WP_Error
//     {
//         $cached_catalog = get_transient(self::TRANSIENT_CATALOG_CATEGORIES);
//         if ($cached_catalog !== false && $refresh === false) {
//             return $cached_catalog;
//         }
        
//         $data = RemoteRequestHandler::makeRequest(self::CATEGORIES_URL, 'POST', null, true);
       
//         if (isset($data->is_request_error)) {
//             self::log_message('Catalog request failure! Error: ' . $data->msg . 'status' . $data->status_code, 'error');
//             return new WP_Error('auth_failed', 'authentication failed', array('status' => 401));
//         }

//         if (is_wp_error($data)) {
//             self::log_message('Catalog request failure! Error: ' . $data->msg, 'error');
//             return $data;
//         }
        
//         // Verifica se $data é um array ou um objeto stdClass e faz a conversão, se necessário
//         if (is_object($data) && $data instanceof stdClass) {
//             $data = (array) $data;
//         }
        
//         if (!is_array($data)) {
//             // Se $data não for um array, retorna um WP_Error
//             return new WP_Error('unexpected_response', 'Expected array but received something else', array('status' => 500));
//         }

//         set_transient(self::TRANSIENT_CATALOG_CATEGORIES, $data, HOUR_IN_SECONDS);
//         // update_option(self::OPTION_CATALOG, $data);
//         return  $data;
//     }

//     /**
//      * Metodo respoinsavel por iserir no DB do catalogo
//      * @var array{id: mixed, item_name: string, status: string, type: string, is_free: mixed, version: string, filepath: string, image: string,
//      *  description: string, demo: string, update_date: string, category: string, downloads: string, internal_downloads: string, data: string, 
//      *  instance: string, count: int} $data
//      *
//      * @return void
//      */
//     public static function lnd_insert_update_catalog()
// {
//     global $wpdb;

//     self::insert_update_catalog_categories();

//     $catalogo = self::get_catalogo(true);
//     if (empty($catalogo) || is_wp_error($catalogo)) {
//         self::log_message("Error Updated/Inserted catalog in: $catalogo");
//         return;
//     }

//     if (isset($catalogo->is_request_error)) {
//         return $catalogo;
//     }

//     $table_name = $wpdb->prefix . 'lnd_master_catalog';
//     $wpdb->query("TRUNCATE TABLE $table_name");

//     foreach ($catalogo as $plugin) {
//         $data = self::prepare_plugin_data($plugin);
//         // $existing_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $data['id']));

//         // if ($existing_row) {
//         //     $wpdb->update($table_name, $data, array('id' => $data['id']));
//         // } else {
//             $wpdb->insert($table_name, $data);
//         // }
//     }

//     self::log_message("Inserted files in: $table_name");
//     return $catalogo;
// }

// private static function insert_update_catalog_categories()
// {
//     global $wpdb;
//     $categories = self::get_categories(true);
//     if (empty($categories) || is_wp_error($categories)) {
//         self::log_message("Error Updated/Inserted categories in: $categories");
//         return;
//     }
//     $table_name = $wpdb->prefix . 'lnd_master_catalog_category';

//     $wpdb->query("TRUNCATE TABLE $table_name");

//     foreach ($categories as $category) {
//         $data = self::prepare_insert_categories($category);
//         // $existing_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $data['id']));

//         // if ($existing_row) {
//         //     $wpdb->update($table_name, $data, array('id' => $data['id']));
//         // } else {
//             $wpdb->insert($table_name, $data);
//         // }
//     }

//     self::log_message("Inserted categories in: $table_name");

//     return true;
// }

//     private static function log_message($message, $type = 'info')
//     {
//         if (defined('WP_DEBUG') && WP_DEBUG === true) {
//             error_log("CreateTables $type: $message");
//         }
//     }

//     private static function prepare_plugin_data(stdClass $plugin)
//     {
//         $filepath = $plugin->filepath;
//         $exploded = explode('/', $filepath);
//         $instance = empty($plugin->instance) ? 'lnd-library' : $plugin->instance;
        
//         return [
//             'id' => $plugin->id,
//             'status' => sanitize_text_field($plugin->status),
//             'item_name' => sanitize_text_field($plugin->item_name),
//             'type' => sanitize_text_field($plugin->type),
//             'is_free' => $plugin->is_free,
//             'version' => sanitize_text_field($plugin->version),
//             'filepath' => sanitize_text_field($filepath),
//             'image' => isset($plugin->image) && $plugin->image != null ? esc_url_raw($plugin->image): '',
//             'description' => isset($plugin->description) && $plugin->description != null ?wp_kses_post($plugin->description) : '',
//             'demo' => isset($plugin->demo) && $plugin->demo != null ? esc_url_raw($plugin->demo) : '',
//             'update_date' => sanitize_text_field($plugin->update_date),
//             'data' => sanitize_text_field($plugin->created),
//             'downloads' => esc_url_raw(PageDownloads::generate_download_link($instance, $exploded[0], $plugin->version)),
//             'internal_downloads' => esc_url_raw(PageDownloads::generate_download_link('lnd-internal-downloads', $exploded[0], 'latest')),
//             'instance' => intval($plugin->instance),
//             'count' => intval($plugin->count),
//             'category_id' => sanitize_text_field($plugin->category)
//         ];
//     }

//     private static function prepare_insert_categories($categories)
//     {
//         return [
//             'id' => intval($categories->id),
//             'name' => sanitize_text_field($categories->name),
//             'parent_id' => $categories->parent_id == 0 ?  null : intval($categories->parent_id),
//             'created' => sanitize_text_field($categories->created)
//         ];
       
//     }

//     private static function prepare_insert_templates($templates)
//     {
//         return [
//             'id' => intval($templates->id),
//             'filename' => sanitize_text_field($templates->filename),
//             'category_id' => $templates->category_id == 0 ?  null : intval($templates->parent_id),
//             'img' => sanitize_text_field($templates->img),
//         ];
//     }

//     private static function prepare_insert_categories_templates($templates_categories)
//     {
//         return [
//             'id' => intval($templates_categories->id),
//             'name' => sanitize_text_field($templates_categories->name),
//             'parent_id' => $templates_categories->category_id == 0 ?  null : intval($templates_categories->parent_id)
//         ];
//     }
// }