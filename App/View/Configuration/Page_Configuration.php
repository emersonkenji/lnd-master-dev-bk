<?php

namespace App\View\Configuration;

use App\View\LND_View_Settings;

class Page_Configuration
{

    public static function init()
    {
        LND_View_Settings::get_header_pages();
        Self::page_configuration();
    }

    public static function page_configuration()
    {
        $link = get_site_url() . '/lnd-plataforma/';
        // echo __('Configuration', 'lnd_master_dev');
        Self::setting_card();

?>
        <!-- <div class="container"> -->
        <!-- <div class="p-0 text-center card">
                <div class="card-header"> Configurações basicas</div>
                <div class="card-body">
                    <h5 class="card-title">ShortCode de acesso plataforma</h5>
                    <p class="card-text"> [lnd_master_plataforma] </p>
                    <a href="<?php #echo $link 
                                ?>" class="btn btn-primary">Link de acesso a plataforma</a>
                </div>
                <div class="card-footer text-muted">LND Master Developer</div>
            </div> -->
        <!-- </div> -->
    <?php
    }

    public static function setting_card()
    {
    ?>
        <div class="lnd-container">
            <div class="container-fluid">
                <div class="card-lnd">
                    <div class="lnd-card-header">
                        <?php echo __('Configuration', 'lnd_master_dev'); ?>
                    </div>
                    <div class="lnd-card-body">
                        <div class="row d-flex">

                            <div class="col">
                                <div class="p-3 card border-secondary-subtle rounded-1">
                                    <form method="post" class="row needs-validation" name="form-conf" action="<?php echo esc_html(get_site_url() . '/wp-admin/admin.php?page=lnd-master-dev-conf'); ?>" >

                                        <!-- <div class="form-row">
                                            <label for="text-input">Campo de Texto:</label>
                                            <input type="text" id="text-input" name="text-input">
                                        </div>

                                        <div class="form-row">
                                            <label for="radio-input" class="col-sm-4 col-form-label">Campo de Rádio:</label>
                                            <input type="radio" id="radio-input-1" name="radio-input" value="option1">
                                            <label for="radio-input-1" class="col-sm-8 col-form-label">Opção 1</label>
                                            <input type="radio" id="radio-input-2" name="radio-input" value="option2">
                                            <label for="radio-input-2" class="col-sm-8 col-form-label">Opção 2</label>
                                        </div>

                                        <div class="form-row">
                                            <label for="checkbox-input">Campo de Caixa de Seleção:</label>
                                            <input type="checkbox" id="checkbox-input" name="checkbox-input">
                                        </div> -->

                                        <div class="mb-3 row">
                                            <label for="inputTextName" class="col-sm-4 col-form-label">Ecolha um titulo</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm" name="lnd-input-text" id="inputTextName" required>
                                            </div>
                                        </div>



                                        <fieldset class="mb-3 row">
                                            <legend class="pt-0 col-form-label col-sm-4">Radios</legend>
                                            <div class="col-sm-8">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="lnd-grid-radios" id="gridRadios1" value="option1" checked>
                                                    <label class="form-check-label" for="gridRadios1">
                                                        First radio
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="lnd-grid-radios" id="gridRadios2" value="option2">
                                                    <label class="form-check-label" for="gridRadios2">
                                                        Second radio
                                                    </label>
                                                </div>
                                                <div class="form-check disabled">
                                                    <input class="form-check-input" type="radio" name="lnd-grid-radios" id="gridRadios3" value="option3" disabled>
                                                    <label class="form-check-label" for="gridRadios3">
                                                        Third disabled radio
                                                    </label>
                                                </div>
                                            </div>
                                        </fieldset>


                                        <div class="mb-3 row">
                                            <div class="col-sm-8 offset-sm-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="lnd-input-checkbox" id="gridCheck1">
                                                    <label class="form-check-label" for="gridCheck1">Example checkbox</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fld-installed-type fld-installed-type-r">
                                                <div class="form-group form-group-sm row">
                                                    <label for="el_end_point" class="col-sm-4 col-form-label sm-text-right">"Elite Licenser API End Point"</label>
                                                    <div class="col-sm">
                                                        <input type="text" name="el_end_point" class="form-control form-control-sm" id="el_end_point" value="<?php // esc_html($this->GetOption("el_end_point", "")); ?>" placeholder="<?php __("Elite Licenser API End Point"); ?>" data-bv-notempty="true" data-bv-notempty-message="<?php __("%s is required", "Elite Licenser API End Point"); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                        <button type="submit" class="btn btn-primary">Salvar</button>
                                    </form>


                                    <!-- <div class="card-header">
                                        <?php //echo __('Configuration', 'lnd_master_dev'); 
                                        ?>
                                    </div>
                                    <div class="card-body">
                                        
                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div> -->
                                </div>
                            </div>

                            <div class="col">
                                <div class="p-0 card border-secondary-subtle rounded-1">
                                    <div class="card-header">
                                        <?php echo __('Configuration', 'lnd_master_dev'); ?>
                                    </div>
                                    <div class="card-body">

                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
