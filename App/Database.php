<?php

namespace App;

/**
 * Lnd_Plugin Class
 */
class Database
{
    protected static $db_version = '2.5';

    public static function init()
    {
        if (empty(get_option( 'lnd_library_get_options_select_category'))) {
            Database::lnd_library_insert_category();
        }
    }    

    public static function get_table()
    {
        global $wpdb;

        return $wpdb->prefix . "lnd_items_tbl";
    }

    // public static function create()
    // {
    //     global $wpdb;
    //     $charset_collate = '';
    //     if (!empty($wpdb->charset)) {
    //         $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    //     } else {
    //         $charset_collate = "DEFAULT CHARSET=utf8";
    //     }
    //     if (!empty($wpdb->collate)) {
    //         $charset_collate .= " COLLATE $wpdb->collate";
    //     }
    //     $tbl_sql = "CREATE TABLE " . self::get_table() . " (
    //       id int(12) NOT NULL ,
    //       status varchar(255) NOT NULL,
    //       item_name varchar(255) NOT NULL,
    //       type varchar(255) NOT NULL,
    //       is_free BOOLEAN NOT NULL DEFAULT FALSE,
    //       version varchar(255) NOT NULL,
    //       filepath varchar(255) NOT NULL,
    //       image varchar(255) NULL,
    //       description TEXT  NULL default '',
    //       demo TEXT  NULL default '',
    //       category TEXT  NULL default '',
    //       update_date DATETIME NOT NULL,
    //       data DATE NOT NULL,
    //       downloads TEXT  NULL default '',
    //       internal_downloads TEXT  NULL default '',
    //       instance TEXT  NULL default '',
    //       count int(12) NOT NULL,
        
    //       PRIMARY KEY  (id)
    //       )" . $charset_collate . ";";

    //     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    //     dbDelta($tbl_sql);


    //     update_option("lnd_db_version", self::$db_version);
    // }

    // public static function lnd_update_check() {
    //     if (!self::table_exists() || get_option('lnd_db_version') != self::$db_version) {
    //         self::create();
    //         return true;
    //     }
    //     return false;
    //   }

    public static function get_installed_plugins($installed_plugins)
    {
        global $wpdb;
        $tabela_lnd = $wpdb->prefix . 'lnd_items_tbl';
        $query = "SELECT *  FROM " . $tabela_lnd . " WHERE type = 'plugin' AND filepath IN ('" . implode("','", $installed_plugins) . "') AND status = 'publish'";
        return $wpdb->get_results($query);
    }

    public static function get_installed_theme($installed_theme)
    {
        global $wpdb;
        $tabela_lnd = $wpdb->prefix . 'lnd_items_tbl';
        $query = "SELECT *  FROM " . $tabela_lnd . " WHERE type = 'theme' AND item_name IN ('" . implode("','", str_replace("-", " ", $installed_theme)) . "')";
        return $wpdb->get_results($query);
    }

    public static function uninstall()
    {
        if (self::table_exists()) {
            global $wpdb;
            $table_name = self::get_table();
            $wpdb->query("DROP TABLE IF EXISTS $table_name");
        }
    }

    public static function table_exists()
    {
        global $wpdb;
        $table_name = self::get_table();
        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));
        if ($wpdb->get_var($query) != $table_name) {
            return false;
        } else {
            return true;
        }
    }

    public static function lnd_count_itens($type){
        global $wpdb;
        $table_name = Database::get_table();
        $queryNum = "SELECT COUNT(*) FROM " . $table_name . " ";
        $queryNum .= " WHERE type = '$type' AND status = 'publish'";
        $num = $wpdb->get_var($queryNum);
        
        return $num;
    }

    public static function lnd_return_itens($type)
    {
        global $wpdb;
        $table_name = self::get_table();
        $page = "";
        if (isset($_GET["paged"])) {
            $page = $_GET["paged"];
        } else {
            $page = 1;
        }

        $limit_per_page = 15;
        $offset = ($page - 1) * $limit_per_page;
        $query = "SELECT * FROM " . $table_name . " ";
        $queryNum = "SELECT COUNT(*) FROM " . $table_name . " ";
        $query .= " WHERE type = '$type' ";
        $queryNum .= " WHERE type = '$type' ";

        /**
         * Get search plugins
         */
        if (isset($_POST['seach_' . $type])) {

            $p = sanitize_text_field($_POST['seach_' . $type]);
            $query .= "AND (item_name LIKE '%$p%' OR filepath LIKE '%$p%' OR category LIKE '%$p%' AND status = 'publish' ) ";
            $queryNum .= "AND (item_name LIKE '%$p%' OR filepath LIKE '%$p%' OR category LIKE '%$p%' AND status = 'publish') ";
        }

        /**
         * Get plugins installed.
         */
        if (isset($_GET['installed-' . $type])) {
            $installed_plugins = array_keys(get_plugins());
            $installed_wp_themes = wp_get_themes();
            $installed_themes = array();
            foreach ($installed_wp_themes as $theme_slug => $theme_infos) {
                $installed_themes[] = $theme_slug;

                $installed_themes[] = $theme_slug . '/style.css';
            }
            $installed_products = array_merge($installed_themes, $installed_plugins);
            $query .= " AND filepath IN ('" . implode("','", $installed_products) . "') AND status = 'publish'";
            $queryNum .= " AND filepath IN ('" . implode("','", $installed_products) . "')AND status = 'publish'";
        }

        /**
         * Get all plugins
         */
        if (!isset($_GET['installed-' . $type]) && !isset($_POST['seach_' . $type]) && !isset($_POST['category'])) {
            $query .= " AND status = 'publish' ORDER BY update_date DESC LIMIT $offset, $limit_per_page";
            $queryNum .= "AND status = 'publish'";
        }
        $results = $wpdb->get_results($query);
        $num = $wpdb->get_var($queryNum);

        $total_pages = ceil($num / $limit_per_page);

        return array($results, $page, $total_pages);
    }

    public static function lnd_library_insert_category()
    {
        global $wpdb;
        $tabela_lnd = self::get_table();
        $list = $wpdb->get_results("SELECT distinct(category) FROM $tabela_lnd");
        $response = [];
        foreach ($list as $key => $value) {

            $key = explode(' | ', $value->category);
            $obj = array_merge($key);

            foreach ($obj as $key => $value) {
                $response[] = $value;
            }
        };

        sort($response, SORT_FLAG_CASE | SORT_NATURAL);
        $lnd_category = '';
        foreach (array_filter(array_unique($response)) as $category) {
            $lnd_category .= '<option value="' . $category . '">' . $category . '</option>';
        }
        update_option( 'lnd_library_get_options_select_category', array_filter(array_unique($response)) );

        return $lnd_category;
        
    }
}
