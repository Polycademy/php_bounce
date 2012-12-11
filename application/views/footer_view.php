<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<footer>
			<div class="container">
				<h3 class="footer_link">Ready for the real challenge? Take on the bootcamp at <a href="//polycademy.com">Polycademy</a>!</h3>
				<em class="copyright_notice">&copy; Polycademy & Code for Australia 2012</em>
			</div>
		</footer
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= base_url($js_assets) ?>/jquery/jquery-1.8.2.min.js"><\/script>')</script>
		
		<script src="<?= base_url($js_assets) ?>/highlight/highlight.pack.js"></script>
        <script src="<?= base_url($js_assets) ?>/bootstrap/bootstrap.min.js"></script>
        <script src="<?= base_url($js_assets) ?>/main.js"></script>
		
		<? if(ENVIRONMENT == 'production'){ ?>
			<script>
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '<?=$google_analytics_key?>']);
				_gaq.push(['_trackPageview']);

				(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();
			</script>
		<? } ?>
		
	</body>
</html>