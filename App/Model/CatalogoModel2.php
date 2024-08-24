php

namespace App\Model;

use App\Database as AppDatabase;

if (!defined('ABSPATH')) {
    exit();
}

class CatalogoModel2
{
    private $wpdb;
    private $table_name;
    private $limit = 30;
    private $page = 1;
    private $order = 'update_date';
    private $order_by = 'DESC';
    private $type = null;
    private $query = null;
    private $category = null;
    private $filter = null;
    private $plans = null;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = AppDatabase::get_table();
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function setOrder($order)
    {
        $this->order = !empty($order) && $order !== '' ? $order :  $this->order;
    }

    public function setOrderBy($order_by)
    {
        $this->order_by = !empty($order_by) && $order_by !== '' ? $order_by :  $this->order_by;
    }

    public function setType($type = null)
    {
        $this->type = $type;
    }

    /**
     * Campo search
     *
     * @param string $query
     * @return void
     */
    public function setQuery($query = null)
    {
        $this->query = $query;
    }

    public function setCategory($category = null)
    {
        $this->category = $category;
    }

    public function setFilter($filter = null)
    {
        $this->filter = $filter;
    }
    public function setPlans($plans = null)
    {
        $this->plans = $plans;
    }

    public function executeQuery()
    {
        $start = ($this->page - 1) * intval( $this->limit);
        $query = "SELECT * FROM " . $this->table_name . " ";
        $queryNum = "SELECT COUNT(*) FROM " . $this->table_name . " ";
        $query .= " WHERE status = 'publish'";
        $queryNum .= " WHERE status = 'publish'";

        if ($this->type !== null) {
            $type = $this->type;
            $query .= " AND type = '$type' ";
            $queryNum .= " AND type = '$type'";
        }

         if ($this->plans !== null && $this->plans != '' && $this->plans !='library' ) {
            $plans = $this->plans;
            $query .= " AND instance REGEXP '$plans' ";
            $queryNum .= " AND instance REGEXP '$plans'";
        }

        if ($this->query !== null && $this->category !== null) {
            $p = sanitize_text_field($this->query);
            $c = sanitize_text_field($this->category);
            $query .= "AND category = '$c'";
            $query .=  "AND (item_name LIKE '%$p%' OR filepath LIKE '%$p%' )";
        }

        if ($this->query !== null) {
            $p = sanitize_text_field($this->query);
            $query .= " AND (item_name LIKE '%$p%' OR filepath LIKE '%$p%' OR category LIKE '%$p%') ";
            $queryNum .= " AND (item_name LIKE '%$p%' OR filepath LIKE '%$p%' OR category LIKE '%$p%') ";
        }

        if ($this->category !== null) {
            $query .= 'AND category LIKE "%' . str_replace(' ', '%', $this->category) . '%" ';
            $queryNum .= 'AND category LIKE "%' . str_replace(' ', '%', $this->category) . '%" ';
        }

        if ($this->filter !== null && $this->filter == 'installed') {
            $installed_plugins = array_keys(get_plugins());
            $installed_wp_themes = wp_get_themes();
            $installed_themes = array();
            foreach ($installed_wp_themes as $theme_slug => $theme_infos) {
                $installed_themes[] = $theme_slug;
                $installed_themes[] = $theme_slug . '/style.css';
            }
            $installed_products = array_merge($installed_themes, $installed_plugins);
            $query .= " AND filepath IN ('" . implode("','", $installed_products) . "') AND status = 'publish'";
            $queryNum .= " AND filepath IN ('" . implode("','", $installed_products) . "')AND status = 'publish'";
        }

        if ($this->filter !== null && $this->filter == 'free') {
            $query .= "AND is_free = '1'";
            $queryNum .= "AND is_free = '1'";
        }

        $query .= "ORDER BY $this->order $this->order_by ";
        $queryNum .= "ORDER BY $this->order $this->order_by ";
        $filter_query = $query . 'LIMIT ' . $start . ', ' . $this->limit . '';
        $total_data = $this->wpdb->get_var($queryNum);
        $result = $this->wpdb->get_results($filter_query);

        $dataResult = [
            'total' => intval($total_data),
            'page' => intval($this->page),
            'limit' => intval($this->limit),
            'result' => $result
        ];
        
        return $dataResult;
    }
}
