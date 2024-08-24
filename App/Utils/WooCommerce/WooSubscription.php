<?php

namespace App\Utils\WooCommerce;

use WC_Order_Item_Product;

class WooSubscription
{
    public static function get_subscriptions_data($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }


        $subscriptions_data = array();

        if (function_exists('wcs_get_users_subscriptions')) {
            $subscriptions = wcs_get_users_subscriptions($user_id);
            if ($subscriptions !== []) {
                foreach ($subscriptions as $subscription) {
                    $subscription_data = array(
                        'id' => $subscription->get_id(),
                        'status' => $subscription->get_status(),
                        'start_date' => $subscription->get_date('start'),
                        'end_date' => $subscription->get_date('end'),
                        'next_payment_date' => $subscription->get_date('next_payment'),
                        'product_name' => '',
                        'order_id' => $subscription->get_parent_id(),
                    );

                    $order = wc_get_order($subscription_data['order_id']);
                    if ($order) {
                        $items = $order->get_items();
                        foreach ($items as $item) {
                            $product = new WC_Order_Item_Product($item->get_id());
                            $subscription_data['product_name'] = $product->get_name();
                            $subscription_data['product_id'] = $product->get_product_id();
                        }
                    }

                    $subscriptions_data[] = $subscription_data;
                }
            }
        }

        return $subscriptions_data;
    }
}
