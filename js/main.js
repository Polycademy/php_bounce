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
	
	//equalise_height(".mission_grid > div");
	//$(window).resize(function() { 
	//	equalise_height(".mission_grid > div");
	//});
	
});

//FOR highlight js
$(document).ready(function() {
	$('.mission_description pre > code').each(function(i, e) {hljs.highlightBlock(e, '<span class="indent">\t</span>')});
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
			
				//numerical keys need to be accessed like [0]
				//associative keys need to be accessed like .blah.blah
				
				/* 
				 * We're expecting response to be like this:
				 * array(
				 * 		0 => array(
				 *			'line'	=> bool/int
				 *			'message'	=> 'string',
				 *		),
				 * );
				 */
				 
				//console.log(response);
				
				//there'll be multiple response elements
				$.each(response, function(index, value){
					//console.log(value);
					//console.log(index);
					
					//THIS WILL ALWAYS RUN because currently all values are objects
					if(typeof value === "object"){
						//console.log("THIS IS AN OBJECT");	
						//console.log(value.line);
						
						//setup the message
						//if value.message is false.. then we are not going to append onto the message
						var console_output = value.message;
						
						//if it is an error
						if(value.status === false){
						
							//if this is an error, then there may be a line number
							if(typeof value.line === "number"){
							
								console_output = console_output + ". You should check out line " + value.line + " in your code.";
								//setting marker at editor (line -1)
								editor.setMarker(value.line - 1, '● %N%');
								
							}
							
							console_output = "<span class=\"output_error\">" + console_output + "</span>";
						
						//if it is not an error
						}else if(value.status === true){
						
							//if this is not a mission, there would not be a value.message
							if(value.message){
								console_output = "<span class=\"success\">" + console_output + "</span>";
							}
							
						}
									
						//if value.output is not a boolean and non-empty, then that means there was something outputted by the execution!
						//if the message was false, that means there was not success/error message meaning this was not a mission
						//therefore we can just replace console_output with the value.output and forget about any of those errors
						if(typeof value.output !== "boolean" && value.output !== ""){
							if(value.message !== false){
								console_output = value.output + "<br />" + console_output;
							}else{
								console_output = value.output
							}
						}
						
						//we want some spacing don't we?
						console_output = console_output + "<br />";
						
						//append onto the div
						$(".output_container > div").append(console_output);
						
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
				 
				//console.log(response);
				$(".xml_results > pre > code").html(response);
				
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