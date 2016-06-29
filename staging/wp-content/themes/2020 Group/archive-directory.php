<?php get_header(); ?>

<div class="row">
	<div class="small-12 columns" role="main">

    <header class="members-header">
      <h1><span><?php _e( 'International Members Directory' ); ?></span></h1>
    </header>
    		
  	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <div class="member-firms">
	  	<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p class="int-breadcrumbs">','</p>');
			} ?>  
	    
	    <div class="row">
	      <div class="small-12 medium-3 columns">
		      <div class="continent-list">
		        <h3>Continents</h3>
		        <?php
    		
			    		$args = array(
			
						  'taxonomy' 				=> 'location',
						  'hide_empty' 			=> 0,
						  'depth' 					=>1,
						  'orderby'     		=> 'name',
						  'title_li'    		=> __( '' ),
						
						);
						?>
						<ul class="continent-btns">
						<?php
						wp_list_categories($args);
						?>
						</ul>
					</div>
	      </div>
				<div class="small-12 medium-9 columns">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						<!--
<header class="article-header">
	  					<h1><a href="<?php /* the_permalink(); */ ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	  				</header>
	  
	  				<section class="article-footer">
	  					<?php /* the_post_thumbnail( 'thumbnail' ); */ ?>
	  					<?php /* the_excerpt(); */ ?>
	  				</section>
-->
	  				<div class="member-copy">
		  				<h2>Member Firms Archive</h2>
		  				
		  				<p class="intro">2020 International was established specifically to serve the needs of smaller independent accounting firms.</p>
		  				
		  				<p class="intro">Having consulted with thousands of firms around the world we discovered that other membership associations were often only interested in major metropolitan centers, they limited membership through territorial exclusivity and the membership fee was often beyond the budget of most firms.</p>
		  				
		  				<p>The firms we work with were also adamant they remain independent, however they still wanted (and needed) and international network so as to help them better serve the needs of their clients.</p>
		  				
		  				<p>In an increasingly globalized marketplace, clients are involved in international transactions more than ever and expect their accountant to have the necessary contacts and resources to assist them.</p>
		  				<p>It is the easiest and quickest way to get answers to foreign tax questions and other issues related to doing business in a particular foreign country as well as opportunities for referral of work.</p>
		  				<p>Click on a region in the left hand navigation for the contact details of our Member Firms.</p>
	  				</div>
	  			</article>
	  		</div>
	    </div>
	  </div>
    <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>
<div id="country-scroll" data-stellar-background-ratio="0.05" style="background-position: 0% 14px;">
  <h2>Welcome to 2020 International</h2>
  <p>2020 International is an international association of independent Accounting and Consulting firms dedicated to providing our clients with an unparalleled level of service on an international basis.</p>
</div>
    
<div class="strat-alli">
  <h2>Strategic Alliance</h2>
  
  <div class="row">
    <div class="small-12 large-5 large-offset-1 columns all-list">
	    <h3>UC & CS Global</h3>
	    
	    <p>2020 are delighted to announce a Strategic Alliance with UC & CS Global. Currently UC & CS America has a network of 40 affiliated firms with 52 offices in 13 countries.  More than 750 professionals managed by 115 partners that attend more than 1,500 clients all over the American Continent.</p>
	    
	    <p>UC & CS Global<br>
	    1040 Avenue of the Americas (40th Street)<br>
	    18th & 25th Floors<br>
	    New York 10018<br>
	    USA</p>
		    
		  <p>Contact: Mr Mauricio Mobarack (President)<br>
	    Email: <a href="">mauricio.mobarak@uccs-america.org</a><br>
	    Web:  <a href="">www.uccs-america.org</a><br>
	    Tel: +52 (55) 3095 3922<br>
	    Fax: +52 (55) 5440 4596</p>
	  </div>
    
    <div class="small-12 large-5 end columns all-list">
	    <p>In addition UC & CS Global have the following alliances:</p>
	    
	    <h4>LATAX - <a href="">www.latax.org</a></h4>
	    <p>Latino Association of Tax Preparers, located in New York, USA with coverage of almost 100 Latin Members in all the USA, preparing more than 20,000 tax returns for Latin and Non Latin People and Companies across the USA.</p>
	    
	    <h4>LATAX - <a href="">www.latax.org</a></h4>
	    <p>In July 2011, UC & CS Global signed a strategic alliance contract with CPAsNET.com. A CPA Network with more than 30 Affiliated Members across the USA, for accessing to the USA market for the UC & CS Member's Clients.</p>
	    
	    <h4>LATAX - <a href="">www.latax.org</a></h4>
	    <p>GRM is an international development management company, specialising in the provision of project design, management expertise and technical assistance to development projects for bilateral and multilateral funding agencies, governments and corporations.  They have over 38 years of development experience, managing in excess of 700 projects in more than 120 countries for private, government, bilateral and multilateral clients.</p>
	  </div>
  </div>
</div>
  
<div class="row">
  <div class="small-12 large-10 large-offset-1 columns int-resource">
	  <h3>International Resources</h3>
	  
	  <p class="resources">The following resources are free to download:</p>
	  
	  <div class="avail-downloads">
		  <h4>United Kingdom</h4>
		  
		  <div class="row">
			  <div class="small-12 large-6 columns">
				  <a href="" class="download">Doing Business in the United Kingdom - 2013/14</a>
			  </div>
			  <div class="small-12 large-6 columns">
				  <a href="" class="download">Taxes in the United Kingdom - 2013/14</a>
			  </div>
		  </div>
	  </div>
  </div>
</div>
  
<div class="member-say-wrap">
  <div class="row">
	  <div class="small-12 large-10 large-offset-1 columns">
		  <p>What Our Members Say</p>
		  
		  <p class="quote">"The inaugural 2020 International Conference at Niagara-on-the-Lake  was both illuminating and most enjoyable. It was excellent to meet with individuals from the USA and Canada who were dealing with many of the same problems in many different ways so offering a great deal of food for thought. The organisation and hospitality was wonderful and I would certainly say it was time well spent."</p>
		  
		  <p class="person">Alan Marks, Martin Green Ravden (London, U.K.)</p>
		  
	  </div>
  </div>
</div>

<?php get_footer();
