<?php
/**
 * Import/Export page view
 * 
 * Data available via $data array:
 * - message: string
 * - error: string
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Import/Export Constants', 'php-constants-manager'); ?></h1>
    
    <?php if (!empty($data['message'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html(urldecode($data['message'])); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($data['error'])): ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php
                switch ($data['error']) {
                    case 'no_constants':
                        _e('No constants found to export.', 'php-constants-manager');
                        break;
                    case 'no_file':
                        _e('No file was uploaded.', 'php-constants-manager');
                        break;
                    case 'invalid_file':
                        _e('Invalid file type. Only CSV files are allowed.', 'php-constants-manager');
                        break;
                    case 'read_error':
                        _e('Error reading the uploaded file.', 'php-constants-manager');
                        break;
                    default:
                        echo esc_html($data['error']);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php
    // Display import error details if available
    $import_errors = get_transient('pcm_import_errors');
    if ($import_errors && !empty($import_errors)) {
        delete_transient('pcm_import_errors');
        ?>
        <div class="notice notice-error">
            <p><strong><?php _e('Import Errors:', 'php-constants-manager'); ?></strong></p>
            <p><?php _e('The following rows had errors and were not imported:', 'php-constants-manager'); ?></p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <?php 
                // Limit to first 10 errors to avoid overwhelming display
                $display_errors = array_slice($import_errors, 0, 10);
                foreach ($display_errors as $error): 
                ?>
                    <li><?php echo esc_html($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (count($import_errors) > 10): ?>
                <p><em><?php printf(__('... and %d more errors.', 'php-constants-manager'), count($import_errors) - 10); ?></em></p>
            <?php endif; ?>
        </div>
        <?php
    }
    ?>
    
    <div class="pcm-import-export-container">
        <div class="pcm-import-export-grid">
            <!-- Export Section -->
            <div class="pcm-card">
                <div class="pcm-card-header">
                    <h2><?php _e('Export Constants', 'php-constants-manager'); ?></h2>
                    <p class="description"><?php _e('Download all your constants as a CSV file for backup or migration purposes.', 'php-constants-manager'); ?></p>
                </div>
                <div class="pcm-card-body">
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="pcm-export-form">
                        <input type="hidden" name="action" value="pcm_export_csv" />
                        <?php wp_nonce_field('pcm_export_csv', 'pcm_nonce'); ?>
                        
                        <div class="pcm-export-info">
                            <div class="pcm-info-item">
                                <span class="pcm-info-icon">📄</span>
                                <div class="pcm-info-content">
                                    <strong><?php _e('Format:', 'php-constants-manager'); ?></strong>
                                    <span><?php _e('CSV (Comma Separated Values)', 'php-constants-manager'); ?></span>
                                </div>
                            </div>
                            <div class="pcm-info-item">
                                <span class="pcm-info-icon">📊</span>
                                <div class="pcm-info-content">
                                    <strong><?php _e('Includes:', 'php-constants-manager'); ?></strong>
                                    <span><?php _e('Name, Value, Type, Status, Description', 'php-constants-manager'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="pcm-btn pcm-btn-primary">
                            <span class="pcm-btn-icon">⬇️</span>
                            <?php _e('Export Constants', 'php-constants-manager'); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Import Section -->
            <div class="pcm-card">
                <div class="pcm-card-header">
                    <h2><?php _e('Import Constants', 'php-constants-manager'); ?></h2>
                    <p class="description"><?php _e('Upload a CSV file to import constants. Existing constants with the same name will be skipped.', 'php-constants-manager'); ?></p>
                </div>
                <div class="pcm-card-body">
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" class="pcm-import-form">
                        <input type="hidden" name="action" value="pcm_import_csv" />
                        <?php wp_nonce_field('pcm_import_csv', 'pcm_nonce'); ?>
                        
                        <div class="pcm-file-upload-area">
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required class="pcm-file-input" />
                            <label for="csv_file" class="pcm-file-label">
                                <span class="pcm-file-icon">📁</span>
                                <span class="pcm-file-text">
                                    <strong><?php _e('Choose CSV file', 'php-constants-manager'); ?></strong>
                                    <span><?php _e('or drag and drop', 'php-constants-manager'); ?></span>
                                </span>
                            </label>
                            <div class="pcm-file-selected" style="display: none;">
                                <span class="pcm-file-name"></span>
                                <button type="button" class="pcm-file-remove">×</button>
                            </div>
                        </div>
                        
                        <div class="pcm-import-requirements">
                            <h4><?php _e('CSV Format Requirements:', 'php-constants-manager'); ?></h4>
                            <ul>
                                <li><?php _e('First row can be headers (optional): Name, Value, Type, Active, Description', 'php-constants-manager'); ?></li>
                                <li><?php _e('Minimum columns: Name, Value, Type', 'php-constants-manager'); ?></li>
                                <li><?php _e('Constant names must be uppercase with underscores (A-Z, 0-9, _)', 'php-constants-manager'); ?></li>
                                <li><?php _e('Valid types: string, integer, float, boolean, null', 'php-constants-manager'); ?></li>
                                <li><?php _e('Active column: 1 for active, 0 for inactive (default: 1)', 'php-constants-manager'); ?></li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="pcm-btn pcm-btn-primary" disabled id="import-submit-btn">
                            <span class="pcm-btn-icon">⬆️</span>
                            <?php _e('Import Constants', 'php-constants-manager'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>