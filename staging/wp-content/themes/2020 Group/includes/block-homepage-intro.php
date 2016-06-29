<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'home-hero' );?>
<div class="intro-content" style="background-image: url('<?php echo $thumb['0'];?>'); ">
	<header class="page-header">
		<div class="row">
			<div class="small-12 large-3 columns main-logo">
				<div class="main-logo">
					<a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/2020-innovation.png"  alt="" class="logo"></a>
					<a href="#" class="toggle"><span></span></a>
				</div>
			</div>
			<div class="small-12 large-9 columns">
				<div class="utilities">
					<div class="header-menu">
						<div class="search-box">
							<span class="icon-search search"></span>
						</div>
						<?php include_once('block-header-user.php'); ?>
	        </div>
	        <div class="header-sec-menu">
  	        
            <dl class="header-content-filter">
              <dt><a href="#"><span>Content Filter</span></a></dt>
              <dd>
                <ul class="menudrop">
                  <li <?php if (isset($_GET['lc']) && $_GET['lc']=='uk') { echo 'class="active"';}?>><a href="?lc=uk" class="location-filter" data-filter="uk">UK Only</a></li>
                  <li <?php if (isset($_GET['lc']) && $_GET['lc']=='nuk') { echo 'class="active"';}?>><a href="?lc=nuk" class="location-filter" data-filter="nuk">Rest of World</a></li>
                  <li <?php if (!isset($_GET['lc']) || ( isset($_GET['lc']) && $_GET['lc']!='uk'  && $_GET['lc']!='nuk' )) { echo 'class="active"'; }?>><a href="?" class="location-filter" data-filter="all">Everything</a></li>
                </ul>
              </dd>
            </dl>
  	        
	        	<?php dynamic_sidebar('transposh-area'); ?>
		        <?php echo do_shortcode('[aelia_currency_selector_widget title="Select Currency" widget_type="buttons"]'); ?>
		        <?php
		            $defaults = array(
		                'theme_location'  => 'header',
		                'menu'            => 'header secondary menu',
		                'container'       => '',
		                'echo'            => true,
		                'fallback_cb'     => 'wp_page_menu',
		                'before'          => '',
		                'after'           => '',
		                'link_before'     => '',
		                'link_after'      => '',
		                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		                'depth'           => 1,
		                'walker'          => ''
		            );
								wp_nav_menu( $defaults );
		        ?>
		        </div>
				</div>
			</div>
		</div>
	</header>
	<div class="home-menu-wrap">
		<div class="row">
			<div class="small-12 columns">
				<?php wp_nav_menu( array( 'menu' => 'Main Menu', 'container_class' => 'main-menu' ) ); ?>
			</div>
		</div>
	</div>

	<div class="lower-panel">
		<a href="#homeScroll" class="down-arrow animated bounce">
	    <img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/homepage/down-arrow.png" alt="">
	  </a>
		<div class="row">
			<div class="small-12 medium-7 columns">
				<?php the_content(); ?>
			</div>
			<div class="small-12 medium-4 medium-offset-1 columns">
				<ul class="menu" id="home-tick">
					<li><a href="/about/" class="gen-btn orange icon lightbulb">I'm Interested in</a>
						<ul class="sub-menu">
							<li><a href="<?php echo get_option('home'); ?>/setting-practice/" class="gen-btn orange ">Setting Up a Practice</a></li>
							<li><a href="<?php echo get_option('home'); ?>/growing-practice/" class="gen-btn orange ">Growing Your Practice</a></li>
							<li><a href="<?php echo get_option('home'); ?>/professional-development-2/" class="gen-btn orange ">Professional Development</a></li>
							<li><a href="<?php echo get_option('home'); ?>/leadership-management/" class="gen-btn orange ">Leadership &#038; Management</a></li>
							<li><a href="<?php echo get_option('home'); ?>/succession-exit/" class="gen-btn orange ">Succession &#038; Exit</a></li>
							<li><a href="<?php echo get_option('home'); ?>/useful-stuff/" class="gen-btn orange ">Other Useful Stuff</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php endwhile; endif; ?>