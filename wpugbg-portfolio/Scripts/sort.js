// Attach to document.onready.
// This is a shortcut - when the jQuery function receives a function as parameter,
// it assumes that we want to attach on document.ready.
// When the callback is executed, the jQuery object is passed as first argument.
jQuery( function ( $ ) {
   
   var projects = $('.portfolio-sort-list');
   
   if ( projects.length > 0 ) {
      projects.sortable({
         // on update we should execute an AJAX request updating all posts' order
         update   : function ( event, ui ) {
            $('#loading-animation').show();
            $.ajax({
               url      : ajaxurl,  // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
               type     : 'POST',
               async    : true,
               cache    : false,
               dataType : 'json',
               data     : {
                  action            : 'portfolio_sort',  // Tell WordPress how to handle this ajax request
                  // Serialize the sortable's item id's into an array of string, e.g. "11,13,8,18,10"
                  post_ids_ordered  : projects.sortable('toArray').toString()
               },
               success  : function ( response ) {
                  $('#loading-animation').hide();
               },
               error    : function ( xhr, textStatus, e ) {
                  alert('Error occured!');
                  // Log more information about the error
                  window.console && console.error && console.error.apply( console, arguments );
                  $('#loading-animation').hide();
               }
            });
         }
      });
   }
   
});