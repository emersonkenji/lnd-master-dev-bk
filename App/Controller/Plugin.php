<?php

namespace App\Controller;

include_once ABSPATH .  'wp-admin/includes/class-wp-upgrader.php';
include_once ABSPATH .  'wp-admin/includes/class-plugin-upgrader.php';
include_once ABSPATH .  'wp-admin/includes/plugin.php';
require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php' );

use Plugin_Upgrader;
use WP_Upgrader;
use WP_Ajax_Upgrader_Skin;


class Plugin
{
    static $plugin_update_info = array();

    public static function init()
    {
        if (is_admin()) {
            if (!function_exists('get_plugins')) {
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            add_filter('site_transient_update_plugins', array(__CLASS__, 'modify_update_plugins'), 9999999);
            add_filter('site_transient_update_plugins', array(__CLASS__, 'get_catalogo_itens'), 9999999);
            // add_filter('site_transient_update_plugins', array( __CLASS__, 'disable_check_updater'), 9999998);
        }
    }

    public static function lnd_install_plugin($package, $args = array())
    {
        $skin  = new WP_Ajax_Upgrader_Skin();
		$lnd_install = new Plugin_Upgrader( $skin );
        $defaults    = array(
            'clear_update_cache' => true,
            'overwrite_package'  => false, // Do not overwrite files.
        );
        $parsed_args = wp_parse_args($args, $defaults);

        add_filter('upgrader_source_selection', array($lnd_install, 'check_package'));

        if ($parsed_args['clear_update_cache']) {
            // Clear cache so wp_update_plugins() knows about the new plugin.
            add_action('upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0);
        }

        $lnd_install->run(
            array(
                'package'           => $package,
                'destination'       => WP_PLUGIN_DIR,
                'clear_destination' => $parsed_args['overwrite_package'],
                'clear_working'     => true,
                'hook_extra'        => array(
                    'type'   => 'plugin',
                    'action' => 'install',
                ),
               
            )
        );

        remove_action('upgrader_process_complete', 'wp_clean_plugins_cache', 9);
        remove_filter('upgrader_source_selection', array($lnd_install, 'check_package'));

        if (!$lnd_install->result || is_wp_error($lnd_install->result)) {
            return $lnd_install->result;
        }

        // Force refresh of plugin update information.
        wp_clean_plugins_cache($parsed_args['clear_update_cache']);

        if ($parsed_args['overwrite_package']) {
            /**
             * Fires when the upgrader has successfully overwritten a currently installed
             * plugin or theme with an uploaded zip package.
             *
             * @since 5.5.0
             *
             * @param string  $package      The package file.
             * @param array   $data         The new plugin or theme data.
             * @param string  $package_type The package type ('plugin' or 'theme').
             */
            do_action('upgrader_overwrote_package', $package, $lnd_install->new_plugin_data, 'plugin');
        }

        return $lnd_install;
    }


    public static function installPlugin($plugin)
    {
        if (!function_exists('lnd_install')) {
            Self::lnd_install_plugin($plugin);
            wp_clean_plugins_cache(true);
            return true;
        } else {
            echo 'Falha na instalação';
        }
    }


    public static function activatePlugin($plugin)
    {
        // Define the new plugin you want to activate
        $plugin_path = (ABSPATH . 'wp-content/plugins/' . $plugin);

        // Get already-active plugins   
        $active_plugins = get_option('active_plugins');
        // Make sure your plugin isn't active
        if (isset($active_plugins[$plugin_path])){
            return false;
        }

        // Include the plugin.php file so you have access to the activate_plugin() function
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        // Activate your plugin
        activate_plugin($plugin_path);

        return true;
    }

    public static function updatePlugin($plugin)
    {
        if (!function_exists('run')) {
            $skin  = new WP_Ajax_Upgrader_Skin();

            $lnd_update = new WP_Upgrader($skin);

            $lnd_update->run($plugin);

            wp_clean_plugins_cache(true);

            return true;
        } else {
            echo 'Falha na instalação';
        }
    }

    public static function get_version_compare($info, $valor)
    {
        if (file_exists($info)) {
            $pluginData = get_plugin_data($info);

            if (version_compare($valor, $pluginData['Version'], '>')) {

                $version = '<spam class="border border-white badge bg-light text-danger rounded-1" style="border-radius: 3px;">' . __('Update to: ', 'lnd-master-dev') . $valor . '</spam>';
            } else {

                $version = '<spam class="border border-white badge bg-light text-primary rounded-1" style="border-radius: 3px;">' . __('Installed: ', 'lnd-master-dev') . $pluginData['Version'] . '</spam>';
            }
        } else {

            $version = '<spam class="border border-white badge bg-light text-success rounded-1" style="border-radius: 3px;">' . __('Version: ', 'lnd-master-dev') . $valor . '</spam>';
        }

        return  $version;
    }

    public static function get_new_plugins($data_inicial)
    {
        $data_final = date('d-m-Y');
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));
        if ($dias <= 30) {
            $new_product = '<span class="p-1 mt-2 position-absolute start-100 translate-middle badge bg-success"  style="border-radius:3px; margin-left:-20px ">'.__('New', 'lnd-master-dev').'</span>';
        } else {
            $new_product = '';
        }
        return $new_product;
    }

    public static function get_catalogo_itens($sql)
    {
        $code = get_option("lnd_master_dev_key");
        $email = get_option("LNDMasterDevPlugin_lic_email");
        $active  = get_option('active_plugins', array());
        $getPlugins = get_plugins();


        $items = '';
        foreach ($sql as $valor) :

            $info = (ABSPATH . 'wp-content/plugins/' . $valor->filepath);
            $LNDPath = explode('/', $valor->filepath);
            $LNDPaths = $LNDPath[0];
            $download_now = $LNDPaths;
            $LND_Version = $valor->version;
            $data_inicial = $valor->data;

            if (file_exists($info)) {
                $pluginInfo = get_plugin_data($info);
            }

            $versionPlugin = self::get_version_compare($info, $valor->version);

            $new = self::get_new_plugins($data_inicial);

            $download_nonce  = wp_create_nonce("action-download-now");

            $install_plugin = self_admin_url('admin.php?page=lnd-master-dev&tab='  . $LNDPaths . '&plugin_name=' . $valor->item_name . '&version=' . $valor->version . '&_wpnonce=' . $download_nonce);

            $activate_path = 'admin.php?page=lnd-master-dev&tab=' . $valor->filepath . '&plugin_name=' . $valor->item_name . '&version=' . $valor->version . '&_wpnonce=' . $download_nonce;

            $activate_Plugins = 'admin.php?page=lnd-master-dev_license';

            $download_nonce  = wp_create_nonce("action-download-now");

            $download_plugin = 'download.php?action=download-plugin&plugin_file=' . $download_now . '&plugin_name=' . $download_now . '&version=' . $LND_Version . '&_wpnonce=' . $download_nonce;

            $action_plugin_download = admin_url($download_plugin);

            $description = '';
            if (strlen($valor->description) > 150) {
                $description = substr($valor->description, 0, 150) . '...';
            } else {
                $description = $valor->description;
            }

            $imgResponse = plugins_url() . '/lnd-master-dev/assets/images/lnd-downloads.jpg';
            if (!empty($valor->image)) {
                $imgResponse = $valor->image;
            }

            $LNDBotao      = '';
            $btn_downloads = '';

            if (!empty($code) && !empty($email)) {
                $btn_downloads = '<a href="' . esc_url($action_plugin_download) . '" class="btn_downloads" target="_blank"><span class="dashicons dashicons-download"></span></a>';

                if (!file_exists($info)) {
                    $LNDBotao = '<form method="POST"  action="' . $install_plugin . '"><input  type="submit" name="btn-install"  class="cssbuttons-io-button" value="' . __('INSTALL', 'lnd-master-dev') . '"></form>';
                } else {
                    if (!in_array($valor->filepath, $active)) {
                        $LNDBotao = '<form method="POST"  action="' . $activate_path . '"><input  type="submit" name="btn-activate" class="btn_activeted" value="' . __('ACTIVATE', 'lnd-master-dev') . '"></form>';
                    } else {
                        if (array_key_exists($valor->filepath, $getPlugins) && version_compare($valor->version, $pluginInfo['Version'], '>')) {
                            $LNDBotao = '<form method="POST"  action="' . $install_plugin . '"><input  type="submit" name="btn-update" class="btn_update" value="' . __('UPDATE', 'lnd-master-dev') . '"></form>';
                        } else {
                            $LNDBotao = '<form method="POST"  action="' . $install_plugin . '"><input type="submit" name="activo" class="btn_active" disabled value="' . __('ACTIVE', 'lnd-master-dev') . '"></form>';
                        }
                    }
                }
            } else {
                $LNDBotao = '<a href="' . $activate_Plugins . '" class="btn btn-danger btn-sm">' . __('Ativar plugin', 'lnd-master-dev') . '</a>';
            }

            $data = date('d/m/Y', strtotime($valor->update_date));

            $items .= '<div class="col ">
                            <div class="p-0 m-0 card h-100">
                                <img src="' . $imgResponse . '" width="260" height="132" class="card-img-top" alt="' . $valor->item_name . '"></img>
                                    <div class="p-1 m-1  d-md-flex justify-content-md-center">' . $versionPlugin . '</div>
                                        <div class="card-body ">  
                                            <p class="m-1 text-muted">' . __('Updated: ', 'lnd-master-dev') . $data  . '</p>
                                            <p class="card-title"><strong>' . $new . " " . $valor->item_name . '</strong> </p>
                                            <p class="card-text">' . $description . ' - <a href="' . $valor->demo . '" target="_blank">' . __('Demonstration', 'lnd-master-dev') . '</a></p>
                                        </div>
                                    <div class="card-footer ">
                                        <div class="row ">
                                            <div class="gap-1 p-1 mx-auto d-grid col-6" id="lnd-buttons">' . $LNDBotao . '</div>
                                            <div class="p-1 col d-md-flex justify-content-md-end">' . $btn_downloads . '</div>
                                        </div>
                                    </div>
                            </div>
                        </div>  
                        ';
        endforeach;

        return $items;
    }

    public static function total_record($limit_per_page, $seach = null)
    {
        global $wpdb;
        $tabela_lnd = $wpdb->prefix . 'lnd_items_tbl';

        $sql_total = $wpdb->get_results("SELECT * FROM $tabela_lnd WHERE type = 'plugin' AND item_name LIKE '%$seach%'");

        $total_record = count($sql_total);

        $total_pages = ceil($total_record / $limit_per_page);

        return $total_pages;
    }

    // public static function lnd_get_actions_plugins()
    // {
    //     if (!empty($_POST['btn-install'])) {
    //         $lnd_plugin_name = sanitize_text_field($_GET['tab']);
    //         $lnd_plugin_version = sanitize_text_field($_GET['version']);
    //         $links = LND_Controller::control_links($lnd_plugin_name, $lnd_plugin_version);
    //         Plugin::installPlugin($links);
    //     }

    //     if (isset($_POST['btn-activate'])) {
    //         $plugin_path = $_GET['tab'];
    //         Plugin::activatePlugin($plugin_path);

    //         echo '<br><div class="container  align-self-center badge bg-primary text-light"  style="width: 100%,">
    //             <h3 class="text-light"> Plugin ativado com sucesso.</h3>
    //             <p class="fs-5">' . $_GET['plugin_name'] . '</p>
    //             </div>';
    //     }

    //     if (!empty($_POST['btn-update'])) {
    //         $lnd_plugin_name = sanitize_text_field($_GET['tab']);
    //         $lnd_plugin_version = sanitize_text_field($_GET['version']);
    //         $links = LND_Controller::control_links($lnd_plugin_name, $lnd_plugin_version);
    //         $path = (ABSPATH . 'wp-content/plugins/' . $lnd_plugin_name);
    //         $lnd_args = array('package' =>  $links, 'destination' => $path, 'clear_destination' => true, 'clear_working' => false, 'abort_if_destination_exists' => false, 'is_multi' => false, 'hook_extra' => false);
    //         Plugin::updatePlugin($lnd_args);
    //     }
    // }

    /**
     * @since 1.0.6
     */
    public static function modify_update_plugins($transient)
    {
        foreach (self::$plugin_update_info as $slug => $plugin) {
            $file_path = $plugin['file_path'];

            if (!isset($transient->response[$file_path])) {
                $transient->response[$file_path] = new \stdClass;
            }

            $transient->response[$file_path]->slug = $slug;
            $transient->response[$file_path]->plugin = $file_path;
            $transient->response[$file_path]->new_version = $plugin['version'];
            $transient->response[$file_path]->package = $plugin['source'];

            if (empty($transient->response[$file_path]->url) && !empty($plugin['external_url'])) {
                $transient->response[$file_path]->url = $plugin['external_url'];
            }
        }

        return $transient;
    }

    /**
     * @since 1.0.6
     */
    public static function disable_check_updater($transient)
    {
        foreach (self::$plugin_update_info as $fp => $plugin) {
            $file_path = $plugin['file_path'];

            if (isset($transient->response[$file_path])) {
                unset($transient->response[$file_path]);
            }
        }

        return $transient;
    }
}
