<?php

// namespace App\Model;

// class CreateTables
// {
//     private $db_version = '1.1';

//     public function __construct()
//     {
//         add_action('plugin_loaded', array($this, 'check_for_db_updates'));
//     }

//     public function install()
//     {
//         $this->create_tables();
//         update_option('lnd_master_dev_update_catalogo_db_version', $this->db_version);
//     }

//     public function check_for_db_updates()
//     {
//         $installed_version = get_option('lnd_master_dev_update_catalogo_db_version');

//         if ($installed_version != $this->db_version) {
//             $this->install();
//         }
//     }

//     public function create_tables()
//     {
//         global $wpdb;

//         $charset_collate = $wpdb->get_charset_collate();

//         $sql1 = "CREATE TABLE {$wpdb->prefix}lnd_master_templates_categories (
//             id mediumint(9) NOT NULL,
//             name varchar(255) NOT NULL,
//             parent_id mediumint(9) DEFAULT NULL,
//             PRIMARY KEY (id),
//             FOREIGN KEY (parent_id) REFERENCES {$wpdb->prefix}lnd_master_templates_categories(id) ON DELETE CASCADE
//         ) $charset_collate;";

//         $sql2 = "CREATE TABLE {$wpdb->prefix}lnd_master_catalog_category (
//             id mediumint(9) NOT NULL,
//             name varchar(255) NOT NULL,
//             parent_id mediumint(9) DEFAULT NULL,
//             created timestamp NOT NULL DEFAULT current_timestamp(),
//             update_date timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
//             PRIMARY KEY (id),
//             FOREIGN KEY (parent_id) REFERENCES {$wpdb->prefix}lnd_master_catalog_category(id) ON DELETE CASCADE
//         ) $charset_collate;";

//         $sql3 = "CREATE TABLE {$wpdb->prefix}lnd_master_templates_files (
//             id mediumint(9) NOT NULL,
//             category_id mediumint(9) NOT NULL,
//             img varchar(255) NOT NULL,
//             filename varchar(255) NOT NULL,
//             created timestamp NOT NULL DEFAULT current_timestamp(),
//             update_date timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
//             count bigint(20) NOT NULL DEFAULT 0,
//             PRIMARY KEY (id),
//             FOREIGN KEY (category_id) REFERENCES {$wpdb->prefix}lnd_master_templates_categories(id) ON DELETE CASCADE
//         ) $charset_collate;";

//         $sql4 = "CREATE TABLE {$wpdb->prefix}lnd_master_catalog (
//             id mediumint(9) NOT NULL,
//             status varchar(255) NOT NULL,
//             item_name varchar(255) NOT NULL,
//             type varchar(255) NOT NULL,
//             is_free BOOLEAN NOT NULL DEFAULT FALSE,
//             version varchar(255) NOT NULL,
//             filepath varchar(255) NOT NULL,
//             image varchar(255) NULL,
//             description TEXT NULL DEFAULT '',
//             demo varchar(255) DEFAULT NULL,
//             update_date DATETIME NOT NULL,
//             data DATE NOT NULL,
//             downloads TEXT NULL DEFAULT '',
//             internal_downloads TEXT NULL DEFAULT '',
//             instance bigint(20) DEFAULT NULL,
//             count bigint(20) NOT NULL DEFAULT 0,
//             PRIMARY KEY (id)
//         ) $charset_collate;";

//         $sql5 = "CREATE TABLE {$wpdb->prefix}lnd_master_catalog_categories (
//             catalog_id mediumint(9) NOT NULL,
//             category_id mediumint(9) NOT NULL,
//             PRIMARY KEY (catalog_id, category_id),
//             FOREIGN KEY (catalog_id) REFERENCES {$wpdb->prefix}lnd_master_catalog(id) ON DELETE CASCADE,
//             FOREIGN KEY (category_id) REFERENCES {$wpdb->prefix}lnd_master_catalog_category(id) ON DELETE CASCADE
//         ) $charset_collate;";

//         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//         dbDelta($sql1);
//         dbDelta($sql2);
//         dbDelta($sql3);
//         dbDelta($sql4);
//         dbDelta($sql5);
//     }
// }
