<?php

namespace App\Controller\Updates;

use App\Request\CatalogManager;
use App\Request\Downloads;

class Updates
{
    private const OPTION_PLUGINS_DATA = '_lnd_plugins_datajson';
    private const OPTION_THEMES_DATA = '_lnd_themes_datajson';
    private const OPTION_PLUGINS_REMOVE = '_lnd_plugins_remove_datajson';

    public static function init()
    {
        // add_action('lnd_update_cron', array(__CLASS__, 'get_catalogo'), 9);

        add_action('lnd_update_cron', array(CatalogManager::class, 'lnd_insert_update_catalog'));

        add_filter('site_transient_update_plugins', array(__CLASS__, 'lnd_get_plugin_update'), 99);

        add_filter('site_transient_update_themes', array(__CLASS__, 'lnd_get_theme_update'), 99);

        add_action('init', array(__CLASS__, 'lnd_plugin_data'));

        add_action('init', array(__CLASS__, 'lnd_theme_data'));

        add_action('init', array(__CLASS__, 'lnd_plugin_data_remove'));
    }

    public static function lnd_get_plugin_remove_info()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins');
        $plugins = [];

        foreach ($all_plugins as $key => $value) {
            $is_active = in_array($key, $active_plugins);
            $slug = explode('/', $key)[0];
            if (!in_array($slug, ['lnd-auto-updates', 'lnd-update-catalogo'])) {
                $plugins[$key] = [
                    'name' => $value['Name'],
                    'path' => $key,
                    'slug' => $slug,
                    'lnd_slug' => $key,
                    'version' => $value['Version'],
                    'active' => $is_active,
                ];
            }
        }

        return $plugins;
    }

    public static function lnd_check_plugin_remove_update($packages, $plugins_info)
    {
        $response = [];
        foreach ($packages as $plugin) {
            if ($plugin->type === 'plugin') {
                $lnd_version = $plugin->version;
                $lnd_path = $plugin->filepath;
                foreach ($plugins_info as $info) {
                    if ($info['lnd_slug'] == $lnd_path && $info['active'] && version_compare($lnd_version, $info['version'], '>=')) {
                        $response[] = $info['path'];
                    }
                }
            }
        }

        update_option(self::OPTION_PLUGINS_REMOVE, $response);
        return $response;
    }

    public static function lnd_plugin_data_remove()
    {
        $packages = CatalogManager::get_catalogo();

        if (is_wp_error($packages) || (isset($packages['is_request_error']) && $packages['is_request_error'] == true)) {
            return;
        }

        $plugins_info = self::lnd_get_plugin_remove_info();

        if ($plugins_info) {
            return self::lnd_check_plugin_remove_update($packages, $plugins_info);
        }
    }

    public static function lnd_get_plugin_info()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins');
        $plugins = [];

        foreach ($all_plugins as $key => $value) {
            $is_active = in_array($key, $active_plugins);
            $slug = explode('/', $key)[0];
            if ($is_active && $slug != 'lnd-auto-updates') {
                $plugins[$key] = [
                    'name' => $value['Name'],
                    'path' => $key,
                    'slug' => $slug,
                    'lnd_slug' => $key,
                    'version' => $value['Version'],
                    'active' => $is_active,
                ];
            }
        }

        return $plugins;
    }

    public static function lnd_check_plugin_update($packages, $plugins_info)
    {
        $response = [];
        $current_date = date('Y-m-d');

        foreach ($packages as $plugin) {
            if ($plugin->type !== 'plugin') continue;

            $lnd_path = $plugin->filepath;
            $lnd_version = $plugin->version;
            $lnd_name = explode('/', $lnd_path)[0];
            $lnd_file = Downloads::control_links($lnd_name, $lnd_version, $plugin->is_free);

            foreach ($plugins_info as $info) {
                if ($info['lnd_slug'] === $lnd_path && version_compare($info['version'], $lnd_version, '<')) {
                    $response[$info['path']] = [
                        'lnd_date' => $current_date,
                        'lnd_path' => $info['path'],
                        'lnd_version' => $lnd_version,
                        'lnd_slug' => $info['slug'],
                        'lnd_file' => $lnd_file,
                        'lnd_free' => $plugin->is_free,
                        'lnd_status' => $plugin->status
                    ];

                    set_transient('lnd_upgrade_' . $info['slug'], $response[$info['path']]);
                }
            }
        }

        update_option(self::OPTION_PLUGINS_DATA, wp_json_encode($response));
        return $response;
    }

    public static function lnd_plugin_data()
    {
        $packages = CatalogManager::get_catalogo();

        if (is_wp_error($packages) || (isset($packages['is_request_error']) && $packages['is_request_error'] == true)) {
            return;
        }

        $plugins_info = self::lnd_get_plugin_info();

        if ($plugins_info) {
            self::lnd_check_plugin_update($packages, $plugins_info);
        }
    }

    public static function lnd_get_plugin_update($transient)
    {
        if (!is_object($transient) || !isset($transient->response)) {
            return $transient;
        }

        $plugins_info = json_decode(get_option(self::OPTION_PLUGINS_DATA), true);
        if (empty($plugins_info)) {
            return $transient;
        }

        foreach ($plugins_info as $path => $info) {
            if ($info['lnd_status'] === 'draft') continue;

            $plugin_file = WP_PLUGIN_DIR . '/' . $path;
            if (!file_exists($plugin_file)) continue;

            $plugin_data = get_plugin_data($plugin_file);
            if (version_compare($plugin_data['Version'], $info['lnd_version'], '<')) {
                $transient->response[$path] = (object) [
                    'slug' => $info['lnd_slug'],
                    'new_version' => $info['lnd_version'],
                    'package' => $info['lnd_file'],
                    'plugin' => $path
                ];
            }
        }

        return $transient;
    }

    public static function lnd_get_theme_info()
    {
        $themes = [];
        $get_themes = wp_get_themes();

        foreach ($get_themes as $theme) {
            $stylesheet = $theme->get_stylesheet();
            $themes[$stylesheet] = [
                'Name' => $theme->get('Name'),
                'ThemeURI' => $theme->get('ThemeURI'),
                'Slug' => $stylesheet,
                'LndSlug' => $stylesheet,
                'Description' => $theme->get('Description'),
                'Author' => $theme->get('Author'),
                'AuthorURI' => $theme->get('AuthorURI'),
                'Version' => $theme->get('Version'),
                'Template' => $theme->get('Template'),
                'Status' => $theme->get('Status'),
                'Tags' => $theme->get('Tags'),
                'TextDomain' => $theme->get('TextDomain'),
                'DomainPath' => $theme->get('DomainPath')
            ];
        }

        return $themes;
    }

    public static function lnd_check_theme_update($packages, $themes_info)
    {
        $response = [];
        $current_date = date('Y-m-d');

        foreach ($packages as $theme) {
            if ($theme->type !== 'theme') continue;

            $lnd_path = $theme->filepath;
            $lnd_name = explode('/', $lnd_path)[0];
            $lnd_version = $theme->version;
            $lnd_file = Downloads::control_links($lnd_name, $lnd_version, $theme->is_free);

            foreach ($themes_info as $info) {
                if ($info['LndSlug'] === $lnd_name && version_compare($info['Version'], $lnd_version, '<')) {
                    $response[$info['Slug']] = [
                        'lnd_date' => $current_date,
                        'lnd_path' => $info['Slug'],
                        'lnd_version' => $lnd_version,
                        'lnd_slug' => $info['Slug'],
                        'lnd_file' => $lnd_file
                    ];

                    set_transient('lnd_upgrade_' . $info['Slug'], $response[$info['Slug']]);
                }
            }
        }

        update_option(self::OPTION_THEMES_DATA, wp_json_encode($response));
        return $response;
    }

    public static function lnd_theme_data()
    {

        $packages = CatalogManager::get_catalogo();

        if (is_wp_error($packages) || (isset($packages['is_request_error']) && $packages['is_request_error'] == true)) {
            return;
        }

        $themes_info = self::lnd_get_theme_info();

        if ($themes_info) {
            self::lnd_check_theme_update($packages, $themes_info);
        }
    }

    public static function lnd_get_theme_update($transient)
    {
        if (!is_object($transient)) {
            return $transient;
        }

        $themes_info = json_decode(get_option(self::OPTION_THEMES_DATA), true);
        if (empty($themes_info)) {
            return $transient;
        }

        foreach ($themes_info as $path => $info) {
            $transient->response[$path] = [
                'new_version' => $info['lnd_version'],
                'package' => $info['lnd_file']
            ];
        }

        return $transient;
    }
}
