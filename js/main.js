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
				
				//STILL NEED TO TEST WHITELIST ERROR...
				//response comes in as like
				/*
					response =>
						0 => object =>      //<---- We have errors like this
								message
								type
								..etc
						1 => string... etc //<----- these are usually output or single error messages, or mission check messages...
						..etc
				*/
				//numerical keys need to be accessed like [0]
				//associative keys need to be accessed like .blah.blah
				
				//there'll be multiple response elements
				$.each(response, function(index, value){
					//console.log(value);
					//console.log(index);
					
					//if this is an object and not just a string value, then we need to access it again...
					if(typeof value === "object"){
						console.log("THIS IS AN OBJECT");
						
						//console.log(value.type);
						
						var error_output = "Oops! You have received a " + value.type + ", the error is due to '" + value.message + "'. Check out line " + value.line + " in your code.<br />";
						$(".output_container > div").append(error_output);
					}else{
					
						//if not an object, then it is the output
						var output = value + '<br />';
						$(".output_container > div").append(output);
					
					}
					
					
				});
				
				$(".output_container > div").append("<br />");
				
				//now need to scroll to the bottom
				var div = $(".output_container > div")[0];
				var scrollHeight = Math.max(div.scrollHeight, div.clientHeight);
				var scroll = scrollHeight - div.clientHeight;
				
				$(".output_container > div").animate({
					scrollTop: scroll,
				}, "slow");
				
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