<?php

namespace App\Utils\Elite;
// require_once dirname(__FILE__) . "/woocommerce-product-license-manager.php";
// require_once dirname(__FILE__) . "/EliteCaller.php";
// require_once dirname(__FILE__) . "/Deactivate.php";
// require_once plugin_dir_path(__DIR__) . "EL_WOOCommerceAddon.php";

class CustonMyMenuPage
{

    public  function __construct()
    {
        add_filter('woocommerce_account_menu_items',  [$this, 'lnd_custom_my_account_menu_items'], 30);
        add_action('init',  [$this, 'lnd_add_endpoint']);
        add_action('wp_loaded',  [$this, 'lnd_custon_endpoint_flush_rewrite_rules']);
        add_action('woocommerce_account_lnd-controle-license_endpoint',  [$this, 'lnd_custom_endpoint_content']);
    }

    /**
     * lnd Adicionando um novo item no menu minha conta
     * *
     * @param array $items
     * @return array
     */

    public function lnd_custom_my_account_menu_items($items)
    {
        // Remove the logout menu item.
        $logout = $items['customer-logout'];
        unset($items['downloads']);
        unset($items['members']);
        unset($items['subscriptions']);
        unset($items['edit-address']);
        unset($items['edit-account']);
        unset($items['customer-logout']);

        $items['members'] = __('Meus planos', 'woocommerce');
        $items['subscriptions'] = __('Minhas assinaturas', 'woocommerce');
        // Insert your custom endpoint.
        $items['lnd-controle-license'] = __('Minhas Licenças', 'woocommerce');

        $items['baixar-arquivos'] = __('Downloads', 'woocommerce');
        $items['edit-addres'] = __('Meu endereço', 'woocommerce');
        $items['edit-account'] = __('Detalhes da conta', 'woocommerce');

        // Insert back the logout item.
        $items['customer-logout'] = $logout;

        return $items;
    }

    /**
     * Endpoint HTML content.
     */
    public function lnd_custom_endpoint_content()
    {
        $licenses = $this->lnd_get_licenses();
        $this->lnd_table_license($licenses);
    }

    public function lnd_table_license($licenses)
    {
        if (empty($licenses)) {
            return null;
        }
        // $EL_WOOCommerceAddon = new EL_WOOCommerceAddon();
        // $youtube = $EL_WOOCommerceAddon->GetOption("lnd_url_youtube", "");
        // $countLicenses = 0;
        // $countLicenses = count($licenses);

        $this->header_menu_itens();

        foreach ($licenses as $license) {
            $this->lnd_table($license);
        }
    }

    public function header_menu_itens($link = [])
    {
        $EL_WOOCommerceAddon = new EL_WOOCommerceAddon();
?>
        <div class="header-menu-itens-dashboard">
            <h3> Somente licensas ativas </h3>
            <?php
            if (!empty($EL_WOOCommerceAddon->GetOption("lnd_url_youtube", ""))) {
                $youtube = $EL_WOOCommerceAddon->GetOption("lnd_url_youtube", "");
            ?>
                <div class="header-menu-itens-dashboard-icons">
                    <i class="fa fa-play-circle" aria-hidden="true" style="cursor:pointer" data-lnd_url="<?php echo $youtube ?>" data-toggle="tooltip" title="Tutorial de instalação"></i>
                </div>
            <?php
            }
            ?>

        </div>

        <?php
    }

    public function lnd_get_licenses()
    {
        $current_user = wp_get_current_user();
        $orders = wc_get_orders(array('customer_id' => $current_user->ID));
        $licenses = [];
        foreach ($orders as $order) {
            $orderMeta = $order->get_meta_data();
            $elmeta = $this->LndGetElMeta($orderMeta, $metaId);
            foreach ($elmeta as  $value) {
                $licenses[] = implode($value);
            }
        }
        return $licenses;
    }

    function LndGetElMeta(&$allMeta, &$metaid = 0)
    {
        foreach ($allMeta as $a) {
            $d = $a->get_data();
            if (!empty($d['key'] == '_el_meta')) {
                $metaid = $d['id'];
                return !empty($d['value']) ? unserialize($d['value']) : [];
            }
        }
        return [];
    }

    public function lnd_table($licenses)
    {
        $licenseManager = new EL_WOOCommerceAddon();
        $apiKey =  $licenseManager->GetOption("el_api_key", "");
        $apiEndPoint =   $licenseManager->GetOption("el_end_point", "");

        $eliteObject = new EliteCaller($apiEndPoint, $apiKey);
        $listActiveDomains = $eliteObject->ViewLicense($licenses, $apiKey, $error);
        if ($listActiveDomains != 'Unknown') {
            $dateObj = strtotime($listActiveDomains->data->entry_time);
            $date = date('d-m-Y', $dateObj);
            $list = $listActiveDomains->data->active_domains;
            $downloadPlugin = get_home_url() . "/lnd-downloads/lnd-internal-downloads/" . $listActiveDomains->data->product_base_name . "/latest/";
            $pluginName = $listActiveDomains->data->product_name;
        ?>
            <div class="lnd-card blue">
                <div class="lnd-row">
                    <div class="lnd-col">
                        <h3>
                            <?php echo esc_html($listActiveDomains->data->product_name); ?>
                            <a href="<?php echo $downloadPlugin ?>" style="cursor:pointer" data-toggle="tooltip" title="Download de Plugins <?php echo $pluginName ?>"><i class="fa fa-download"></i></a>
                        </h3>

                        <p class="lnd-p2"><?php echo esc_html($licenses); ?></p>
                    </div>

                    <div class="lnd-col lnd-border">
                        <p class="lnd-p">Licença <span class="lnd-span"><?php echo esc_html($listActiveDomains->data->license_title); ?></span></p>
                        <p class="lnd-p">Dominios Max <span class="lnd-span"><?php echo esc_html($listActiveDomains->data->max_domain); ?></span></p>
                        <p class="lnd-p">Data de entrada <span class="lnd-span"><?php echo esc_html($date); ?></span></p>
                    </div>
                    <div class="lnd-col">
                        <button class="lnd-button-card" data-lnd_get_license="<?php echo $licenses ?>">Abrir</button>
                    </div>
                </div>
                <div class="lnd-row" id="lnd-row-table">
                    <?php echo $this->lnd_table_domains($licenses, $list) ?>
                </div>

            </div>

        <?php
            $this->lndModalDeactivate();
        }
    }

    public function lnd_table_domains($licenses, $list)
    {
        ?>
        <section class="lnd-section" id="lnd-section-<?php echo $licenses ?>">
            <!--for demo wrap-->
            <h5 class="lnd-h5">Lista de dominios ativos</h5>
            <div class="tbl-header">
                <table class="lnd-table">
                    <thead>
                        <tr>
                            <th style="width:10%">#</th>
                            <th style="width:70%">Dominio</th>
                            <th style="width:20%">Desativar</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="tbl-content">
                <table class="lnd-tbl" cellpadding="0" cellspacing="0" border="0">
                    <tbody class="lnd-tblbody">
                        <?php echo $this->lnd_list_tr($list, $licenses) ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php
    }

    function lnd_list_tr($list, $license)
    {
        if (empty($list)) {
        ?>
            <tr class="lnd-not-domains">
                <td style="width:100%"><?php echo 'Nenhum domínio ativo'; ?></td>
            </tr>
        <?php
        }
        $totalDomains = '0';
        foreach ($list as $domains) {
            $totalDomains++;
        ?>
            <tr id="<?php echo 'lnd-' . $domains ?>">
                <td style="width:7%"><?php echo $totalDomains ?></td>
                <td style="width:73%"><?php echo $domains ?></td>
                <td style="width:20%">
                    <button class="lnd-button-remove" data-lnd_button_remove_domain="<?php echo $domains ?>" data-lnd-license_key="<?php echo $license ?>">
                        <span class="dashicons dashicons-trash"></span>Remover
                    </button>
                </td>
            </tr>
        <?php
        }
    }

    /**
     * LND Adiciona endpoint
     */
    public function lnd_add_endpoint()
    {
        add_rewrite_endpoint('lnd-controle-license', EP_ROOT | EP_PAGES);
    }

    /**
     * Flush the rewrite rules if needed.
     *
     * @since 0.3.0
     */
    public function lnd_custon_endpoint_flush_rewrite_rules()
    {
        if (is_network_admin() || 'no' === get_option('lnd_custon_endpoint_flush_rewrite_rules')) {
            return;
        }
        update_option('lnd_custon_endpoint_flush_rewrite_rules', 'no');
        flush_rewrite_rules();
        return;
    }

    function lndModalDeactivate()
    {
    ?>
        <!-- Modal LND -->
        <div class="container-lnd">
            <div class="cookiesContent" id="cookiesPopup">
                <div class="content-lnd" id="content-lnd">
                    <img class="image-modal-lnd" src="https://lojanegociosdigital.com.br/wp-content/uploads/2022/12/x.png" alt="desativar-lnd-img" />
                    <p>Quer realmente desativar esse dominio?</p>
                    <div class="lnd-desativate-domain-p"></div>
                </div>
                <div class="modal-buttons" id="">
                    <button class="button-lnd-close">Fechar</button>
                    <button class="button-lnd-deactivate">Desativar</button>
                </div>
            </div>

        </div>
        <!-- FIM MODAL LND -->
<?php
    }
}
