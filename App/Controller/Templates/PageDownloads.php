<?php

namespace App\Controller\Templates;

use App\View\LND_Get_Itens;

/**
 * Class PageDownloads
 * 
 * Handles download functionality for various user access levels.
 */
class PageDownloads
{
    /**
     * Access levels in order of increasing privileges.
     */
    const ACCESS_LEVELS = ['basic', 'gold', 'profissional', 'diamond', 'lnd-library'];

    /**
     * Initialize the class.
     */
    public static function init()
    {
        add_action('init', [__CLASS__, 'register_rewrite_rules']);
        add_action('wp_loaded', [__CLASS__, 'maybe_flush_rewrite_rules']);
        add_filter('query_vars', [__CLASS__, 'add_query_vars']);
        add_action('template_include', [__CLASS__, 'handle_download_request']);
        add_action('plugins_loaded', [__CLASS__, 'load_dependencies']);
    }

    /**
     * Register custom rewrite rules.
     */
    public static function register_rewrite_rules()
    {
        add_rewrite_rule(
            '^downloads-files/([^/]+)/([^/]+)/([^/]+)/?$',
            'index.php?downloads_files=$matches[1]&params[slug]=$matches[2]&params[version]=$matches[3]',
            'top'
        );
    }

    /**
     * Add custom query vars.
     *
     * @param array $vars Existing query vars.
     * @return array Modified query vars.
     */
    public static function add_query_vars($vars)
    {
        $vars[] = 'downloads_files';
        $vars[] = 'params';
        return $vars;
    }

    /**
     * Handle download requests.
     *
     * @param string $template The current template path.
     * @return string The template path to use.
     */
    public static function handle_download_request($template)
    {

        $downloads = get_query_var('downloads_files');
        if (empty($downloads)) {
            return $template;
        }

        if (!is_user_logged_in()) {
            wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
            exit;
        }

        $params = get_query_var('params');
        $instance = LND_Get_Itens::lnd_response_instance();

        self::process_download($downloads, $instance, $params);
        return;
    }

    /**
     * Process the download based on user access level.
     *
     * @param string $access The requested access level.
     * @param array $instance User instance data.
     * @param array $params Download parameters.
     */
    private static function process_download($access, $instance, $params)
    {
        if (!isset($params['slug']) || !isset($params['version'])) {
            self::send_error_response('Invalid download parameters.');
            return;
        }

        if ($access === 'lnd-internal-downloads' || self::has_access($access, $instance)) {
            self::initiate_download($params['slug'], $params['version']);
        } else {
            self::send_error_response('You are not authorized to access this download.', 403);
        }
    }

    /**
     * Check if the user has access to the requested download.
     *
     * @param string $requested_access The requested access level.
     * @param array $instance User instance data.
     * @return bool Whether the user has access.
     */
    private static function has_access($requested_access, $instance)
    {
        if ($instance['subscriber'] === 'active') {
            return true;
        }

        $user_levels = $instance['members'];
        $requested_index = array_search($requested_access, self::ACCESS_LEVELS);

        foreach ($user_levels as $level) {
            $user_index = array_search($level, self::ACCESS_LEVELS);
            if ($user_index !== false && $user_index >= $requested_index) {
                return true;
            }
        }

        return false;
    }

    /**
     * Initiate the file download.
     *
     * @param string $slug The download slug.
     * @param string $version The download version.
     */
    private static function initiate_download($slug, $version)
    {
        $file_url = self::get_download_url($slug, $version);
        $filename = self::get_filename($slug, $version);

        if (!self::is_file_accessible($file_url)) {
            self::send_error_response('File not found or inaccessible.', 404);
            return;
        }

        header('Content-Type: application/force-download');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
        header('Content-Length: ' . self::get_remote_file_size($file_url));
        
        ob_clean();
        flush();
        readfile($file_url);
        exit;
    }

    /**
     * Get the download URL.
     *
     * @param string $slug The download slug.
     * @param string $version The download version.
     * @return string The download URL.
     */
    private static function get_download_url($slug, $version)
    {
        return LND_MASTER_DOWNLOADS_URL_API . 'downloads/' . $slug;
    }

    /**
     * Get the filename for the download.
     *
     * @param string $slug The download slug.
     * @param string $version The download version.
     * @return string The filename.
     */
    private static function get_filename($slug, $version)
    {
        return $version === 'latest' ? "{$slug}.zip" : "{$slug}-v{$version}.zip";
    }

    /**
     * Check if the remote file is accessible.
     *
     * @param string $url The file URL.
     * @return bool Whether the file is accessible.
     */
    private static function is_file_accessible($url)
    {
        $headers = get_headers($url, 1);
        return strpos($headers[0], '200') !== false && isset($headers['content-length']) && $headers['content-length'] > 0;
    }

    /**
     * Get the size of a remote file.
     *
     * @param string $url The file URL.
     * @return int The file size.
     */
    private static function get_remote_file_size($url)
    {
        $headers = get_headers($url, 1);
        return isset($headers['content-length']) ? $headers['content-length'] : 0;
    }

    /**
     * Send an error response.
     *
     * @param string $message The error message.
     * @param int $status The HTTP status code.
     */
    private static function send_error_response($message, $status = 400)
    {
        wp_send_json(['error' => $message], $status);
        exit;
    }

    /**
     * Flush the rewrite rules if needed.
     */
    public static function maybe_flush_rewrite_rules()
    {
        if (is_network_admin() || 'no' === get_option('page_downloads_flush_rewrite_rules')) {
            return;
        }
        update_option('page_downloads_flush_rewrite_rules', 'no');
        flush_rewrite_rules();
    }

    /**
     * Load dependencies required for this class.
     */
    public static function load_dependencies()
    {
        // Load any required files or initialize any necessary globals here
    }

    public static function generate_download_link($instance, $slug, $version)
    {
       $link_url = set_url_scheme( get_site_url(), 'https' );
       if( $slug ) {
        $link = $link_url . '/downloads-files/' . $instance . '/' . $slug . '/' . $version . '/' ;
        return $link;
       }
       return null;

    }
}