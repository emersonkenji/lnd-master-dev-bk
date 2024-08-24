<?php

namespace App\View\Pages;

use App\View\LND_Get_Itens;

class Page_Plataforma
{
    public static function init()
    {
        add_shortcode('lnd_master_plataforma', [__CLASS__, 'lnd_shortcode_plataforma']);
    }

    public static function lnd_shortcode_plataforma()
    {
        ob_start();

        $page_plataforma = new self(); // Instancia a classe

        // Adiciona o conteúdo da página
        // $page_plataforma->lnd_master_user_plans();
        // $page_plataforma->lnd_master_filters_plans(
        //     Database::lnd_count_itens('plugin'),
        //     Database::lnd_count_itens('theme')
        // );
        // $page_plataforma->lnd_master_plataforma_filters(
            self::get_categories_options();
        // );
        // $page_plataforma->lnd_master_plataforma_modal();

        return ob_get_clean();
    }

    private static function get_categories_options() {
        // $lnd_library_options_category = get_option('lnd_library_get_options_select_category');
        // $lnd_category = '';

        // foreach ($lnd_library_options_category as $category) {
        //     $lnd_category .= '<option value="' . esc_attr($category) . '">' . esc_html($category) . '</option>';
        // }
        echo '<div class="container" id="alert-menssage"></div>';

        echo '<div class="container lnd-post-grid" id="lnd-post-grid"></div>';

        // return $lnd_category;
    }

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

        $basic = in_array('b', $plans) ? 'active' : '';
        $gold = in_array('g', $plans) ? 'active' : '';
        $profissional = in_array('p', $plans) ? 'active' : '';
        $diamond = in_array('d', $plans) ? 'active' : '';
        $lnd_library = in_array('l', $plans) ? 'active' : '';

        $imgUrl = WP_PLUGIN_URL . '/lnd-master-dev/assets/images';

         ?>
        <div class="container mt-4" id="lnd-controller">
            <div class="row d-flex justify-content-between">
                <div class="col-7">
                    <div class="lnd-radio">
                        <button class="lnd-button-radio <?php echo $basic ?>" id="lnd-button-radio-basic" data-button_label="Basic" data-order_data="basic">
                            <img width="30" src="<?php echo $imgUrl . '/basic.png' ?>" alt="plano-basico" />
                            <span class="lnd-span-radio"></span>
                        </button>

                        <button class="lnd-button-radio <?php echo $gold ?>" id="lnd-button-radio-gold" data-button_label="Gold" data-order_data="basic|gold">
                            <img width="30" src="<?php echo $imgUrl . '/gold.png' ?>" alt="plano-gold" />
                            <span class="lnd-span-radio"></span>
                        </button>

                        <button class="lnd-button-radio <?php echo $profissional ?>" id="lnd-button-radio-profissional" data-button_label="Profissional" data-order_data="basic|gold|profissional">
                            <img width="30" src="<?php echo $imgUrl . '/profissional.png' ?>" alt="plano-profissional" />
                            <span class="lnd-span-radio"></span>
                        </button>

                        <button class="lnd-button-radio <?php echo $diamond ?>" id="lnd-button-radio-diamond" data-button_label="Diamante" data-order_data="basic|gold|profissional|diamond">
                            <img width="30" src="<?php echo $imgUrl . '/diamante.png' ?>" alt="plano-diamante" />
                            <span class="lnd-span-radio"></span>
                        </button>

                        <button class="lnd-button-radio <?php echo $lnd_library ?>" id="lnd-button-radio-completo" data-button_label="LND Library" data-order_data="library">
                            <img width="30" src="<?php echo $imgUrl . '/completo.png' ?>" alt="plano-completo" />
                            <span class="lnd-span-radio"></span>
                        </button>
                    </div>
                </div>
                <div class="col-5">
                    <div class="lnd-radio-count">
                        <div class="lnd-count" id="lnd-count">
                            <p><b>Totais na plataforma: </b><?php echo $total_plugins + $total_temas ?> -> Plugins <?php echo $total_plugins ?> -> Temas <?php echo $total_temas ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    }

    public function lnd_master_user_plans()
    {
        $lnd_area_membros = '';
        $lnd_get_plans = LND_Get_Itens::lnd_response_instance();
        $hasMembers = isset($lnd_get_plans['members']);

        if ($hasMembers) {
            foreach ($lnd_get_plans['members'] as $member) {
                $plan = '';
                switch ($member) {
                    case 'basic':
                        $plan = 'Basic';
                        break;
                    case 'gold':
                        $plan = 'Gold';
                        break;
                    case 'mega-pack-profissional':
                        $plan = 'Profissional';
                        break;
                    case 'diamond':
                        $plan = 'Diamante';
                        break;
                    case 'lnd-library':
                        $plan = 'LND Library';
                        break;
                }
                $lnd_area_membros .= '<div>' . esc_html($plan) . '</div>';
            }
        }
        echo $lnd_area_membros;
    }

    public function lnd_master_plataforma_filters($categories_options)
    {
        ?>
        <div class="lnd-filters">
            <select id="lnd-select-category">
                <option value="">Selecione uma categoria</option>
                <?php echo $categories_options; ?>
            </select>
        </div>
        <?php
    }

    public function lnd_master_plataforma_modal()
    {
        ?>
        <!-- Modal -->
        <div class="modal fade" id="lndModal" tabindex="-1" role="dialog" aria-labelledby="lndModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="lndModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Modal body text goes here.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
