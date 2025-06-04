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
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'integer':
                        $value = intval($value);
                        break;
                    case 'float':
                        $value = floatval($value);
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
        add_menu_page(
            __('PHP Constants Manager', 'php-constants-manager'),
            __('PHP Constants', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager',
            array($this, 'render_admin_page'),
            'dashicons-admin-generic',
            80
        );
        
        add_submenu_page(
            'php-constants-manager',
            __('All Constants', 'php-constants-manager'),
            __('All Constants', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'php-constants-manager',
            __('Add New Constant', 'php-constants-manager'),
            __('Add New', 'php-constants-manager'),
            'manage_options',
            'php-constants-manager-add',
            array($this, 'render_add_page')
        );
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
            'confirm_bulk_delete' => __('Are you sure you want to delete the selected constants?', 'php-constants-manager')
        ));
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Check if editing
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($action === 'edit' && $id) {
            $this->render_edit_page($id);
            return;
        }
        
        // Create list table instance
        $list_table = new PCM_List_Table();
        $list_table->prepare_items();
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('PHP Constants', 'php-constants-manager'); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=php-constants-manager-add'); ?>" class="page-title-action"><?php _e('Add New', 'php-constants-manager'); ?></a>
            
            <?php 
            // Check for transient notices
            $transient_notice = get_transient('pcm_admin_notice');
            if ($transient_notice) {
                delete_transient('pcm_admin_notice');
                ?>
                <div class="notice notice-<?php echo esc_attr($transient_notice['type']); ?> is-dismissible">
                    <p><?php echo esc_html($transient_notice['message']); ?></p>
                </div>
                <?php
            }
            ?>
            
            <?php if (isset($_GET['message'])): ?>
                <?php
                $messages = array(
                    'saved' => __('Constant saved successfully.', 'php-constants-manager'),
                    'deleted' => __('Constant deleted successfully.', 'php-constants-manager'),
                    'toggled' => __('Constant status updated successfully.', 'php-constants-manager'),
                    'bulk_deleted' => __('Selected constants deleted successfully.', 'php-constants-manager'),
                    'bulk_activated' => __('Selected constants activated successfully.', 'php-constants-manager'),
                    'bulk_deactivated' => __('Selected constants deactivated successfully.', 'php-constants-manager'),
                );
                $message = isset($messages[$_GET['message']]) ? $messages[$_GET['message']] : '';
                if ($message):
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php echo esc_html($message); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="pcm_bulk_action" />
                <?php wp_nonce_field('pcm_bulk_action', 'pcm_nonce'); ?>
                <?php $list_table->display(); ?>
            </form>
        </div>
        <?php
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
        ?>
        <div class="wrap">
            <h1><?php echo esc_html($title); ?></h1>
            
            <?php
            // Check if constant is already defined (for edit mode)
            if ($is_edit && defined($constant->name)) {
                $existing_value = constant($constant->name);
                ?>
                <div class="notice notice-warning">
                    <p><?php 
                        printf(
                            __('Note: The constant "%s" is currently defined with value: %s. Changes will only take effect if this predefined constant is removed.', 'php-constants-manager'),
                            esc_html($constant->name),
                            '<code>' . esc_html(var_export($existing_value, true)) . '</code>'
                        ); 
                    ?></p>
                </div>
                <?php
            }
            ?>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="pcm-form">
                <input type="hidden" name="action" value="pcm_save_constant" />
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?php echo esc_attr($constant->id); ?>" />
                <?php endif; ?>
                <?php wp_nonce_field('pcm_save_constant', 'pcm_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="constant-name"><?php _e('Constant Name', 'php-constants-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="constant-name" name="constant_name" class="regular-text" 
                                   value="<?php echo $is_edit ? esc_attr($constant->name) : ''; ?>" 
                                   required pattern="[A-Z][A-Z0-9_]*" 
                                   <?php echo $is_edit ? 'readonly' : ''; ?> />
                            <p class="description"><?php _e('Use uppercase letters, numbers, and underscores only. Must start with a letter.', 'php-constants-manager'); ?></p>
                            <?php if ($is_edit): ?>
                                <p class="description"><?php _e('Note: Constant names cannot be changed after creation.', 'php-constants-manager'); ?></p>
                            <?php endif; ?>
                            <div id="constant-name-feedback"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="constant-value"><?php _e('Value', 'php-constants-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="constant-value" name="constant_value" class="regular-text" 
                                   value="<?php echo $is_edit ? esc_attr($constant->value) : ''; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="constant-type"><?php _e('Type', 'php-constants-manager'); ?></label>
                        </th>
                        <td>
                            <select id="constant-type" name="constant_type">
                                <option value="string" <?php selected($is_edit ? $constant->type : '', 'string'); ?>><?php _e('String', 'php-constants-manager'); ?></option>
                                <option value="integer" <?php selected($is_edit ? $constant->type : '', 'integer'); ?>><?php _e('Integer', 'php-constants-manager'); ?></option>
                                <option value="float" <?php selected($is_edit ? $constant->type : '', 'float'); ?>><?php _e('Float', 'php-constants-manager'); ?></option>
                                <option value="boolean" <?php selected($is_edit ? $constant->type : '', 'boolean'); ?>><?php _e('Boolean', 'php-constants-manager'); ?></option>
                                <option value="null" <?php selected($is_edit ? $constant->type : '', 'null'); ?>><?php _e('NULL', 'php-constants-manager'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="constant-active"><?php _e('Status', 'php-constants-manager'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="constant-active" name="constant_active" value="1" 
                                       <?php checked($is_edit ? $constant->is_active : true, true); ?> />
                                <?php _e('Active', 'php-constants-manager'); ?>
                            </label>
                            <p class="description"><?php _e('Only active constants are loaded.', 'php-constants-manager'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="constant-description"><?php _e('Description', 'php-constants-manager'); ?></label>
                        </th>
                        <td>
                            <textarea id="constant-description" name="constant_description" rows="3" class="large-text"><?php 
                                echo $is_edit ? esc_textarea($constant->description) : ''; 
                            ?></textarea>
                            <p class="description"><?php _e('Optional: Describe what this constant is used for.', 'php-constants-manager'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php echo $is_edit ? __('Update Constant', 'php-constants-manager') : __('Add Constant', 'php-constants-manager'); ?>
                    </button>
                    <a href="<?php echo admin_url('admin.php?page=php-constants-manager'); ?>" class="button"><?php _e('Cancel', 'php-constants-manager'); ?></a>
                </p>
            </form>
        </div>
        <?php
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
        
        // Check if constant is already defined (only for new constants)
        if (!$id && defined($name)) {
            $existing_value = constant($name);
            $message = sprintf(
                __('The constant "%s" is already defined with value: %s. You can still add it to manage it when it\'s not predefined.', 'php-constants-manager'),
                $name,
                var_export($existing_value, true)
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
     * Add settings link to plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=php-constants-manager') . '">' . __('Settings', 'php-constants-manager') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

// Initialize plugin
add_action('plugins_loaded', array('PHP_Constants_Manager', 'get_instance'));

// Activation hook
register_activation_hook(__FILE__, function() {
    $db = new PCM_DB();
    $db->create_table();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});