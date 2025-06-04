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
            'type' => __('Type', 'php-constants-manager'),
            'is_active' => __('Status', 'php-constants-manager'),
            'description' => __('Description', 'php-constants-manager'),
            'created_at' => __('Created', 'php-constants-manager')
        );
    }
    
    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'name' => array('name', false),
            'type' => array('type', false),
            'is_active' => array('is_active', false),
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
                $value = esc_html($item->value);
                if (strlen($value) > 50) {
                    $value = substr($value, 0, 50) . '...';
                }
                return '<code>' . $value . '</code>';
                
            case 'type':
                return esc_html(ucfirst($item->type));
                
            case 'description':
                $desc = esc_html($item->description);
                if (strlen($desc) > 100) {
                    $desc = substr($desc, 0, 100) . '...';
                }
                return $desc;
                
            case 'created_at':
                return date_i18n(get_option('date_format'), strtotime($item->created_at));
                
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
        // Check if constant is already defined elsewhere
        if (defined($item->name)) {
            $existing_value = constant($item->name);
            $our_value = $item->value;
            
            // Type-cast our value to match what it would be when defined
            switch ($item->type) {
                case 'boolean':
                    $our_value = filter_var($our_value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'integer':
                    $our_value = intval($our_value);
                    break;
                case 'float':
                    $our_value = floatval($our_value);
                    break;
                case 'null':
                    $our_value = null;
                    break;
            }
            
            // Check if it's our definition or external
            if ($existing_value !== $our_value || !$item->is_active) {
                return sprintf(
                    '<span class="pcm-status-predefined" title="%s">%s</span>',
                    esc_attr(sprintf(__('Already defined with value: %s', 'php-constants-manager'), var_export($existing_value, true))),
                    __('Predefined', 'php-constants-manager')
                );
            }
        }
        
        $toggle_url = wp_nonce_url(
            admin_url('admin-post.php?action=pcm_toggle_constant&id=' . $item->id),
            'pcm_toggle_constant',
            'pcm_nonce'
        );
        
        $status = $item->is_active ? 
            '<span class="pcm-status-active">' . __('Active', 'php-constants-manager') . '</span>' : 
            '<span class="pcm-status-inactive">' . __('Inactive', 'php-constants-manager') . '</span>';
        
        $toggle_text = $item->is_active ? __('Deactivate', 'php-constants-manager') : __('Activate', 'php-constants-manager');
        
        return sprintf(
            '%s <a href="%s" class="pcm-toggle-link">%s</a>',
            $status,
            $toggle_url,
            $toggle_text
        );
    }
    
    /**
     * Prepare items
     */
    public function prepare_items() {
        // Set column headers
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Get current page
        $current_page = $this->get_pagenum();
        $per_page = $this->get_items_per_page('constants_per_page', 20);
        
        // Get query args
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'name';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC';
        $search = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
        
        // Get items
        $args = array(
            'orderby' => $orderby,
            'order' => $order,
            'search' => $search,
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page
        );
        
        $this->items = $this->db->get_constants($args);
        
        // Get total items
        $total_items = $this->db->count_constants(array('search' => $search));
        
        // Set pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
    
    /**
     * Extra table navigation
     */
    public function extra_tablenav($which) {
        if ($which === 'top') {
            ?>
            <div class="alignleft actions">
                <?php
                $active_count = $this->db->count_constants(array('is_active' => true));
                $inactive_count = $this->db->count_constants(array('is_active' => false));
                $total_count = $active_count + $inactive_count;
                
                printf(
                    '<span class="displaying-num">%s</span>',
                    sprintf(
                        _n('%s constant', '%s constants', $total_count, 'php-constants-manager'),
                        number_format_i18n($total_count)
                    )
                );
                
                if ($active_count > 0) {
                    printf(
                        ' <span class="pcm-status-count">(%s %s)</span>',
                        number_format_i18n($active_count),
                        __('active', 'php-constants-manager')
                    );
                }
                ?>
            </div>
            <?php
        }
    }
    
    /**
     * Message for no items
     */
    public function no_items() {
        _e('No constants found.', 'php-constants-manager');
    }
}