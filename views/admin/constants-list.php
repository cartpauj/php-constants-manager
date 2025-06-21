<?php
/**
 * Constants list page view
 * 
 * Data available via $data array:
 * - list_table: PHPCM_List_Table
 * - transient_notice: array
 * - message: string
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('PHP Constants', 'php-constants-manager'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=php-constants-manager&action=add')); ?>" class="page-title-action"><?php esc_html_e('Add New', 'php-constants-manager'); ?></a>
    
    <hr class="wp-header-end">
    
    <?php 
    // Check for transient notices
    if ($data['transient_notice']) {
        ?>
        <div class="notice notice-<?php echo esc_attr($data['transient_notice']['type']); ?> is-dismissible">
            <p><?php echo esc_html($data['transient_notice']['message']); ?></p>
        </div>
        <?php
    }
    ?>
    
    <?php if (isset($data['message']) && $data['message']): ?>
        <?php
        $messages = array(
            'saved' => __('Constant saved successfully.', 'php-constants-manager'),
            'deleted' => __('Constant deleted successfully.', 'php-constants-manager'),
            'toggled' => __('Constant status updated successfully.', 'php-constants-manager'),
            'bulk_deleted' => __('Selected constants deleted successfully.', 'php-constants-manager'),
            'bulk_activated' => __('Selected constants activated successfully.', 'php-constants-manager'),
            'bulk_deactivated' => __('Selected constants deactivated successfully.', 'php-constants-manager'),
        );
        $message_text = isset($messages[$data['message']]) ? $messages[$data['message']] : '';
        if ($message_text):
        ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html($message_text); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php
    // Display type filter links
    $views = $data['list_table']->get_views();
    if (!empty($views)) {
        echo '<ul class="subsubsub">';
        $view_links = array();
        foreach ($views as $class => $view) {
            $view_links[] = '<li class="' . esc_attr($class) . '">' . wp_kses_post($view) . '</li>';
        }
        echo wp_kses_post(implode('', $view_links));
        echo '</ul>';
        echo '<div class="clear"></div>';
    }
    ?>
    
    <form method="get" class="search-form">
        <?php
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        ?>
        <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_REQUEST['page'] ?? 'php-constants-manager'))); ?>" />
        <?php if (isset($_REQUEST['type_filter']) && sanitize_text_field(wp_unslash($_REQUEST['type_filter'])) !== 'all'): ?>
            <input type="hidden" name="type_filter" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_REQUEST['type_filter']))); ?>" />
        <?php endif; ?>
        <?php
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        ?>
        <?php $data['list_table']->search_box(esc_html__('Search Constants', 'php-constants-manager'), 'search_constants'); ?>
    </form>
    
    <form method="post">
        <?php wp_nonce_field('phpcm_bulk_action', 'phpcm_nonce'); ?>
        <div class="constants-table-wrapper">
            <?php $data['list_table']->display(); ?>
        </div>
    </form>
</div>