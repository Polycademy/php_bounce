<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<h1>Welcome to CodeIgniter!</h1>
			<h2>Do something</h2>
			<?= form_open($code_submit) ?>
				<?= form_textarea(array('name' => 'code', 'id' => 'codemirror')) ?>
				<?= form_submit('submit', 'Execute!') ?>
			<?= form_close() ?>
			
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
		</div>