<?php
/**
 * Database handler for PHP Constants Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class PHPCM_DB {
    
    /**
     * Table name
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'phpcm_constants';
    }
    
    /**
     * Check if table exists
     */
    public function table_exists() {
        global $wpdb;
        
        // Check cache first
        $cache_key = 'phpcm_table_exists';
        $cached_result = wp_cache_get($cache_key, 'phpcm_constants');
        
        if (false !== $cached_result) {
            return $cached_result;
        }
        
        $table_name = $wpdb->esc_like($this->table_name);
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
        
        $exists = $result === $this->table_name;
        
        // Cache for 10 minutes (table creation/deletion is rare)
        wp_cache_set($cache_key, $exists, 'phpcm_constants', 600);
        
        return $exists;
    }
    
    /**
     * Create database table (also handles updates via dbDelta)
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
        $result = dbDelta($sql);
        
        // dbDelta returns an array of queries that were executed
        // Check if there were any database errors after running dbDelta
        if (!empty($wpdb->last_error)) {
            $error_msg = "PHP Constants Manager: Database operation failed for table '{$this->table_name}'.";
            $error_msg .= " Database error: " . $wpdb->last_error;
            $error_msg .= " Possible causes: insufficient database permissions, storage space, or MySQL version compatibility.";
            
            // Log the error
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                error_log($error_msg);
            }
            
            // Store error for admin notice
            set_transient('phpcm_db_error', $error_msg, 300); // 5 minutes
            
            return false;
        }
        
        // Verify table exists after dbDelta (for creation, not updates)
        if (!$this->table_exists()) {
            $error_msg = "PHP Constants Manager: Table '{$this->table_name}' does not exist after database operation.";
            $error_msg .= " This may indicate insufficient database permissions or other database configuration issues.";
            
            // Log the error
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                error_log($error_msg);
            }
            
            // Store error for admin notice
            set_transient('phpcm_db_error', $error_msg, 300); // 5 minutes
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Get all constants
     */
    public function get_constants($args = array()) {
        global $wpdb;
        
        // Check if table exists first
        if (!$this->table_exists()) {
            return array();
        }
        
        // Generate cache key based on arguments
        $cache_key = 'phpcm_constants_' . md5(serialize($args));
        $cached_result = wp_cache_get($cache_key, 'phpcm_constants');
        
        if (false !== $cached_result) {
            return $cached_result;
        }
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => -1,
            'offset' => 0,
            'search' => '',
            'is_active' => null,
            'type' => null
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
        
        // Add type condition
        if (isset($args['type']) && !empty($args['type'])) {
            $where .= ' AND type = %s';
            $where_values[] = $args['type'];
        }
        
        // Build order by clause
        $orderby = in_array($args['orderby'], array('name', 'value', 'type', 'is_active', 'created_at')) ? $args['orderby'] : 'name';
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        $order_clause = " ORDER BY {$orderby} {$order}";
        
        // Build limit clause
        $limit_clause = '';
        if ($args['limit'] > 0) {
            $limit_clause = $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        
        // Build complete query
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $sql = "SELECT * FROM {$this->table_name} WHERE {$where}{$order_clause}{$limit_clause}";
        
        if (!empty($where_values)) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $results = $wpdb->get_results($wpdb->prepare($sql, $where_values));
        } else {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $results = $wpdb->get_results($sql);
        }
        
        // Cache the results for 5 minutes
        wp_cache_set($cache_key, $results, 'phpcm_constants', 300);
        
        return $results;
    }
    
    /**
     * Get active constants
     */
    public function get_active_constants() {
        return $this->get_constants(array('is_active' => true));
    }
    
    /**
     * Get all constants (no filters)
     */
    public function get_all_constants() {
        return $this->get_constants();
    }
    
    /**
     * Get single constant
     */
    public function get_constant($id) {
        global $wpdb;
        
        // Check if table exists first
        if (!$this->table_exists()) {
            return null;
        }
        
        // Check cache first
        $cache_key = 'phpcm_constant_' . $id;
        $cached_result = wp_cache_get($cache_key, 'phpcm_constants');
        
        if (false !== $cached_result) {
            return $cached_result;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->get_row($wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $id
        ));
        
        // Cache the result for 5 minutes
        wp_cache_set($cache_key, $result, 'phpcm_constants', 300);
        
        return $result;
    }
    
    /**
     * Get constant by name
     */
    public function get_constant_by_name($name) {
        global $wpdb;
        
        // Check if table exists first
        if (!$this->table_exists()) {
            return null;
        }
        
        // Check cache first
        $cache_key = 'phpcm_constant_name_' . sanitize_key($name);
        $cached_result = wp_cache_get($cache_key, 'phpcm_constants');
        
        if (false !== $cached_result) {
            return $cached_result;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->get_row($wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT * FROM {$this->table_name} WHERE name = %s",
            $name
        ));
        
        // Cache the result for 5 minutes
        wp_cache_set($cache_key, $result, 'phpcm_constants', 300);
        
        return $result;
    }
    
    /**
     * Count constants
     */
    public function count_constants($args = array()) {
        global $wpdb;
        
        // Check if table exists first
        if (!$this->table_exists()) {
            return 0;
        }
        
        $defaults = array(
            'search' => '',
            'is_active' => null,
            'type' => null
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Generate cache key based on arguments
        $cache_key = 'phpcm_count_constants_' . md5(serialize($args));
        $cached_result = wp_cache_get($cache_key, 'phpcm_constants');
        
        if (false !== $cached_result) {
            return $cached_result;
        }
        
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
        
        // Add type condition
        if (isset($args['type']) && !empty($args['type'])) {
            $where .= ' AND type = %s';
            $where_values[] = $args['type'];
        }
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where}";
        
        if (!empty($where_values)) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $result = $wpdb->get_var($wpdb->prepare($sql, $where_values));
        } else {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $result = $wpdb->get_var($sql);
        }
        
        // Cache the result for 5 minutes
        wp_cache_set($cache_key, $result, 'phpcm_constants', 300);
        
        return $result;
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
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->insert(
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
        
        // Clear all cache when data changes
        if ($result !== false) {
            $this->clear_cache();
        }
        
        return $result;
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
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->update(
            $this->table_name,
            $update_data,
            array('id' => $id),
            $format,
            array('%d')
        );
        
        // Clear all cache when data changes
        if ($result !== false) {
            $this->clear_cache();
        }
        
        return $result;
    }
    
    /**
     * Delete constant
     */
    public function delete_constant($id) {
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
        
        // Clear all cache when data changes
        if ($result !== false) {
            $this->clear_cache();
        }
        
        return $result;
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
    
    /**
     * Clear all cache for constants
     */
    private function clear_cache() {
        // Clear the entire cache group
        wp_cache_flush_group('phpcm_constants');
    }
}