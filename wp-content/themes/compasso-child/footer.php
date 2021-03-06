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
			<div class="four columns">
				<?php dynamic_sidebar('footer4'); ?>
			</div>
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
    jQuery( document ).ready(function() {
      console.log('doc ready');
      
      if(window.innerWidth > 768){
        jQuery('.menu-item-home a').html("<i class='icon-home icon-2x'></i>");
      }
      window.onresize = function(event) {
        console.log('window resized');
        if(window.innerWidth > 768){
          jQuery('.menu-item-home a').html("<i class='icon-home icon-2x'></i>");
          console.log('home set to icon');
        }else{
          jQuery('.menu-item-home a').html("Home");
          console.log('home set to Home');
        }
      }
      jQuery('.search-background button').html("<div class='rotate180'><i class='icon-search icon-2x'></i></div>");
      
      console.log('switches done');
    });
  </script>

<?php wp_footer(); ?>
</body>
</html>