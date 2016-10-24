(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';
		
	  var file_frame;
	  $('.tararama_btn').live('click', function( event ){

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: $( this ).data( 'Ajouter une image au diaporama' ),
		  library: {type: 'image'},
		  button: {
			text: $( this ).data( 'Ajouter' ),
		  },
		  multiple: true  
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {		 
		  				var files = file_frame.state().get('selection').toArray();
						var values;
						for (var i = 0; i < files.length; i++) {
							var file = files[i].toJSON();
							if(values===undefined){
								var values = file.id;
							}
							else{
								var values = values+','+file.id;
							}
						};
						wp.media.editor.insert('[tararama ids="' + values + '" auto="true" pause="4000"]');
		});

		// Finally, open the modal
		file_frame.open();
	  });
  
		
	});
	
})(jQuery, this);
// Uploading files
