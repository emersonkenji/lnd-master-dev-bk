<?php

namespace App\Utils\WooCommerce;

use App\Utils\WooCommerce\WooMembership;
use App\Utils\WooCommerce\WooSubscription;
use WC_Order_Query;

class User
{
    public static function get_user_data($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        $user_data = array();
        $user_data['status'] = is_user_logged_in() ? 'logged' : 'visitor';

        $user = get_user_by('id', $user_id);

        if ($user) {
            $user_data['data_user'] = array(
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'name' => $user->display_name,
                'first_name' => $user->user_firstname,
                'last_name' => $user->user_lastname,
                'avatar' => get_avatar_url($user->ID),
                'registration_date' => $user->user_registered,
                'roles' => $user->roles,
            );
            $user_data['plans'] = [
                'membership'   => WooMembership::get_memberships_data(),
                'subscription' => WooSubscription::get_subscriptions_data()
            ];
        }

        return $user_data;
    }

    public static function get_user_simple_data($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        $user_data = array();
        $user_data['status'] = is_user_logged_in();

        $user = get_user_by('id', $user_id);

        if ($user) {
            $user_data['data_user'] = array(
                'id' => $user->ID,
                'email' => $user->user_email,
            );
        }

        return $user_data;
    }

    // Função para obter os dados de downloads
    public static function get_customer_downloads($user_id) {
        $orders = new WC_Order_Query(array(
            'customer' => $user_id,
            'limit' => -1,
            'return' => 'ids'
        ));
    
        $download_data = array();
    
        foreach ($orders->get_orders() as $order_id) {
            $order = wc_get_order($order_id);
            $downloads = $order->get_downloadable_items();
    
            foreach ($downloads as $download) {
                // $download_data[] = array(
                //     'order_id' => $order_id,
                //     'product_name' => $download['product_name'],
                //     'download_count' => $download['downloads_remaining'],
                //     'download_expiry' => $download['access_expires']
                // );
                $download_data[] = $download;
            }
        }
    
        return $download_data;
    }
}
