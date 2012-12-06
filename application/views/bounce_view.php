<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div id="container">
			<h1 class="main_heading">PHP Bounce</h1>
			<div class="main_sections row-fluid">
				<div class="span4 mission_parameters">
					<section class="parameter_container">
						<h4>Your mission is...</h4>
						<p>
							Blah
						</p>
					</section>
				</div>
				<div class="span4 code_editor">
					<section class="editor_container">
						<h4>Code Pad</h4>
						<?= form_open($code_submit) ?>
							<?= form_textarea(array('name' => 'code', 'id' => 'codemirror')) ?>
							<?= form_submit('submit', 'Execute!') ?>
						<?= form_close() ?>
					</section>
				</div>
				<div class="span4 output_zone">
					<section class="output_container">
						<h4>Results</h4>
						<p>
						BLAHLABLAH
						</p>
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
					theme: "blackboard",
					autoClearEmptyLines: true,
					autofocus: true,
				});
			</script>
		</div>