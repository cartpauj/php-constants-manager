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