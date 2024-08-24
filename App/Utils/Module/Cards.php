<?php

namespace App\Utils\Module;

use App\Controller\Plugin;
use App\Controller\Theme;
use stdClass;

class Cards
{
    /**
     * Metodo responsavel por enviar info para cards de plugins e temas.
     *
     * @param stdClass $sql
     * @return void
     */
    public static function init($sql)
    {
        $items = [];
        $index = 0;
        foreach ($sql as $valor) :

            $data = new stdClass;

            $data->infoPathPlugin = (ABSPATH . 'wp-content/plugins/' . $valor->filepath);
            $data->infoPathTheme = (ABSPATH . 'wp-content/themes/' . $valor->filepath);
            $LNDPath = explode('/', $valor->filepath);
            $data->path = $LNDPath[0];

            if (file_exists($data->infoPathPlugin) || $data->path) {
                // $pluginInfo = get_plugin_data($data->infoPathPlugin);
                // $data->pluginInfo = get_plugin_data($data->infoPathPlugin);
                // $data->pluginInfo = get_plugins();
                $themeInfo = wp_get_theme($data->path);
            }

            $data->themeInfo = $themeInfo;
            if ($valor->type == 'plugin') {
                $data->version = Plugin::get_version_compare($data->infoPathPlugin, $valor->version);
            }

            if ($valor->type == 'theme') {
                $data->version = Theme::get_version_compare($themeInfo['Version'], $valor->version);
            }

            $data->new = Plugin::get_new_plugins($valor->data);

            $data->description = self::get_clean_description($valor->description);

            $data->img = plugins_url() . '/lnd-master-dev/assets/images/lnd-downloads.jpg';
            if (!empty($valor->image)) {
                $data->img = $valor->image;
            }

            $result = Self::set_data($valor, $data);

            $items[$index] = $result;

            $index++;

        endforeach;
        return $items;
    }

    /**
     * Renderiza dados de envio
     *
     * @param sql $sql
     * @param processedSql $data
     * @return stdClass
     */
    private static function set_data($sql, $data)
    {
        $result = new stdClass;
        $result->id           = $sql->id;
        $result->path         = $data->path;
        $result->version      = $sql->version;
        $result->itens        = $sql->is_free;
        $result->type         = $sql->type;
        $result->filepath     = $sql->filepath;
        $result->license      = 'admin.php?page=lnd-master-dev_license';
        $result->code         = get_option("lnd_master_dev_key");
        $result->img          = $data->img;
        $result->versionCard  = $data->version;
        $result->update_Date  = $sql->update_date;
        $result->new          = $data->new;
        $result->item_name    =  $sql->item_name;
        $result->description  =  $data->description;
        $result->demostration =  $sql->demo;        
        $result->themeInfo   =  $data->themeInfo;
        // $result->chave_key    =  $sql->chave_key;
        // $result->buttons      = Self::lnd_render_buttons($result, $sql, $data->infoPathPlugin, $data->infoPathTheme);
        $result->buttons      = Self::lnd_render_buttons($result, $sql, $data->infoPathPlugin, $data->infoPathTheme);

        return $result;
    }

    /**
     * Clear description html
     *
     * @param string $description
     * @return void
     */
    private static function get_clean_description($description)
    {
        $description = strip_tags($description); // Remove tags HTML e PHP
        $max_length = 50;
        if (strlen($description) > $max_length) {
            $description = substr($description, 0, $max_length) . '...';
        }
        return $description;
    }

    /**
     * Metodo responsavel por renderizar tipo de botao -> install, update, activate, activated
     *
     * @param stdclass $data
     * @param stdclass $sql
     * @param string $infoPathPlugin
     * @param string $infoPathTheme
     * @return void
     */
    public static function lnd_render_buttons($data, $sql, $infoPathPlugin, $infoPathTheme)
    {
        $active  = get_option('active_plugins', array());
        $theme_active  = get_option('template');
        $getPlugins = get_plugins();
        if (file_exists($infoPathPlugin)) {
            $pluginInfo = get_plugin_data($infoPathPlugin);
        }
        $download_nonce  = wp_create_nonce("action-download-now");
        $download_plugin = sprintf('admin.php?action=download-plugin&plugin_file=%s&plugin_name=%s&version=%s&lnd_itens=%s&_wpnonce=%s', $data->path, $data->path, $data->version, $data->itens, $download_nonce);
        $action_plugin_download = admin_url($download_plugin);

        if (!empty($data->code) || isset($data->itens) &&  $data->itens == '1') {

            // $buttonDownload = Self::lnd_btn_downlaods($action_plugin_download);
            $buttonDownload = $action_plugin_download;

            if (!file_exists($infoPathPlugin) && !file_exists($infoPathTheme)) {

                // $buttonCard = Self::lnd_btn_install($data);
                $buttonCard = 'install';
            } else {
                if (!in_array($sql->filepath, $active) && ($data->path !== $theme_active)) {

                    // $buttonCard = Self::lnd_btn_activate($data);
                    $buttonCard = 'activate';
                } else {
                    if (

                        array_key_exists($sql->filepath, $getPlugins)
                        && version_compare($sql->version, $pluginInfo['Version'], '>')
                        || $sql->item_name == $data->themeInfo['Name']
                        && version_compare($sql->version, $data->themeInfo['Version'], '>')
                    ) {

                        // $buttonCard = Self::lnd_btn_update($data);
                        $buttonCard = 'update';
                        // $buttonCard =  [$data];
                    } else {

                        $buttonCard = 'activated';
                    }
                }
            }
        } else {

            $buttonCard = 'license';
        }

        $buttons = new stdClass;
        $buttons->buttonCard = $buttonCard;
        $buttons->buttonDownload = $buttonDownload;

        return $buttons;
    }

    /**
     * Metodo resposavel por criar link botão de downloads
     *
     * @param string $link
     * @return void
     */
    private static function lnd_btn_downlaods($link)
    {
        $btn = sprintf("<a class='card-action' href='%s'><i class='fa-solid fa-download'></i></a>", $link);
        return $btn;
    }

    /**
     * Metodo resposavel por criar link botão de install
     *
     * @param stdClass $obj
     * @return void
     */
    private static function lnd_btn_install(stdClass $obj)
    {
        $btn = sprintf('<button id = "lnd-install" class="text-center card-button-install"  data-lnd_name="%s" data-lnd_version ="%s" data-lnd_itens="%s" data-lnd_type="%s" >' . __('Install', 'lnd-auto-update') . '</button>', $obj->path, $obj->version, $obj->itens, $obj->type);
        return $btn;
    }

    /**
     * Metodo resposavel por criar link botão de activate
     *
     * @param stdClass $obj
     * @return void
     */
    private static function lnd_btn_activate(stdClass $obj)
    {
        $btn = sprintf('<button name=lnd-activate id="lnd-btn-activate" class="card-button-activate" data-lnd_filepath="%s" data-lnd_name="%s" data-lnd_itens="%s" data-lnd_type="%s">' . __('Activate', 'lnd-auto-update') . '</button>', $obj->filepath, $obj->path, $obj->itens, $obj->type);
        return $btn;
    }

    /**
     * Metodo resposavel por criar link botão de Update
     *
     * @param stdClass $obj
     * @return void
     */
    private static function lnd_btn_update(stdClass $obj)
    {
        $btn = sprintf('<button name=lnd-update id="lnd-btn-update" class="card-button-update" data-lnd_name="%s" data-lnd_version="%s" data-lnd_itens="%s" data-lnd_type="%s">' . __('Update', 'lnd-auto-update') . '</button>', $obj->path, $obj->version, $obj->itens, $obj->type);
        return $btn;
    }

    /**
     * Metodo resposavel por criar link botão de activated
     *
     * @return void
     */
    private static function lnd_btn_activated()
    {
        $btn = '<button type="submit" name="activo" class="card-button-active" disabled >' . __('activated', 'lnd-auto-update') . '</button>';
        return $btn;
    }

    /**
     * Metodo resposavel por criar link botão de license
     *
     * @param stdClass $obj
     * @return void
     */
    private static function lnd_btn_license(stdClass $obj)
    {
        $btn = $btn = sprintf('<a href="%s" class="card-button-activate-key">' . __('Activate license', 'lnd-auto-update') . '</a>', $obj->license);
        return $btn;
    }
}
