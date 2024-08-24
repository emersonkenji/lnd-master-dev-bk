<?php
namespace App\Utils\WordPress;

class CustomAjaxAuth {
    private static ?CustomAjaxAuth $instance = null;

    private function __construct() {
        add_action('wp_ajax_nopriv_custom_ajax_login', [$this, 'custom_ajax_login']);
        add_action('wp_ajax_nopriv_custom_ajax_register', [$this, 'custom_ajax_register']);
        
        add_action('wp_ajax_nopriv_check_login_status', [$this, 'check_login_status']);
        add_action('wp_ajax_check_login_status', [$this, 'check_login_status']);
        
        add_action('wp_ajax_custom_ajax_logout', [$this, 'custom_ajax_logout']);
    }

    public function custom_ajax_login() {
        check_ajax_referer('custom-login-nonce', 'security');

        $info = [
            'user_login'    => sanitize_text_field($_POST['username']),
            'user_password' => $_POST['password'],
            'remember'      => true
        ];

        $user_signon = wp_signon($info, false);

        if (is_wp_error($user_signon)) {
            wp_send_json_error(['message' => __('Nome de usuário ou senha incorretos.')]);
        } else {
            wp_send_json_success(['message' => __('Login bem-sucedido, redirecionando...')]);
        }
    }

    public function custom_ajax_register() {
        check_ajax_referer('custom-register-nonce', 'security');

        $user_login = sanitize_text_field($_POST['username']);
        $user_email = sanitize_email($_POST['email']);
        $user_pass  = $_POST['password'];

        $user_id = wp_create_user($user_login, $user_pass, $user_email);
        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => $user_id->get_error_message()]);
        } else {
            wp_send_json_success(['message' => __('Registro bem-sucedido, você pode fazer login agora.')]);
        }
    }

    public function check_login_status() {
        // Opcional: adicionar verificação de nonce aqui se desejar
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            wp_send_json_success([
                'user' => [
                    'display_name' => $current_user->display_name,
                    'user_email' => $current_user->user_email,
                ]
            ]);
        } else {
            wp_send_json_error(['message' => 'User not logged in']);
        }
    }

    public function custom_ajax_logout() {
        // Opcional: adicionar verificação de nonce aqui se desejar
        wp_logout();
        wp_send_json_success(['message' => 'Logged out successfully']);
    }

    public static function get_instance(): CustomAjaxAuth {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function init() {
        self::get_instance();
    }
}