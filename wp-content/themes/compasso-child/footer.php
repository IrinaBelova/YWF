		</div>
	</div>

	<?php if (g7_option('footer_widget')) : ?>
	<div id="footer-widget">
		<div class="container">
			<div class="five columns">
				<?php dynamic_sidebar('footer1'); ?>
			</div>
			<div class="six columns">
				<?php dynamic_sidebar('footer2'); ?>
			</div>
			<div class="five columns">
				<?php dynamic_sidebar('footer3'); ?>
			</div>
			<!--<div class="four columns">
				<?php dynamic_sidebar('footer4'); ?>
			</div>-->
		</div>
	</div>
	<?php endif; ?>

	<footer id="bottom">
		<div class="container">
			<div class="eight columns footer1">
				<?php echo g7_option('footer_text_1'); ?>
			</div>
			<div class="eight columns footer2">
				<?php echo g7_option('footer_text_2'); ?>
			</div>
		</div>
	</footer>
	<script type="text/javascript">
	$( document ).ready(function() {
    $('#menu-item-50 a').html("<i class='icon-home icon-3x'></i>")
    
  });
	</script
<?php wp_footer(); ?>
</body>
</html>