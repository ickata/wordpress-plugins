( function ( $ ) {
   
   var editor, plugin_url;
   
   // Load plugin specific language pack
   tinymce.PluginManager.requireLangPack('mcehelloworld');  // see langs/en.js
   
   tinymce.create('tinymce.plugins.TinyMCEHelloWorld', {
      /**
       * Initializes the plugin, this will be executed after the plugin has been created.
       * This call is done before the editor instance has finished it's initialization so use the onInit event
       * of the editor instance to intercept that event.
       *
       * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
       * @param {string} url Absolute URL to where the plugin is located.
       */
      init : function ( ed, url ) {
         editor      = ed;
         plugin_url  = url;
         // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('helloWorldCmd');
         ed.addCommand('helloWorldCmd', function() {
            ed.windowManager.open({
               file : url + '/dialog.htm?callback=MceHelloWorld_InsertPlaceholder&width=320&height=240&name=Hristo',
               width : 320,
               height : 170,
               inline : 1
            },
            {
               plugin_url : url // Plugin absolute URL
            });
         });
         
         // Register mcehelloworld button
         ed.addButton('mcehelloworld', {
            title : 'mcehelloworld.desc',
            cmd   : 'helloWorldCmd',
            image : url + '/img/button.png'
         });
         
         // Add a node change handler, selects the button in the UI when a placeholder is selected
         ed.onNodeChange.add( function( ed, cm, element ) {
            cm.setActive('mcehelloworld', element.nodeName == 'IMG' && element.getAttribute('name') == 'mcehelloworld');
         });
      },
      
      /**
       * Returns information about the plugin as a name/value array.
       * The current keys are longname, author, authorurl, infourl and version.
       *
       * @return {Object} Name/value array containing information about the plugin.
       */
      getInfo : function () {
         return {
            longname    : 'TinyMCE Hello World',
            author      : 'Hristo Chakarov',
            authorurl   : 'http://blog.ickata.net',
            infourl     : 'http://blog.ickata.net',
            version     : '1.1'
         };
      }
   });
   
   // Register plugin
   tinymce.PluginManager.add('mcehelloworld', tinymce.plugins.TinyMCEHelloWorld);
   
   // register insert callback
   // will be called from the plugin's dialog window
   // 'this' is the root object (window)
   this.MceHelloWorld_InsertPlaceholder = function ( win, json_data ) {
      var data = JSON.parse( json_data );
      // insert the image placeholder
      // store all properties as query params in the placeholder's url
      editor.execCommand('mceInsertContent', 0, [
         // we use an empty GIF (spacer) and a background image
         '<img src="', plugin_url, '/img/space.gif?', GenerateQueryString( data ), '"',
         // set image dimensions
         ' width="', data.width, '"',
         ' height="', data.height, '"',
         // set some styles
         ' style="background: url(', plugin_url, '/img/placeholder.gif) no-repeat center; outline: 1px solid black"',
         // this will be used when parsing content on Page
         ' name="mcehelloworld"',
         '>'
      ].join(''));
      // close the window - TinyMCE expects a reference to the dialog's window object - WHAT?
      editor.windowManager.close( win );
	}
   
   // generates a query string from a hash
   function GenerateQueryString( params ) {
      var pairs = [];
      for ( var name in params ) {
         pairs.push( name + '=' + params[ name ] );
      }
      return pairs.join('&');
   }
})( jQuery );