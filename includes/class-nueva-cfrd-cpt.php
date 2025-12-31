<?php

class Nueva_CFRD_CPT
{

    public function run()
    {
        add_action('init', array($this, 'register_cpt'));
    }

    public function register_cpt()
    {
        $labels = array(
            'name' => 'Nueva Layouts',
            'singular_name' => 'Nueva Layout',
            'menu_name' => 'Nueva Layouts',
            'name_admin_bar' => 'Nueva Layout',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Layout',
            'new_item' => 'New Layout',
            'edit_item' => 'Edit Layout',
            'view_item' => 'View Layout',
            'all_items' => 'All Layouts',
            'search_items' => 'Search Layouts',
            'not_found' => 'No layouts found.',
            'not_found_in_trash' => 'No layouts found in Trash.',
        );

        $args = array(
            'labels' => $labels,
            'public' => false, // Not accessible via URL directly
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'nueva-layout'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-layout',
            'supports' => array('title'), // Only title, we use meta boxes for the rest
        );

        register_post_type('nueva_layout', $args);
    }
}
