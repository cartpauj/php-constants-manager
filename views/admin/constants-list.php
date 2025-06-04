<?php
/**
 * Constants list page view
 * 
 * @var PCM_List_Table $list_table
 * @var array $transient_notice
 * @var string $message
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('PHP Constants', 'php-constants-manager'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=php-constants-manager-add'); ?>" class="page-title-action"><?php _e('Add New', 'php-constants-manager'); ?></a>
    
    <?php 
    // Check for transient notices
    if ($transient_notice) {
        ?>
        <div class="notice notice-<?php echo esc_attr($transient_notice['type']); ?> is-dismissible">
            <p><?php echo esc_html($transient_notice['message']); ?></p>
        </div>
        <?php
    }
    ?>
    
    <?php if (isset($message) && $message): ?>
        <?php
        $messages = array(
            'saved' => __('Constant saved successfully.', 'php-constants-manager'),
            'deleted' => __('Constant deleted successfully.', 'php-constants-manager'),
            'toggled' => __('Constant status updated successfully.', 'php-constants-manager'),
            'bulk_deleted' => __('Selected constants deleted successfully.', 'php-constants-manager'),
            'bulk_activated' => __('Selected constants activated successfully.', 'php-constants-manager'),
            'bulk_deactivated' => __('Selected constants deactivated successfully.', 'php-constants-manager'),
        );
        $message_text = isset($messages[$message]) ? $messages[$message] : '';
        if ($message_text):
        ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html($message_text); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php
    // Display type filter links
    $views = $list_table->get_views();
    if (!empty($views)) {
        echo '<ul class="subsubsub">';
        $view_links = array();
        foreach ($views as $class => $view) {
            $view_links[] = "<li class='$class'>$view</li>";
        }
        echo implode('', $view_links);
        echo '</ul>';
        echo '<div class="clear"></div>';
    }
    ?>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="pcm_bulk_action" />
        <?php wp_nonce_field('pcm_bulk_action', 'pcm_nonce'); ?>
        <?php $list_table->display(); ?>
    </form>
</div>