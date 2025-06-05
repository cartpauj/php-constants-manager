<?php
/**
 * WP_List_Table extension for PHP Constants Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load WP_List_Table if not loaded
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class PCM_List_Table extends WP_List_Table {
    
    /**
     * Database handler
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => __('constant', 'php-constants-manager'),
            'plural' => __('constants', 'php-constants-manager'),
            'ajax' => false
        ));
        
        $this->db = new PCM_DB();
    }
    
    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'php-constants-manager'),
            'value' => __('Value', 'php-constants-manager'),
            'is_active' => __('Status', 'php-constants-manager'),
            'predefined' => __('Predefined', 'php-constants-manager'),
            'description' => __('Description', 'php-constants-manager'),
            'created_at' => __('Created', 'php-constants-manager')
        );
    }
    
    /**
     * Get hidden columns
     */
    public function get_hidden_columns() {
        return get_hidden_columns($this->screen);
    }
    
    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'name' => array('name', false),
            'is_active' => array('is_active', false),
            'predefined' => array('predefined', false),
            'created_at' => array('created_at', false)
        );
    }
    
    /**
     * Get bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'delete' => __('Delete', 'php-constants-manager'),
            'activate' => __('Activate', 'php-constants-manager'),
            'deactivate' => __('Deactivate', 'php-constants-manager')
        );
    }
    
    /**
     * Default column handler
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'value':
                $type = esc_html(ucfirst($item->type));
                
                // Handle NULL type constants - don't display value
                if ($item->type === 'null') {
                    return '<div><strong>' . $type . '</strong></div>';
                }
                
                // Handle empty string values - don't display value
                if (empty($item->value) && $item->value !== '0') {
                    return '<div><strong>' . $type . '</strong></div>';
                }
                
                // Display value for non-empty values
                $value = esc_html($item->value);
                if (strlen($value) > 50) {
                    $value = substr($value, 0, 50) . '...';
                }
                return '<div><strong>' . $type . '</strong><br><code>' . $value . '</code></div>';
                
            case 'description':
                $desc = esc_html($item->description);
                if (strlen($desc) > 100) {
                    $desc = substr($desc, 0, 100) . '...';
                }
                return $desc;
                
            case 'created_at':
                return date_i18n(get_option('date_format'), strtotime($item->created_at));
                
            case 'predefined':
                return $this->column_predefined($item);
                
            default:
                return esc_html($item->$column_name);
        }
    }
    
    /**
     * Column checkbox
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="constant[]" value="%d" />',
            $item->id
        );
    }
    
    /**
     * Column name
     */
    public function column_name($item) {
        $edit_url = admin_url('admin.php?page=php-constants-manager&action=edit&id=' . $item->id);
        $delete_url = wp_nonce_url(
            admin_url('admin-post.php?action=pcm_delete_constant&id=' . $item->id),
            'pcm_delete_constant',
            'pcm_nonce'
        );
        
        $actions = array(
            'edit' => sprintf('<a href="%s">%s</a>', $edit_url, __('Edit', 'php-constants-manager')),
            'delete' => sprintf(
                '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                $delete_url,
                esc_js(__('Are you sure you want to delete this constant?', 'php-constants-manager')),
                __('Delete', 'php-constants-manager')
            )
        );
        
        return sprintf(
            '<strong><a href="%s">%s</a></strong>%s',
            $edit_url,
            esc_html($item->name),
            $this->row_actions($actions)
        );
    }
    
    /**
     * Column status
     */
    public function column_is_active($item) {
        $nonce = wp_create_nonce('pcm_toggle_constant');
        
        return sprintf(
            '<label class="pcm-toggle-switch" data-id="%d" data-nonce="%s"><input type="checkbox" %s><span class="pcm-toggle-slider"></span></label>',
            $item->id,
            $nonce,
            checked($item->is_active, true, false)
        );
    }
    
    /**
     * Column predefined
     */
    public function column_predefined($item) {
        $plugin_instance = PHP_Constants_Manager::get_instance();
        $predefined_check = $plugin_instance->is_constant_predefined(
            $item->name, 
            $item->value, 
            $item->type, 
            $item->is_active
        );
        
        if ($predefined_check['is_predefined']) {
            return sprintf(
                '<span class="pcm-predefined-yes" title="%s" style="color: #dc3232; font-weight: bold;">%s</span>',
                esc_attr(sprintf(__('Already defined with value: %s', 'php-constants-manager'), var_export($predefined_check['existing_value'], true))),
                __('Yes', 'php-constants-manager')
            );
        }
        
        return sprintf(
            '<span class="pcm-predefined-no">%s</span>',
            __('No', 'php-constants-manager')
        );
    }
    
    /**
     * Prepare items
     */
    public function prepare_items() {
        // Set column headers
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Get current page
        $current_page = $this->get_pagenum();
        $per_page = $this->get_items_per_page('constants_per_page', 50);
        
        // Get query args
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'name';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC';
        $search = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
        $type_filter = isset($_REQUEST['type_filter']) ? $_REQUEST['type_filter'] : 'all';
        
        // Reset to page 1 if we have a search term or type filter
        if (!empty($search) || $type_filter != 'all') {
            $_REQUEST['paged'] = 1;
            $current_page = 1;
        }
        
        // Get items
        $args = array(
            'orderby' => $orderby,
            'order' => $order,
            'search' => $search,
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page
        );
        
        // Add type filter if specified
        if ($type_filter != 'all') {
            $args['type'] = $type_filter;
        }
        
        $this->items = $this->db->get_constants($args);
        
        // Get total items
        $count_args = array('search' => $search);
        if ($type_filter != 'all') {
            $count_args['type'] = $type_filter;
        }
        $total_items = $this->db->count_constants($count_args);
        
        // Set pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
    
    /**
     * Get views for type filtering
     */
    public function get_views() {
        $current_filter = isset($_REQUEST['type_filter']) ? $_REQUEST['type_filter'] : 'all';
        $base_url = admin_url('admin.php?page=php-constants-manager');
        $search_query = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
        
        // Add search parameter to base URL if present
        if (!empty($search_query)) {
            $base_url = add_query_arg('s', urlencode($search_query), $base_url);
        }
        
        // Get type counts
        $search_args = !empty($search_query) ? array('search' => $search_query) : array();
        $type_counts = array(
            'string' => $this->db->count_constants(array_merge($search_args, array('type' => 'string'))),
            'integer' => $this->db->count_constants(array_merge($search_args, array('type' => 'integer'))),
            'float' => $this->db->count_constants(array_merge($search_args, array('type' => 'float'))),
            'boolean' => $this->db->count_constants(array_merge($search_args, array('type' => 'boolean'))),
            'null' => $this->db->count_constants(array_merge($search_args, array('type' => 'null')))
        );
        
        $total_count = array_sum($type_counts);
        
        // Start with All link
        $views = array();
        $class = ($current_filter == 'all') ? ' class="current"' : '';
        $views['all'] = sprintf(
            '<a href="%s"%s>%s <span class="count">(%s)</span></a>',
            $base_url,
            $class,
            __('All', 'php-constants-manager'),
            number_format_i18n($total_count)
        );
        
        // Add type filters
        $type_labels = array(
            'string' => __('String', 'php-constants-manager'),
            'integer' => __('Integer', 'php-constants-manager'),
            'float' => __('Float', 'php-constants-manager'),
            'boolean' => __('Boolean', 'php-constants-manager'),
            'null' => __('Null', 'php-constants-manager')
        );
        
        foreach ($type_labels as $type => $label) {
            $count = $type_counts[$type];
            if ($count > 0) {
                $class = ($current_filter == $type) ? ' class="current"' : '';
                $type_url = add_query_arg('type_filter', $type, $base_url);
                $views[$type] = sprintf(
                    '<a href="%s"%s>%s <span class="count">(%s)</span></a>',
                    $type_url,
                    $class,
                    esc_html($label),
                    number_format_i18n($count)
                );
            }
        }
        
        return $views;
    }
    
    /**
     * Extra table navigation
     */
    public function extra_tablenav($which) {
        if ($which === 'top') {
            // Type filters are now handled by get_views()
            return;
        }
    }
    
    /**
     * Message for no items
     */
    public function no_items() {
        _e('No constants found.', 'php-constants-manager');
    }
}