/* ==========================================================================
   PLUGINS
   ========================================================================== */
// Avoid `console` errors in browsers that lack a console.
if (!(window.console && console.log)) {
    (function() {
        var noop = function() {};
        var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'markTimeline', 'table', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn'];
        var length = methods.length;
        var console = window.console = {};
        while (length--) {
            console[methods[length]] = noop;
        }
    }());
}

$(function(){

	// Equalise column height for any plurality of rows, just make sure to give a selector that has multiple elements
	function equalise_height(selector){
		var maxHeight = 0;
		$(selector).height("auto").each(function(){ 
			maxHeight = $(this).height() > maxHeight ? $(this).height() : maxHeight; 
		}).height(maxHeight);
	}
	
	equalise_height(".main_sections > div");
	$(window).resize(function() { 
		equalise_height(".main_sections > div");
	});
	
});

$(function(){

	$(".mission_code_form").submit(function(event){
	
		//setting up variables
		// find all the inputs and seralise them
		var $form = $(this), $inputs = $form.find("textarea"), serializedData = $form.serialize();
		
		// let's disable the inputs for the duration of the ajax request
		$inputs.attr("disabled", "disabled");

		// fire off the request to /form.php
		$.ajax({
			url: $bounce_base_url + $ajax_path,
			type: "post",
			data: serializedData,
			dataType: 'json', //for response?
			// callback handler that will be called on success
			success: function(response, textStatus, jqXHR){
				//response is ALWAYS a JSON object
				
				//we need
				
				//console.log(response);
				//console.log(response[0]);
				//console.log(response[0].message);
				
				//first test if response is a json object...
				console.log(response);
				//this works
				//numerical keys need to be accessed like [0]
				//associative keys need to be accessed like .blah.blah
				console.log(response[0].message.type);
				
				//there'll be multiple response elements
				$.each(response, function(index, value){
					
					//if this is an object and not just a string value, then we need to access it again...
					if(typeof this === "object"){
						console.log('It is an object!');
						var error_output = "Oops! You have received a " + this.message.type + ", the error is due to '" + this.message.message + "'. Check out line " + this.message.line + " in your code.<br /><br />";
						$('.output_container > div').append(error_output);
					}
					
				});
				//we need to parse this json object
				//now that it is a json object, we have to test the value..
				//console.log(response);
				//
				//$('.output_container > pre').append(response);
			},
			// callback handler that will be called on completion
			// which means, either on success or error
			complete: function(){
				// enable the inputs
				$inputs.removeAttr("disabled");
			}
		});

		// prevent default posting of form
		event.preventDefault();
		
	});
	
	$(".php_parse_xml_form").submit(function(event){
	
		//setting up variables
		// find all the inputs and seralise them
		var $form = $(this), $inputs = $form.find("textarea"), serializedData = $form.serialize();
		
		// let's disable the inputs for the duration of the ajax request
		$inputs.attr("disabled", "disabled");
		
		
		// fire off the request to /form.php
		$.ajax({
			url: $parse_base_url + $ajax_path,
			type: "post",
			data: serializedData,
			// callback handler that will be called on success
			success: function(response, textStatus, jqXHR){
				// log a message to the console
				//console.log("Hooray, it worked!");
			},
			// callback handler that will be called on error
			error: function(jqXHR, textStatus, errorThrown){
				// log the error to the console
				console.log(
					"The following error occured: "+
					textStatus, errorThrown
				);
			},
			// callback handler that will be called on completion
			// which means, either on success or error
			complete: function(){
				// enable the inputs
				$inputs.removeAttr("disabled");
			}
		});

		// prevent default posting of form
		event.preventDefault();
		
	});

});