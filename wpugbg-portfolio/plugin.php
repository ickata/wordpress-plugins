<?php

/*
   Plugin Name: WPUGBG Portfolio
   Plugin URI: http://blog.ickata.net/
   Description: Just a demo plug-in
   Author: Hristo Chakarov
   Version: 1.0
   Author URI: http://blog.ickata.net/
*/


/**************************************** REGISTER PORTFOLIO CUSTOM POST TYPE */

// Custom types
add_action('init','register_custom_types');
function register_custom_types(){
   
   // Portfolio type
   register_post_type('portfolio', array(
      'labels' => array(
         'name'                  => 'Portfolio',
         'singular_name'         => 'Project',
         'add_new'               => 'New Project',
         'add_new_item'          => 'New Project',
         'edit_item'             => 'Edit Project',
         'edit'                  => 'Edit',
         'new_item'              => 'New',
         'view_item'             => 'Preview',
         'search_items'          => 'Search Portfolio',
         'not_found'             => 'No Projects',
         'not_found_in_trash'    => 'No Projects in Trash'
      ),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true, 
      'query_var'          => 'portfolio',
      'rewrite'            => array('slug'=>'projects/%portfolio_category%','with_front'=>false),
      '_builtin'           => false, // It's a custom post type, not built in!
      '_edit_link'         => 'post.php?post=%d',
      'capability_type'    => 'post',
      'hierarchical'       => true,
      'supports'           => array('title','editor','author','thumbnail','excerpt', 'page-attributes'),
      'menu_position'      => 6
   ));
   
   // Portfolio category
   register_taxonomy('portfolio_category', 'portfolio',
      array(
         'hierarchical'     => true, 
         'label'            => 'Categories', 
         'singular_label'   => 'Category', 
         'public'           => true,
         'query_var'        => 'portfolio_category',
         'rewrite'          => array('slug' => 'projects','with_front'=>false),
         '_builtin'         => false
      )
   );
}


/************************************************* INCLUDE ONLY IF ADMIN AREA */

if ( is_admin() ) {
   // Post type sorting
   include( dirname(__FILE__) . '/sort.php' );
}
