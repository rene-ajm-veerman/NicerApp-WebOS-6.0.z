		<h1><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></h1>

		<p>
		View the source of this page/iframe to see how it's used.
		</p>
				
		<script type='text/javascript'>
				var theme = {
					centerGapRadius: 30,
					stripes: [
						'<?php echo SA_WEB;?>/seductiveapps/siteMedia/vividThemes/spinner/default/stripe_1.png', 
						'<?php echo SA_WEB;?>/seductiveapps/siteMedia/vividThemes/spinner/default/stripe_2.png', 
						'<?php echo SA_WEB;?>/seductiveapps/siteMedia/vividThemes/spinner/default/stripe_3.png'
					]
				};
				setTimeout (function () {
				var loaderIcon = sa.acs.addIcon(
					true, //whether or not to absolutely position
					document.body, //parent element to stick icon to (will be positioned in the middle of the parent element)
					180, 180, //width and height in pixels
					theme, //see var theme above
					true //start running immediately
				);
				}, 2000);
		</script>
