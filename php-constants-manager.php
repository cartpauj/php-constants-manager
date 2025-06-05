<?php
/**
 * Plugin Name: PHP Constants Manager
 * Plugin URI: https://example.com/php-constants-manager
 * Description: Safely manage PHP constants (defines) through the WordPress admin interface
 * Version: 1.1.0
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
define('PCM_VERSION', '1.1.0');
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
     * Track constants defined by this plugin
     */
    private $defined_by_plugin = array();
    
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
        add_action('admin_init', array($this, 'handle_admin_actions'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_notices', array($this, 'show_admin_notices'));
        
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
     * Handle admin actions (including bulk actions)
     */
    public function handle_admin_actions() {
        // Only process on our plugin pages
        if (!isset($_GET['page']) || strpos($_GET['page'], 'php-constants-manager') !== 0) {
            return;
        }
        
        // Process bulk actions for the main constants page
        if ($_GET['page'] === 'php-constants-manager') {
            $this->process_bulk_actions();
        }
    }
    
    /**
     * Load view template
     */
    private function load_view($template, $data = array()) {
        $template_path = $this->views_path . $template . '.php';
        
        if (!file_exists($template_path)) {
            wp_die(sprintf(__('View template not found: %s', 'php-constants-manager'), $template));
        }
        
        // Make data available to template (avoiding extract for security)
        // Variables will be accessed via $data array in templates
        
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
        // Check if table exists first for performance
        if (!$this->db->table_exists()) {
            // Only run create_table if table doesn't exist
            $table_ready = $this->db->create_table();
            
            // If table creation failed, log and bail out gracefully
            if (!$table_ready) {
                $error_msg = "PHP Constants Manager: Cannot load constants - database table creation failed.";
                if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                    error_log($error_msg);
                }
                
                // Store error for admin notice (only for admin users)
                if (function_exists('current_user_can') && current_user_can('manage_options')) {
                    set_transient('pcm_load_error', $error_msg, 300);
                }
                
                return;
            }
        }
        
        $constants = $this->db->get_active_constants();
        
        foreach ($constants as $constant) {
            if (!defined($constant->name)) {
                $value = $constant->value;
                
                switch ($constant->type) {
                    case 'boolean':
                        if (is_string($value)) {
                            $lower_value = strtolower(trim($value));
                            // Handle various string representations of boolean values
                            if (in_array($lower_value, ['true', '1', 'yes', 'on'], true)) {
                                $value = true;
                            } elseif (in_array($lower_value, ['false', '0', 'no', 'off', ''], true)) {
                                $value = false;
                            } else {
                                // Fallback to filter_var for other cases
                                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                                if ($value === null) {
                                    $value = false; // Default to false for invalid boolean strings
                                }
                            }
                        } elseif (is_numeric($value)) {
                            $value = (bool)intval($value);
                        } else {
                            $value = (bool)$value;
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
                // Track that we successfully defined this constant
                $this->defined_by_plugin[] = $constant->name;
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
        // Handle value properly - preserve quotes and special characters
        $value = isset($_POST['constant_value']) ? wp_unslash($_POST['constant_value']) : '';
        $type = sanitize_text_field($_POST['constant_type']);
        $is_active = !empty($_POST['constant_active']);
        // Handle description properly - preserve quotes and special characters
        $description = isset($_POST['constant_description']) ? wp_unslash($_POST['constant_description']) : '';
        
        // Validate constant name
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $name)) {
            wp_die(__('Invalid constant name', 'php-constants-manager'));
        }
        
        // Validate and normalize value based on type
        $validation_result = $this->validate_constant_value($value, $type);
        if ($validation_result['error']) {
            // Store error message in transient
            set_transient('pcm_admin_notice', array(
                'type' => 'error',
                'message' => $validation_result['message']
            ), 30);
            
            // Redirect back to form
            $redirect_url = $id ? 
                admin_url('admin.php?page=php-constants-manager&action=edit&id=' . $id) :
                admin_url('admin.php?page=php-constants-manager&action=add');
            wp_redirect($redirect_url);
            exit;
        }
        
        // Use the normalized value (already normalized to lowercase for booleans)
        $value = $validation_result['value'];
        
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
            $result = $this->db->update_constant($id, array(
                'value' => $value,
                'type' => $type,
                'is_active' => $is_active,
                'description' => $description
            ));
            
            if ($result !== false) {
                wp_redirect(admin_url('admin.php?page=php-constants-manager&message=saved'));
                exit;
            } else {
                wp_die(__('Failed to update constant.', 'php-constants-manager'));
            }
        } else {
            // Check if constant already exists in our database
            $existing_constant = $this->db->get_constant_by_name($name);
            if ($existing_constant) {
                // Store error message in transient
                set_transient('pcm_admin_notice', array(
                    'type' => 'error',
                    'message' => sprintf(
                        __('A constant with the name "%s" already exists. Please <a href="%s">edit the existing constant</a> instead of creating a new one.', 'php-constants-manager'),
                        esc_html($name),
                        admin_url('admin.php?page=php-constants-manager&action=edit&id=' . $existing_constant->id)
                    )
                ), 30);
                
                wp_redirect(admin_url('admin.php?page=php-constants-manager&action=add'));
                exit;
            }
            
            $result = $this->db->insert_constant(array(
                'name' => $name,
                'value' => $value,
                'type' => $type,
                'is_active' => $is_active,
                'description' => $description
            ));
            
            if ($result !== false) {
                wp_redirect(admin_url('admin.php?page=php-constants-manager&message=saved'));
                exit;
            } else {
                wp_die(__('Failed to save constant.', 'php-constants-manager'));
            }
        }
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
     * Process bulk actions (called from admin page)
     */
    private function process_bulk_actions() {
        // Only process if this is a POST request with bulk action data
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
            return;
        }
        
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
        
        if (empty($ids) || empty($action) || $action === '-1') {
            return; // No action selected or no items selected
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
        
        if ($message) {
            // Set success message in transient and redirect
            $message_text = '';
            switch ($message) {
                case 'bulk_deleted':
                    $message_text = sprintf(_n('%d constant deleted successfully.', '%d constants deleted successfully.', count($ids), 'php-constants-manager'), count($ids));
                    break;
                case 'bulk_activated':
                    $message_text = sprintf(_n('%d constant activated successfully.', '%d constants activated successfully.', count($ids), 'php-constants-manager'), count($ids));
                    break;
                case 'bulk_deactivated':
                    $message_text = sprintf(_n('%d constant deactivated successfully.', '%d constants deactivated successfully.', count($ids), 'php-constants-manager'), count($ids));
                    break;
            }
            
            if ($message_text) {
                set_transient('pcm_admin_notice', array(
                    'type' => 'success',
                    'message' => $message_text
                ), 30);
            }
            
            wp_redirect(admin_url('admin.php?page=php-constants-manager'));
            exit;
        }
    }
    
    /**
     * Handle bulk actions (legacy admin-post handler)
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
        
        // Check if we actually defined this constant during load_managed_constants
        if (in_array($name, $this->defined_by_plugin)) {
            // We successfully defined it, so it's not predefined
            return array('is_predefined' => false, 'existing_value' => $existing_value);
        }
        
        // If we didn't define it but it exists and we have it in our database,
        // then something else defined it first (e.g., wp-config.php)
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
     * Show admin notices for database errors
     */
    public function show_admin_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Check for database creation errors
        $db_error = get_transient('pcm_db_error');
        if ($db_error) {
            delete_transient('pcm_db_error');
            ?>
            <div class="notice notice-error">
                <p><strong><?php _e('PHP Constants Manager Database Error:', 'php-constants-manager'); ?></strong></p>
                <p><?php echo esc_html($db_error); ?></p>
                <p>
                    <?php _e('Please check:', 'php-constants-manager'); ?>
                </p>
                <ul style="margin-left: 20px;">
                    <li><?php _e('Database user has CREATE TABLE permissions', 'php-constants-manager'); ?></li>
                    <li><?php _e('Sufficient disk space available', 'php-constants-manager'); ?></li>
                    <li><?php _e('MySQL version compatibility', 'php-constants-manager'); ?></li>
                    <li><?php _e('WordPress database configuration in wp-config.php', 'php-constants-manager'); ?></li>
                </ul>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=php-constants-manager-help#troubleshooting'); ?>" class="button">
                        <?php _e('View Troubleshooting Guide', 'php-constants-manager'); ?>
                    </a>
                    <button type="button" class="button button-secondary" onclick="location.reload();">
                        <?php _e('Retry', 'php-constants-manager'); ?>
                    </button>
                </p>
            </div>
            <?php
        }
        
        // Check for constant loading errors
        $load_error = get_transient('pcm_load_error');
        if ($load_error) {
            delete_transient('pcm_load_error');
            ?>
            <div class="notice notice-warning">
                <p><strong><?php _e('PHP Constants Manager Warning:', 'php-constants-manager'); ?></strong></p>
                <p><?php echo esc_html($load_error); ?></p>
                <p>
                    <?php _e('Your managed constants are not being loaded. The plugin interface may not work correctly until this is resolved.', 'php-constants-manager'); ?>
                </p>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=php-constants-manager-help#troubleshooting'); ?>" class="button button-primary">
                        <?php _e('View Troubleshooting Guide', 'php-constants-manager'); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Validate constant value based on its type
     * 
     * @param string $value The value to validate
     * @param string $type The expected type
     * @return array Array with 'error' boolean, 'message' string, and 'value' (normalized)
     */
    private function validate_constant_value($value, $type) {
        $result = array(
            'error' => false,
            'message' => '',
            'value' => $value
        );
        
        switch ($type) {
            case 'string':
                // Strings are always valid, no validation needed
                break;
                
            case 'integer':
                if (!is_numeric($value) || (string)(int)$value !== (string)$value) {
                    $result['error'] = true;
                    $result['message'] = sprintf(
                        __('Invalid integer value "%s". Please enter a whole number (e.g., 42, -10, 0).', 'php-constants-manager'),
                        esc_html($value)
                    );
                } else {
                    $result['value'] = (string)(int)$value; // Normalize
                }
                break;
                
            case 'float':
                if (!is_numeric($value)) {
                    $result['error'] = true;
                    $result['message'] = sprintf(
                        __('Invalid float value "%s". Please enter a number (e.g., 3.14, -2.5, 10).', 'php-constants-manager'),
                        esc_html($value)
                    );
                } else {
                    $result['value'] = (string)(float)$value; // Normalize
                }
                break;
                
            case 'boolean':
                $lower_value = strtolower(trim($value));
                $valid_true = array('true', '1', 'yes', 'on');
                $valid_false = array('false', '0', 'no', 'off', '');
                
                if (!in_array($lower_value, array_merge($valid_true, $valid_false), true)) {
                    $result['error'] = true;
                    $result['message'] = sprintf(
                        __('Invalid boolean value "%s". Please enter one of: true, false, 1, 0, yes, no, on, off (or leave empty for false).', 'php-constants-manager'),
                        esc_html($value)
                    );
                } else {
                    // Normalize to true/false string
                    $result['value'] = in_array($lower_value, $valid_true, true) ? 'true' : 'false';
                }
                break;
                
            case 'null':
                // For null type, value should be empty
                $result['value'] = '';
                break;
                
            default:
                $result['error'] = true;
                $result['message'] = sprintf(
                    __('Invalid constant type "%s".', 'php-constants-manager'),
                    esc_html($type)
                );
        }
        
        return $result;
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
        
        // Create CSV output using WordPress filesystem API
        // Add UTF-8 BOM for proper Excel/spreadsheet compatibility
        echo chr(0xEF).chr(0xBB).chr(0xBF);
        
        // Output CSV headers
        echo 'Name,Value,Type,Active,Description' . "\r\n";
        
        // Add data rows
        foreach ($constants as $constant) {
            $row = array(
                $constant->name,
                $constant->value,
                $constant->type,
                $constant->is_active ? '1' : '0',
                $constant->description
            );
            
            // Properly escape CSV values
            $escaped_row = array();
            foreach ($row as $field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    $escaped_row[] = '"' . str_replace('"', '""', $field) . '"';
                } else {
                    $escaped_row[] = $field;
                }
            }
            
            echo implode(',', $escaped_row) . "\r\n";
        }
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
        
        // Read CSV file using WordPress filesystem API
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        
        $file_contents = $wp_filesystem->get_contents($file['tmp_name']);
        if ($file_contents === false) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=read_error'));
            exit;
        }
        
        // Remove UTF-8 BOM if present (common with files exported from Excel/Google Sheets)
        $bom = pack('H*','EFBBBF'); // UTF-8 BOM bytes: 0xEF 0xBB 0xBF
        if (substr($file_contents, 0, 3) === $bom) {
            $file_contents = substr($file_contents, 3);
        }
        
        // Parse CSV data
        $csv_lines = explode("\n", $file_contents);
        $csv_data = array();
        foreach ($csv_lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $csv_data[] = str_getcsv($line);
        }
        
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $updated = 0; // Track updated constants
        $error_details = array(); // Track specific errors
        $line = 0;
        
        // Check if overwrite existing constants is enabled
        $overwrite_existing = !empty($_POST['overwrite_existing']);
        
        // Validate and skip header row (required)
        if (empty($csv_data) || !isset($csv_data[0][0])) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=empty_file'));
            exit;
        }
        
        $first_row = $csv_data[0];
        $first_cell = strtolower(trim($first_row[0]));
        
        // Require header row
        if ($first_cell !== 'name' && $first_cell !== 'constant name') {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=missing_header'));
            exit;
        }
        
        // Validate minimum required headers
        if (count($first_row) < 3) {
            wp_redirect(admin_url('admin.php?page=php-constants-manager-import-export&error=invalid_header'));
            exit;
        }
        
        // Start processing from row 2 (skip header)
        $data_start_index = 1;
        
        for ($i = $data_start_index; $i < count($csv_data); $i++) {
            $data = $csv_data[$i];
            $csv_line_number = $i + 1; // Actual line number in CSV file
            $line++; // Processing counter
            
            // Skip empty rows
            if (empty(array_filter($data))) {
                continue;
            }
            
            // Validate minimum required columns
            if (count($data) < 3) {
                $errors++;
                $error_details[] = sprintf(
                    __('Line %d: Missing required columns (need at least Name, Value, Type)', 'php-constants-manager'),
                    $csv_line_number
                );
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
                $error_details[] = sprintf(
                    __('Line %d: Invalid constant name "%s" (must be uppercase letters, numbers, and underscores only)', 'php-constants-manager'),
                    $csv_line_number,
                    esc_html($name)
                );
                continue;
            }
            
            // Validate type
            if (!in_array($type, array('string', 'integer', 'float', 'boolean', 'null'))) {
                $type = 'string';
            }
            
            // Validate value matches type
            $validation_result = $this->validate_constant_value($value, $type);
            if ($validation_result['error']) {
                $errors++;
                $error_details[] = sprintf(
                    __('Line %d: %s (Constant: %s)', 'php-constants-manager'),
                    $csv_line_number,
                    $validation_result['message'],
                    esc_html($name)
                );
                continue;
            }
            
            // Use normalized value
            $value = $validation_result['value'];
            
            // Check if constant already exists in our database
            $existing = $this->db->get_constant_by_name($name);
            if ($existing) {
                if ($overwrite_existing) {
                    // Update existing constant
                    $result = $this->db->update_constant($existing->id, array(
                        'value' => $value,
                        'type' => $type,
                        'is_active' => $is_active,
                        'description' => $description
                    ));
                    
                    if ($result !== false) {
                        $updated++;
                    } else {
                        $errors++;
                        $error_details[] = sprintf(
                            __('Line %d: Database error updating constant \"%s\"', 'php-constants-manager'),
                            $csv_line_number,
                            esc_html($name)
                        );
                    }
                } else {
                    // Skip existing constant
                    $skipped++;
                }
                continue;
            }
            
            // Insert new constant
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
                $error_details[] = sprintf(
                    __('Line %d: Database error saving constant \"%s\"', 'php-constants-manager'),
                    $csv_line_number,
                    esc_html($name)
                );
            }
        }
        
        // Build success message
        $message_parts = array();
        if ($imported > 0) {
            $message_parts[] = sprintf(_n('%d constant imported', '%d constants imported', $imported, 'php-constants-manager'), $imported);
        }
        if ($updated > 0) {
            $message_parts[] = sprintf(_n('%d constant updated', '%d constants updated', $updated, 'php-constants-manager'), $updated);
        }
        if ($skipped > 0) {
            $message_parts[] = sprintf(_n('%d constant skipped (already exists)', '%d constants skipped (already exist)', $skipped, 'php-constants-manager'), $skipped);
        }
        if ($errors > 0) {
            $message_parts[] = sprintf(_n('%d error occurred', '%d errors occurred', $errors, 'php-constants-manager'), $errors);
        }
        
        $message = implode(', ', $message_parts);
        
        // Store error details in transient if there are errors
        if (!empty($error_details)) {
            set_transient('pcm_import_errors', $error_details, 300); // 5 minutes
        }
        
        $redirect_url = admin_url('admin.php?page=php-constants-manager-import-export&message=' . urlencode($message));
        
        wp_redirect($redirect_url);
        exit;
    }
}

// Initialize plugin
add_action('plugins_loaded', array('PHP_Constants_Manager', 'get_instance'), 0);

// Activation hook
register_activation_hook(__FILE__, 'pcm_activation_hook');

/**
 * Plugin activation hook
 */
function pcm_activation_hook() {
    $db = new PCM_DB();
    $db->create_table();
    
    // Store the database version
    update_option('pcm_db_version', PCM_VERSION);
}

/**
 * Check for database updates on plugin load
 */
add_action('plugins_loaded', 'pcm_check_db_version', 0);

function pcm_check_db_version() {
    $installed_version = get_option('pcm_db_version', '0');
    
    // If version has changed, run database update
    if (version_compare($installed_version, PCM_VERSION, '<')) {
        $db = new PCM_DB();
        $result = $db->create_table();
        
        if ($result) {
            update_option('pcm_db_version', PCM_VERSION);
        }
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});