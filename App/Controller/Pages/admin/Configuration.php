<?php
namespace App\Controller\Pages\admin;

class Configuration
{
    const OPTION_GROUP = 'lnd_master_options_group';
    const OPTION_NAME = 'lnd_master_cron_status';
    const CRON_HOOK = 'lnd_master_cron_hook';

    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_init', [__CLASS__, 'check_cron_status']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'deactivate_plugin']);
        // add_action(self::CRON_HOOK, [__CLASS__, 'cron_job']);
    }

    public static function register_settings()
    {
        register_setting(self::OPTION_GROUP, self::OPTION_NAME);

        add_settings_section(
            'lnd_master_settings_section',
            'Configurações do LND Master DEV',
            null,
            'lnd_master_options',
            ['section_class' => 'test']
        );

        add_settings_field(
            self::OPTION_NAME,
            'Ativar Cron Job',
            [ __CLASS__, 'render_cron_status_field' ],
            'lnd_master_options',
            'lnd_master_settings_section',
            [ 'class' => 'classe-html-tr' ]
        );
    }

    public static function render_cron_status_field() {
        $options = get_option(self::OPTION_NAME);
    
        $checked = isset($options['cron_status']) && $options['cron_status'] === 'on' ? 'checked' : '';
    
        printf(
            '<div class="checkbox-wrapper-3">
                <input type="checkbox" id="cbx-3" name="%s[cron_status]" %s value="on" />
                <label for="cbx-3" class="toggle"><span></span></label>
            </div>', 
            self::OPTION_NAME, 
            $checked
        );

    }

    public static function settings_page()
    {
        ?>
        <div class="wrap">
            <h1>Configurações do Meu Plugin</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections('lnd_master_options');
                submit_button();
                ?>
            </form>
        </div> 
        <?php
    }

    public static function check_cron_status()
    {
        $options = get_option(self::OPTION_NAME);
        if ($options == 1) {
            self::activate_cron();
        } else {
            self::deactivate_cron();
        }
    }

    public static function activate_cron()
    {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time(), 'hourly', self::CRON_HOOK);
        }
    }

    public static function deactivate_cron()
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }
    }

    public static function cron_job()
    {
        error_log('Cron job executado');
    }

    public static function deactivate_plugin()
    {
        self::deactivate_cron();
    }
}
