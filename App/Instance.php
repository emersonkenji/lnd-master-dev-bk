<?php

namespace App;

use App\Auth\AuthToken;
use App\Controller\Enqueue\Enqueue;
use App\Controller\Templates\{Dashboard, PageDownloads, PagePlataforma};
use App\Controller\Updates\Updates;
use App\Controller\Ajax\AdminPageCatalog;
use App\Controller\Ajax\AjaxCatalogo;
use App\Controller\Api\ApiCatalogo;
use App\Controller\Core;
use App\Controller\Pages\admin\Configuration;
use App\Model\Database as ModelDatabase;
use App\Request\{CatalogManager, Downloads};
use App\Utils\{Cron, WooCommerce\WooCreateProd};
use App\Utils\WordPress\CustomAjaxAuth;
use App\View\Pages\Page_Plataforma;

class Instance
{
    private static $instance = null;

    private function __construct()
    {
        // Private constructor to prevent direct instantiation
    }

    public static function get_instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void
    {
        $this->add_hooks();
        $this->initialize_components();
        // $this->handle_admin_functionality();
    }

    private function add_hooks(): void
    {
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_action_links']);
        add_action('init', [$this, 'plugin_textdomain']);
    }

    private function initialize_components(): void
    {
        Database::init();
        ModelDatabase::init();
        Core::init();
        new AuthToken();
        Enqueue::init();
        Cron::init();
        Configuration::init();
        // new ApiCatalogo();
        new AjaxCatalogo();
        AdminPageCatalog::init();
        Page_Plataforma::init();
        PageDownloads::init();
        PagePlataforma::init();
        Dashboard::init();
        Updates::init();
        Downloads::init();
        new WooCreateProd();
        CustomAjaxAuth::get_instance();
    }

    private function handle_admin_functionality(): void
    {
        if (is_admin()) {
            // $this->update_catalog_if_needed();
        }
    }

    private function update_catalog_if_needed(): void
    {
        // $update = Database::lnd_update_check();
        // if ($update) {
        //     // CatalogManager::get_catalogo();
        //     // CatalogManager::lnd_insert_update_catalog();
        // }
    }

    public function add_action_links($links): array
    {
        $settingsLink = '<a href="' . esc_url(admin_url('admin.php?page=lnd-master-dev_license')) . '">' . 
                        __('Settings', 'lnd-master-dev') . '</a>';
        array_unshift($links, $settingsLink);
        return $links;
    }

    public function plugin_textdomain(): void
    {
        load_plugin_textdomain(
            'lnd-master-dev',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
}