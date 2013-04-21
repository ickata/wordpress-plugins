<?php
/*
   Plugin Name: TinyMCE Hello World
   Plugin URI: http://blog.ickata.net
   Description: Just a TinyMCE Example
   Author: Hristo Chakarov
   Version: 1.1
   Author URI: http://blog.ickata.net/
*/

include 'simple_html_dom.php';   // php DOM parser library
 
// init process for button control
add_action('init', 'mcehelloworld_addbuttons');
function mcehelloworld_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
      return;
   }
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
      add_filter("mce_external_plugins", "add_mcehelloworld_tinymce_plugin");
      add_filter('mce_buttons', 'register_mcehelloworld_button');
   }
}
 
function register_mcehelloworld_button( $buttons ) {
   array_push($buttons, "separator", "mcehelloworld");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_mcehelloworld_tinymce_plugin( $plugin_array ) {
   $plugin_array['mcehelloworld'] = plugins_url( '/tinymce/plugins/mcehelloworld/plugin.js', __FILE__ );
   return $plugin_array;
}

// enqueue CSS file
add_action('init', 'mcehelloworld_init');
function mcehelloworld_init() {
   wp_register_style(
      'mcehelloworld',
      plugins_url( '/css/style.css', __FILE__ ),
      array(), // no dependencies
      '1.0'
   );
}

add_action( 'wp_print_styles', 'mcehelloworld_print_styles' );
function mcehelloworld_print_styles() {
   wp_enqueue_style('mcehelloworld');
}

// filter content - replace placeholder with some custom DOM
add_filter('the_content', 'mcehelloworld_the_content');
function mcehelloworld_the_content( $content ) {
   // use the DOM parser to get all images as objects
   $dom     = str_get_html( $content );
   $images  = $dom->find('img[name=mcehelloworld]');
   
   foreach ( $images as $img ) {
      // check if the image is our placeholder
      if ( $img->name == 'mcehelloworld' ) {
         // extract properties from the src query string
         preg_match( "/img\/space.gif\?([^\"]+)/", $img->src, $match );
         // we have to replace &amp; entity with &
         $query   = str_replace( '&amp;', '&', $match[1] );
         parse_str( $query, $params );
         // map properties
         $name    = $params['name'];
         
         // generate the DOM that should replace the placeholder on the page
         $html =
            "<span class=\"mcehelloworld-wrapper\">" .
               "<span style=\"width: {$img->width}px; height: {$img->height}px;\">" .
                  "<span>Hello, {$name}</span>" .
               "</span>" .
            "</span>";
         
         // replace, again using the DOM library
         $img->outertext = $html;
      }
   }
   
   // return the html as string
   return $dom->save();
}