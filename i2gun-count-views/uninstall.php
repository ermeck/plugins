<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

include dirname(__FILE__) . '/i2g-check.php';

if (i2g_check_field('i2g_views')) {
    global $wpdb;
    $query = "ALTER TABLE $wpdb->posts DROP i2g_views";
    $wpdb->query($query);
}

