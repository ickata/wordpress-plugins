<?php

/**
 * jQueryUI Portfolio Sort UI
**/


/**************************************************** PORTFOLIO SORT TEMPLATE */

// this function is called when rendering the template in Admin Area
function portfolio_display_sort_page() {
   $projects = new WP_Query('post_type=portfolio&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
<div class="wrap">
   <h2>Projects Order <img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" style="display: none" /></h2>
   <ul class="portfolio-sort-list">
   <?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
      <li id="<?php the_id(); ?>">
         <?php if ( has_post_thumbnail() ) the_post_thumbnail( array(100,30) ); echo ' '; the_title(); ?>
      </li>
   <?php endwhile; ?>
   </ul>
</div><!-- .wrap -->
 
<?php
}

// register the page
add_action('admin_menu' , 'portfolio_add_sort_page'); 
function portfolio_add_sort_page() {
   add_submenu_page('edit.php?post_type=portfolio', 'Projects Order', 'Order', 'edit_posts', basename(__FILE__), 'portfolio_display_sort_page');
}


/************************************************* INCLUDE CSS & JS RESOURCES */
add_action('admin_init', 'portfolio_sort_init');
function portfolio_sort_init() {
   wp_register_style(
      'portfolio-sort-ui',
      plugins_url( '/css/sort.css', __FILE__ ),
      array(), // no dependencies
      '1.0'
   );
   wp_register_script(
      'portfolio-sort-ui',
      plugins_url( '/Scripts/sort.js', __FILE__ ),
      array( 'jquery-ui-sortable' ),   // we require the jQueryUI Sortables
      '1.0'
   );
}

add_action( 'admin_print_styles', 'portfolio_print_sort_styles' );
function portfolio_print_sort_styles() {
   global $pagenow;
   
   // make sure we load the resource only where we need it
   $pages = array('edit.php');
   if ( in_array( $pagenow, $pages ) ) {
      wp_enqueue_style('portfolio-sort-ui');
   }
}

add_action( 'admin_print_scripts', 'portfolio_print_sort_scripts' );
function portfolio_print_sort_scripts() {
   global $pagenow;
   
   // make sure we load the resource only where we need it
   $pages = array('edit.php');
   if ( in_array( $pagenow, $pages ) ) {
      wp_enqueue_script('portfolio-sort-ui');
   }
}


/********************************************************* AJAX SORT HANDLING */

// this will handle all wp-admin AJAX requests that have a POST $action = "portfolio_sort"
add_action('wp_ajax_portfolio_sort', 'portfolio_set_sort_order');
function portfolio_set_sort_order() {
   global $wpdb; // WordPress database class
   
   $post_ids_ordered = explode( ',', $_POST['post_ids_ordered'] );
   $counter          = 1;
   
   foreach ( $post_ids_ordered as $project_id ) {
      // this will execute an SQL query on every iteration, so it should be optimized
      $wpdb->update( $wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $project_id ) );
      $counter += 1;
   }
}

?>