(function( $ ) {
	'use strict';
	
	console.log( rm_store_options );

	var template = wp.template( 'working-hours' );

	var dayType = $('input[name="days"]:checked').val();
	
	var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ];
	
	if( dayType == 'all' ) {
		days = ['all'];
	}

	$('#working-hours-template').html( template( days ) );
	
	$(document).on('click', 'input[name="days"]', function() {
		var dayType = $(this).val();		
		var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ];
		if( dayType == 'all' ) {
			days = ['all']
		}		
		$('#working-hours-template').html( template( days ) );
	});

	$(document).on('click focus', '.timepicker', function(){
		$(this).timepicker({
			stepMinute: 15,
		});
	});
	

})( jQuery );
