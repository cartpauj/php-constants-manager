<?php
/**
 * Database handler for PHP Constants Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class PCM_DB {
    
    /**
     * Table name
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'pcm_constants';
    }
    
    /**
     * Create database table
     */
    public function create_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            value text,
            type varchar(20) NOT NULL DEFAULT 'string',
            is_active tinyint(1) NOT NULL DEFAULT 1,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name (name),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Get all constants
     */
    public function get_constants($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => -1,
            'offset' => 0,
            'search' => '',
            'is_active' => null
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = '1=1';
        $where_values = array();
        
        // Add search condition
        if (!empty($args['search'])) {
            $where .= ' AND (name LIKE %s OR value LIKE %s OR description LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        // Add active condition
        if ($args['is_active'] !== null) {
            $where .= ' AND is_active = %d';
            $where_values[] = $args['is_active'] ? 1 : 0;
        }
        
        // Build query
        $sql = "SELECT * FROM {$this->table_name} WHERE {$where}";
        
        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }
        
        // Add order by
        $orderby = in_array($args['orderby'], array('name', 'value', 'type', 'is_active', 'created_at')) ? $args['orderby'] : 'name';
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY {$orderby} {$order}";
        
        // Add limit
        if ($args['limit'] > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get active constants
     */
    public function get_active_constants() {
        return $this->get_constants(array('is_active' => true));
    }
    
    /**
     * Get single constant
     */
    public function get_constant($id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Get constant by name
     */
    public function get_constant_by_name($name) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE name = %s",
            $name
        ));
    }
    
    /**
     * Count constants
     */
    public function count_constants($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'search' => '',
            'is_active' => null
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = '1=1';
        $where_values = array();
        
        // Add search condition
        if (!empty($args['search'])) {
            $where .= ' AND (name LIKE %s OR value LIKE %s OR description LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        // Add active condition
        if ($args['is_active'] !== null) {
            $where .= ' AND is_active = %d';
            $where_values[] = $args['is_active'] ? 1 : 0;
        }
        
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where}";
        
        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }
        
        return $wpdb->get_var($sql);
    }
    
    /**
     * Insert constant
     */
    public function insert_constant($data) {
        global $wpdb;
        
        $defaults = array(
            'name' => '',
            'value' => '',
            'type' => 'string',
            'is_active' => true,
            'description' => ''
        );
        
        $data = wp_parse_args($data, $defaults);
        
        // Check if constant already exists
        if ($this->get_constant_by_name($data['name'])) {
            return false;
        }
        
        return $wpdb->insert(
            $this->table_name,
            array(
                'name' => $data['name'],
                'value' => $data['value'],
                'type' => $data['type'],
                'is_active' => $data['is_active'] ? 1 : 0,
                'description' => $data['description']
            ),
            array('%s', '%s', '%s', '%d', '%s')
        );
    }
    
    /**
     * Update constant
     */
    public function update_constant($id, $data) {
        global $wpdb;
        
        $update_data = array();
        $format = array();
        
        // Prepare update data
        if (isset($data['value'])) {
            $update_data['value'] = $data['value'];
            $format[] = '%s';
        }
        
        if (isset($data['type'])) {
            $update_data['type'] = $data['type'];
            $format[] = '%s';
        }
        
        if (isset($data['is_active'])) {
            $update_data['is_active'] = $data['is_active'] ? 1 : 0;
            $format[] = '%d';
        }
        
        if (isset($data['description'])) {
            $update_data['description'] = $data['description'];
            $format[] = '%s';
        }
        
        if (empty($update_data)) {
            return false;
        }
        
        return $wpdb->update(
            $this->table_name,
            $update_data,
            array('id' => $id),
            $format,
            array('%d')
        );
    }
    
    /**
     * Delete constant
     */
    public function delete_constant($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Toggle constant active status
     */
    public function toggle_constant($id) {
        global $wpdb;
        
        $constant = $this->get_constant($id);
        if (!$constant) {
            return false;
        }
        
        return $this->update_constant($id, array(
            'is_active' => !$constant->is_active
        ));
    }
}