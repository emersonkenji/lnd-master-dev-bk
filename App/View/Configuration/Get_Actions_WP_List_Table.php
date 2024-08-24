<?php

namespace App\View\Configuration;

use WC_Product;
use WC_Product_Attribute;
use WC_Product_External;
use WC_Product_Grouped;
use WC_Product_Simple;
use WC_Product_Variable;

if (!defined('ABSPATH')) {
    exit();
}

class Get_Actions_WP_List_Table
{

    public function __construct()
    {
        $this->get_create_product_all();
        $this->get_create_product_multiple();
    }

    public function get_create_product_all()
    {
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ('lnd-master-dev-downloads' != $page) {
            return;
        }
        if (!isset($_POST['element'])) {
            return;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'create_product_all') {
            $elements = $_POST['element'];
            
            foreach ($elements as $key => $id) {
                $value = Self::get_info_by_id($id);
                $item = [];
                $content = array();
                foreach ($value as $info) {
                    
                    $slug = explode('/', $info->filepath)[0];
                    $link_url = set_url_scheme(get_site_url(), 'https');
                    $link_downloads = $link_url . '/lnd-downloads/lnd-internal-downloads/' . $slug . '/latest/';

                    $item['name'] = $info->type == 'plugin' ? $info->item_name : 'Theme ' . $info->item_name;
                    $item['description'] = $info->description != null ? $info->description : '';
                    $item['SKU'] = '';
                    $item['slug'] = $slug;
                    $item['download_url'] = $link_downloads;
                    $item['imagem'] = $info->image;
                    $item['type'] = $info->type;
                    $new_contets = array(
                        'h1' => $info->item_name,
                        'p' => $info->description,
                        'li' => [$info->item_name],
                        'type' => $info->type
                    );
                    array_push($content, $new_contets);

                }
                $item['content'] = $content;
                $product_id = Self::create_simple_product($item);
            }
            
        }
    }

    public function get_create_product_multiple()
    {
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ('lnd-master-dev-downloads' != $page) {
            return;
        }
        if (!isset($_POST['elements'])) {
            return;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'form_create_product_multiple') {
            $item = [];
            $elements = $_POST['elements'];
            $itens_download = $_POST['elements-ids'];
            $downloads = array();
            $content = array();
            foreach ($elements as $key => $id) {
                $value = Self::get_info_by_id($id);
                foreach ($value as $info) {
                    

                    $slug = explode('/', $info->filepath)[0];
                    $link_url = set_url_scheme(get_site_url(), 'https');
                    $link_downloads = $link_url . '/lnd-downloads/lnd-internal-downloads/' . $slug . '/latest/';

                    $item['name'] = $info->type == 'plugin' ? $info->item_name : $info->item_name;
                    // $item['name'] = $info->item_name;
                    $item['description'] = $info->description != null ? $info->description : '';
                    $item['SKU'] =  $slug .'|LND';
                    $item['slug'] = $slug;
                    $item['type'] = $info->type;
                    // $item['download_url'] = $link_downloads;
                    $item['imagem'] = $info->image;
                    $new_download = array(
                        'name' => $info->type == 'plugin' ? $info->item_name : 'Theme ' . $info->item_name,
                        'file' => $link_downloads
                    );

                    $new_contets = array(
                        'h1' => $info->item_name,
                        'p' => $info->description,
                        'li' => [],
                        'type' => $info->type
                    );
                    array_push($content, $new_contets);

                    array_push($downloads, $new_download);
                    $item['content'] = $content;
                }
            }
            
            $download = explode(',', $itens_download[0]);
            
            foreach ($download as $key => $id_downloads) {
                $d = Self::get_info_by_id($id_downloads);
                foreach ($d as $v) {
                    $slugDownloads = explode('/', $v->filepath)[0];
                    $link_url = set_url_scheme(get_site_url(), 'https');
                    $link_download = $link_url . '/lnd-downloads/lnd-internal-downloads/' . $slugDownloads . '/latest/';

                    $new_download = array(
                        'name' => $v->item_name,
                        'file' => $link_download
                    );
                    $new_content = $v->item_name;
                    // );
                    array_push($content[0]['li'],  $new_content);
                    array_push($downloads, $new_download);
                }
            }

            $item['downloads'] = $downloads;
            $item['content'] = $content;
            $product_id = Self::create_simple_product($item);
        }
    }

    public static function get_info_by_id($id)
    {
        global $wpdb;
        $tabela_lnd = $wpdb->prefix . 'lnd_items_tbl';
        $query = "SELECT *  FROM " . $tabela_lnd . " WHERE id = $id";
        return $wpdb->get_results($query);
    }

    //funcionando 
    public function create_simple_product(array $item)
    {
        require_once(ABSPATH . 'wp-load.php');
        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-includes/post.php');
        require_once(ABSPATH . 'wp-includes/media.php');
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        require_once(ABSPATH . 'wp-includes/user.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            // $user_id = get_current_user(); // this has NO SENSE AT ALL, because wp_insert_post uses current user as default value
            $user_id = get_current_user_id(); // obter o ID do usuário atual
            if (!$user_id) {
                $user_id = get_option('default_role'); // definir como o usuário padrão do site
            }
            $post_id = wp_insert_post(array(
                'post_author' => $user_id,
                'post_title' => $item['name'],
                'post_content' => empty($item['content']) ? $item['description'] : Self::insert_description($item['content']) ,
                'post_status' => 'publish',
                'post_type' => "product",
                'post_category' => array(123),
            ));
            
            wp_set_object_terms($post_id, 'simple', 'product_type');

            $new_excerpt = 'Conteúdo premium original. Acesso imediato, Suporte dinâmico feito por quem trabalha a anos no ramo.'; // Replace this with the new excerpt

            // Update the post excerpt
            $update_post = array(
                'ID'           => $post_id,
                'post_excerpt' => $new_excerpt,
            );

            wp_update_post($update_post);

            // $tags = array('tag1', 'tag2', 'tag3'); // Replace these tags with the tags you want to add to your product

            // // Add the tags to the product
            // wp_set_post_terms($post_id, $tags, 'product_tag');

            update_post_meta($post_id, '_visibility', 'visible');
            update_post_meta($post_id, 'post_excerpt', 'teste');
            update_post_meta($post_id, '_stock_status', 'instock');
            update_post_meta($post_id, 'total_sales', '0');
            update_post_meta($post_id, '_downloadable', 'yes');
            update_post_meta($post_id, 'slug', $item['SKU']);
            update_post_meta($post_id, '_virtual', 'yes');
            update_post_meta($post_id, '_regular_price', "59.90");
            update_post_meta($post_id, '_sale_price', "34.90");
            update_post_meta($post_id, '_purchase_note', "");
            update_post_meta($post_id, '_featured', "no");
            update_post_meta($post_id, '_weight', "");
            update_post_meta($post_id, '_length', "");
            update_post_meta($post_id, '_width', "");
            update_post_meta($post_id, '_height', "");
            update_post_meta($post_id, '_sku', $item['SKU']);
            update_post_meta($post_id, '_product_attributes', array());
            update_post_meta($post_id, '_sale_price_dates_from', "");
            update_post_meta($post_id, '_sale_price_dates_to', "");
            update_post_meta($post_id, '_price', "34.90");
            update_post_meta($post_id, '_sold_individually', "");
            update_post_meta($post_id, '_manage_stock', "no");
            update_post_meta($post_id, '_backorders', "no");
            update_post_meta($post_id, '_stock', "");
            // $tags = array('WordPress', 'Woocommerce', 'criação de sites', 'super pack elementor pro', 'elementor pro', 'elementor pro free', 'elementor pro gratis', 'hostgator');

            // wp_set_post_terms($post_id, $tags, 'post_tag');

            if (!empty($item['download_url'])) {
                $downdloadArray = array('name' => $item['name'], 'file' => $item['download_url']);
                $file_path = md5($item['download_url']);
                $_file_paths[$file_path] = $downdloadArray;
                // grant permission to any newly added files on any existing orders for this product
                do_action('woocommerce_process_product_file_download_paths', $post_id, 0, $downdloadArray);
                update_post_meta($post_id, '_downloadable_files', $_file_paths);
                update_post_meta($post_id, '_download_limit', '');
                update_post_meta($post_id, '_download_expiry', '');
                update_post_meta($post_id, '_download_type', '');
            }
            if (!empty($item['downloads'])) {
                $download_files = [];
                foreach ($item['downloads'] as $index => $download) {
                    $downdloadArray = array('name' => $download['name'], 'file' => $download['file']);
                    $file_path = md5($download['file']);
                    $key = $download['name'] . '|' . $file_path; // cria a chave composta
                    $download_files[$key] = $downdloadArray;
                    do_action('woocommerce_process_product_file_download_paths', $post_id, $index, $downdloadArray);
                }

                update_post_meta($post_id, '_downloadable_files', $download_files);
            }
            
            $image_url = $item['imagem'];
            if ($image_url) {
                Self::upload_product_image($post_id, $image_url);
            }
        }
    }

    public function insert_description(array $content)
    {
        $description = '';
        $description .= '<h1>'.ucwords($content[0]['type']).': '.$content[0]['h1'].'</h1>';
        $description .= '<h4>Descrição: </h4>';
        $description .= '<p>'.$content[0]['p'].'</p>';
        $description .= '<h4>Conteúdo incluso:</h4>';
        $content_li = '';
        foreach ($content[0]['li'] as $li) {
            $content_li .= '<li>PLugin: '.$li.'</li>';
        }
        $description .= '<ol>' .$content_li.'</ol>';
        $description .= '<h5>Conteúdo premium original. Acesso imediato, Suporte dinâmico feito por quem trabalha a anos no ramo.</h5>';
        $description .= '<h5>Este '.$content[0]['type'].' e milhares de plugins e temas esta dentro da nossa biblioteca "LND Library" <a href=="https://lojanegociosdigital.com.br/lnd-auto-update/">CLIQUE AQUI PARA VER A BIBLIOTECA COMPLETA</a></h5>';

        return $description;

    }

    /**
     * Upload a product image to WordPress media library
     *
     * @param string $image_url URL of product image
     * @return int|false ID of uploaded image or false on failure
     */
    private static function upload_product_image($post_id, $image_url)
    {
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Get the file name
        $file_name = basename($image_url);
        require_once ABSPATH . '/wp-admin/includes/file.php';
        require_once ABSPATH . '/wp-admin/includes/media.php';
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Faz o upload da imagem para a biblioteca de mídia
        $image_id = media_sideload_image($image_url, 0, $file_name, 'id');

        // Associa a imagem ao produto
        update_post_meta($post_id, '_thumbnail_id', $image_id);
        // Associa a imagem a galeria do produto
        update_post_meta($post_id, '_product_image_gallery', $image_id);
    }
}
