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

class PHPCM_List_Table extends WP_List_Table {
    
    /**
     * Database handler
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => esc_html__('constant', 'php-constants-manager'),
            'plural' => esc_html__('constants', 'php-constants-manager'),
            'ajax' => false
        ));
        
        $this->db = new PHPCM_DB();
    }
    
    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'name' => esc_html__('Name', 'php-constants-manager'),
            'value' => esc_html__('Value', 'php-constants-manager'),
            'is_active' => esc_html__('Status', 'php-constants-manager'),
            'predefined' => esc_html__('Predefined', 'php-constants-manager'),
            'description' => esc_html__('Description', 'php-constants-manager'),
            'created_at' => esc_html__('Created', 'php-constants-manager')
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
            'delete' => esc_html__('Delete', 'php-constants-manager'),
            'activate' => esc_html__('Activate', 'php-constants-manager'),
            'deactivate' => esc_html__('Deactivate', 'php-constants-manager')
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
            admin_url('admin-post.php?action=phpcm_delete_constant&id=' . $item->id),
            'phpcm_delete_constant',
            'phpcm_nonce'
        );
        
        $actions = array(
            'edit' => sprintf('<a href="%s">%s</a>', $edit_url, esc_html__('Edit', 'php-constants-manager')),
            'delete' => sprintf(
                '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                $delete_url,
                /* translators: JavaScript confirmation message when deleting a constant */
                esc_js(__('Are you sure you want to delete this constant?', 'php-constants-manager')),
                esc_html__('Delete', 'php-constants-manager')
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
        $nonce = wp_create_nonce('phpcm_toggle_constant');
        
        return sprintf(
            '<label class="phpcm-toggle-switch" data-id="%d" data-nonce="%s"><input type="checkbox" %s><span class="phpcm-toggle-slider"></span></label>',
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
                '<div class="phpcm-predefined-badge phpcm-tooltip" title="%s"><span class="phpcm-predefined-badge-text">%s</span></div>',
                esc_attr(__('This constant is likely not taking effect because something else has already defined it before this plugin could. Try enabling the Early Loading setting to see if that helps. If that doesn\'t work, it may be a PHP or WordPress constant that cannot be altered by this plugin.', 'php-constants-manager')),
'⚠️ ' . esc_html__('Yes', 'php-constants-manager')
            );
        }
        
        return sprintf(
            '<span class="phpcm-predefined-no">%s</span>',
            esc_html__('No', 'php-constants-manager')
        );
    }
    
    /**
     * Override single row to add CSS classes for predefined constants
     */
    public function single_row($item) {
        $plugin_instance = PHP_Constants_Manager::get_instance();
        $predefined_check = $plugin_instance->is_constant_predefined(
            $item->name, 
            $item->value, 
            $item->type, 
            $item->is_active
        );
        
        $css_class = '';
        if ($predefined_check['is_predefined']) {
            $css_class = 'predefined-constant';
        }
        
        echo '<tr class="' . esc_attr($css_class) . '">';
        $this->single_row_columns($item);
        echo '</tr>';
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
        
        // Get query args from URL parameters (GET requests for table filtering/sorting)
        // These are read-only operations that don't require nonce verification
        // All inputs are properly sanitized before use
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field(wp_unslash($_REQUEST['orderby'])) : 'name';
        $order = isset($_REQUEST['order']) ? sanitize_text_field(wp_unslash($_REQUEST['order'])) : 'ASC';
        $search = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';
        $type_filter = isset($_REQUEST['type_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['type_filter'])) : 'all';
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        
        // Reset to page 1 if we have a search term or type filter
        if (!empty($search) || $type_filter != 'all') {
            // Reset pagination for new search/filter
            // Note: This modifies $_REQUEST which is acceptable for pagination reset
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
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $current_filter = isset($_REQUEST['type_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['type_filter'])) : 'all';
        $base_url = admin_url('admin.php?page=php-constants-manager');
        $search_query = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        
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
            esc_url($base_url),
            $class,
            esc_html__('All', 'php-constants-manager'),
            number_format_i18n($total_count)
        );
        
        // Add type filters
        $type_labels = array(
            'string' => esc_html__('String', 'php-constants-manager'),
            'integer' => esc_html__('Integer', 'php-constants-manager'),
            'float' => esc_html__('Float', 'php-constants-manager'),
            'boolean' => esc_html__('Boolean', 'php-constants-manager'),
            'null' => esc_html__('Null', 'php-constants-manager')
        );
        
        foreach ($type_labels as $type => $label) {
            $count = $type_counts[$type];
            if ($count > 0) {
                $class = ($current_filter == $type) ? ' class="current"' : '';
                $type_url = add_query_arg('type_filter', $type, $base_url);
                $views[$type] = sprintf(
                    '<a href="%s"%s>%s <span class="count">(%s)</span></a>',
                    esc_url($type_url),
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
        esc_html_e('No constants found.', 'php-constants-manager');
    }
}