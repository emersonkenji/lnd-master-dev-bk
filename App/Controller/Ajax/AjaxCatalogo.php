<?php

namespace App\Controller\Ajax;

use App\Model\CatalogoModel;
use App\Model\Dashboard\TemplatesModel;
use App\Utils\Module\Cards;
use App\Utils\WooCommerce\{WooMembership, WooSubscription, User};
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class AjaxCatalogo
{
    private CatalogoModel $catalogoModel;

    public function __construct()
    {
        $this->catalogoModel = new CatalogoModel();
        add_action('rest_api_init', [$this, 'register_rest_route']);

        add_action('wp_ajax_get_catalogo', [$this, 'getCatalogo']);
        add_action('wp_ajax_nopriv_get_catalogo', [$this, 'getCatalogo']);

        add_action('wp_ajax_get_catalogo_dashboard', [$this, 'getCatalogoDashboard']);
        add_action('wp_ajax_nopriv_get_catalogo_dashboard', [$this, 'getCatalogoDashboard']);

        add_action('wp_ajax_get_data_user', [$this, 'getDataUser']);
        add_action('wp_ajax_nopriv_get_data_user', [$this, 'getDataUser']);

        add_action('wp_ajax_get_templates_files', [$this, 'getTemplates']);
        add_action('wp_ajax_nopriv_get_templates_files', [$this, 'getTemplates']);
    }

    public function register_rest_route()
    {
        register_rest_route('templates/v1', '/download/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'downloadTemplates'],
            'permission_callback' => function () {
                return true; // Ou implemente sua própria lógica de permissão
            },
        ]);

        register_rest_route('files/v1', '/download/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'downloadFiles'],
            'permission_callback' => function () {
                return true; // Ou implemente sua própria lógica de permissão
            },
        ]);
    }

    public function getTemplates()
    {
        $templates = [];
        $templates['user'] = User::get_user_data();
        $templates['plan'] = $this->getAccessPlan();
        $templates['categories']= TemplatesModel::get_instance()->get_templates_categories();
        $templates['templates']= TemplatesModel::get_instance()->get_templates();
        wp_send_json( $templates );
    }

    public function getCatalogo(): void
    {
        $this->setQueryParamsFromRequest();
        $result = $this->catalogoModel->executeQuery();

        $result['processedResults'] = Cards::init($result['result']);
        $result['totalPages'] = ceil($result['total'] / $result['limit']);

        wp_send_json($result);
    }

    public function getCatalogoDashboard(): void
    {
        $this->setQueryParamsFromRequest();
        $result = $this->catalogoModel->executeQuery();

        $result['user'] = User::get_user_data();
        $result['totalPages'] = ceil($result['total'] / $result['limit']);
        $result['category'] = CatalogoModel::get_categories();
        $result['plans'] = [
            'membership'   => WooMembership::get_memberships_data(),
            'subscription' => WooSubscription::get_subscriptions_data()
        ];
        $result['plan'] = $this->getAccessPlan();

        wp_send_json($result);
    }

    public function getDataUser(): void
    {
        $user = User::get_user_data();
        wp_send_json($user);
    }

    private function setQueryParamsFromRequest(): void
    {
        $params = [
            'limit' => $_POST['limit'] ?? 30,
            'page' => $_POST['page'] ?? 1,
            'order' => $_POST['order'] ?? '',
            'orderBy' => $_POST['order_by'] ?? '',
            'type' => isset($_POST['type']) && $_POST['type'] !== '' ? $_POST['type'] : null,
            'query' => isset($_POST['query']) && $_POST['query'] !== '' ? $_POST['query'] : null,
            'category' => isset($_POST['category']) && $_POST['category'] !== '' ? $_POST['category'] : null,
            'filter' => $_POST['filter'] ?? null,
            'plans' => $_POST['plans'] ?? null,
        ];
        foreach ($params as $key => $value) {
            $this->catalogoModel->setQueryParam($key, $value);
        }
    }

    private function getAccessPlan(): ?int
    {
        $memberships = WooMembership::get_memberships_data();
        $subscriptions = WooSubscription::get_subscriptions_data();
        $activeMembers = [];

        $priority = [
            'lnd-library' => 5,
            'diamond' => 4,
            'profissional' => 3,
            'gold' => 2,
            'basic' => 1,
            17869 => 5,
            15443 => 0
        ];

        foreach ($memberships as $membership) {
            if ($membership['status'] === 'active') {
                $activeMembers[] = $membership['slug'];
            }
        }

        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] === 'active') {
                $activeMembers[] = $subscription['product_id'];
            }
        }

        $highestPriority = 0;
        $highestPlan = null;
        foreach ($activeMembers as $member) {
            if (isset($priority[$member]) && $priority[$member] > $highestPriority) {
                $highestPriority = $priority[$member];
                $highestPlan = $priority[$member];
            }
        }

        return $highestPlan;
    }

    
    public function downloadTemplates($request)
    {
        // check_ajax_referer('custom-register-nonce', 'security');
        // check_ajax_referer('ajax_nonce');
        $fileId = $request->get_param('id');
        if (!$fileId) {
            return new WP_REST_Response('ID do arquivo não fornecido', 400);
        }

        $fileUrl = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/files/templates_downloads/' . $fileId;

        $headers = get_headers($fileUrl, 1);
        if (strpos($headers[0], '200') === false) {
            $response = array(
                'code' => '404',
                'message' => 'Erro ao requisitar arquivo',
                'data' => array(
                    'status' => '404'
                )
            );
            wp_send_json($response);
            return;
        }
        if (isset($headers['content-length']) && $headers['content-length'] <= 0) {
            $response = [
                'code' => '200',
                'message' => 'O arquivo não existe ou ocorreu um erro.',
                'data' => array(
                    'status' => '200'
                )
            ];
            wp_send_json($response);
            return;
        }

        preg_match('/filename=([^;]+)/', $headers['content-disposition'], $matches);
        $originalFileName = $matches[1];

        header('Content-Type: application/zip');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . $originalFileName . '"');
        ob_end_flush();
        flush();
        readfile($fileUrl);
        exit();
    }

    public function downloadFiles(WP_REST_Request $request)
    {
        // check_ajax_referer('master-ajax-nonce');
        // $nonce = $request->get_param('_ajax_nonce');
        // // // wp_send_json($nonce);
        // if (!wp_verify_nonce($nonce, 'master-ajax-nonce')) {
        //     return new WP_Error('invalid_nonce', 'Nonce inválido', array('status' => 403));
        // }
        $fileId = $request->get_param('id');
        if (!$fileId) {
            return new WP_REST_Response('ID do arquivo não fornecido', 400);
        }

        $fileUrl = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/catalog/v2/files/downloads/' . $fileId;

        // Envia a requisição POST
        $response = wp_remote_post($fileUrl, array(
            'method'    => 'POST',
            'body'      => array('id' => $fileId), // Inclua quaisquer dados adicionais necessários
        ));

        // Verifica se houve erro na requisição
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return new WP_REST_Response('Erro ao requisitar arquivo: ' . $error_message, 500);
        }

        // Verifica o status da resposta
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body, true);
            return new WP_REST_Response(
                array(
                    'code'    => $status_code,
                    'message' => $response_data['message'] ?? 'Erro ao requisitar arquivo',
                    'data'    => $response_data['data'] ?? array('status' => $status_code),
                ),
                $status_code
            );
        }

        // Obtém os headers e o conteúdo do arquivo
        $headers = wp_remote_retrieve_headers($response);
        $body = wp_remote_retrieve_body($response);

        // Verifica o comprimento do conteúdo
        if (isset($headers['content-length']) && $headers['content-length'] <= 0) {
            return new WP_REST_Response(
                array(
                    'code'    => 200,
                    'message' => 'O arquivo não existe ou ocorreu um erro.',
                    'data'    => array('status' => 200),
                ),
                200
            );
        }

        // Obtém o nome original do arquivo
        $content_disposition = $headers['content-disposition'] ?? '';
        preg_match('/filename=([^;]+)/', $content_disposition, $matches);
        $originalFileName = isset($matches[1]) ? $matches[1] : 'downloaded_file.zip';

        // Envia o arquivo para o cliente
        header('Content-Type: application/zip');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . $originalFileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($body));

        ob_clean();
        flush();
        echo $body;
        exit();
    }
}
