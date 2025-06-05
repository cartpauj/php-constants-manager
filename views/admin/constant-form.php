<?php
/**
 * Constant add/edit form view
 * 
 * @var object|null $constant
 * @var string $title
 * @var bool $is_edit
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>
    
    <?php
    // Check if constant is already defined (for edit mode)
    if ($is_edit) {
        $plugin_instance = PHP_Constants_Manager::get_instance();
        $predefined_check = $plugin_instance->is_constant_predefined(
            $constant->name, 
            $constant->value, 
            $constant->type, 
            $constant->is_active
        );
        
        if ($predefined_check['is_predefined']) {
            ?>
            <div class="notice notice-warning">
                <p><?php 
                    printf(
                        __('Note: The constant "%s" is currently defined with value: %s. Changes will only take effect if this predefined constant is removed.', 'php-constants-manager'),
                        esc_html($constant->name),
                        '<code>' . esc_html(var_export($predefined_check['existing_value'], true)) . '</code>'
                    ); 
                ?></p>
            </div>
            <?php
        }
    }
    ?>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="pcm-form pcm-modern-form">
        <input type="hidden" name="action" value="pcm_save_constant" />
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($constant->id); ?>" />
        <?php endif; ?>
        <?php wp_nonce_field('pcm_save_constant', 'pcm_nonce'); ?>
        
        <div class="pcm-form-container">
            <div class="pcm-form-grid">
                <div class="pcm-form-group pcm-form-group-full">
                    <label for="constant-name" class="pcm-form-label">
                        <span class="pcm-label-text"><?php _e('Constant Name', 'php-constants-manager'); ?></span>
                        <span class="pcm-required">*</span>
                    </label>
                    <div class="pcm-input-wrapper">
                        <input type="text" id="constant-name" name="constant_name" class="pcm-input" 
                               value="<?php echo $is_edit ? esc_attr($constant->name) : ''; ?>" 
                               required pattern="[A-Z][A-Z0-9_]*" 
                               placeholder="MY_CONSTANT_NAME"
                               <?php echo $is_edit ? 'readonly' : ''; ?> />
                        <?php if ($is_edit): ?>
                            <div class="pcm-input-icon">🔒</div>
                        <?php endif; ?>
                    </div>
                    <p class="pcm-help-text"><?php _e('Use uppercase letters, numbers, and underscores only. Must start with a letter.', 'php-constants-manager'); ?></p>
                    <?php if ($is_edit): ?>
                        <p class="pcm-help-text pcm-help-warning"><?php _e('Note: Constant names cannot be changed after creation.', 'php-constants-manager'); ?></p>
                    <?php endif; ?>
                    <div id="constant-name-feedback"></div>
                </div>

                <div class="pcm-form-group">
                    <label for="constant-value" class="pcm-form-label">
                        <span class="pcm-label-text"><?php _e('Value', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="pcm-input-wrapper">
                        <input type="text" id="constant-value" name="constant_value" class="pcm-input" 
                               value="<?php echo $is_edit ? esc_attr($constant->value) : ''; ?>" 
                               placeholder="Enter constant value" />
                    </div>
                </div>

                <div class="pcm-form-group">
                    <label for="constant-type" class="pcm-form-label">
                        <span class="pcm-label-text"><?php _e('Type', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="pcm-select-wrapper">
                        <select id="constant-type" name="constant_type" class="pcm-select">
                            <option value="string" <?php selected($is_edit ? $constant->type : '', 'string'); ?>><?php _e('String', 'php-constants-manager'); ?></option>
                            <option value="integer" <?php selected($is_edit ? $constant->type : '', 'integer'); ?>><?php _e('Integer', 'php-constants-manager'); ?></option>
                            <option value="float" <?php selected($is_edit ? $constant->type : '', 'float'); ?>><?php _e('Float', 'php-constants-manager'); ?></option>
                            <option value="boolean" <?php selected($is_edit ? $constant->type : '', 'boolean'); ?>><?php _e('Boolean', 'php-constants-manager'); ?></option>
                            <option value="null" <?php selected($is_edit ? $constant->type : '', 'null'); ?>><?php _e('NULL', 'php-constants-manager'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="pcm-form-group">
                    <label for="constant-active" class="pcm-form-label">
                        <span class="pcm-label-text"><?php _e('Status', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="pcm-checkbox-wrapper">
                        <label class="pcm-checkbox-label">
                            <input type="checkbox" id="constant-active" name="constant_active" value="1" 
                                   class="pcm-checkbox"
                                   <?php checked($is_edit ? $constant->is_active : true, true); ?> />
                            <span class="pcm-checkbox-custom"></span>
                            <span class="pcm-checkbox-text"><?php _e('Active', 'php-constants-manager'); ?></span>
                        </label>
                        <p class="pcm-help-text"><?php _e('Only active constants are loaded.', 'php-constants-manager'); ?></p>
                    </div>
                </div>

                <div class="pcm-form-group pcm-form-group-full">
                    <label for="constant-description" class="pcm-form-label">
                        <span class="pcm-label-text"><?php _e('Description', 'php-constants-manager'); ?></span>
                    </label>
                    <div class="pcm-textarea-wrapper">
                        <textarea id="constant-description" name="constant_description" rows="3" class="pcm-textarea" 
                                  placeholder="Optional: Describe what this constant is used for"><?php 
                            echo $is_edit ? esc_textarea($constant->description) : ''; 
                        ?></textarea>
                    </div>
                    <p class="pcm-help-text"><?php _e('Optional: Describe what this constant is used for.', 'php-constants-manager'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="pcm-form-actions">
            <button type="submit" class="pcm-btn pcm-btn-primary">
                <?php if ($is_edit): ?>
                    <span class="pcm-btn-icon">💾</span>
                    <?php _e('Update Constant', 'php-constants-manager'); ?>
                <?php else: ?>
                    <span class="pcm-btn-icon">➕</span>
                    <?php _e('Add Constant', 'php-constants-manager'); ?>
                <?php endif; ?>
            </button>
            <a href="<?php echo admin_url('admin.php?page=php-constants-manager'); ?>" class="pcm-btn pcm-btn-secondary">
                <span class="pcm-btn-icon">✖️</span>
                <?php _e('Cancel', 'php-constants-manager'); ?>
            </a>
        </div>
    </form>
</div>