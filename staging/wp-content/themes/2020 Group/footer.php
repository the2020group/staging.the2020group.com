		<!-- </div> -->

	</div><?php // close 'outer-wrap' ?>


  <div id="location-check" class="location-modal">
    <div class="inner">
      <h1>Location Content Controls</h1>
      <p class="subheader">You appear to be visiting from<span class="lbc-loc-1">outside of the UK</span>.</p>
      <h3>What content would you like to see?</h3>
      <p>You can change your choice using the content filter option in the menu bar.</p>

      <div class="geo-btns">

        <a href="#" class="gen-btn orange icon check location-filter lbc-filter" data-filter=""><span>Show me content based on my location</span></a>
        <a href="#" class="gen-btn orange icon eye location-filter" data-filter="all"><span>Show me all content</span></a>

      </div>
    </div>
  </div>


	<footer class="page-footer" role="contentinfo">

		<div class="row">

			<div class="small-12 large-8 columns">

				<div class="row">

					<div class="small-12 large-6 columns">

						<h4>Footer Links</h4>

						<div class="row collapse">

							<div class="small-6 columns">

				        <?php
						        $defaults = array(
						            'theme_location'  => 'footer',
						            'menu'            => 'footer menu',
						            'container'				=> '',
						            'echo'            => true,
						            'fallback_cb'     => 'wp_page_menu',
						            'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						            'depth'           => 0
						        );

						        wp_nav_menu( $defaults );
						    ?>

							</div>

							<div class="small-6 columns">

						    <?php
						        $defaults = array(
						            'theme_location'  => 'footer',
						            'menu'            => 'footer secondary menu',
						            'container'				=> '',
												'echo'            => true,
						            'fallback_cb'     => 'wp_page_menu',
						            'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						            'depth'           => 0
						        );

						        wp_nav_menu( $defaults );
						    ?>

							</div>

						</div>

					</div>

					<div class="small-12 large-6 columns">

						<h4>Follow Us</h4>

						<ul class="social-links">
							<li><a href="https://www.facebook.com/pages/2020-Group/275922050215" class="facebook" target="_blank"><span class="icon-facebook"></span></a></li>
							<li><a href="https://twitter.com/2020GroupUK" class="twitter" target="_blank"><span class="icon-twitter"></span></a></li>
							<li><a href="https://www.linkedin.com/groups?gid=2707331" class="linkedin" target="_blank"><span class="icon-linkedin"></span></a></li>
							<li><a href="https://www.youtube.com/user/The2020Group" class="youtube" target="_blank"><span class="icon-youtube"></span></a></li>
						</ul>

					</div>

				</div>

				<p>In partnership with:</p>

				<ul class="footer-logos">
					<li><a href="http://www.accaglobal.com/uk/en.html" class="acca" target="_blank"><span>ACCA</span></a></li>
					<li><a href="http://www.acpa.org.uk" class="cpaa" target="_blank"><span>CPAA</span></a></li>
					<li><a href="http://www.icpa.org.uk" class="icpa" target="_blank"><span>ICPA</span></a></li>
					<li><a href="http://www.mercia-group.co.uk/Home" class="mercia" target="_blank"><span>Mercia Group</span></a></li>
				</ul>

			</div>

			<div class="small-12 large-4 columns">

				<!-- <h4>Contact Us</h4> -->

				<?php echo do_shortcode ('[gravityform id="2" name="Footer Contact Us" ajax="true" tabindex=88]'); ?>

			</div>

		</div>

	</footer>

<?php wp_footer(); ?>
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-68206969-1', 'auto');
	ga('send', 'pageview');
</script>

</body>
</html>
