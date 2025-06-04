<?php
/**
 * WP_List_Table extension for All PHP Defines
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load WP_List_Table if not loaded
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class PCM_All_Defines_Table extends WP_List_Table {
    
    /**
     * All PHP constants
     */
    private $all_constants;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => __('constant', 'php-constants-manager'),
            'plural' => __('constants', 'php-constants-manager'),
            'ajax' => false
        ));
        
        $this->all_constants = get_defined_constants(true);
    }
    
    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'name' => __('Name', 'php-constants-manager'),
            'value' => __('Value & Type', 'php-constants-manager'),
            'category' => __('Category', 'php-constants-manager')
        );
    }
    
    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'name' => array('name', false),
            'category' => array('category', false)
        );
    }
    
    /**
     * Default column handler
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'name':
                return '<strong>' . esc_html($item['name']) . '</strong>';
                
            case 'value':
                $value = $item['value'];
                $type = $this->get_type($value);
                
                // Format the value for display
                $display_value = $this->format_value($value);
                if (strlen($display_value) > 100) {
                    $display_value = substr($display_value, 0, 100) . '...';
                }
                
                return '<div><strong>' . esc_html(ucfirst($type)) . '</strong><br><code>' . esc_html($display_value) . '</code></div>';
                
            case 'category':
                return '<span class="pcm-category-' . esc_attr($item['category']) . '">' . esc_html(ucfirst($item['category'])) . '</span>';
                
            default:
                return esc_html($item[$column_name]);
        }
    }
    
    /**
     * Get type of value
     */
    private function get_type($value) {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_int($value)) {
            return 'integer';
        } elseif (is_float($value)) {
            return 'float';
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_array($value)) {
            return 'array';
        } elseif (is_resource($value)) {
            return 'resource';
        } else {
            return 'string';
        }
    }
    
    /**
     * Format value for display
     */
    private function format_value($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_array($value)) {
            return 'Array(' . count($value) . ')';
        } elseif (is_resource($value)) {
            return 'Resource';
        } else {
            return (string) $value;
        }
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
        $per_page = $this->get_items_per_page('all_defines_per_page', 50);
        
        // Get query args
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'name';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC';
        $search = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
        
        // Flatten all constants
        $items = array();
        foreach ($this->all_constants as $category => $constants) {
            foreach ($constants as $name => $value) {
                $items[] = array(
                    'name' => $name,
                    'value' => $value,
                    'category' => $category
                );
            }
        }
        
        // Filter by search term
        if (!empty($search)) {
            $items = array_filter($items, function($item) use ($search) {
                return stripos($item['name'], $search) !== false;
            });
        }
        
        // Sort items
        usort($items, function($a, $b) use ($orderby, $order) {
            $result = 0;
            
            if ($orderby === 'name') {
                $result = strcmp($a['name'], $b['name']);
            } elseif ($orderby === 'category') {
                $result = strcmp($a['category'], $b['category']);
            }
            
            return ($order === 'DESC') ? -$result : $result;
        });
        
        // Get total items
        $total_items = count($items);
        
        // Paginate
        $items = array_slice($items, ($current_page - 1) * $per_page, $per_page);
        
        $this->items = $items;
        
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
            $total_count = 0;
            foreach ($this->all_constants as $category => $constants) {
                $total_count += count($constants);
            }
            
            printf(
                '<div class="alignleft actions"><span class="displaying-num">%s</span></div>',
                sprintf(
                    _n('%s constant', '%s constants', $total_count, 'php-constants-manager'),
                    number_format_i18n($total_count)
                )
            );
        }
    }
    
    /**
     * Message for no items
     */
    public function no_items() {
        _e('No constants found.', 'php-constants-manager');
    }
}