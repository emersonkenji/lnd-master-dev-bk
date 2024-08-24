<?php

namespace App\Model;

class InserterDB
{
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    private function insert($table, $data) {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_map(function($value) {
            return $value === null ? 'NULL' : '%s';
        }, $values);

        $query = "INSERT INTO {$table} (" . implode(', ', $columns) . ") 
                  VALUES (" . implode(', ', $placeholders) . ") 
                  ON DUPLICATE KEY UPDATE " . implode(', ', array_map(function($column) {
                      return "{$column} = VALUES({$column})";
                  }, $columns));
        
        // Filtrar valores para o prepared statement
        $filtered_values = array_map(function($value) {
            return $value === null ? null : $value;
        }, $values);
        
        // Remove valores nulos do prepared statement
        $prepared_query = $this->wpdb->prepare($query, ...array_filter($filtered_values, function($value) {
            return $value !== null;
        }));
        
        $this->wpdb->query($prepared_query);
        return $this->wpdb->insert_id;  // Retornar o ID do último inserido
    }

    public function insert_templates_categories($data) {
        $table = "{$this->wpdb->prefix}lnd_master_templates_categories";
        return $this->insert($table, $data);
    }

    public function insert_catalog_category($data) {
        $table = "{$this->wpdb->prefix}lnd_master_catalog_category";
        return $this->insert($table, $data);
    }

    public function insert_templates_files($data) {
        $table = "{$this->wpdb->prefix}lnd_master_templates_files";
        return $this->insert($table, $data);
    }

    public function insert_catalog($data) {
        $table = "{$this->wpdb->prefix}lnd_master_catalog";
        return $this->insert($table, $data);
    }
}