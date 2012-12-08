<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<article class="home_display">
				<h1><img src="<?= base_url($img_assets) ?>/php_bounce_large.png" /></h1>
				<h2>Learn PHP interactively here!</h2>
				<?= form_open($code_submit, array('class'=>'code_editor_form mission_code_form')) ?>
					<?= form_textarea(array(
						'name' => 'code',
						'id' => 'codemirror',
						'class' => 'code_editor',
						'value' => '
//Begin coding here!
$your_name = \'What is your name?\';
echo $your_name;')) ?>
					<?= form_submit(array('name'=>'submit', 'type'=>'submit', 'value'=>'Execute!', 'class'=>'btn btn-primary code_submit')) ?>
				<?= form_close() ?>
				<div class="output_container">
					<div>
					</div>
				</div>
			</article>
			<article class="calltoaction">
				<h2>Begin your campaign! Go to <?= anchor('bounce/mission/1', 'Mission 1') ?></h2>
			</article>
			<article class="whatisphpbounce">
				<h2>This was brought to you by <a href="//polycademy.com"><img src="<?= base_url($img_assets) ?>/polycademy_logo.png" /></a></h2>
				<section class="faq">
					<h2>FAQ:</h2>
					<h4>What is PHP Bounce?</h4>
					<p>PHP Bounce is a educational REPL (read evaluate print loop) similar to Codecademy. You are given a problem (mission), you write code, you solve the problem, you learn how to program!</p>
					<h4>What is PHP?</h4>
					<p>PHP is a general purpose programming language that is used to build serverside (backend) of web applications. It is the most popular web application development language in the world and has been used to build Facebook, Wikipedia.. etc.</p>
					<h4>How was this built?</h4>
					<p>Blood, sweat and tears. Used a lot of open source libraries. A week of 24/7 hacking. Security was a top concern!</p>
					<h4>Will there be more missions?</h4>
					<p>Yes in time. It is currently a beta and part of the class software at Polycademy which teaches people web application development in 11 or 21 weeks. Missions will get deeper and more complex over time. However to get a real sense of how the stack works you have to actually build something and not just practice!</p>
					<h4>How do you come up with these missions?</h4>
					<p>There's a great open source project called <a href="//www.phptherightway.com">PHP the Right Way</a>, I take my inspiration from there and other sources to build great and engaging missions.</p>
					<h4>How do I understand the errors in the output?</h4>
					<p>Debugging is a core asset to developing. Try understanding what those errors mean. The line number isn't always exactly on where the error occurs in your code, it is when the computer executes into an error, however it is pretty accurate. If you have trouble, search your error type on PHP's manual. Some of the errors are custom made to suit Bounce as we may have mission parameters for your to succeed.</p>
					<h4>Who are you?</h4>
					<p>My name is Roger, founder of Polycademy & <a href="//codeforaustralia.com.au">Code for Australia.</a></p>
				</section>
			</article>
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
		</div>