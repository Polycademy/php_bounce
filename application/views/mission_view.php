<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<? if(!empty($mission_data)){ ?>
				<h1 class="main_heading"><?=$mission_data['title']?></h1>
				<div class="main_sections row-fluid">
					<div class="span4 mission_parameters">
						<section class="parameter_container">
							<h4>Sitrep</h4>
							<div class="mission_description">
								<?=$mission_data['description']?>
							</div>
						</section>
					</div>
					<div class="span8 mission_execution">
						<section class="editor_container">
							<h4>Code Pad</h4>
							<?= form_open($code_submit, array('class'=>'form-horizontal mission_code_form')) ?>
								<?= form_textarea(
									array(
										'name'			=> 'code',
										'id'			=> 'codemirror',
										//'autocomplete'	=> 'off', //firefox will autocomplete this NO LONGER NEEDED
									)
								) ?>
								<?= form_submit(array('name'=>'submit', 'type'=>'submit', 'value'=>'Execute!', 'class'=>'btn btn-primary code_submit')) ?>
							<?= form_close() ?>
						</section>
						<section class="output_container">
							<div></div>
						</section>
					</div>
				</div>
				<script src="<?= base_url($js_assets) ?>/codemirror/codemirror.js"></script>
				<script src="<?= base_url($js_assets) ?>/codemirror/mode/php/php.js"></script>
				<script src="<?= base_url($js_assets) ?>/codemirror/mode/clike/clike.js"></script>
				<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("codemirror"), {
						lineNumbers: true,
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