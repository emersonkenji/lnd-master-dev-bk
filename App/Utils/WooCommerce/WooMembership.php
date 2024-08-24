<?php

namespace App\Utils\WooCommerce;

class WooMembership
{
    public static function get_memberships_data($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        $memberships_data = array();

        if (function_exists('wc_memberships_get_user_memberships')) {
            $memberships = wc_memberships_get_user_memberships($user_id);

            if ($memberships !== []) {
                foreach ($memberships as $membership) {
                    $membership_data = array(
                        'id' => $membership->get_id(),
                        'slug' => $membership->plan->slug,
                        'name' => $membership->plan->name,
                        'status' => $membership->get_status(),
                        'start_date' => $membership->get_start_date(),
                        'end_date' => $membership->get_end_date(),
                        'product_name' => $membership->get_product() == null ? null : $membership->get_product()->get_name(),
                        'product_id' => 
                        $membership->get_product() == null ? null : $membership->get_product()->get_id(),
                        'plan_id' => $membership->get_plan_id(),
                    );

                    $memberships_data[] = $membership_data;
                }
            }
        }
        return $memberships_data;
    }
}
