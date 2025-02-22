<?php 

namespace App\Utils\Module;

class Pagination{
    public Static function init()
    {
        // add_action('lnd_pagination_ajax', array(__CLASS__, 'lnd_pagination_ajax'),10, 3);
    }

    public static function lnd_pagination_ajax($page, $total_data, $limit)
    {
        /**
         * Inicio da paginação
         */
        $total_links = ceil($total_data / $limit);
        $previous_link = '';
        $next_link = '';
        $page_link = '';
        $output = '<div class="row"><div class="col-3 align-self-end"><p class="justify-content-start fs-6 fw-bolder">Total: ' . $total_data . ' </p></div><div class="mt-2 col"><ul class="pagination justify-content-end">';

        if ($total_links > 4) {
            if ($page < 5) {
                for ($count = 1; $count <= 5; $count++) {
                    $page_array[] = $count;
                }
                $page_array[] = '...';
                $page_array[] = $total_links;
            } else {
                $end_limit = $total_links - 5;
                if ($page > $end_limit) {
                    $page_array[] = 1;
                    $page_array[] = '...';
                    for ($count = $end_limit; $count <= $total_links; $count++) {
                        $page_array[] = $count;
                    }
                } else {
                    $page_array[] = 1;
                    $page_array[] = '...';
                    for ($count = $page - 1; $count <= $page + 1; $count++) {
                        $page_array[] = $count;
                    }
                    $page_array[] = '...';
                    $page_array[] = $total_links;
                }
            }
        } else {
            for ($count = 1; $count <= $total_links; $count++) {
                $page_array[] = $count;
            }
        }

        for ($count = 0; $count < count($page_array); $count++) {
            if ($page == $page_array[$count]) {
                $page_link .= '<li class="page-item disabled" ><a class="page-link" href="#" id="page-number" data-page_active="' . $page_array[$count] . '" >' . $page_array[$count] . ' <span class="sr-only"></span></a></li>';

                $previous_id = $page_array[$count] - 1;
                if ($previous_id > 0) {
                    $previous_link = '<li class="page-item active"><a class="page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
                } else {
                    $previous_link = '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';
                }
                $next_id = $page_array[$count] + 1;
                if ($next_id >= $total_links) {
                    $next_link = '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
                } else {
                    $next_link = '<li class="page-item active"><a class="page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
                }
            } else {
                if ($page_array[$count] == '...') {
                    $page_link .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                } else {
                    $page_link .= '<li class="page-item active"><a class="page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '" >' . $page_array[$count] . '</a></li>';
                }
            }
        }

        $output .= $previous_link . $page_link . $next_link;
        $output .= '</ul></div></div>';

        return $output;
    }
    
}