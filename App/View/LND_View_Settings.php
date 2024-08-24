<?php

namespace App\View;

class LND_View_Settings
{
    public static function get_view_header()
    {
    ?>
        <!-- Header -->
        <div class="lnd-header">
            <div class="row ">
                <div class="col-auto text-start align-self-center">
                    <img class="img-fluid me-2" width="230" role="img" src="https://planos.lojanegociosdigital.com.br/wp-content/uploads/2022/06/logo-lnd-library-sem-fundo.png">

                </div>
    
            </div>
        </div>
        <!--  -->
    <?php
    }
    public static function get_header_pages()
    {
    ?>
        <!-- Header -->
        <div class="lnd-header">
            <div class="row ">
                <div class="col-auto text-start align-self-center">
                    <img class="img-fluid me-2" width="230" role="img" src="https://planos.lojanegociosdigital.com.br/wp-content/uploads/2022/06/logo-lnd-library-sem-fundo.png">
                </div>
            </div>
        </div>
        <!--  -->
    <?php
    }

    public static function get_view_header_license()
    {
    ?>
        <div class="row ">
            <div class="col-auto text-start align-self-center">

                <img class="img-fluid me-2" width="230" role="img" src="https://planos.lojanegociosdigital.com.br/wp-content/uploads/2022/06/logo-lnd-library-sem-fundo.png">

            </div>
            <div class="col text-end align-self-center">
                <a type="button" href="https://lojanegociosdigital.com.br/minha-conta/" class="btn_my_account" target="_blank">
                    <?php echo __('My Account', 'lnd-master-dev') ?>
                    <a>
                        <a type="button" href="https://lojanegociosdigital.com.br/lnd-auto-update/" class="btn_ver_mais" target="_blank">
                            <?php echo __('View More', 'lnd-master-dev') ?>
                            <a>
            </div>
        </div>
    <?php
    }

    public static function get_view_options($count_all, $count_installed, $type, $search)
    {
    ?>
        <div class="px-2 py-3 pe-3 bg-dark text-white rounded-1">
            <div class="row align-items-start ">
                <div class="col">
                    <a href="?page=lnd-master-dev&tab=<?php echo $type ?>" type="button" class="btn_box"><?php echo __('All ', 'lnd-master-dev') ?><span class="badge bg-danger text-bg-light "><?= $count_all ?></span></a>
                    <a href="?page=lnd-master-dev&tab=<?php echo $type ?>&installed-<?php echo $search ?>=instalados" type="button" class="btn_box"><?php echo __('Installed ', 'lnd-master-dev') ?><span class="badge bg-danger text-bg-light "><?= $count_installed ?></span>
                    </a>
                </div>
                <div class="col align-items-center ">
                    <form id="plugins-form" method="POST">
                        <p class="search-box ">
                            <input type="search" name="seach_<?php echo $search ?>" class="form-control form-control-dark text-white bg-dark m-1" placeholder="<?php echo __('Search...', 'lnd-master-dev') ?>" aria-label="Search">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }

    public static function get_view_tabs($tab)
    {
    ?>
        <div class="wrap">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=lnd-master-dev&tab=about')); ?>" class="<?php if ($tab == "about") {
                                                                                                                        echo "btn_tabs_active";
                                                                                                                    } else {
                                                                                                                        echo "btn_tabs";
                                                                                                                    }; ?>"><?php echo __('About', 'lnd-master-dev'); ?></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=lnd-master-dev&tab=plugins')); ?>" class="<?php if ($tab == "plugins") {
                                                                                                                        echo "btn_tabs_active";
                                                                                                                    } else {
                                                                                                                        echo "btn_tabs";
                                                                                                                    }; ?>"><?php echo __('Plugins', 'lnd-master-dev'); ?></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=lnd-master-dev&tab=themes')); ?>" class="<?php if ($tab == "themes") {
                                                                                                                        echo "btn_tabs_active";
                                                                                                                    } else {
                                                                                                                        echo "btn_tabs";
                                                                                                                    }; ?>"><?php echo __('Themes', 'lnd-master-dev'); ?></a>
                </li>
            </ul>
        </div>
    <?php
    }
    public static function lnd_modal($tab)
    {
    ?>
        <!-- Modal -->
        <div class="modal fade" id="deactivate_downloads" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- </nav> -->
<?php
    }
}
