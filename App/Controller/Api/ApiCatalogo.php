php 

namespace App\Controller\Api;


use App\Model\CatalogoModel;
use App\Utils\Module\Cards;
use App\Utils\WooCommerce\User;
use App\Utils\WooCommerce\WooMembership;
use App\Utils\WooCommerce\WooSubscription;

class ApiCatalogo
{
    public function __construct() {
        add_action('wp_ajax_get_catalogo', array($this, 'get_catalogo'));
        add_action('wp_ajax_nopriv_get_catalogo', array($this, 'get_catalogo'));

        add_action('wp_ajax_get_catalogo_dashboard', [$this, 'get_catalogo_dashboard']);
        add_action('wp_ajax_nopriv_get_catalogo_dashboard', [$this, 'get_catalogo_dashboard']);

        add_action('wp_ajax_get_data_user', [$this, 'get_data_user']);
        add_action('wp_ajax_nopriv_get_data_user', [$this, 'get_data_user']);

    }

    public function get_catalogo()
    {
        $queryBuilder  = new CatalogoModel;
        $queryBuilder->setLimit(!empty($_POST[ 'limit']) ? $_POST[ 'limit'] : 30);
        $queryBuilder->setPage(!empty($_POST[ 'page' ]) ? $_POST[ 'page' ] : 1 );
        $queryBuilder->setOrder(!empty($_POST['order']) ? $_POST['order'] : '');
        $queryBuilder->setOrderBy(!empty($_POST['order_by']) ? $_POST['order_by'] : '');
        $queryBuilder->setType(isset($_POST['type']) && $_POST['type'] !='' ? $_POST['type'] : null);
        $queryBuilder->setQuery(isset($_POST['query']) && $_POST['query'] != '' ? $_POST['query'] : null);
        $queryBuilder->setCategory(isset($_POST['category']) && $_POST['category']!= '' ? $_POST['category'] : null);
        $queryBuilder->setFilter(!empty($_POST['filter']) ? $_POST['filter'] : null);
        $result =  $queryBuilder->executeQuery();
        
        $result['processedResults'] = Cards::init($result['result']);
        
        $result['totalPages'] = ceil($result['total'] / $result['limit']);
        wp_send_json( $result );
    }

    public function get_catalogo_dashboard()
    {
        $category = get_option( 'lnd_library_get_options_select_category' );
        $queryBuilder  = new CatalogoModel;
        $queryBuilder->setLimit(!empty($_POST[ 'limit']) ? $_POST[ 'limit'] : 30);
        $queryBuilder->setPage(!empty($_POST[ 'page' ]) ? $_POST[ 'page' ] : 1 );
        $queryBuilder->setOrder(!empty($_POST['order']) ? $_POST['order'] : '');
        $queryBuilder->setOrderBy(!empty($_POST['order_by']) ? $_POST['order_by'] : '');
        $queryBuilder->setType(isset($_POST['type']) && $_POST['type'] !='' ? $_POST['type'] : null);
        $queryBuilder->setQuery(isset($_POST['query']) && $_POST['query'] != '' ? $_POST['query'] : null);
        $queryBuilder->setCategory(isset($_POST['category']) && $_POST['category']!= '' ? $_POST['category'] : null);
        $queryBuilder->setFilter(!empty($_POST['filter']) ? $_POST['filter'] : null);
        $queryBuilder->setPlans(!empty($_POST['plans']) ? $_POST['plans'] : null);
        $result =  $queryBuilder->executeQuery();
        
        $result['user'] = User::get_user_data();
        $result['totalPages'] = ceil($result['total'] / $result['limit']);
        $result['category'] = array_values($category);
        $result['plans'] = [
            'membership'   => WooMembership::get_memberships_data(), 
            'subscription' => WooSubscription::get_subscriptions_data()
        ];
        $result['plan'] = Self::get_access_plan();
        
        wp_send_json( $result );
    }

    public static function get_data_user()
    {
        $user = User::get_user_data();
        wp_send_json($user);
    }

    private static function get_access_plan() {
        $memberships = WooMembership::get_memberships_data();
        $subscriptions = WooSubscription::get_subscriptions_data();
        $activeMembers = [];
    
        // Definindo a prioridade dos planos
        $priority = [
            'lnd-library' => 5,
            'diamond' => 4,
            'profissional' => 3,
            'gold' => 2,
            'basic' => 1,
            17869 => 5, // Exemplo de produto de assinatura que dá acesso total
            15443 => 0  // Exemplo de produto de assinatura com acesso profissional
        ];
    
        // Adiciona os planos ativos à lista
        foreach ($memberships as $membership) {
            if ($membership['status'] === 'active') {
                $activeMembers[] = $membership['slug'];
            }
        }
    
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] === 'active') {
                $activeMembers[] = $subscription['product_id'];
            }
        }
    
        // Determina o maior plano disponível
        $highestPriority = 0;
        $highestPlan = null;
        foreach ($activeMembers as $member) {
            if (isset($priority[$member]) && $priority[$member] > $highestPriority) {
                $highestPriority = $priority[$member];
                // $highestPlan = $member;
                $highestPlan = $priority[$member];
            }
        }
    
        return $highestPlan;
    }
}