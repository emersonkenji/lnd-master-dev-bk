<?php

namespace App\Model;

use wpdb;

if (!defined('ABSPATH')) {
    exit();
}

class CatalogoModel
{
    private wpdb $wpdb;
    private string $tableName;
    private array $queryParams = [
        'limit' => 30,
        'page' => 1,
        'order' => 'update_date',
        'orderBy' => 'DESC',
        'type' => null,
        'query' => null,
        'category' => null,
        'filter' => null,
        'plans' => null,
    ];

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        // $this->tableName =  $this->wpdb->prefix . 'lnd_items_tbl';
        $this->tableName =  $this->wpdb->prefix . 'lnd_master_catalog';
    }

    public function setQueryParam(string $key, $value): void
    {
        if (array_key_exists($key, $this->queryParams)) {
            $this->queryParams[$key] = $value;
        }
    }

    public function executeQuery(): array
    {
        $query = $this->buildQuery();
        $countQuery = $this->buildCountQuery();
        $totalData = $this->wpdb->get_var($countQuery);
        $result = $this->wpdb->get_results($query);

        return [
            'total' => intval($totalData),
            'page' => intval($this->queryParams['page']),
            'limit' => intval($this->queryParams['limit']),
            'result' => $result
        ];
    }

    private function buildQuery(): string
    {
        $conditions = $this->buildConditions();
        $start = ($this->queryParams['page'] - 1) * intval($this->queryParams['limit']);
        
        return "SELECT * FROM {$this->tableName} 
                WHERE status = 'publish' 
                {$conditions} 
                ORDER BY {$this->queryParams['order']} {$this->queryParams['orderBy']} 
                LIMIT {$start}, {$this->queryParams['limit']}";
    }

    private function buildCountQuery(): string
    {
        $conditions = $this->buildConditions();
        
        return "SELECT COUNT(*) FROM {$this->tableName} 
                WHERE status = 'publish' 
                {$conditions}";
    }

    private function buildConditions(): string
    {
        $conditions = [];

        if ($this->queryParams['type'] !== null) {
            $conditions[] = "type = '" . esc_sql($this->queryParams['type']) . "'";
        }

        if ($this->queryParams['plans'] !== null && $this->queryParams['plans'] != '' && $this->queryParams['plans'] != 'library') {
            $conditions[] = "instance REGEXP '" . esc_sql($this->queryParams['plans']) . "'";
        }

        if ($this->queryParams['query'] !== null) {
            $searchTerm = '%' . $this->wpdb->esc_like(sanitize_text_field($this->queryParams['query'])) . '%';
            $conditions[] = $this->wpdb->prepare("(item_name LIKE %s OR filepath LIKE %s)", $searchTerm, $searchTerm);
        }

        if ($this->queryParams['category'] !== null) {
            $categoryTerm = '%' . $this->wpdb->esc_like(str_replace(' ', '%', $this->queryParams['category'])) . '%';
            $conditions[] = $this->wpdb->prepare("category_id LIKE %s", $categoryTerm);
        }

        if ($this->queryParams['filter'] === 'installed') {
            $conditions[] = $this->getInstalledProductsCondition();
        }

        if ($this->queryParams['filter'] === 'free') {
            $conditions[] = "is_free = '1'";
        }

        return !empty($conditions) ? 'AND ' . implode(' AND ', $conditions) : '';
    }

    private function getInstalledProductsCondition(): string
    {
        $installedPlugins = array_keys(get_plugins());
        $installedThemes = $this->getInstalledThemes();
        $installedProducts = array_merge($installedThemes, $installedPlugins);
        
        return "filepath IN ('" . implode("','", array_map('esc_sql', $installedProducts)) . "')";
    }

    private function getInstalledThemes(): array
    {
        $installedWpThemes = wp_get_themes();
        $installedThemes = [];
        foreach ($installedWpThemes as $themeSlug => $themeInfos) {
            $installedThemes[] = $themeSlug;
            $installedThemes[] = $themeSlug . '/style.css';
        }
        return $installedThemes;
    }

    public static function get_categories()
    {
        global $wpdb;
    $table_name = $wpdb->prefix . 'lnd_master_catalog_category';
    $query = "SELECT id, name FROM {$table_name}";
    $results = $wpdb->get_results($query);

    // Extraia apenas os valores da coluna 'name'
    $categories = array();
    foreach ($results as $result) {
        $categories[] = ['id' => $result->id, 'name' => $result->name];
    }

    return $categories;
    }
}