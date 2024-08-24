php

namespace App\View\Pages;

use App\Database;
use App\View\LND_Get_Itens;


class Page_Plataforma_copy
{
    /**
     * Renderiza a view
     */
    public function init()
    {

        $total_plugins = Database::lnd_count_itens('plugin');
        $total_temas  = Database::lnd_count_itens('theme');
        $lnd_library_options_category = get_option('lnd_library_get_options_select_category');
        $lnd_category = '';

        foreach ($lnd_library_options_category as $category) {
            $lnd_category .= '<option value="' . $category . '">' . $category . '</option>';
        }

        $this->lnd_master_user_plans();
        $this->lnd_master_filters_plans($total_plugins, $total_temas);
        $this->lnd_master_plataforma_filters($lnd_category);
        $this->lnd_master_plataforma_modal();

        echo '<div class="container" id="alert-menssage"></div>';

        echo '<div class="container" id="lnd-post-grid"></div>';
    }


    /**
     * Botoes de filtros de planos 
     */
    public function lnd_master_filters_plans($total_plugins, $total_temas)
    {
        $selctPlan = LND_Get_Itens::lnd_response_instance();
        $hasMembers = isset($selctPlan['members']);

        $plans = [];
        if ($hasMembers) {
            foreach ($selctPlan['members'] as $member) {
                switch ($member) {
                    case 'basic':
                        $plans['plan'] = 'b';
                        break;
                    case 'gold':
                        $plans['plan'] = 'g';
                        break;
                    case 'mega-pack-profissional':
                        $plans['plan'] = 'p';
                        break;
                    case 'diamond':
                        $plans['plan'] = 'd';
                        break;
                    case 'lnd-library':
                        $plans['plan'] = 'l';
                        break;
                }
            }
        }
        if (($hasMembers && in_array('lnd-library', $selctPlan['members'])) || (isset($selctPlan['subscriber']) && $selctPlan['subscriber'] == 'active')) {
            $plans['plan'] = 'l';
        }

        // $plans = [];
        // if (isset($selctPlan['members']) && in_array('basic', $selctPlan['members'])) {
        //     $plans['plan'] = 'b';
        // }
        // if (isset($selctPlan['members']) && in_array('gold', $selctPlan['members'])) {
        //     $plans['plan'] = 'g';
        // }
        // if (isset($selctPlan['members']) && in_array('mega-pack-profissional', $selctPlan['members'])) {
        //     $plans['plan'] = 'p';
        // }
        // if (isset($selctPlan['members']) && in_array('diamond', $selctPlan['members'])) {
        //     $plans['plan'] = 'd';
        // }
        // if (isset($selctPlan['members']) && in_array('lnd-library', $selctPlan['members']) 
        //     || isset($selctPlan['subscriber']) && $selctPlan['subscriber'] == 'active') {
        //     $plans['plan'] = 'l';
        // }

        $basic          = in_array('b', $plans) ? 'checked' : '';
        $gold           = in_array('g', $plans) ? 'checked' : '';
        $profissional   = in_array('p', $plans) ? 'checked' : '';
        $diamond        = in_array('d', $plans) ? 'checked' : '';
        $lnd_library    = in_array('l', $plans) ? 'checked' : '';

        $imgUrl = WP_PLUGIN_URL . '/lnd-master-dev/assets/images';

?>
        <!-- Controles  -->
        <div class="container mt-4" id="lnd-controller">
            <div class="button-group">
                <button class="lnd-button-radio" id="lnd-button-radio-basic" data-button_label="Basic">
                    <img width="30" src=<?php echo  $imgUrl . '/basic.png' ?> alt="plano-basico" />
                    <span class="lnd-span-radio"></span>
                </button>

                <button class="lnd-button-radio" id="lnd-button-radio-gold" data-button_label="Gold">
                    <img width="30" src=<?php echo  $imgUrl . '/gold.png' ?> alt="plano-gold" />
                    <span class="lnd-span-radio"></span>
                </button>

                <button class="lnd-button-radio" id="lnd-button-radio-profissional" data-button_label="Profissional">
                    <img width="30" src=<?php echo  $imgUrl . '/profissional.png' ?> alt="plano-gold" />
                    <span class="lnd-span-radio"></span>
                </button>

                <button class="lnd-button-radio" id="lnd-button-radio-diamond" data-button_label="Diamante">
                    <img width="30" src=<?php echo  $imgUrl . '/diamante.png' ?> alt="plano-gold" />
                    <span class="lnd-span-radio"></span>
                </button>

                <button class="lnd-button-radio" id="lnd-button-radio-completo" data-button_label="LND Library">
                    <img width="30" src=<?php echo  $imgUrl . '/completo.png' ?> alt="plano-gold" />
                    <span class="lnd-span-radio"></span>
                </button>

            </div>




            <div class="container mt-4" id="lnd-controller">
                <div class="row">
                    <div class="col-auto">
                        <div class="lnd-radio">

                            <input label="<?php echo __('Basico', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-plans" id="lnd-radio-control-basic" value="basic" data-order_data="basic" autocomplete="off" <?php echo $basic ?>>

                            <input label="<?php echo __('Gold', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-plans" id="lnd-radio-control-gold" value="basic|gold" data-order_data="basic|gold" autocomplete="off" <?php echo $gold ?>>

                            <input label="<?php echo __('Profissional', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-plans" id="lnd-radio-control-pro" value="basic|gold|profissional" data-order_data="basic|gold|profissional" autocomplete="off" <?php echo $profissional ?>>

                            <input label="<?php echo __('Diamante', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-plans" id="lnd-radio-control-diamond" value="basic|gold|profissional|diamond" data-order_data="basic|gold|profissional|diamond" autocomplete="off" <?php echo $diamond ?>>

                            <input label="<?php echo __('LND Biblioteca', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-plans" id="lnd-radio-control-lnd-library" value="library" autocomplete="off" <?php echo $lnd_library ?>>

                        </div>
                    </div>
                    <div class="col">
                        <div class="lnd-radio-count">
                            <div class="lnd-count" id="lnd-count">
                                <p><b>Totais na plataforma: </b><?php echo $total_plugins + $total_temas ?> -> Plugins <?php echo $total_plugins ?> -> Temas <?php echo $total_temas ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  -->
        <?php
    }

    /**
     * Mostra os plano que o usuario tem acesso na plataforma
     * @return 2 container contenco informaçoes de acesso aos planos
     */
    public function lnd_master_user_plans()
    {
        $lnd_area_membros = '';
        $lnd_assinantes = '';
        if (!is_user_logged_in()) {
            // $lnd_area_membros = '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '">Acessar minha conta</a>';
            $lnd_area_membros = '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '">Acessar minha conta</a>';

            $lnd_assinantes = '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '">Acessar minha conta</a>';
        } else {

            $user_id = get_current_user_id();

            $args = array('status' => array('active'));

            /**
             * Woo membershipping
             */
            $active_memberships = wc_memberships_get_user_memberships($user_id, $args);
            if ($active_memberships !== []) {
                foreach ($active_memberships as  $value) {
                    if ($value) {
                        $slug = ucfirst($value->plan->slug);
                        $lnd_area_membros .= '<span class="badge text-bg-secondary me-1">' . $slug . '</span>';
                    }
                }
            } else {
                $lnd_area_membros .= 'Nenhum plano ativo encontrado';
            }

            /**
             * Woo subscription
             */
            $subscriptions = wcs_get_users_subscriptions($user_id);

            if ($subscriptions !== []) {

                foreach ($subscriptions as $subscription) {
                    if ($subscription->has_status('active')) {

                        $product_name = $subscription->get_base_data();

                        $order = wc_get_order($product_name['parent_id']);

                        $items = $order->get_items();

                        foreach ($items as $item) {
                            $product_name = $item->get_name();
                            // echo $product_name . '<br>';
                            if (stripos($product_name, 'lnd library') !== false) {
                                // echo "O nome do produto contém a string lnd library <br>";
                                $lnd_assinantes = 'LND Library Ativo';
                            } else {
                                $lnd_assinantes = "Seu produto não da acesso a nossa biblioteca de downloads";
                            }
                        }
                    } else {
                        $lnd_assinantes = 'Nenhuma assinatura encontrada ';
                    }
                }
            } else {
                $lnd_assinantes = 'Nenhuma assinatura encontrada ';
            }
        }

        ?>
            <div class="container mt-4" id="lnd-controller">

                <div class="row">
                    <div class="mb-3 col-sm-6 mb-sm-0">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Você e membro do pacote.</h5>
                                <p class="card-text"><b>Area de membros: </b><?php echo $lnd_area_membros; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Você e assinante do pacote.</h5>
                                <p class="card-text"><b>Assinante status: </b><?php echo $lnd_assinantes; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <?php

    }

    /**
     * Controles e filtros de plugins e temas
     * @return html botoes e filtros 
     */
    public function lnd_master_plataforma_filters($lnd_category)
    {
        ?>
            <!-- Controles  -->
            <div class="container mt-4" id="lnd-controller">
                <div class="row align-items-center ">
                    <div class="col-auto">
                        <div class="lnd-col-radio">
                            <div class="lnd-radio">
                                <input label="<?php echo __('Todos', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-control" id="lnd-radio-control-all" value="all" data-order_data="all" autocomplete="off" checked>
                                <input label="<?php echo __('Grátis', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-control" id="lnd-radio-control-free" value="free" data-order_data="free" autocomplete="off">
                            </div>

                            <div class="lnd-radio">
                                <input label="<?php echo __('Plugins', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-type" id="lnd-radio-type-plugin" data-order_data="plugin" value="plugin" autocomplete="off">
                                <input label="<?php echo __('Temas', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-type" id="lnd-radio-type-theme" data-order_data="theme" value="theme" autocomplete="off">
                            </div>

                            <div class="lnd-radio">
                                <input label="<?php echo __('Data', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-order" id="lnd-order-update" data-order_data="update_date" value="update_date" autocomplete="off" checked>
                                <input label="<?php echo __('Nome', 'lnd-master-dev') ?>" class="lnd-radio-input" type="radio" name="lnd-radio-order" id="lnd-order-name" data-order_data="item_name" value="item_name" autocomplete="off">
                            </div>

                            <div class="lnd-radio">
                                <input label="Maior" class="lnd-radio-input" type="radio" name="lnd-radio-order-by" id="lnd-order-desc" data-order_data="desc" value="desc" autocomplete="off" checked>
                                <input label="Menor" class="lnd-radio-input" type="radio" name="lnd-radio-order-by" id="lnd-order-asc" data-order_data="asc" value="asc" autocomplete="off">
                            </div>

                            <div class="lnd-select">
                                <select class="lnd-select lnd-form-select-grid " id="lnd-form-select-grid" aria-label="Default select example">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30" selected>30</option>
                                    <option value="40">40</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <div class="lnd-select-category">
                                <select class="lnd-select-category lnd-select" id="lnd-form-select-category" aria-label="Default select example">
                                    <option value="" selected><?php echo __('Todas Categorias', 'lnd-master-dev') ?></option>
                                    <?php echo $lnd_category ?>
                                </select>
                            </div>
                            <div class="lnd-master-btn-clear-div">
                                <input type="button" value="X" class="lnd-master-btn-clear" id="lnd-reset-filters">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="lnd-search-box">
                            <input class="lnd-input-search" id="lnd-search-box" placeholder="<?php echo __('Search...', 'lnd-master-dev') ?>">
                        </div>
                    </div>
                </div>
            </div>
            <!--  -->
        <?php
    }

    /**
     * Spinners Loading
     */
    public function lnd_master_plataforma_modal()
    {
        ?>
            <!-- modal -->
            <div class="ajax_load">
                <div class="ajax_load_box">
                    <div class="ajax_load_box_circle"></div>
                    <div class="ajax_load_box_title">Aguarde, carregando!</div>
                </div>
            </div>
            <!--  -->
    <?php
    }
}
