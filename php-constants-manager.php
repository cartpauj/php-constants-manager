<?php
/**
 * Plugin Name: PHP Constants Manager
 * Plugin URI: https://example.com/php-constants-manager
 * Description: Safely manage PHP constants (defines) through the WordPress admin interface
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: php-constants-manager
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PCM_VERSION', '1.0.0');
define('PCM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PCM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PCM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once PCM_PLUGIN_DIR . 'includes/class-pcm-list-table.php';
require_once PCM_PLUGIN_DIR . 'includes/class-pcm-all-defines-table.php';
require_once PCM_PLUGIN_DIR . 'includes/class-pcm-db.php';

/**
 * Main plugin class
 */
class PHP_Constants_Manager {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Database handler
     */
    private $db;
    
    /**
     * Views directory path
     */
    private $views_path;
    
    /**
     * Get instance of the class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->db = new PCM_DB();
        $this->views_path = PCM_PLUGIN_DIR . 'views/';
        
        // Hook into WordPress
        //add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Load constants early
        add_action('plugins_loaded', array($this, 'load_managed_constants'), 1);
        
        // Add settings link
        add_filter('plugin_action_links_' . PCM_PLUGIN_BASENAME, array($this, 'add_settings_link'));
        
        // Handle form submissions
        add_action('admin_post_pcm_save_constant', array($this, 'handle_save_constant'));
        add_action('admin_post_pcm_delete_constant', array($this, 'handle_delete_constant'));
        add_action('admin_post_pcm_toggle_constant', array($this, 'handle_toggle_constant'));
        add_action('admin_post_pcm_bulk_action', array($this, 'handle_bulk_action'));
        add_action('admin_post_pcm_export_csv', array($this, 'handle_export_csv'));
        add_action('admin_post_pcm_import_csv', array($this, 'handle_import_csv'));
        
        // Handle AJAX requests
        add_action('wp_ajax_pcm_check_constant', array($this, 'ajax_check_constant'));
        add_action('wp_ajax_pcm_toggle_constant', array($this, 'ajax_toggle_constant'));
        
        // Handle screen options
        add_filter('set-screen-option', array($this, 'set_screen_options'), 10, 3);
    }
    
    /**
     * Load view template
     */
    private function load_view($template, $data = array()) {
        $template_path = $this->views_path . $template . '.php';
        
        if (!file_exists($template_path)) {
            wp_die(sprintf(__('View template not found: %s', 'php-constants-manager'), $template));
        }
        
        // Extract data to variables
        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }
        
        include $template_path;
    }
    
    /**
     * Initialize plugin
     */
    /*public function init() {
        // Load text domain
        load_plugin_textdomain('php-constants-manager', false, dirname(PCM_PLUGIN_BASENAME) . '/languages');
    }*/
    
    /**
     * Load managed constants
     */
    public function load_managed_constants() {
        $constants = $this->db->get_active_constants();
        
        foreach ($constants as $constant) {
            if (!defined($constant->name)) {
                $value = $constant->value;
                
                switch ($constant->type) {
                    case 'boolean':
                        if (is_string($value)) {
                            $lower_value = strtolower(trim($value));
                            $value = in_array($lower_value, ['true', '1', 'yes', 'on'], true);
                        } else {
                            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        }
                        break;
                    case 'integer':
                        if (is_numeric($value)) {
                            $value = intval($value);
                        } else {
                            $value = 0;
                        }
                        break;
                    case 'float':
                        if (is_numeric($value)) {
                            $value = floatval($value);
                        } else {
                            $value = 0.0;
                        }
                        break;
                    case 'null':
                        $value = null;
                        break;
                }
                
                define($constant->name, $value);
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        $main_page = add_menu_page(
            __('PHP Constants Manager', 'php-constants-manager'),
            __('PHP Constants', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager',
            array($this, 'render_admin_page'),
            'dashicons-admin-generic',
            80
        );
        
        $my_constants_page = add_submenu_page(
            'php-constants-manager',
            __('My Constants', 'php-constants-manager'),
            __('My Constants', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager',
            array($this, 'render_admin_page')
        );
        
        $all_constants_page = add_submenu_page(
            'php-constants-manager',
            __('All Constants', 'php-constants-manager'),
            __('All Constants', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager-all-defines',
            array($this, 'render_all_defines_page')
        );
        
        $import_export_page = add_submenu_page(
            'php-constants-manager',
            __('Import/Export', 'php-constants-manager'),
            __('Import/Export', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager-import-export',
            array($this, 'render_import_export_page')
        );
        
        $help_page = add_submenu_page(
            'php-constants-manager',
            __('Help', 'php-constants-manager'),
            __('Help', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager-help',
            array($this, 'render_help_page')
        );
        
        // Add screen options hooks
        add_action("load-$main_page", array($this, 'add_my_constants_screen_options'));
        add_action("load-$all_constants_page", array($this, 'add_all_constants_screen_options'));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'php-constants-manager') === false) {
            return;
        }
        
        wp_enqueue_style('pcm-admin-style', PCM_PLUGIN_URL . 'assets/admin.css', array(), PCM_VERSION);
        wp_enqueue_script('pcm-admin-script', PCM_PLUGIN_URL . 'assets/admin.js', array('jquery'), PCM_VERSION, true);
        
        wp_localize_script('pcm-admin-script', 'pcm_ajax', array(
            'confirm_delete' => __('Are you sure you want to delete this constant?', 'php-constants-manager'),
            'confirm_bulk_delete' => __('Are you sure you want to delete the selected constants?', 'php-constants-manager'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pcm_check_constant')
        ));
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Check if editing or adding
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($action === 'edit' && $id) {
            $this->render_edit_page($id);
            return;
        } elseif ($action === 'add') {
            $this->render_add_page();
            return;
        }
        
        // Create list table instance
        $list_table = new PCM_List_Table();
        $list_table->prepare_items();
        
        // Prepare data for view
        $transient_notice = get_transient('pcm_admin_notice');
        if ($transient_notice) {
            delete_transient('pcm_admin_notice');
        }
        
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        
        $this->load_view('admin/constants-list', array(
            'list_table' => $list_table,
            'transient_notice' => $transient_notice,
            'message' => $message
        ));
    }
    
    /**
     * Render add page
     */
    public function render_add_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $this->render_form();
    }
    
    /**
     * Render edit page
     */
    public function render_edit_page($id) {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $constant = $this->db->get_constant($id);
        if (!$constant) {
            wp_die(__('Constant not found.', 'php-constants-manager'));
        }
        
        $this->render_form($constant);
    }
    
    /**
     * Render constant form
     */
    private function render_form($constant = null) {
        $is_edit = $constant !== null;
        $title = $is_edit ? __('Edit Constant', 'php-constants-manager') : __('Add New Constant', 'php-constants-manager');
        
        $this->load_view('admin/constant-form', array(
            'constant' => $constant,
            'title' => $title,
            'is_edit' => $is_edit
        ));
    }
    
    /**
     * Handle save constant
     */
    public function handle_save_constant() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        if (!check_admin_referer('pcm_save_constant', 'pcm_nonce')) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = sanitize_text_field($_POST['constant_name']);
        $value = sanitize_text_field($_POST['constant_value']);
        $type = sanitize_text_field($_POST['constant_type']);
        $is_active = !empty($_POST['constant_active']);
        $description = sanitize_textarea_field($_POST['constant_description']);
        
        // Validate constant name
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $name)) {
            wp_die(__('Invalid constant name', 'php-constants-manager'));
        }
        
        // Check if constant is predefined elsewhere and warn
        $predefined_check = $this->is_constant_predefined($name, $value, $type, $is_active);
        if ($predefined_check['is_predefined']) {
            $action_text = $id ? __('updated', 'php-constants-manager') : __('added', 'php-constants-manager');
            $message = sprintf(
                __('The constant "%s" has been %s, but it is already defined elsewhere with value: %s. Your definition will only take effect when the predefined constant is removed.', 'php-constants-manager'),
                $name,
                $action_text,
                var_export($predefined_check['existing_value'], true)
            );
            
            // Store message in transient to show after redirect
            set_transient('pcm_admin_notice', array(
                'type' => 'warning',
                'message' => $message
            ), 30);
        }
        
        // Save constant
        if ($id) {
            $this->db->update_constant($id, array(
                'value' => $value,
                'type' => $type,
                'is_active' => $is_active,
                'description' => $description
            ));
        } else {
            $this->db->insert_constant(array(
                'name' => $name,
                'value' => $value,
                'type' => $type,
                'is_active' => $is_active,
                'description' => $description
            ));
        }
        
        wp_redirect(admin_url('admin.php?page=php-constants-manager&message=saved'));
        exit;
    }
    
    /**
     * Handle delete constant
     */
    public function handle_delete_constant() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        if (!check_admin_referer('pcm_delete_constant', 'pcm_nonce')) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id) {
            $this->db->delete_constant($id);
        }
        
        wp_redirect(admin_url('admin.php?page=php-constants-manager&message=deleted'));
        exit;
    }
    
    /**
     * Handle toggle constant
     */
    public function handle_toggle_constant() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        if (!check_admin_referer('pcm_toggle_constant', 'pcm_nonce')) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id) {
            $this->db->toggle_constant($id);
        }
        
        wp_redirect(admin_url('admin.php?page=php-constants-manager&message=toggled'));
        exit;
    }
    
    /**
     * Handle bulk actions
     */
    public function handle_bulk_action() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        if (!check_admin_referer('pcm_bulk_action', 'pcm_nonce')) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        if ($action === '-1') {
            $action = isset($_POST['action2']) ? $_POST['action2'] : '';
        }
        
        $ids = isset($_POST['constant']) ? array_map('intval', $_POST['constant']) : array();
        
        if (empty($ids) || empty($action)) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager'));
            exit;
        }
        
        $message = '';
        
        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $this->db->delete_constant($id);
                }
                $message = 'bulk_deleted';
                break;
                
            case 'activate':
                foreach ($ids as $id) {
                    $this->db->update_constant($id, array('is_active' => true));
                }
                $message = 'bulk_activated';
                break;
                
            case 'deactivate':
                foreach ($ids as $id) {
                    $this->db->update_constant($id, array('is_active' => false));
                }
                $message = 'bulk_deactivated';
                break;
        }
        
        wp_redirect(admin_url('admin.php?page=php-constants-manager&message=' . $message));
        exit;
    }
    
    /**
     * AJAX handler to check if constant is defined
     */
    public function ajax_check_constant() {
        if (!check_ajax_referer('pcm_check_constant', 'nonce', false)) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        $constant_name = sanitize_text_field($_POST['constant_name']);
        
        if (empty($constant_name)) {
            wp_send_json_error('Invalid constant name');
        }
        
        $predefined_check = $this->is_constant_predefined($constant_name);
        
        wp_send_json_success(array(
            'is_defined' => defined($constant_name),
            'is_predefined' => $predefined_check['is_predefined'],
            'value' => $predefined_check['existing_value']
        ));
    }
    
    /**
     * AJAX handler to toggle constant status
     */
    public function ajax_toggle_constant() {
        if (!check_ajax_referer('pcm_toggle_constant', 'nonce', false)) {
            wp_send_json_error(__('Security check failed', 'php-constants-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$id) {
            wp_send_json_error(__('Invalid constant ID', 'php-constants-manager'));
        }
        
        $constant = $this->db->get_constant($id);
        if (!$constant) {
            wp_send_json_error(__('Constant not found', 'php-constants-manager'));
        }
        
        $new_status = !$constant->is_active;
        $this->db->update_constant($id, array('is_active' => $new_status));
        
        wp_send_json_success(array(
            'new_status' => $new_status,
            'message' => $new_status ? __('Constant activated', 'php-constants-manager') : __('Constant deactivated', 'php-constants-manager')
        ));
    }
    
    /**
     * Render all defines page
     */
    public function render_all_defines_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Create list table instance
        $list_table = new PCM_All_Defines_Table();
        $list_table->prepare_items();
        
        $this->load_view('admin/all-defines', array(
            'list_table' => $list_table
        ));
    }
    
    /**
     * Render help page
     */
    public function render_help_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $this->load_view('admin/help');
    }
    
    /**
     * Render import/export page
     */
    public function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Check for success/error messages
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        $error = isset($_GET['error']) ? $_GET['error'] : '';
        
        $this->load_view('admin/import-export', array(
            'message' => $message,
            'error' => $error
        ));
    }
    
    /**
     * Check if a constant is truly predefined (not defined by this plugin)
     * 
     * @param string $name Constant name
     * @param mixed $our_value Our stored value
     * @param string $our_type Our stored type
     * @param bool $is_active Whether our constant is active
     * @return array Array with 'is_predefined' boolean and 'existing_value' if predefined
     */
    public function is_constant_predefined($name, $our_value = null, $our_type = 'string', $is_active = true) {
        if (!defined($name)) {
            return array('is_predefined' => false, 'existing_value' => null);
        }
        
        $existing_value = constant($name);
        
        // If we don't have our value info, it's predefined
        if ($our_value === null) {
            return array('is_predefined' => true, 'existing_value' => $existing_value);
        }
        
        // Check if this constant exists in our database
        $our_constant = $this->db->get_constant_by_name($name);
        if (!$our_constant) {
            // Not in our database but is defined = predefined elsewhere
            return array('is_predefined' => true, 'existing_value' => $existing_value);
        }
        
        // Type-cast our value to match what it would be when defined
        $typed_our_value = $our_value;
        switch ($our_type) {
            case 'boolean':
                if (is_string($our_value)) {
                    $lower_value = strtolower(trim($our_value));
                    $typed_our_value = in_array($lower_value, ['true', '1', 'yes', 'on'], true);
                } else {
                    $typed_our_value = filter_var($our_value, FILTER_VALIDATE_BOOLEAN);
                }
                break;
            case 'integer':
                $typed_our_value = intval($our_value);
                break;
            case 'float':
                $typed_our_value = floatval($our_value);
                break;
            case 'null':
                $typed_our_value = null;
                break;
        }
        
        // Key insight: Constants from wp-config.php are defined BEFORE this plugin loads
        // So if our constant is active and we're in the load_managed_constants phase,
        // we might have failed to define it because it was already defined
        
        // If our constant is active and values match exactly, it could be either:
        // 1. We successfully defined it, OR 
        // 2. Something else defined it with the same value
        
        // The key is: if we're active and values match, we need to check if WE defined it
        // We can do this by checking if the constant was already defined before we tried to define it
        if ($is_active && $existing_value === $typed_our_value) {
            // If this plugin successfully defined the constant, it's not predefined
            // We can't reliably detect this after the fact, so we'll assume if values match
            // and we're active, then we either defined it or it matches our intended definition
            return array('is_predefined' => false, 'existing_value' => $existing_value);
        }
        
        // If our constant is inactive and values match, something else defined it
        if (!$is_active && $existing_value === $typed_our_value) {
            return array('is_predefined' => true, 'existing_value' => $existing_value);
        }
        
        // If values don't match, it's definitely predefined elsewhere
        return array('is_predefined' => true, 'existing_value' => $existing_value);
    }
    
    /**
     * Add screen options for My Constants page
     */
    public function add_my_constants_screen_options() {
        add_screen_option('per_page', array(
            'label' => __('Constants per page', 'php-constants-manager'),
            'default' => 50,
            'option' => 'constants_per_page'
        ));
        
        // Create temporary list table to get columns
        $list_table = new PCM_List_Table();
        $columns = $list_table->get_columns();
        
        // Remove checkbox column from options (always visible)
        unset($columns['cb']);
        
        // Column management works automatically with get_hidden_columns() in the table
    }
    
    /**
     * Add screen options for All Constants page
     */
    public function add_all_constants_screen_options() {
        add_screen_option('per_page', array(
            'label' => __('Constants per page', 'php-constants-manager'),
            'default' => 50,
            'option' => 'all_defines_per_page'
        ));
        
        // Create temporary list table to get columns
        $list_table = new PCM_All_Defines_Table();
        $columns = $list_table->get_columns();
        
        // Column management works automatically with get_hidden_columns() in the table
    }
    
    
    /**
     * Handle screen options save
     */
    public function set_screen_options($status, $option, $value) {
        if (in_array($option, array('constants_per_page', 'all_defines_per_page'))) {
            return $value;
        }
        return $status;
    }
    
    /**
     * Add settings link to plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=php-constants-manager') . '">' . __('Settings', 'php-constants-manager') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Handle CSV export
     */
    public function handle_export_csv() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        if (!check_admin_referer('pcm_export_csv', 'pcm_nonce')) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        // Get all constants
        $constants = $this->db->get_all_constants();
        
        if (empty($constants)) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=no_constants'));
            exit;
        }
        
        // Set headers for CSV download
        $filename = 'php-constants-' . date('Y-m-d-H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Create CSV output
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV headers
        fputcsv($output, array('Name', 'Value', 'Type', 'Active', 'Description'));
        
        // Add data rows
        foreach ($constants as $constant) {
            fputcsv($output, array(
                $constant->name,
                $constant->value,
                $constant->type,
                $constant->is_active ? '1' : '0',
                $constant->description
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Handle CSV import
     */
    public function handle_import_csv() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'php-constants-manager'));
        }
        
        if (!check_admin_referer('pcm_import_csv', 'pcm_nonce')) {
            wp_die(__('Security check failed', 'php-constants-manager'));
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=no_file'));
            exit;
        }
        
        $file = $_FILES['csv_file'];
        
        // Validate file type
        $file_info = pathinfo($file['name']);
        if (strtolower($file_info['extension']) !== 'csv') {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=invalid_file'));
            exit;
        }
        
        // Read CSV file
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=read_error'));
            exit;
        }
        
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $line = 0;
        
        // Skip header row if it exists
        $first_row = fgetcsv($handle);
        if ($first_row && (strtolower($first_row[0]) === 'name' || strtolower($first_row[0]) === 'constant name')) {
            // This looks like a header row, skip it
        } else {
            // This is data, process it
            rewind($handle);
        }
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $line++;
            
            // Skip empty rows
            if (empty(array_filter($data))) {
                continue;
            }
            
            // Validate minimum required columns
            if (count($data) < 3) {
                $errors++;
                continue;
            }
            
            $name = trim($data[0]);
            $value = isset($data[1]) ? trim($data[1]) : '';
            $type = isset($data[2]) ? trim($data[2]) : 'string';
            $is_active = isset($data[3]) ? (bool)$data[3] : true;
            $description = isset($data[4]) ? trim($data[4]) : '';
            
            // Validate constant name
            if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $name)) {
                $errors++;
                continue;
            }
            
            // Validate type
            if (!in_array($type, array('string', 'integer', 'float', 'boolean', 'null'))) {
                $type = 'string';
            }
            
            // Check if constant already exists in our database
            $existing = $this->db->get_constant_by_name($name);
            if ($existing) {
                $skipped++;
                continue;
            }
            
            // Insert constant
            $result = $this->db->insert_constant(array(
                'name' => $name,
                'value' => $value,
                'type' => $type,
                'is_active' => $is_active,
                'description' => $description
            ));
            
            if ($result) {
                $imported++;
            } else {
                $errors++;
            }
        }
        
        fclose($handle);
        
        // Build success message
        $message_parts = array();
        if ($imported > 0) {
            $message_parts[] = sprintf(_n('%d constant imported', '%d constants imported', $imported, 'php-constants-manager'), $imported);
        }
        if ($skipped > 0) {
            $message_parts[] = sprintf(_n('%d constant skipped (already exists)', '%d constants skipped (already exist)', $skipped, 'php-constants-manager'), $skipped);
        }
        if ($errors > 0) {
            $message_parts[] = sprintf(_n('%d error occurred', '%d errors occurred', $errors, 'php-constants-manager'), $errors);
        }
        
        $message = implode(', ', $message_parts);
        $redirect_url = admin_url('admin.php?page=php-constants-manager-import-export&message=' . urlencode($message));
        
        wp_redirect($redirect_url);
        exit;
    }
}

// Initialize plugin
add_action('plugins_loaded', array('PHP_Constants_Manager', 'get_instance'), 0);

// Activation hook
register_activation_hook(__FILE__, function() {
    $db = new PCM_DB();
    $db->create_table();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});