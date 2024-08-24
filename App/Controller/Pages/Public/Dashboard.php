<?php

namespace App\Controller\Pages\Public;

use App\Utils\View;

class Dashboard extends Page
{
    /**
     * Método responsável por retornar o conteúdo da page admin library (view) da nossa pagina admin
     * @return string
     */
    public static function getDashboard()
    {
        
        // $json_file_path = MASTER_LND_MASTER_DEV . 'assets/js/dashboard/asset-manifest.json';

        // $data = '';
        // if (file_exists($json_file_path)) {
        //     $context = stream_context_create([
        //         'ssl' => [
        //             'verify_peer' => false,
        //             'verify_peer_name' => false,
        //         ],
        //     ]);
            
        //     $json_data = file_get_contents($json_file_path, false, $context); // Lê o conteúdo do arquivo
        //     $data = json_decode($json_data, true); 
        // } else {
        //     $data = array(); // Retorna um array vazio se o arquivo não for encontrado
        // }

        
        // if (!empty($data)) {
        //     // Processar os dados do JSON
        //     foreach ($data as $item) {
        //         // Faça algo com cada item
        //     }
        // } else {
        //     // O arquivo JSON está vazio ou não foi encontrado
        // }

        parent::register_script(
            'react-dashboard', 
            '/assets/js/dashboard.min.js',
            '1.0.2'
        );
        // parent::register_script(
        //     'react-dashboard', 
        //     '/assets/js/dashboard/'. $data['entrypoints'][1],
        //     '1.0.2'
        // );
        // parent::register_style(
        //     'react', 
        //     '/assets/js/dashboard/'. $data['entrypoints'][0],
        //     '1.0.1',
            
        // );
        parent::register_style( 
            'react-global-css', 
            '/assets/css/global.min.css',
            '1.0.1',
            
        );
        parent::register_style(
            'react-dashboard-css', 
            '/assets/css/Dashboard.min.css',
            '1.0.1'
        ); 

        parent::localize_script('react-dashboard', 'appLocalizer', array(
            'url' => admin_url('admin-ajax.php'),
            'download_files' => site_url('/wp-json/files/v1/download/'),
            'download_templates' => site_url('/wp-json/templates/v1/download/'),
            'nonce' => wp_create_nonce('master-ajax-nonce'),
            'login_nonce' => wp_create_nonce('custom-login-nonce'),
            'register_nonce' => wp_create_nonce('custom-register-nonce')
        ));

        $content = View::render('dashboard/dashboard');

        return parent::getPage($content);
    }
}
