<?php

namespace App\View;

use App\Controller\Plugin;
use App\Controller\Theme;

class LND_Get_Itens
{
    public static function lnd_get_plugins($sql)
    {
        $code = get_option("lnd_master_dev_key");
        $email = get_option("LNDMasterDevPlugin_lic_email");
        $active  = get_option('active_plugins', array());
        $getPlugins = get_plugins();
        $theme_active  = get_option('template');
        $itens = '';
        foreach ($sql as $valor) :

            $info = (ABSPATH . 'wp-content/plugins/' . $valor->filepath);
            $lnd_info_theme = (ABSPATH . 'wp-content/themes/' . $valor->filepath);
            $LNDPath = explode('/', $valor->filepath);
            $LNDPaths = $LNDPath[0];
            $download_now = $LNDPaths;
            $LND_Version = $valor->version;
            $data_inicial = $valor->data;
            $filepath = $valor->filepath;
            $lnd_type = $valor->type;
            $lnd_itens = $valor->is_free;

            if (file_exists($info) || $LNDPaths) {
                $pluginInfo = get_plugin_data($info);
                $themeInfo = wp_get_theme($LNDPaths);
            }

            if ($valor->type == 'plugin') {
                $versionPlugin = Plugin::get_version_compare($info, $valor->version);
            }

            if ($valor->type == 'theme') {
                $versionPlugin = Theme::get_version_compare($themeInfo['Version'], $LND_Version);
            }

            $new = Plugin::get_new_plugins($data_inicial);
            $download_nonce  = wp_create_nonce("action-download-now");
            $activate_Plugins = 'admin.php?page=lnd-master-dev_license';
            $download_plugin = 'admin.php?action=download-plugin&plugin_file=' . $download_now . '&plugin_name=' . $download_now . '&version=' . $LND_Version . '&lnd_itens=' . $valor->is_free . '&_wpnonce=' . $download_nonce;
            $action_plugin_download = admin_url($download_plugin);

            $description = '';
            if (strlen($valor->description) > 50) {
                $description = substr($valor->description, 0, 50) . '...';
            } else {
                $description = $valor->description;
            }

            $imgResponse = plugins_url() . '/lnd-master-dev/assets/images/lnd-downloads.jpg';
            if (!empty($valor->image)) {
                $imgResponse = $valor->image;
            }

            $button      = '';
            $btn_downloads = '';

            if (!empty($code) && !empty($email) || isset($lnd_itens) && $lnd_itens == '1') {
                $btn_downloads = '<a class="card-action" href="' . esc_url($action_plugin_download) . '"><i class="fa-solid fa-download"></i></a>';

                if (!file_exists($info) && !file_exists($lnd_info_theme)) {
                    $button = '<button id = "lnd-install" class="card-button-install text-center"  data-lnd_name=' . $LNDPaths . ' data-lnd_version =' . $LND_Version . ' data-lnd_itens=' . $lnd_itens . ' data-lnd_type=' . $lnd_type . '>' . __('Install', 'lnd-master-dev') . '</button>';
                } else {
                    if (!in_array($valor->filepath, $active) && ($LNDPaths != $theme_active)) {
                        $button = '<button type="submit" name=lnd-activate id="lnd-btn-activate" class="card-button-activate" data-lnd_filepath=' . $filepath . ' data-lnd_name=' . $LNDPaths . ' data-lnd_itens=' . $lnd_itens . ' data-lnd_type=' . $lnd_type . '>' . __('Activate', 'lnd-master-dev') . '</button>';
                    } else {
                        if (array_key_exists($valor->filepath, $getPlugins) && version_compare($valor->version, $pluginInfo['Version'], '>') || $valor->item_name == $themeInfo['Name'] && version_compare($valor->version, $themeInfo['Version'], '>')) {
                            $button = '<button type="submit" name=lnd-update id="lnd-btn-update" class="card-button-update" data-lnd_name=' . $LNDPaths . ' data-lnd_version=' . $LND_Version . ' data-lnd_itens=' . $lnd_itens . ' data-lnd_type=' . $lnd_type . '>' . __('Update', 'lnd-master-dev') . '</button>';
                        } else {
                            $button = '<button type="submit" name="activo" class="card-button-active" disabled >' . __('activated', 'lnd-master-dev') . '</button>';
                        }
                    }
                }
            } else {
                $button = '<a href="' . $activate_Plugins . '" class="card-button-activate-key">' . __('Activate license', 'lnd-master-dev') . '</a>';
            }

            $args = array(
                'img' => $imgResponse, 'version' => $versionPlugin, 'update_date' => $valor->update_date, 'new' => $new, 'item_name' => $valor->item_name, 'description' => $description, 'demo' => $valor->demo, 'button' => $button, 'btn_downloads' => $btn_downloads,  'path' => $LNDPaths, 'type' => $lnd_type
            );

            $itens .=  LND_View_Card::lnd_view_card($args);

        endforeach;

        return $itens;
    }

    public static function lnd_get_plugins_plataforma($sql)
    {
        $nonce = wp_create_nonce( 'lnd-master-downloads' );

        $itens = '';
        foreach ($sql as $valor) :

            $LNDPath = explode('/', $valor->filepath);
            $LNDPaths = $LNDPath[0];
            $data_inicial = $valor->data;
            $url_downloads = $valor->downloads;
            $lnd_type = $valor->type;
            $lnd_itens = $valor->is_free;
            $access = trim($valor->instance);
            $versionPlugin = '<spam class="badge border border-white bg-light text-success rounded-1" style="border-radius: 3px;">' . __('Version: ', 'lnd-master-dev') . $valor->version . '</spam>';
            $new = Plugin::get_new_plugins($data_inicial);
            $buy = 'https://lojanegociosdigital.com.br/lnd-auto-update/';
            $description = '';
            if (strlen($valor->description) > 50) {
                $description = substr($valor->description, 0, 50) . '...';
            } else {
                $description = $valor->description;
            }

            $button = '';
            if ( is_user_logged_in() ) {

                $instance = Self::lnd_response_instance();

                if ($lnd_itens === '1' ) {

                    $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';
                    
                } else {

                    if ( in_array($access, array('basic', 'gold', 'profissional', 'diamond', 'lnd-library', '', null)) && $instance['subscriber'] === 'active'   ) {

                        $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';

                    } 
                    elseif (in_array($access, array('basic', 'gold', 'profissional', 'diamond', 'lnd-library', '', null)) && array_search('lnd-library', $instance['members']) !== false) {
                        
                        $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';

                    } 
                    elseif ( in_array($access, array('basic', 'gold', 'profissional', 'diamond') ) && array_search('diamond', $instance['members'] ) !== false   ) {

                        $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';

                    } 
                    elseif ( in_array($access, array('basic', 'gold', 'profissional') ) && array_search('mega-pack-profissional', $instance['members'] ) !== false   ) {

                        $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';

                    } 
                    elseif ( in_array($access, array('basic', 'gold') ) && array_search('gold', $instance['members'] ) !== false   ) {

                        $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';

                    } 
                    elseif ( in_array($access, array('basic') ) && array_search('basic', $instance['members'] ) !== false   ) {

                        $button = '<a href="' . $url_downloads . '" class="card-button-downloads" target="_blank">' . __('Baixar Agora', 'lnd-master-dev') . '</a>';

                    } 
                    else {

                        $button = '<a href="'.$buy.'" class="card-button-plans" target="_blank">' . __('Comprar Acesso', 'lnd-master-dev') . '</a>';
                    
                    }
                }
            } else {
                $button = '<a href="#" class="card-button-myaccount" target="_blank">' . __('Minha Conta', 'lnd-master-dev') . '</a>';
            }

            $imgResponse = plugins_url() . '/lnd-master-dev/assets/images/lnd-downloads.jpg';
            if (!empty($valor->image)) {
                $imgResponse = $valor->image;
            }


            $args = array(
                'img' => $imgResponse, 'version' => $versionPlugin, 'update_date' => $valor->update_date, 'new' => $new, 'item_name' => $valor->item_name, 'description' => $description, 'demo' => $valor->demo, 'button' => $button,  'path' => $LNDPaths, 'type' => $lnd_type
            );

            $itens .=  LND_View_Card::lnd_view_card_plataforma($args);

        endforeach;

        return $itens;
    }

    public static function lnd_response_instance()
    {
        $response = [];
        $lnd_area_membros = [];
        $lnd_assinantes = '';

        if (!is_user_logged_in()) {
            $lnd_area_membros = null;
            $lnd_assinantes = null;
        } else {

            $user_id = get_current_user_id();

            $args = array('status' => array('active'));

            /**
             * Woo membershipping
             */
            if (function_exists('wc_memberships_get_user_memberships')) {
                $active_memberships = wc_memberships_get_user_memberships($user_id, $args);
                if ($active_memberships !== []) {
                    foreach ($active_memberships as  $value) {
                        if ($value) {
                            $lnd_area_membros[] = $value->plan->slug;
                        }
                    }
                } else {
                    $lnd_area_membros = [];
                }
            }

            /**
             * Woo subscription
             */
            if (function_exists('wc_memberships_get_user_memberships')) {
                $subscriptions = wcs_get_users_subscriptions($user_id);

                if ($subscriptions !== []) {

                    foreach ($subscriptions as $subscription) {
                        if ($subscription->has_status('active')) {

                            $product_name = $subscription->get_base_data();

                            $order = wc_get_order($product_name['parent_id']);

                            $items = $order->get_items();

                            foreach ($items as $item) {
                                $product_name = $item->get_name();

                                if (stripos($product_name, 'lnd library') !== false) {

                                    $lnd_assinantes = 'active';
                                } else {
                                    $lnd_assinantes = null;
                                }
                            }
                        } else {
                            $lnd_assinantes = null;
                        }
                    }
                } else {
                    $lnd_assinantes = null;
                }
            }

            $response = array('members' => $lnd_area_membros, 'subscriber' => $lnd_assinantes);
            // $response = array('members' => ['lnd-library'], 'subscriber' => 'active');

        }

        return $response;
    }
}
