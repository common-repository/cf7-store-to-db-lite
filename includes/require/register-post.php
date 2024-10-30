<?php

defined('ABSPATH') or die('No script kiddies please!!');

$labels = array(
    'name' => _x('CF7 Entries: Form Store TO DB', 'post type general name', 'contact-form-7-store-to-db-lite'),
    'singular_name' => _x('Form Store TO DB', 'post type singular name', 'contact-form-7-store-to-db-lite'),
    'menu_name' => _x('CF7 Entries', 'admin menu', 'contact-form-7-store-to-db-lite'),
    'name_admin_bar' => _x('Form Store TO DB', 'add new on admin bar', 'contact-form-7-store-to-db-lite'),
    'add_new' => _x('New', 'Form Store TO DB', 'contact-form-7-store-to-db-lite'),
    'edit_item' => __('View Full Entry Details', 'contact-form-7-store-to-db-lite'),
    'view_item' => __('&nbsp;', 'contact-form-7-store-to-db-lite'),
    'all_items' => __('All Entries', 'contact-form-7-store-to-db-lite'),
    'search_items' => __('Search Entries', 'contact-form-7-store-to-db-lite'),
    'parent_item_colon' => __('Parent Entries:', 'contact-form-7-store-to-db-lite'),
    'not_found' => __('No Entries Found.', 'contact-form-7-store-to-db-lite'),
    'not_found_in_trash' => __('No Entries Found in Trash.', 'contact-form-7-store-to-db-lite')
);

$args = array(
    'labels' => $labels,
    'description' => __('Description.', 'contact-form-7-store-to-db-lite'),
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_icon' => 'dashicons-admin-users',
    'query_var' => true,
    'rewrite' => array('slug' => 'cf7stdb-entries'),
    'capability_type' => 'post',
    'capabilities' => array(
        'create_posts' => 'do_not_allow',
        'edit_posts' => true,
        'edit_post' => false,
        'delete_post' => true
    ),
    'map_meta_cap' => true,
    'has_archive' => false,
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => false
);
