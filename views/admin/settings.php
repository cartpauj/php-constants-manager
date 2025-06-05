<?php
/**
 * Settings page view
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Extract data array for use in template
$message = isset($data['message']) ? $data['message'] : '';
$error = isset($data['error']) ? $data['error'] : '';
$mu_plugin_exists = isset($data['mu_plugin_exists']) ? $data['mu_plugin_exists'] : false;
$early_loading_enabled = isset($data['early_loading_enabled']) ? $data['early_loading_enabled'] : false;
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('PHP Constants Manager - Settings', 'php-constants-manager'); ?></h1>
    
    <hr class="wp-header-end">
    
    <!-- Show success/error messages -->
    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                switch ($message) {
                    case 'early_loading_enabled':
                        esc_html_e('Early loading has been enabled. A must-use plugin file has been created to load your constants before other plugins.', 'php-constants-manager');
                        break;
                    case 'early_loading_disabled':
                        esc_html_e('Early loading has been disabled. The must-use plugin file has been removed.', 'php-constants-manager');
                        break;
                    case 'settings_saved':
                        esc_html_e('Settings have been saved successfully.', 'php-constants-manager');
                        break;
                    default:
                        echo esc_html($message);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php
                switch ($error) {
                    case 'mu_plugin_create_failed':
                        esc_html_e('Failed to create the must-use plugin file. Please check that the mu-plugins directory is writable or contact your hosting provider.', 'php-constants-manager');
                        break;
                    case 'mu_plugin_remove_failed':
                        esc_html_e('Failed to remove the must-use plugin file. Please manually delete the file at wp-content/mu-plugins/php-constants-manager-early.php', 'php-constants-manager');
                        break;
                    default:
                        echo esc_html($error);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <div class="pcm-settings-content">
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('pcm_save_settings', 'pcm_nonce'); ?>
            <input type="hidden" name="action" value="pcm_save_settings">
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="early_loading_enabled"><?php esc_html_e('Early Loading', 'php-constants-manager'); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php esc_html_e('Early Loading Options', 'php-constants-manager'); ?></legend>
                                
                                <label for="early_loading_enabled">
                                    <input type="checkbox" name="early_loading_enabled" id="early_loading_enabled" value="1" <?php checked($early_loading_enabled); ?>>
                                    <?php esc_html_e('Enable early loading of constants before other plugins', 'php-constants-manager'); ?>
                                </label>
                                
                                <p class="description">
                                    <?php esc_html_e('When enabled, this plugin will create a must-use plugin file that loads your constants before other regular plugins. This helps ensure your constants are available to other plugins that may need them. Note: If downstream code tries to define the same constant without checking if it already exists, PHP warnings may occur.', 'php-constants-manager'); ?>
                                </p>
                                
                                <?php if ($mu_plugin_exists): ?>
                                    <p class="pcm-status-info">
                                        <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                        <?php esc_html_e('Must-use plugin file exists:', 'php-constants-manager'); ?>
                                        <code><?php echo esc_html(WPMU_PLUGIN_DIR . '/php-constants-manager-early.php'); ?></code>
                                    </p>
                                <?php else: ?>
                                    <p class="pcm-status-info">
                                        <span class="dashicons dashicons-warning" style="color: #dba617;"></span>
                                        <?php esc_html_e('Must-use plugin file does not exist yet.', 'php-constants-manager'); ?>
                                    </p>
                                <?php endif; ?>
                            </fieldset>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php submit_button(__('Save Settings', 'php-constants-manager')); ?>
        </form>
        
    </div>
</div>

<style>
.pcm-settings-content {
    max-width: 1000px;
}

.pcm-status-info {
    margin: 10px 0;
    padding: 8px 12px;
    background: #f0f0f1;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pcm-status-info .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

.pcm-status-info code {
    background: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
}

.pcm-info-section {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #dcdcde;
}

.pcm-info-section h2 {
    color: #1d2327;
    border-bottom: 1px solid #ccd0d4;
    padding-bottom: 10px;
}

.pcm-info-section h3 {
    color: #1d2327;
    margin-top: 25px;
}

.pcm-info-section h4 {
    color: #1d2327;
    margin-top: 20px;
    margin-bottom: 10px;
}

.pcm-info-section code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
}

.pcm-info-section ul, .pcm-info-section ol {
    margin-left: 20px;
}

.pcm-info-section li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.pcm-info-box {
    background: #f8f9fa;
    border: 1px solid #e1e1e1;
    border-radius: 6px;
    padding: 20px;
    margin: 20px 0;
}

.pcm-info-box h4 {
    margin-top: 0;
    color: #0073aa;
}

.pcm-info-box ul {
    margin-bottom: 15px;
}

.pcm-info-box p:last-child {
    margin-bottom: 0;
}
</style>