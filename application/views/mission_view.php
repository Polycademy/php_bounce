<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<? if(!empty($mission_data)){ ?>
				<article class="mission_testing">
				
					<h1><?=$mission_data['title']?></h1>
					
					<div class="mission_grid row">
					
						<section class="mission_sitrep span6">
							<div class="mission_description">
								<?=$mission_data['description']?>
							</div>
						</section>
						
						<aside class="mission_execution span6">
							<div class="execution_container span6" data-spy="affix" data-offset-top="70">
								<div class="editor_container">
									<?= form_open($code_submit, array('class'=>'mission_code_form')) ?>
										<?= form_textarea(
											array(
												'name'			=> 'code',
												'id'			=> 'codemirror',
												'value'			=> $mission_data['default'],
											)
										) ?>
										<?= form_submit(array('name'=>'submit', 'type'=>'submit', 'value'=>'Execute!', 'class'=>'btn btn-primary code_submit')) ?>
									<?= form_close() ?>
								</div>
								<div class="output_container">
									<div></div>
								</div>
							</div>
						</aside>
					
					</div>
					
				</article>
				<script src="<?= base_url($js_assets) ?>/codemirror/codemirror.js"></script>
				<script src="<?= base_url($js_assets) ?>/codemirror/mode/php/php.js"></script>
				<script src="<?= base_url($js_assets) ?>/codemirror/mode/clike/clike.js"></script>
				<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("codemirror"), {
						lineNumbers: true,
						onGutterClick: function(codemirror, number) {
							var info = codemirror.lineInfo(number);
							if (info.markerText)
								codemirror.clearMarker(number);
							else
								codemirror.setMarker(number, "● %N%");
						},
						matchBrackets: true,
						mode: "text/x-php",
						indentUnit: 4,
						indentWithTabs: true,
						enterMode: "keep",
						tabMode: "shift",
						theme: "rubyblue",
						autoClearEmptyLines: true,
						autofocus: true,
					});
				</script>
				<script>
					//passing variables from PHP to js
					var $bounce_base_url = "<?= base_url() ?>";
					var $ajax_path = "<?= $code_submit ?>";
				</script>
			<? }else{ ?>
				<h1>There's no mission here!</h1>
			<? } ?>
		</div>