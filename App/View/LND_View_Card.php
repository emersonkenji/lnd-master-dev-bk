<?php

namespace App\View;

class LND_View_Card
{
    public static function lnd_view_card($args)
    {
        $options = array(
            'img'           => '',
            'version'       => '',
            'update_date'   => '',
            'new'           => '',
            'item_name'     => '',
            'description'   => '',
            'demo'          => '',
            'button'        => '',
            'btn_downloads' => '',
            'path'          => '',
            'type'          => ''
        );
        $args = wp_parse_args($args, $options);
        $lnd_type = ucwords($args['type']);

        $data = date('d/m/Y', strtotime($args['update_date']));

            $itens = '
            <div class="col" id="lnd-div">
                <div class="card h-100 shadow p-1 m-1 lnd-card-grid">
                    <img src="'. $args['img'] .'" width="260" height="132" class="card-img-top rounded-1" alt="' . $args['item_name'] . '">
                        <span class="position-absolute  start-0 p-1 mt-2 ms-4 translate-middle badge" style="background: red; border-radius:3px; ">' . __( $lnd_type , 'lnd-master-dev') . '</span>
                        '.__( $args['new'] , 'lnd-master-dev') .'
                    </img>
                    
                    <div class="card-body p-1">
                    '. $args['btn_downloads'] .'
                        '.$args['version'].' 
                        <p class="h6"><em class="text-white"><small>Atualizado: </em><em class="text-white">' . $data . '</em></small></p>
                        <p class="card-title text-white"><strong>' . $args['item_name'] .'</strong> </p>
                        <p class="lnd-description text-white">'. strip_tags($args['description']) .' - <a href="'. $args['demo'] .'" target="_blank">'. __('Demonstration', 'lnd-master-dev') .'</a></p>
                    </div
                    <div class=" lnd-footer-card-' . $args['path'] . '">
                    '. $args['button'] .'
                        
                    </div>
                </div>
            </div>';

        return $itens;

    }

    public static function lnd_view_card_plataforma($args)
    {
        $options = array(
            'img'           => '',
            'version'       => '',
            'update_date'   => '',
            'new'           => '',
            'item_name'     => '',
            'description'   => '',
            'demo'          => '',
            'button'        => '',
            'path'          => '',
            'type'          => ''
        );
        $args = wp_parse_args($args, $options);
        $lnd_type = ucwords($args['type']);

        $data = date('d/m/Y', strtotime($args['update_date']));

            $itens = '
            <div class="col" id="lnd-div">
                    <div class="card h-100 shadow p-1 m-1 lnd-card-grid">
                        <img src="'. $args['img'] .'" width="260" height="132" class="card-img-top rounded-1" alt="' . $args['item_name'] . '">
                            <span class="position-absolute  start-0 p-1 mt-2 ms-4 translate-middle badge" style="background: red; border-radius:3px; ">' . __( $lnd_type , 'lnd-master-dev') . '</span>
                            '.__( $args['new'] , 'lnd-master-dev') .'
                        </img>
                        
                        <div class="card-body p-1">
                            '.$args['version'].' 
                            <p class="h6"><em class="text-white"><small>Atualizado: </em><em class="text-white">' . $data . '</em></small></p>
                            <p class="card-title text-white"><strong>' . $args['item_name'] .'</strong> </p>
                            <p class="lnd-description text-white">'. strip_tags($args['description']) .' - <a href="'. $args['demo'] .'" target="_blank">'. __('Demonstration', 'lnd-master-dev') .'</a></p>
                        </div>

                        <div class=" lnd-footer-card-' . $args['path'] . '">
                        '. $args['button'] .'
                            
                        </div>
                    </div>
                </div>';

        return $itens;

    }
}

