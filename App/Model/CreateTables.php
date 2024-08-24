<?php

namespace App\Model;

use App\Request\CatalogManager;
use Exception;

class CreateTables
{
    private $db_version = '1.6';
    private $option_name = 'lnd_master_dev_update_catalogo_db_version';
    private $wpdb;
    private $charset_collate;
    private $tables = [];

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();

        $this->define_tables();

        add_action('plugins_loaded', [$this, 'check_for_db_updates']);
    }

    private function define_tables()
    {
        $prefix = $this->wpdb->prefix;

        $this->tables = [
            'templates_categories' => [
                'name' => "{$prefix}lnd_master_templates_categories",
                'sql' => "CREATE TABLE {$prefix}lnd_master_templates_categories (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    parent_id mediumint(9) DEFAULT NULL,
                    created timestamp NOT NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (id)
                ) {$this->charset_collate};"
            ],
            'catalog_category' => [
                'name' => "{$prefix}lnd_master_catalog_category",
                'sql' => "CREATE TABLE {$prefix}lnd_master_catalog_category (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    parent_id mediumint(9) DEFAULT NULL,
                    created timestamp NOT NULL DEFAULT current_timestamp(),
                    update_date timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (id)
                ) {$this->charset_collate};"
            ],
            'templates_files' => [
                'name' => "{$prefix}lnd_master_templates_files",
                'sql' => "CREATE TABLE {$prefix}lnd_master_templates_files (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    category_id mediumint(9) NOT NULL,
                    img varchar(255) NOT NULL,
                    filename varchar(255) NOT NULL,
                    created timestamp NOT NULL DEFAULT current_timestamp(),
                    update_date timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    count bigint(20) NOT NULL DEFAULT 0,
                    PRIMARY KEY (id)
                ) {$this->charset_collate};"
            ],
            'catalog' => [
                'name' => "{$prefix}lnd_master_catalog",
                'sql' => "CREATE TABLE {$prefix}lnd_master_catalog (
                    id mediumint(9) NOT NULL,
                    status varchar(255) NOT NULL,
                    item_name varchar(255) NOT NULL,
                    type varchar(255) NOT NULL,
                    is_free BOOLEAN NOT NULL DEFAULT FALSE,
                    version varchar(255) NOT NULL,
                    filepath varchar(255) NOT NULL,
                    image varchar(255) NULL,
                    description TEXT NULL DEFAULT '',
                    demo varchar(255) DEFAULT NULL,
                    update_date DATETIME NOT NULL,
                    data DATE NOT NULL,
                    downloads TEXT NULL DEFAULT '',
                    internal_downloads TEXT NULL DEFAULT '',
                    instance bigint(20) DEFAULT NULL,
                    count bigint(20) NOT NULL DEFAULT 0,
                    category_id  mediumint(9)  NOT NULL default 0,
                    PRIMARY KEY (id)
                ) {$this->charset_collate};"
            ],
            // 'catalog_categories' => [
            //     'name' => "{$prefix}lnd_master_catalog_categories",
            //     'sql' => "CREATE TABLE {$prefix}lnd_master_catalog_categories (
            //         id mediumint(9) NOT NULL AUTO_INCREMENT,
            //         catalog_id mediumint(9) NOT NULL,
            //         category_id mediumint(9) NOT NULL,
            //         PRIMARY KEY (catalog_id, category_id),
            //         FOREIGN KEY (catalog_id) REFERENCES {$prefix}lnd_master_catalog(id) ON DELETE CASCADE,
            //         FOREIGN KEY (category_id) REFERENCES {$prefix}lnd_master_catalog_category(id) ON DELETE CASCADE
            //     ) {$this->charset_collate};"
            // ],
            'catalog_old' => [
                'name' => "{$prefix}lnd_items_tbl",
                'sql' => "CREATE TABLE {$prefix}lnd_items_tbl (
                    id int(12) NOT NULL ,
                    status varchar(255) NOT NULL,
                    item_name varchar(255) NOT NULL,
                    type varchar(255) NOT NULL,
                    is_free BOOLEAN NOT NULL DEFAULT FALSE,
                    version varchar(255) NOT NULL,
                    filepath varchar(255) NOT NULL,
                    image varchar(255) NULL,
                    description TEXT  NULL default '',
                    demo TEXT  NULL default '',
                    category TEXT  NULL default '',
                    update_date DATETIME NOT NULL,
                    data DATE NOT NULL,
                    downloads TEXT  NULL default '',
                    internal_downloads TEXT  NULL default '',
                    instance TEXT  NULL default '',
                    count int(12) NOT NULL,
                    PRIMARY KEY (id)
                ) {$this->charset_collate};"
            ],

        ];
    }

    public function install()
    {
        try {
            $this->create_tables();
            $this->add_category_column();
            $this->add_foreign_keys();
            update_option($this->option_name, $this->db_version);
            // CatalogManager::get_catalogo();
            // CatalogManager::lnd_insert_update_catalog();
            $this->log_message("Database tables created successfully. Version: {$this->db_version}");
        } catch (Exception $e) {
            $this->log_message("Error creating database tables: " . $e->getMessage(), 'error');
        }
    }

    public function check_for_db_updates()
    {
        $installed_version = get_option($this->option_name);

        if ($installed_version != $this->db_version) {
            $this->install();
        }
    }

    private function create_tables()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($this->tables as $table) {
            dbDelta($table['sql']);
            $this->verify_table_creation($table['name']);
        }
    }

    private function verify_table_creation($table_name)
    {
        if ($this->wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            throw new Exception("Failed to create table: $table_name");
        }
    }

    private function log_message($message, $type = 'info')
    {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            error_log("CreateTables $type: $message");
        }
    }

    public function get_db_version()
    {
        return $this->db_version;
    }

    public function get_table_names()
    {
        return array_column($this->tables, 'name');
    }

    public function add_category_column()
    {
        // global $wpdb;
        // $table_name = $wpdb->prefix . 'lnd_master_catalog';
        // $category_table = $wpdb->prefix . 'lnd_master_catalog_category';

        // // Verifica se a coluna já existe
        // $column = $wpdb->get_results($wpdb->prepare(
        //     "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'category_id'",
        //     DB_NAME,
        //     $table_name
        // ));

        // if (empty($column)) {
        //     // Adiciona a coluna category_id
        //     $wpdb->query("ALTER TABLE $table_name ADD COLUMN category_id mediumint(9) NULL DEFAULT NULL");

        //     // Adiciona a chave estrangeira
        //     $wpdb->query("ALTER TABLE $table_name 
        //               ADD CONSTRAINT fk_category_id 
        //               FOREIGN KEY (category_id) 
        //               REFERENCES $category_table(id) 
        //               ON DELETE SET NULL");

        //     $this->log_message("Added category_id column and foreign key constraint to $table_name");
        // } else {
        //     $this->log_message("category_id column already exists in $table_name");
        // }

        // // Adiciona outras chaves estrangeiras
        // $this->add_foreign_keys();
    }

    private function add_foreign_keys()
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $foreign_keys = [
            "{$prefix}lnd_master_templates_categories" => [
                'column' => 'parent_id',
                'references' => "{$prefix}lnd_master_templates_categories(id)"
            ],
            "{$prefix}lnd_master_catalog_category" => [
                'column' => 'parent_id',
                'references' => "{$prefix}lnd_master_catalog_category(id)"
            ],
            "{$prefix}lnd_master_templates_files" => [
                'column' => 'category_id',
                'references' => "{$prefix}lnd_master_templates_categories(id)"
            ],
            "{$prefix}lnd_master_catalog" => [
                'column' => 'category_id',
                'references' => "{$prefix}lnd_master_catalog_category(id)"
            ],
            // "{$prefix}lnd_master_catalog_categories" => [
            //     'column' => 'catalog_id',
            //     'references' => "{$prefix}lnd_master_catalog(id)"
            // ],
            // "{$prefix}lnd_master_catalog_categories" => [
            //     'column' => 'category_id',
            //     'references' => "{$prefix}lnd_master_catalog_category(id)"
            // ]
        ];

        foreach ($foreign_keys as $table => $fk) {
            $wpdb->query("ALTER TABLE $table 
                      ADD CONSTRAINT fk_{$fk['column']}
                      FOREIGN KEY ({$fk['column']}) 
                      REFERENCES {$fk['references']}
                      ON DELETE CASCADE");
        }
    }
}
