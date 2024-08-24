<?php

namespace App;


use App\Model\CreateTables;
use App\Utils\Cron;

class Registers
{
    public static function init()
    {
        register_activation_hook(MASTER_LND_BASE_FILE, [__CLASS__, 'on_activation']);
        register_deactivation_hook(MASTER_LND_BASE_FILE, [__CLASS__, 'on_deactivate']);
        register_uninstall_hook(MASTER_LND_BASE_FILE, [__CLASS__, 'on_uninstall']);
        
    }

    public static function on_activation()
    {
        new CreateTables();
    }

    public static function on_deactivate()
    {
        flush_rewrite_rules( );
        delete_option( 'every-6-hour' );
        wp_clear_scheduled_hook('every-6-hour');
        wp_clear_scheduled_hook('lnd_update_cron');
        wp_clear_scheduled_hook('lnd_update_plugin_data');

    }

    public static function on_uninstall()
    {
        delete_option('lnd_library_get_options_select_category');
        delete_option('lnd_get_catalog');
        delete_option('_lnd_plugins_datajson');
        delete_option('lnd_master_dev_key');
        delete_option('LNDMasterDevPlugin_lic_email');
        // Database::uninstall();
        Cron::remove_processing_cron();
    }
}
