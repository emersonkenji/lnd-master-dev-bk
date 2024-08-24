<?php
namespace App\Utils\WooCommerce;

use WC_Product;
use WC_Product_Simple;
use WC_Product_Download;
use WP_Error;

if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}

class WooCreateProd {
    private $page;

    public function __construct()
    {
        $this->page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        
        add_action('admin_init', [$this, 'handle_actions']);
    }

    public function handle_actions()
    {
        if ($this->page !== 'lnd-master-dev-downloads') {
            return;
        }

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create_product_all':
                    $this->create_product_all();
                    break;
                case 'form_create_product_multiple':
                    $this->create_product_multiple();
                    break;
            }
        }
    }

    private function create_product_all()
    {
        if (!isset($_POST['element']) || !is_array($_POST['element'])) {
            return;
        }

        foreach ($_POST['element'] as $id) {
            $product_data = $this->prepare_product_data($id);
            $this->create_woocommerce_product($product_data);
        }
    }

    private function create_product_multiple()
    {
        if (!isset($_POST['elements']) || !isset($_POST['elements-ids'])) {
            return;
        }

        $product_data = $this->prepare_multiple_product_data($_POST['elements'], $_POST['elements-ids']);
        $this->create_woocommerce_product($product_data);
    }

    private function prepare_product_data($id)
    {
        $info = $this->get_info_by_id($id);
        if (empty($info)) {
            return null;
        }

        $info = $info[0];
        $slug = explode('/', $info->filepath)[0];
        $link_url = set_url_scheme(get_site_url(), 'https');
        $link_downloads = $link_url . '/downloads-files/lnd-internal-downloads/' . $slug . '/latest/';

        return [
            'name' => $info->type === 'plugin' ? $info->item_name : "Theme {$info->item_name}",
            'description' => $info->description ?? '',
            'sku' => "{$slug}|LND",
            'slug' => $slug,
            'download_url' => $link_downloads,
            'image' => $info->image,
            'type' => $info->type,
            'content' => [
                'h1' => $info->item_name,
                'p' => $info->description,
                'li' => [$info->item_name],
                'type' => $info->type
            ]
        ];
    }

    private function prepare_multiple_product_data($elements, $elements_ids)
    {
        $product_data = [
            'name' => '',
            'description' => '',
            'sku' => '',
            'slug' => '',
            'type' => '',
            'image' => '',
            'downloads' => [],
            'content' => [
                'h1' => '',
                'p' => '',
                'li' => [],
                'type' => ''
            ]
        ];

        foreach ($elements as $id) {
            $info = $this->get_info_by_id($id)[0];
            $slug = explode('/', $info->filepath)[0];
            $link_url = set_url_scheme(get_site_url(), 'https');
            $link_downloads = $link_url . '/downloads-files/lnd-internal-downloads/' . $slug . '/latest/';

            $product_data['name'] = $info->item_name;
            $product_data['description'] = $info->description ?? '';
            $product_data['sku'] = "{$slug}|LND";
            $product_data['slug'] = $slug;
            $product_data['type'] = $info->type;
            $product_data['image'] = $info->image;

            $product_data['downloads'][] = [
                'name' => $info->type === 'plugin' ? $info->item_name : "Theme {$info->item_name}",
                'file' => $link_downloads
            ];

            $product_data['content']['h1'] = $info->item_name;
            $product_data['content']['p'] = $info->description;
            $product_data['content']['type'] = $info->type;
        }

        $download_ids = explode(',', $elements_ids[0]);
        foreach ($download_ids as $id) {
            
            $info = $this->get_info_by_id($id)[0];
            $slug = explode('/', $info->filepath)[0];
            $link_url = set_url_scheme(get_site_url(), 'https');
            $link_downloads = $link_url . '/downloads-files/lnd-internal-downloads/' . $slug . '/latest/';

            $product_data['downloads'][] = [
                'name' => $info->item_name,
                'file' => $link_downloads
            ];
            $product_data['content']['li'][] = $info->item_name;
        }

        return $product_data;
    }

    public function create_woocommerce_product(array $item)
{
    if (!$this->check_woocommerce_active()) {
        return false;
    }

    $product = new WC_Product_Simple();

    $product->set_name($item['name']);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_description($this->generate_description($item['content']));
    $product->set_sku($item['sku']);
    $product->set_price(34.90);
    $product->set_regular_price(59.90);
    $product->set_sale_price(34.90);
    $product->set_manage_stock(false);
    $product->set_downloadable(true);
    $product->set_virtual(true);

    // Set product downloads
    $downloads = array();
    if (!empty($item['downloads'])) {
        foreach ($item['downloads'] as $download) {
            $downloads[] = array(
                'name' => $download['name'],
                'file' => $download['file']
            );
        }
    } elseif (!empty($item['download_url'])) {
        $downloads[] = array(
            'name' => $item['name'],
            'file' => $item['download_url']
        );
    }
    $product->set_downloads($downloads);

    // Set product image
    if (!empty($item['image'])) {
        $attachment_id = $this->upload_product_image($item['image']);
        if ($attachment_id) {
            $product->set_image_id($attachment_id);
        }
    }

    // Save the product
    $product_id = $product->save();
    if ($product_id) {
        // Set product category
        wp_set_object_terms($product_id, 'simple', 'product_type');

        // Set custom meta
        update_post_meta($product_id, 'slug', $item['slug']);

        // Set excerpt
        $excerpt = 'Conteúdo premium original. Acesso imediato, Suporte dinâmico feito por quem trabalha a anos no ramo.';
        $product->set_short_description($excerpt);
        $product->save();
    }

    return $product_id;
}

    private function check_woocommerce_active()
    {
        return class_exists('WooCommerce');
    }

    private function generate_description(array $content)
    {
        $description = "<h1>" . ucwords($content['type']) . ": {$content['h1']}</h1>";
        $description .= "<h4>Descrição:</h4>";
        $description .= "<p>{$content['p']}</p>";
        $description .= "<h4>Conteúdo incluso:</h4>";
        $description .= "<ol>" . implode('', array_map(fn($li) => "<li>Plugin: {$li}</li>", $content['li'])) . "</ol>";
        $description .= "<h5>Conteúdo premium original. Acesso imediato, Suporte dinâmico feito por quem trabalha a anos no ramo.</h5>";
        $description .= "<h5>Este {$content['type']} e milhares de plugins e temas esta dentro da nossa biblioteca \"LND Library\" <a href=\"https://lojanegociosdigital.com.br/lnd-auto-update/\">CLIQUE AQUI PARA VER A BIBLIOTECA COMPLETA</a></h5>";

        return $description;
    }

    private function upload_product_image($image_url)
    {
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            return false;
        }

        $file_array = [
            'name' => basename($image_url),
            'tmp_name' => $tmp
        ];

        $id = media_handle_sideload($file_array, 0);

        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        return $id;
    }

    private function get_info_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lnd_master_catalog';
        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $id);
        return $wpdb->get_results($query);
    }
}