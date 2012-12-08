<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<h1 class="main_heading">Mission <?=($type == 'add')? 'Add' : 'Update' ?> Editor</h1>
			<? if(!empty($status)){ ?>
				<div class="form_status">
					<h4>Form Status Messages:</h4>
					<ul>
						<?=$status?>
					</ul>
				</div>
			<? } ?>
			<nav class="editor_nav">
				<ul class="nav nav-tabs">
					<li><?= anchor('mission_editor', 'Mission Editor List') ?></li>
					<li><?= anchor('mission_editor/add', 'Mission Editor Add') ?></li>
				</ul>
			</nav>
			<?= form_open($editor_submit, array('class' => 'mission_editor_form')) ?>
				<div class="main_sections row-fluid">
					<div class="span6 mission_parameters">
						<section class="parameter_container">
							<h4>Enter Mission Details</h4>
							<?= form_label('Mission Title', 'mission_title_input') ?>
							<?= form_input(
								array(
									'name'	=> 'title',
									'class'	=> 'input-block-level',
									'id'	=> 'mission_title_input',
									'value'	=> (!empty($mission_data['title'])) ? $mission_data['title'] :set_value('title'),
								)
							) ?>
							<?= form_label('Mission Description', 'mission_description_input') ?>
							<?= form_textarea(
								array(
									'name'	=> 'description',
									'class'	=> 'input-block-level',
									'id'	=> 'mission_description_input',
									'value'	=> (!empty($mission_data['description'])) ? $mission_data['description'] : set_value('description')
								)
							) ?>
							<?= form_label('Mission Whitelist (optional & comma separated)', 'mission_description_input') ?>
							<?= form_input(
								array(
									'name'	=> 'whitelist',
									'class'	=> 'input-block-level',
									'id'	=> 'mission_whitelist_input',
									'value'	=> (!empty($mission_data['whitelist'])) ? $mission_data['whitelist'] : set_value('whitelist')
								)
							) ?>
							<?= form_label('Mission Number', 'mission_number_input') ?>
							<?= form_input(
								array(
									'name'	=> 'number',
									'class'	=> 'input-block-level',
									'id'	=> 'mission_number_input',
									'value'	=> (!empty($mission_data['mission_number'])) ? $mission_data['mission_number'] : set_value('number')
								)
							) ?>
						</section>
					</div>
					<div class="span6 code_editor">
						<section class="editor_container">
							<h4>Enter Mission Parameters (don't put (;) at end)</h4>
							<?= form_textarea(
								array(
									'name'	=> 'parameters',
									'id'	=> 'codemirror',
									'value'	=>	(!empty($mission_data['parameters'])) ? $mission_data['parameters'] : set_value('parameters'),
								)
							) ?>
						</section>
					</div>
				</div>
				<?= form_submit(array('name'=>'submit', 'type'=>'submit', 'value'=>'Submit!', 'class'=>'btn btn-primary')) ?>
			<?= form_close() ?>
			
			<div class="php_parse_xml">
				<h4>Enter code here to see the XML so you can construct the path.</h4>
				<?= form_open($xml_submit, array('class'=>'php_parse_xml_form')) ?>
					<?= form_textarea(
						array(
							'name'	=> 'code',
							'id'	=> 'codemirror_xml',
						)
					) ?>
					<?= form_submit(array('name'=>'submit', 'type'=>'submit', 'value'=>'Show XML Parse', 'class'=>'btn btn-primary')) ?>
				<?= form_close() ?>
				<div class="xml_results">
					<pre>
						<code>
						</code>
					</pre>
				</div>
			</div>
			<div class="array_example">
				<h4>Array Example</h4>
				<pre>
					<code>
array(
	'variable_declaration'	=> array(
		'paths'	=> array(
			'//node:BASEPATHS' => array(
				'subNode:CHILDPATH' => array(
					'subNode:SUBCHILDPATH',
				),
			),
			'//node:FULLPATH',
		),
		'tests'	=> array(
			'ERROR MESSAGES' => array(
				'VALUES',
			),
		),
	),
);
					</code>
				</pre>
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
				});
				var editor = CodeMirror.fromTextArea(document.getElementById("codemirror_xml"), {
					lineNumbers: true,
					matchBrackets: true,
					mode: "text/x-php",
					indentUnit: 4,
					indentWithTabs: true,
					enterMode: "keep",
					tabMode: "shift",
					theme: "rubyblue",
					autoClearEmptyLines: true,
				});
			</script>
			<script>
				//passing variables from PHP to js
				var $parse_base_url = "<?= base_url() ?>";
				var $ajax_path = "<?= $xml_submit ?>";
			</script>
		</div>