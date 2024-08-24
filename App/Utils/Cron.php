<?php

namespace App\Utils;

// use App\Database;

class Cron
{
    public static function init()
    {
        add_action('init', array(__CLASS__, 'schedule_processing_cron'));
        add_filter('cron_schedules', array(__CLASS__, 'register_cron_interval'));
        // add_action('lnd_update_cron', array(Database::class, 'lnd_library_insert_category'));
    }

    public static function register_cron_interval($schedules)
    {
        $schedules['hourly'] = array(
            'interval' => 60,
            'display'  => __('Hourly')
        );
        $schedules['every-6-hour'] = array(
            'interval' => 21600,
            'display'  => __('Every 6 hours')
        );
        return $schedules;
    }

    public static function schedule_processing_cron()
    {
        if (!wp_next_scheduled('process_files_cron')) {
            wp_schedule_event(time(), 'every_minute', 'process_files_cron');
        }

        if (!wp_next_scheduled('lnd_update_cron')) {
            wp_schedule_event(time(), 'every-6-hour', 'lnd_update_cron');
        }
    }

    public static function remove_processing_cron()
    {
        $timestamp = wp_next_scheduled('process_files_cron');
        wp_unschedule_event($timestamp, 'process_files_cron');
    }

}
