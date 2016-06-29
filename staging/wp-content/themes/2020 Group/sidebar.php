<div id="main-sidebar" class="sidebar" role="complementary">


	<?php

  if (!is_user_logged_in()) : ?>

    <div class="cta-block side-block text-block texturebg">
      <h4>Not yet a member?</h4>
      <!-- <?php if(	(has_term(93, 'product_cat')) || (has_term(95, 'product_cat'))  || (has_term(100, 'product_cat'))  || (has_term(109, 'product_cat'))  || (has_term(30, 'product_cat'))  || (has_term(99, 'product_cat'))	|| (has_term(94, 'product_cat'))	) : ?>
      	<p>Become a 2020 member to access some 2020 webinars as part of your subscription and others at discounted rates</p>
  	  <?php endif; ?> -->
      <p>Join 1000's of other accountancy professionals. Benefit from our wealth of knowledge, tools, tips and downloads now.</p>
      <a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn green icon trophy">Join Now</a>
      <p class="already-member">Already a member? <a href="/login?ref=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="white-link">Login now</a></p>
    </div>

  <?php endif; ?>

  <?php if (is_page( array('about', 'history', 'partners', 'speakers'))) : ?>

	 <?php
			$ancestor_id=27;
			$descendants = get_pages(array('child_of' => $ancestor_id));
			$incl = "";

			foreach ($descendants as $page) {
			   if (($page->post_parent == $ancestor_id) ||
			       ($page->post_parent == $post->post_parent) ||
			       ($page->post_parent == $post->ID))
			   {
			      $incl .= $page->ID . ",";
			   }
			}
	  ?>

		<ul class="sidebar-list">
		   <?php wp_list_pages(array("child_of" => $ancestor_id, "include" => $incl, "title_li" => "<span class='list-title'>About 2020</span>", "sort_column" => "menu_order"));?>
		</ul>

	<?php endif; ?>

	<?php dynamic_sidebar( 'main-sidebar' ); ?>

	<?php include_once('includes/block-testimonials.php'); ?>

 	<?php if ( is_object($post) && !is_page(array(249, 1189)) && in_array( $post->post_parent,array(196,263,259))) : ?>

		<div class="services-form">
			<h4>For further information please complete this contact form</h4>

			<?php echo do_shortcode('[gravityform id=4 title=false description=false ajax=true tabindex=49]'); ?>

		</div>

	<?php endif; ?>

  <?php // if (is_page('tax-protection') ) : ?>

<?php /*
		<a href="" class="side-block download-btn">
  		Download a pdf brochure <br>for 2020 Tax Protection
  	</a>



	<?php // elseif (is_page( array('about', 'history', 'partners', 'speakers')) ) : ?>

	  <?php

$ancestor_id=27; // this code is wrong
			$descendants = get_pages(array('child_of' => $ancestor_id));
			$incl = "";

			foreach ($descendants as $page) {
			   if (($page->post_parent == $ancestor_id) ||
			       ($page->post_parent == $post->post_parent) ||
			       ($page->post_parent == $post->ID))
			   {
			      $incl .= $page->ID . ",";
			   }
			}

	  ?>


		<ul class="sidebar-list">
		   <?php // wp_list_pages(array("child_of" => $ancestor_id, "include" => $incl, "link_before" => "", "title_li" => "", "sort_column" => "menu_order"));?>
		</ul>



  <?php //if (is_page('forms-letters-and-tools') ) : ?>

		<?php // include_once('includes/block-email-download.php');  ?>

		<?php
<div class="email-download-block purplebg">

			<div class="download-text">
				<span class="icon-download"></span>

				<h4>For full details about the Tailored Service, delivery and printing costs enter your email below to download</h4>
			</div>

			<form action="" method="" id="email-download-form">
				<label for="email-download" class="download-label">Email Download</label>
				<input name="email-download" class="download-input" value="" placeholder="Enter your email address...">
			</form>
		</div>

		<div class="side-block checklist-block">
			<h4>The Benefits of becoming part of our Practice Exchange</h4>
			<ul class="checklist">
				<li>Duis enim diam, dictum et libero sed, pulvinar</li>
				<li>Duis enim diam, dictum et libero sed, pulvinar</li>
				<li>Duis enim diam, dictum et libero sed, pulvinar</li>
				<li>Duis enim diam, dictum et libero sed, pulvinar</li>
			</ul>
		</div>
*/ ?>


	<?php // else : ?>

  <?php // endif; ?>


    	<?php /*
			<div class="side-block text-block dbluebg">
				<h4>Become a member
				to download this content</h4>
				<p>Mauris hendrerit auctor lacus in ultrices. Sed sagittis magna neque, at gravida massa ultrices id.</p>
				<a href="" class="gen-btn orange icon trophy">Join Now</a>
			</div>

			<div class="side-block text-block">
				<h4>Top 10 ways to improve take up rates</h4>
				<p>This top ten guide provides tips on how to improve fee protection take up rates</p>
				<a href="" class="gen-btn blue icon right-arrow">Find out more</a>
			</div>
			*/ ?>

</div>

