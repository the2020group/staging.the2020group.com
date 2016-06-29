<?php

/*
 * Template Name: Benchmark Tool
 */

  get_header(); ?>

	<div class="row">

		<div class="small-12 columns benchmark-practice-check" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

			</article>

			<div id="benchmark-login" class="popup benchmark-login" style="display:none">
	  			<h3>Login</h3>

	  			<form id="benchmark-signin">
	  				<input type="email" name="email" placeholder="email address" />
	  				<input type="password" name="password" placeholder="password" />
	  				<button type="submit" id="bm-submit">Login</button>
	  			</form>
	  			<p class="error"></p>
			</div>

			<?php endwhile; else : ?>

			<article class="post-not-found">

				<header class="not-found-header">

					<h2><?php _e( 'Nothing Found!' ); ?></h2>

				</header>

				<section class="not-found-content">

					<p><?php _e( 'Please check what you are looking for.' ); ?></p>

				</section>

			</article>

			<?php endif; ?>

		</div>

	</div>

	<?php if(is_user_logged_in()) : ?>

		<script>
			(function($) {
		    	$(document).ready(function() {
		    		$('input[name=input_34]').val('1');
		    	})
		    })(jQuery);
		</script>

	<?php endif; ?>


<?php get_footer(); ?>
