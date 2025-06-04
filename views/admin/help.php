<?php
/**
 * Help page view
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('PHP Constants Manager - Help', 'php-constants-manager'); ?></h1>
    
    <hr class="wp-header-end">
    
    <div class="pcm-help-content">
        
        <h2><?php _e('What are PHP Constants?', 'php-constants-manager'); ?></h2>
        <p><?php _e('PHP constants are identifiers for values that cannot be changed during script execution. Unlike variables, constants do not have a dollar sign ($) prefix and are typically written in UPPERCASE.', 'php-constants-manager'); ?></p>
        <p><?php _e('Examples: <code>SITE_URL</code>, <code>API_KEY</code>, <code>DEBUG_MODE</code>', 'php-constants-manager'); ?></p>
        
        <h2><?php _e('How to Use This Plugin', 'php-constants-manager'); ?></h2>
        <ol>
            <li><strong><?php _e('Add New Constants:', 'php-constants-manager'); ?></strong> <?php _e('Go to "My Constants" and click "Add New" to create a new constant.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Manage Existing:', 'php-constants-manager'); ?></strong> <?php _e('View, edit, activate/deactivate, or delete your constants from the "My Constants" page.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('View All Constants:', 'php-constants-manager'); ?></strong> <?php _e('Use "All Constants" to see every constant defined in your WordPress installation.', 'php-constants-manager'); ?></li>
        </ol>
        
        <h2><?php _e('Constant Types', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('String', 'php-constants-manager'); ?></h3>
        <p><?php _e('Text values. Most common type for configuration settings.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>SITE_NAME</code> = "My Website", <code>API_URL</code> = "https://api.example.com"</p>
        
        <h3><?php _e('Integer', 'php-constants-manager'); ?></h3>
        <p><?php _e('Whole numbers (positive, negative, or zero).', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>MAX_POSTS</code> = 10, <code>TIMEOUT</code> = 30</p>
        
        <h3><?php _e('Float', 'php-constants-manager'); ?></h3>
        <p><?php _e('Decimal numbers.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>TAX_RATE</code> = 0.08, <code>VERSION</code> = 1.5</p>
        
        <h3><?php _e('Boolean', 'php-constants-manager'); ?></h3>
        <p><?php _e('True or false values. Use "true", "1", "yes", or "on" for true; anything else for false.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>DEBUG_MODE</code> = true, <code>MAINTENANCE</code> = false</p>
        
        <h3><?php _e('Null', 'php-constants-manager'); ?></h3>
        <p><?php _e('Represents no value. Useful for optional settings.', 'php-constants-manager'); ?></p>
        <p><strong><?php _e('Examples:', 'php-constants-manager'); ?></strong> <code>OPTIONAL_SETTING</code> = null</p>
        
        <h2><?php _e('Understanding the "Predefined" Column', 'php-constants-manager'); ?></h2>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong><?php _e('Predefined: No', 'php-constants-manager'); ?></strong> - <?php _e('This constant is only defined by this plugin. You have full control over it.', 'php-constants-manager'); ?></p>
        </div>
        
        <div class="notice notice-warning" style="margin: 20px 0;">
            <p><strong><?php _e('Predefined: Yes', 'php-constants-manager'); ?></strong> - <?php _e('This constant is already defined by WordPress core, another plugin, or your theme. Your definition will only take effect if the original definition is removed.', 'php-constants-manager'); ?></p>
        </div>
        
        <h2><?php _e('Why Can\'t I Override Existing Constants?', 'php-constants-manager'); ?></h2>
        <p><?php _e('PHP constants cannot be redefined once they are set. This is a fundamental rule of PHP, not a limitation of this plugin.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('What Happens When:', 'php-constants-manager'); ?></h3>
        <ul>
            <li><strong><?php _e('Constant is Active + Not Predefined:', 'php-constants-manager'); ?></strong> <?php _e('Your constant is successfully defined and working.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Constant is Active + Predefined:', 'php-constants-manager'); ?></strong> <?php _e('The predefined value takes precedence. Your value is stored but not used.', 'php-constants-manager'); ?></li>
            <li><strong><?php _e('Constant is Inactive:', 'php-constants-manager'); ?></strong> <?php _e('Your constant is not defined at all, regardless of predefined status.', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2><?php _e('Load Order Matters', 'php-constants-manager'); ?></h2>
        <p><?php _e('This plugin defines your constants during the <code>plugins_loaded</code> action with priority 1, which means it loads very early in the WordPress loading process. However, some constants may already be defined by:', 'php-constants-manager'); ?></p>
        <ul>
            <li><?php _e('<strong>wp-config.php file</strong> - Loads before any plugins', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>WordPress core</strong> - Many constants defined during WordPress bootstrap', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>Must-use plugins (mu-plugins)</strong> - Load before regular plugins', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>PHP extensions</strong> - Built-in PHP constants', 'php-constants-manager'); ?></li>
            <li><?php _e('<strong>Other plugins with higher priority</strong> - Plugins using <code>plugins_loaded</code> with priority 0 or negative values', 'php-constants-manager'); ?></li>
        </ul>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <p><strong><?php _e('Technical Note:', 'php-constants-manager'); ?></strong> <?php _e('Your constants are defined during <code>plugins_loaded</code> priority 1, which means they are available to:', 'php-constants-manager'); ?></p>
            <ul style="margin-left: 20px;">
                <li><?php _e('All theme code (themes load after plugins)', 'php-constants-manager'); ?></li>
                <li><?php _e('Most other plugins (unless they use higher priority)', 'php-constants-manager'); ?></li>
                <li><?php _e('WordPress hooks like <code>init</code>, <code>wp_loaded</code>, etc.', 'php-constants-manager'); ?></li>
            </ul>
        </div>
        
        <h2><?php _e('Best Practices', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('Naming Conventions', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Use UPPERCASE letters only', 'php-constants-manager'); ?></li>
            <li><?php _e('Separate words with underscores: <code>MY_CUSTOM_SETTING</code>', 'php-constants-manager'); ?></li>
            <li><?php _e('Start with a letter, not a number', 'php-constants-manager'); ?></li>
            <li><?php _e('Use descriptive names: <code>MAX_LOGIN_ATTEMPTS</code> instead of <code>MLA</code>', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('What to Define as Constants', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Configuration settings that never change during script execution', 'php-constants-manager'); ?></li>
            <li><?php _e('API keys and URLs', 'php-constants-manager'); ?></li>
            <li><?php _e('File paths and directories', 'php-constants-manager'); ?></li>
            <li><?php _e('Feature flags and debug settings', 'php-constants-manager'); ?></li>
            <li><?php _e('Version numbers and build information', 'php-constants-manager'); ?></li>
        </ul>
        
        <h3><?php _e('What NOT to Define as Constants', 'php-constants-manager'); ?></h3>
        <ul>
            <li><?php _e('Values that may need to change during script execution', 'php-constants-manager'); ?></li>
            <li><?php _e('User-specific data', 'php-constants-manager'); ?></li>
            <li><?php _e('Large arrays or objects (use variables instead)', 'php-constants-manager'); ?></li>
            <li><?php _e('Sensitive data like passwords (use secure storage methods)', 'php-constants-manager'); ?></li>
        </ul>
        
        <h2><?php _e('Common WordPress Constants', 'php-constants-manager'); ?></h2>
        <p><?php _e('Here are some constants you might see that are already defined by WordPress:', 'php-constants-manager'); ?></p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Constant', 'php-constants-manager'); ?></th>
                    <th><?php _e('Description', 'php-constants-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>WP_DEBUG</code></td>
                    <td><?php _e('Enables WordPress debug mode', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_DEBUG_LOG</code></td>
                    <td><?php _e('Enables debug logging to file', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>ABSPATH</code></td>
                    <td><?php _e('Absolute path to WordPress directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_CONTENT_DIR</code></td>
                    <td><?php _e('Path to wp-content directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>WP_CONTENT_URL</code></td>
                    <td><?php _e('URL to wp-content directory', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>DB_HOST</code></td>
                    <td><?php _e('Database server hostname', 'php-constants-manager'); ?></td>
                </tr>
                <tr>
                    <td><code>DB_NAME</code></td>
                    <td><?php _e('Database name', 'php-constants-manager'); ?></td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php _e('Troubleshooting', 'php-constants-manager'); ?></h2>
        
        <h3><?php _e('My constant isn\'t working', 'php-constants-manager'); ?></h3>
        <ol>
            <li><?php _e('Check if it\'s marked as "Active"', 'php-constants-manager'); ?></li>
            <li><?php _e('Check if it shows "Predefined: Yes" (meaning something else defined it first)', 'php-constants-manager'); ?></li>
            <li><?php _e('Verify the constant name follows PHP naming rules', 'php-constants-manager'); ?></li>
            <li><?php _e('Check for PHP syntax errors in your constant value', 'php-constants-manager'); ?></li>
            <li><?php _e('Consider load order: if another plugin defines the same constant with higher priority (priority 0 or negative), it will override yours', 'php-constants-manager'); ?></li>
        </ol>
        
        <h3><?php _e('I see a warning about predefined constants', 'php-constants-manager'); ?></h3>
        <p><?php _e('This warning appears when you try to define a constant that\'s already defined elsewhere. Your definition is saved but won\'t take effect unless the original definition is removed.', 'php-constants-manager'); ?></p>
        
        <h3><?php _e('How to check if my constant is working', 'php-constants-manager'); ?></h3>
        <p><?php _e('You can test your constants in your theme or plugin code. Remember that your constants are available after the <code>plugins_loaded</code> action (priority 1), so test them in appropriate hooks:', 'php-constants-manager'); ?></p>
        <pre><code>// Test in theme functions.php or after plugins_loaded
add_action('init', function() {
    if (defined('MY_CONSTANT')) {
        echo 'MY_CONSTANT is defined with value: ' . MY_CONSTANT;
    } else {
        echo 'MY_CONSTANT is not defined';
    }
});

// Or test directly in template files (themes load after plugins)
if (defined('MY_CONSTANT')) {
    // Your constant is available here
}</code></pre>
        
        <div class="notice notice-info" style="margin: 20px 0;">
            <h3><?php _e('Need More Help?', 'php-constants-manager'); ?></h3>
            <p><?php _e('Use the "All Constants" page to see every constant currently defined in your WordPress installation. This can help you understand what\'s already taken and avoid conflicts.', 'php-constants-manager'); ?></p>
        </div>
        
    </div>
</div>

<style>
.pcm-help-content h2 {
    border-bottom: 1px solid #ccd0d4;
    padding-bottom: 10px;
    margin-top: 30px;
}

.pcm-help-content h3 {
    color: #1d2327;
    margin-top: 20px;
}

.pcm-help-content code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
}

.pcm-help-content pre {
    background: #f6f7f7;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 15px;
    overflow-x: auto;
    margin: 15px 0;
}

.pcm-help-content pre code {
    background: none;
    padding: 0;
    font-size: 13px;
    line-height: 1.5;
}

.pcm-help-content ul, .pcm-help-content ol {
    margin-left: 20px;
}

.pcm-help-content li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.pcm-help-content .wp-list-table {
    margin: 20px 0;
}

.pcm-help-content .wp-list-table td {
    padding: 12px;
    vertical-align: top;
}

.pcm-help-content .wp-list-table code {
    font-weight: 600;
    color: #1d2327;
}
</style>