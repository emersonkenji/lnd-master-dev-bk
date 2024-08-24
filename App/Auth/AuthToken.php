<?php

namespace App\Auth;

use App\Request\Authentication\Authetication;

class AuthToken
{
    public $plugin_file = __FILE__;
    public $responseObj;
    public $licenseMessage;
    public $showMessage = false;
    function __construct()
    {
      
        $licenseKey = get_option("lnd_master_dev_key", "");
        $liceEmail = get_option("LNDMasterDevPlugin_lic_email", "");
        Authetication::addOnDelete(function () {
            delete_option("lnd_master_dev_key");
        });
      

        if (Authetication::CheckWPPlugin($licenseKey, $liceEmail, $this->licenseMessage, $this->responseObj, __FILE__)) {
            add_action('admin_menu', [$this, 'ActiveAdminMenu'], 99999);
            add_action('admin_post_LNDMasterDevPlugin_el_deactivate_license', [$this, 'action_deactivate_license']);
        } else {
            if (!empty($licenseKey) && !empty($this->licenseMessage)) {
                $this->showMessage = true;
            }
            update_option("lnd_master_dev_key", "") || add_option("lnd_master_dev_key", "");         
            add_action('admin_post_lnd_master_dev_activate_license', [$this, 'action_activate_license']);
            // add_action('wp_ajax_action_activate_license', [$this, 'action_activate_license']);
            add_action('admin_menu', [$this, 'InactiveMenu']);
        }
    }

    function SetAdminStyle()
    {
        wp_register_style("lnd-license-style-lic", plugins_url("/assets/css/style.min.css", MASTER_LND_BASE_FILE), 10);
        wp_enqueue_style("lnd-license-style-lic");
    }

    function ActiveAdminMenu()
    {
        //add_menu_page (  "LndAutoUpdate", "Lnd Auto Update", "activate_plugins", MASTER_LND_BASE, [$this,"Activated"], "dashicons-lock");
        $menu = add_submenu_page(MASTER_LND_SLUG, "LNDMasterDevPlugin License", "License Info", "activate_plugins",  MASTER_LND_SLUG . "_license", [$this, "Activated"]);
        add_action('admin_print_styles-' .  $menu, [$this, 'SetAdminStyle']);
    }

    function InactiveMenu()
    {
        $menu = add_submenu_page(MASTER_LND_SLUG, "LNDMasterDevPlugin License", "License Info", "activate_plugins",  MASTER_LND_SLUG . "_license", [$this, "LicenseForm"]);
        add_action('admin_print_styles-' .  $menu, [$this, 'SetAdminStyle']);
    }

    function action_activate_license()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'lnd_master_dev_activate_license') {
            //check_admin_referer('lnd_master_dev_license');
            $licenseKey = !empty($_POST['lnd_master_dev_license_key']) ? $_POST['lnd_master_dev_license_key'] : "";
           	update_option("lnd_master_dev_key", $licenseKey) ;
            update_option("LNDMasterDevPlugin_lic_email", get_bloginfo('admin_email')) || add_option("LNDMasterDevPlugin_lic_email", get_bloginfo('admin_email'));
            update_option('_site_transient_update_plugins', '');
            wp_safe_redirect(site_url($_POST['_wp_http_referer']));
        }
        // wp_safe_redirect(admin_url('admin.php?page=' . MASTER_LND_BASE . '_license'));
    }
    // function action_activate_license()
    // // {
    // //     // check_admin_referer('el-license');
    //     $licenseKey = !empty($_POST['el_license_key']) ? $_POST['el_license_key'] : "";
    //     $licenseEmail = !empty($_POST['el_license_email']) ? $_POST['el_license_email'] : "";
    //     update_option("lnd_master_dev_key", $licenseKey) || add_option("lnd_master_dev_key", $licenseKey);
    //     update_option("LNDMasterDevPlugin_lic_email", $licenseEmail) || add_option("LNDMasterDevPlugin_lic_email", $licenseEmail);
    //     update_option('_site_transient_update_plugins', '');
    //     wp_safe_redirect(admin_url('admin.php?page=' . MASTER_LND_BASE . '_license'));
    // }


    function action_deactivate_license()
    {
        check_admin_referer('el-license');
        $message = "";
        if (Authetication::RemoveLicenseKey(__FILE__, $message)) {
            update_option("lnd_master_dev_key", "") || add_option("lnd_master_dev_key", "");
            update_option('_site_transient_update_plugins', '');
        }
        wp_safe_redirect(admin_url('admin.php?page=' . MASTER_LND_SLUG));
    }

    function Activated()
    {
?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="LNDMasterDevPlugin_el_deactivate_license" />
            <div class="el-license-container">
                <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("LND Master Dev Plugin License Info", 'lnd-master-dev'); ?> </h3>
                <hr>
                <ul class="el-license-info">
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("Status", 'lnd-master-dev'); ?></span>

                            <?php if ($this->responseObj->is_valid) : ?>
                                <span class="el-license-valid"><?php _e("Valid", 'lnd-master-dev'); ?></span>
                            <?php else : ?>
                                <span class="el-license-valid"><?php _e("Invalid", 'lnd-master-dev'); ?></span>
                            <?php endif; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Type", 'lnd-master-dev'); ?></span>
                            <?php echo $this->responseObj->license_title; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Expired on", 'lnd-master-dev'); ?></span>
                            <?php echo $this->responseObj->expire_date;
                            if (!empty($this->responseObj->expire_renew_link)) {
                            ?>
                                <a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->expire_renew_link; ?>">Renew</a>
                            <?php
                            }
                            ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("Support Expired on", 'lnd-master-dev'); ?></span>
                            <?php
                            echo $this->responseObj->support_end;
                            if (!empty($this->responseObj->support_renew_link)) {
                            ?>
                                <a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->support_renew_link; ?>">Renew</a>
                            <?php
                            }
                            ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("Your License Key", 'lnd-master-dev'); ?></span>
                            <span class="el-license-key"><?php echo esc_attr(substr($this->responseObj->license_key, 0, 9) . "XXXXXXXX-XXXXXXXX" . substr($this->responseObj->license_key, -9)); ?></span>
                        </div>
                    </li>
                </ul>
                <div class="el-license-active-btn">
                    <?php wp_nonce_field('el-license'); ?>
                    <?php submit_button('Deactivate'); ?>
                </div>
            </div>
        </form>
    <?php
    }

    function LicenseForm()
    {
    ?>
        <?php echo $this->showMessage ?>
        <form method="POST" action="admin-post.php?action=lnd_master_dev_activate_license" id="lnd_master_dev_activate_license">
            <input type="hidden" name="action" value="lnd_master_dev_activate_license" />
            <div class="lnd-master-dev-license-container">
                <h3 class="lnd-master-dev-license-title"><i class="dashicons-before dashicons-star-filled"></i> Ativação da Licença do LND Master Dev Plugin</h3>
                <hr>
                <?php if (!empty($this->showMessage) && !empty($this->licenseMessage)) { ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo $this->licenseMessage; ?></p>
                    </div>
                <?php } ?>
                <p>Digite sua chave de licença aqui para ativar o produto e obter atualizações completas e suporte premium.</p>
                <div class="lnd-master-dev-license-field">
                    <label for="lnd_master_dev_license_key">Código de licença</label>
                    <input type="text" class="regular-text code" name="lnd_master_dev_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
                </div>
                <div class="lnd-master-dev-license-active-btn">
                    <?php wp_nonce_field('lnd_master_dev_license'); ?>
                    <input type="submit" value="Ativar" class="button button-primary">
                </div>
            </div>
        </form>
        <div class="pricing-plans-container">
            <h3 class="pricing-plans-title">Nossos Planos</h3>
            <hr>
            <div class="pricing-plan">
                <h4 class="plan-name">Plano Básico</h4>
                <p class="plan-price">R$9,99/mês</p>
                <ul class="plan-features">
                    <li>Recurso 1</li>
                    <li>Recurso 2</li>
                    <li>Recurso 3</li>
                </ul>
                <button class="plan-buy-btn">Comprar</button>
            </div>
            <div class="pricing-plan">
                <h4 class="plan-name">Plano Intermediário</h4>
                <p class="plan-price">R$19,99/mês</p>
                <ul class="plan-features">
                    <li>Recurso 1</li>
                    <li>Recurso 2</li>
                    <li>Recurso 3</li>
                    <li>Recurso 4</li>
                    <li>Recurso 5</li>
                </ul>
                <button class="plan-buy-btn">Comprar</button>
            </div>
            <div class="pricing-plan">
                <h4 class="plan-name">Plano Avançado</h4>
                <p class="plan-price">R$29,99/mês</p>
                <ul class="plan-features">
                    <li>Recurso 1</li>
                    <li>Recurso 2</li>
                    <li>Recurso 3</li>
                    <li>Recurso 4</li>
                    <li>Recurso 5</li>
                    <li>Recurso 6</li>
                    <li>Recurso 7</li>
                </ul>
                <button class="plan-buy-btn">Comprar</button>
            </div>
        </div>

<?php
    }
}
