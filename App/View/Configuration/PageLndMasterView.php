<?php 

namespace App\View\Configuration;

use App\View\LND_View_Settings;

class PageLndMasterView
{
    public function __construct()
    {
        $this->get_div_react();
    }

    public function get_div_react( )
    {
        LND_View_Settings::get_view_header() 
        ?>
         <div class='react-plugin'></div>
        <?php
    }
}