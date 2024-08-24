<?php

namespace App\Model\Dashboard;

use wpdb;

class TemplatesModel
{
    private static wpdb $wpdb;
    private static  TemplatesModel $instance;
    private static string $table_templates;
    private static string $table_categories;

    private function __construct()
    {
        self::init();
    }

    public static function init()
    {
        global $wpdb;
        self::$wpdb = $wpdb;
        self::$table_templates = self::$wpdb->prefix . 'lnd_master_templates_files';
        self::$table_categories = self::$wpdb->prefix . 'lnd_master_templates_categories';
    }

    public static function get_templates()
    {
        
        $table_templates = self::$table_templates;
        $table_categories = self::$table_categories;
        $query = "SELECT f.id, f.filename, f.category_id, f.img, f.created, c.name AS category_name
                FROM {$table_templates} f
                JOIN {$table_categories} c ON f.category_id = c.id
            ";
        $files = self::$wpdb->get_results($query);
        return $files;
    }

    public static function get_templates_categories()
    {
        $table_categories = self::$table_categories;
        $query = "SELECT * FROM {$table_categories}";
        $files = self::$wpdb->get_results($query);
        return $files;
    }

    /**
     * Inacia da class Singleton instance
     *
     * @return TemplatesModel
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
        }

        return self::$instance;
    }
}
