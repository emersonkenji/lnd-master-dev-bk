<?php

namespace App\View\Configuration;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


use WP_List_Table;

class Page_Master_Downloads extends WP_List_Table
{

    private $table_data;

    // Definir as colunas da tabela
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'item_name' => __('Name', 'lnd-master-dev'),
            // 'internal_downloads' => __('Downloads', 'lnd-master-dev'),
            'filepath' => __('Filepath', 'lnd-master-dev'),
            'update_date' => __('Atualizado', 'lnd-master-dev'),
            'type' => __('Tipo', 'lnd-master-dev'),
            'version' => __('Versão', 'lnd-master-dev'),
            'status' => __('Status', 'lnd-master-dev'),
            'data' => __('Adicionado', 'lnd-master-dev')
        );
        return $columns;
    }
    // Definir as colunas da tabela
    public function prepare_items()
    {
        //data
        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();

        $hidden = (is_array(get_user_meta(get_current_user_id(), 'lnd-master_page_lnd-master-dev-downloads', true))) ? get_user_meta(get_current_user_id(), 'lnd-master_page_lnd-master-dev-downloads', true) : array();

        $sortable = $this->get_sortable_columns();

        $primary  = 'item_name';

        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        // usort($this->table_data, array(__CLASS__, 'usort_reorder'));

        /* pagination */
        $per_page = $this->get_items_per_page('elements_per_page', 30);
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil($total_items / $per_page) // use ceil to round up
        ));

        $this->items = $this->table_data;
    }

    private function get_table_data($search = '')
    {
        global $wpdb;
        $tabela_lnd = $wpdb->prefix . 'lnd_items_tbl';


        if (!empty($search)) {
            $query = "SELECT * FROM {$tabela_lnd} WHERE item_name Like '%{$search}%' OR filepath Like '%{$search}%' OR description Like '%{$search}%' OR category Like '%{$search}%'";
        } else {
            $query = "SELECT * FROM $tabela_lnd";
        }

        $resultados = $wpdb->get_results($query, ARRAY_A);

        if ($resultados) {
            return $resultados;
        } else {
            return false;
        }
    }

    public function column_default($item, $column_name)
    {
        // switch ($column_name) {
        //     case 'id':
        //     // case 'downlads':
        //     case 'item_name':
        //     // case 'filepath':
        //     case 'type':
        //     case 'version':
        //     // case 'status':
        //     case 'update_date':
        //     case 'data':
        //     default:
        return $item[$column_name];
        // }
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['id']
        );
    }

    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'item_name'  => array('item_name', false),
            'type'  => array('type', false),
            // 'downloads'  => array('downloads', false),
            'status' => array('status', false),
            'update_date'   => array('update_date', false),
            'data'   => array('data', false)
        );
        return $sortable_columns;
    }

    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'item_name';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Adding action links to column
    public function column_item_name($item)
    {
        $actions = array(
            'downloads'    => sprintf('<a href="%s">' . __('Downloads', 'lnd-master-dev') . '</a>', $item['internal_downloads']),
        );

        return sprintf('%1$s %2$s', $item['item_name'], $this->row_actions($actions));
    }

    // To show bulk action dropdown
    public function get_bulk_actions()
    {
        $actions = array(
            'delete_all'    => __('Delete', 'lnd-master-dev'),
            'draft_all' => __('Move to Draft', 'lnd-master-dev'),
            'publish_all' => __('Publish All', 'lnd-master-dev'),
            'Create Product' => array(
                'create_product_all' => __('products downloads', 'lnd-master-dev'),
                'create_product_multiple' => __('products multiple downloads', 'lnd-master-dev'),
            )
        );
        return $actions;
    }

    public function process_bulk_action()
    {
        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

            $nonce  = $_POST['_wpnonce'];
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die('Nope! Security check failed!');
        }

        $action = $this->current_action();

        switch ($action) {

            case 'delete':
                wp_die('Delete something');
                break;

            case 'save':
                wp_die('Save something');
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }

    public function display()
    {
        if ('create_product_multiple' === $this->current_action()) {
            $ids = isset($_POST['element']) ? $_POST['element'] : array();
            // exibir formulário para inserir imagem em massa
            ?>
            <div class="wrap">
                <h2>Inseris Produto Multiplos downloads</h2>
                <form id="image_form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="elements[]" value="<?php echo implode(',', $ids); ?>" />
                    <input type="hidden" name="elements-ids[]" value="" />
                    <input type="hidden" name="action" value="form_create_product_multiple" />
                    <p id="p_create_product_multiple"></p>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Inserir Multiplos dowloads" />
                    </p>
                </form>
            </div>

            <?php
            parent::display();
        } else {
            // exibir tabela normalmente
            parent::display();
        }
    }
}
