<?php
/*
use App\View\LND_View_Settings;
use App\Database;

$total_plugins = Database::lnd_count_itens('plugin');
$total_themes  = Database::lnd_count_itens('theme');
$lnd_library_options_category = get_option('lnd_library_get_options_select_category');
$lnd_category = '';
foreach ($lnd_library_options_category as $category) { 
    $lnd_category .= '<option value="' . $category . '">' . $category . '</option>'; 
}

LND_View_Settings::get_view_header() 
 
 ?>

<!--  -->

<!-- Spinner -->
<div class="ajax_load">
    <div class="ajax_load_box">
        <div class="ajax_load_box_circle"></div>
        <div class="ajax_load_box_title">Aguarde, carregando!</div>
    </div>
</div>
<!--  -->

<!-- Controles  -->
<div class="wrap" id="lnd-controller">
    <div class="row align-items-center">
        <div class="col-auto">
            <!-- grupo de botões todos, instalados, free -->
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <!-- All -->
                <input type="radio" class="btn-check " name="lnd-radio-control" id="lnd-radio-control-all" value="all" data-order_data="all" autocomplete="off" checked>
                <label class="btn btn-outline-primary lnd-radio" for="lnd-radio-control-all">
                    <i class="fa-solid fa-reply-all"></i>
                    <?php echo __('All', 'lnd-master-dev') ?>
                </label>
                <!-- Free -->
                <input type="radio" class="btn-check " name="lnd-radio-control" id="lnd-radio-control-free" value="free" data-order_data="free" autocomplete="off">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-radio-control-free">
                    <i class="fa-solid fa-certificate"></i>
                    <?php echo __('Free', 'lnd-master-dev') ?>
                </label>
                <!-- Installed -->
                <input type="radio" class="btn-check " name="lnd-radio-control" id="lnd-radio-control-intalled" value="installed" data-order_data="installed" autocomplete="off">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-radio-control-intalled">
                    <i class="fa-solid fa-desktop"></i>
                    <?php echo __('Installed', 'lnd-master-dev') ?>
                </label>
            </div>
            <!-- Grupo de botoes plugin e tema -->
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <!-- Botão plugins -->
                <input type="radio" class="btn-check" name="lnd-radio-type" id="lnd-radio-type-plugin" data-order_data="plugin" value="plugin" autocomplete="off">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-radio-type-plugin">
                    <i class="fa-solid fa-plug-circle-check"></i>
                    <?php echo __('Plugins ', 'lnd-master-dev') ?>
                    <span class="badge bg-secondary text-bg-dark "><?php echo $total_plugins ?></span>
                </label>
                <!-- botão temas -->
                <input type="radio" class="btn-check" name="lnd-radio-type" id="lnd-radio-type-theme" data-order_data="theme" value="theme" autocomplete="off">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-radio-type-theme">
                    <i class="fa-solid fa-newspaper"></i>
                    <?php echo __('Themes ', 'lnd-master-dev') ?>
                    <span class="badge bg-secondary text-bg-dark "><?php echo $total_themes ?></span>
                </label>
            </div>
            <!-- botão de reset filtros -->
            <button class="btn btn-outline-danger" id="lnd-reset-filters">
                <i class="fa-regular fa-circle-xmark"></i>
                <?php echo __('Reset filters', 'lnd-master-dev') ?>
            </button>
        </div>
        <!--  -->

        <div class="col justify-content-end text-end">
            <!-- Grupo de botões de filtro por update ou atualização -->
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check " name="lnd-radio-order" id="lnd-order-update" data-order_data="update_date" value="update_date" autocomplete="off" checked="">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-order-update">
                    <i class="fa-solid fa-calendar-check"></i>
                    <?php echo __('Update Date', 'lnd-master-dev') ?>
                </label>

                <input type="radio" class="btn-check" name="lnd-radio-order" id="lnd-order-name" data-order_data="item_name" value="item_name" autocomplete="off">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-order-name">
                    <i class="fa-solid fa-arrow-down-1-9"></i>
                    <?php echo __('Item Name', 'lnd-master-dev') ?>
                </label>

            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- exibir mensagens apos h1 -->
<h1></h1>
<!--  -->

<!-- filtros -->
<div class="wrap " id="lnd-controller">
    <div class="row g-3 align-items-center">
        <div class="col-auto text-start">
            <input class="lnd-input-search" id="lnd-search-box" placeholder="<?php echo __('Search...', 'lnd-master-dev') ?>">
        </div>
        <div class="col text-end">
            <div class="btn-group " role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check lnd-btn-group" name="lnd-radio-order-by" id="lnd-order-desc" data-order_data="desc" value="desc" autocomplete="off" checked="">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-order-desc">
                    <i class="fa-solid fa-arrow-up-z-a"></i>
                </label>
                <input type="radio" class="btn-check lnd-btn-group" name="lnd-radio-order-by" id="lnd-order-asc" data-order_data="asc" value="asc" autocomplete="off">
                <label class="btn btn-outline-primary lnd-radio" for="lnd-order-asc">
                    <i class="fa-solid fa-arrow-down-a-z"></i>
                </label>
            </div>
            <select class="lnd-select lnd-form-select-grid " id="lnd-form-select-grid" aria-label="Default select example">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30" selected>30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            <select class="lnd-select-category lnd-select" id="lnd-form-select-category" aria-label="Default select example">
                <option value="" selected><?php echo __('Todas Categorias', 'lnd-master-dev') ?></option>
                <?php echo $lnd_category ?>
            </select>
        </div>
        <h1></h1>

    </div>
</div>

<!-- Alertas -->
<div class="wrap" id="alert-menssage"></div>
<!--  -->

<!-- Card groups -->
<div class="wrap" id="lnd-post-grid"></div>
<!--  -->

*/