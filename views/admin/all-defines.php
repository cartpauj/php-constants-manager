<?php
/**
 * All Defines page view
 * 
 * @var PCM_All_Defines_Table $list_table
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('All PHP Defines', 'php-constants-manager'); ?></h1>
    
    <p class="description">
        <?php _e('This table shows all PHP constants that are currently defined in your system, including built-in PHP constants, WordPress constants, and constants from plugins and themes.', 'php-constants-manager'); ?>
    </p>
    
    <form method="post">
        <?php 
        $list_table->search_box(__('Search Constants', 'php-constants-manager'), 'search_constants');
        $list_table->display(); 
        ?>
    </form>
</div>