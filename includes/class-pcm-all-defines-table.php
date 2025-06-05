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
            'singular' => esc_html__('constant', 'php-constants-manager'),
            'plural' => esc_html__('constants', 'php-constants-manager'),
            'ajax' => false
        ));
        
        $this->all_constants = $this->get_categorized_constants();
    }
    
    /**
     * Get columns
     */
    public function get_columns() {
        return array(
            'name' => esc_html__('Name', 'php-constants-manager'),
            'value' => esc_html__('Value & Type', 'php-constants-manager'),
            'category' => esc_html__('Category', 'php-constants-manager')
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
                $type_display = esc_html(ucfirst($type));
                
                // Handle NULL values - don't display value
                if (is_null($value)) {
                    return '<div><strong>' . $type_display . '</strong></div>';
                }
                
                // Handle empty string values - don't display value (but preserve "0")
                if (is_string($value) && $value === '') {
                    return '<div><strong>' . $type_display . '</strong></div>';
                }
                
                // Display value for non-empty values
                $display_value = $this->format_value($value);
                if (strlen($display_value) > 100) {
                    $display_value = substr($display_value, 0, 100) . '...';
                }
                
                return '<div><strong>' . $type_display . '</strong><br><code>' . esc_html($display_value) . '</code></div>';
                
            case 'category':
                $category_class = sanitize_html_class(strtolower(str_replace('/', '-', $item['category'])));
                return '<span class="pcm-category-' . $category_class . '">' . esc_html($item['category']) . '</span>';
                
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
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Get query args from URL parameters (GET requests for table filtering/sorting)
        // These are read-only operations that don't require nonce verification
        // All inputs are properly sanitized before use
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field(wp_unslash($_REQUEST['orderby'])) : 'name';
        $order = isset($_REQUEST['order']) ? sanitize_text_field(wp_unslash($_REQUEST['order'])) : 'ASC';
        $search = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';
        $category_filter = isset($_REQUEST['category_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['category_filter'])) : 'all';
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        
        // Reset to page 1 if we have a search term or category filter
        if (!empty($search) || $category_filter != 'all') {
            // Reset pagination for new search/filter
            // Note: This modifies $_REQUEST which is acceptable for pagination reset
            $_REQUEST['paged'] = 1;
            $current_page = 1;
        }
        
        // Get current page
        if (!isset($current_page)) {
            $current_page = $this->get_pagenum();
        }
        $per_page = $this->get_items_per_page('all_defines_per_page', 50);
        
        // Flatten all constants
        $items = array();
        foreach ($this->all_constants as $category => $constants) {
            // Filter by category if specified
            if ($category_filter != 'all' && sanitize_title($category) != $category_filter) {
                continue;
            }
            
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
     * Get views for category filtering
     */
    public function get_views() {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $current_filter = isset($_REQUEST['category_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['category_filter'])) : 'all';
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        $base_url = admin_url('admin.php?page=php-constants-manager-all-defines');
        
        // Calculate total count
        $total_count = 0;
        foreach ($this->all_constants as $category => $constants) {
            $total_count += count($constants);
        }
        
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
        
        // Category links
        $category_counts = array();
        foreach ($this->all_constants as $category => $constants) {
            $count = count($constants);
            if ($count > 0) {
                $category_counts[$category] = $count;
            }
        }
        
        // Sort categories alphabetically (case-insensitive)
        uksort($category_counts, 'strcasecmp');
        
        // Add category links after All
        foreach ($category_counts as $category => $count) {
            $category_slug = sanitize_title($category);
            $class = ($current_filter == $category_slug) ? ' class="current"' : '';
            $views[$category_slug] = sprintf(
                '<a href="%s&category_filter=%s"%s>%s <span class="count">(%s)</span></a>',
                esc_url($base_url),
                urlencode($category_slug),
                $class,
                esc_html($category),
                number_format_i18n($count)
            );
        }
        
        return $views;
    }
    
    /**
     * Extra table navigation
     */
    public function extra_tablenav($which) {
        if ($which === 'top') {
            // Category filters are now handled by get_views()
            return;
        }
    }
    
    /**
     * Get categorized constants with better categorization
     */
    private function get_categorized_constants() {
        $all_constants = get_defined_constants(true);
        $categorized = array();
        
        // Define better category mappings based on constant prefixes
        $category_patterns = array(
            'PHP' => array('PHP_', 'ZEND_'),
            'File' => array('FILE_', 'PATHINFO_', 'GLOB_', 'LOCK_', 'SEEK_'),
            'Date/Time' => array('DATE_', 'ABDAY_', 'DAY_', 'ABMON_', 'MON_', 'AM_STR', 'PM_STR', 'D_T_FMT', 'D_FMT', 'T_FMT'),
            'Math' => array('M_', 'MATH_'),
            'String' => array('STR_', 'CRYPT_', 'ENT_', 'HTML_'),
            'Network' => array('STREAM_', 'SOCKET_', 'SO_', 'SOL_', 'MSG_', 'DNS_'),
            'Database' => array('MYSQL_', 'MYSQLI_', 'PDO_', 'SQLITE_', 'PGSQL_'),
            'Image' => array('IMG_', 'IMAGETYPE_', 'GD_'),
            'Filter' => array('FILTER_', 'INPUT_'),
            'JSON' => array('JSON_'),
            'PCRE' => array('PREG_'),
            'XML' => array('XML_', 'LIBXML_'),
            'Curl' => array('CURL'),
            'Hash' => array('HASH_'),
            'OpenSSL' => array('OPENSSL_'),
            'SOAP' => array('SOAP_'),
            'Directory' => array('DIRECTORY_SEPARATOR', 'PATH_SEPARATOR', 'SCANDIR_'),
            'Error' => array('E_', 'LOG_'),
            'WordPress Core' => array('WP_', 'WPINC', 'ABSPATH', 'DB_'),
            'WordPress Config' => array('AUTOMATIC_UPDATER_', 'WP_POST_REVISIONS', 'WP_CRON_LOCK_TIMEOUT'),
        );
        
        // Flatten all constants first
        $flat_constants = array();
        foreach ($all_constants as $source_category => $constants) {
            foreach ($constants as $name => $value) {
                $flat_constants[$name] = array(
                    'value' => $value,
                    'source_category' => $source_category
                );
            }
        }
        
        // Categorize constants
        foreach ($flat_constants as $name => $data) {
            $category = 'Other';
            
            // Check against our pattern mappings
            foreach ($category_patterns as $cat => $patterns) {
                foreach ($patterns as $pattern) {
                    if (strpos($name, $pattern) === 0) {
                        $category = $cat;
                        break 2;
                    }
                }
            }
            
            // Special cases for specific constants
            if (in_array($name, array('TRUE', 'FALSE', 'NULL'))) {
                $category = 'Core';
            } elseif ($data['source_category'] === 'user') {
                $category = 'User Defined';
            } elseif ($data['source_category'] === 'Core' && $category === 'Other') {
                $category = 'PHP Core';
            }
            
            if (!isset($categorized[$category])) {
                $categorized[$category] = array();
            }
            
            $categorized[$category][$name] = $data['value'];
        }
        
        return $categorized;
    }
    
    /**
     * Message for no items
     */
    public function no_items() {
        esc_html_e('No constants found.', 'php-constants-manager');
    }
}