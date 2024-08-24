<?php 

namespace App\Request;

Class Downloads
{
    private const DOWNLOAD_URL = LND_MASTER_DOWNLOADS_URL_API . 'downloads-lnd/';

    public static function init()
    {
        add_action('init', array(__CLASS__, 'download_now'));
    }

    public static function control_links($name, $version, $itens)
    {
        $pass               = get_option("lnd_master_dev_key");
        $render_name        = $name . '/' . $version;
        if (!empty($pass) && isset($itens) && $itens == 0) {
            if (isset($name) && isset($version)) {
                return self::DOWNLOAD_URL . $pass . '/' . $render_name . '/';
            }
        } 
        if (isset($itens) && $itens == 1) {
            if (isset($name) && isset($version)) {
                return self::DOWNLOAD_URL . $render_name . '/';
            }
        } 
        return 'Acesso negado';
    }

    public static function download_now()
    {
        if (
            isset($_GET['action']) && $_GET['action'] == 'download-plugin' ||
            isset($_GET['action']) && $_GET['action'] == 'download-theme'
        ) {
            if (
                isset($_GET['plugin_file']) && $_GET['plugin_file'] != '' ||
                isset($_GET['theme_file']) && $_GET['theme_file'] != ''
            ) {
                if (!wp_verify_nonce($_GET['_wpnonce'], "action-download-now")) {
                    echo __('Verification error.', 'lnd-master-dev');
                    exit();
                }

                if ($_GET['action'] == 'download-plugin') {
                    $lnd_plugin_name = sanitize_text_field($_GET['plugin_name']);
                    $lnd_plugin_version = sanitize_text_field($_GET['version']);
                    $lnd_itens = isset($_GET['lnd_itens']) && $_GET['lnd_itens'] != '' ? $_GET['lnd_itens'] : '';
                    $file = Self::control_links($lnd_plugin_name, $lnd_plugin_version, $lnd_itens);
                    $filename = $lnd_plugin_name . '-v' . $lnd_plugin_version . '.zip';
                }
                if ($_GET['action'] == 'download-theme') {
                    $lnd_plugin_name = sanitize_text_field($_GET['theme_file']);
                    $lnd_plugin_version = sanitize_text_field($_GET['theme_version']);
                    $lnd_itens = isset($_GET['lnd_itens']) && $_GET['lnd_itens'] != '' ? $_GET['lnd_itens'] : '';
                    $file = self::control_links($lnd_plugin_name, $lnd_plugin_version, $lnd_itens);
                    $filename = $lnd_plugin_name . '.zip';
                }
                //Verifica se o arquivo existe
                $headers = get_headers($file, 1);

                if (strpos($headers[0], '200') === false) {
                    $response = array(
                        'code' => '404',
                        'message' => 'Erro ao requisitar arquivo',
                        'data' => array(
                            'status' => '404'
                        )
                    );
                    wp_send_json($response );
                    return;
                }
                if (isset($headers['content-length']) && $headers['content-length'] == 0 ) {
                    $response = [
                        'code' => '200',
                        'message' => 'O arquivo não existe ou ocorreu um erro.',
                        'data' => array(
                            'status' => '200'
                        )
                    ];
                    wp_send_json($response );
                    return;
                }
                header('Content-Type: application/zip');
                header( 'Content-Description: File Transfer' );
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                ob_end_flush();
                flush();
                readfile($file);
                exit();
            } else {
                echo __('Error sending some variable.', 'lnd-master-dev');
                exit();
            }
        }
    }
}