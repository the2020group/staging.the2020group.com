<?php

/*
 * Template Name: Homepage
 */

get_header(); ?>

		<div class="home-intro">
			<?php include('includes/block-homepage-intro.php'); ?>
    </div>

    <div id="homeScroll" class="are-you">
    	<?php include('includes/block-homepage-are-you.php'); ?>
    </div>

    <div class="key-areas">
    	<?php include('includes/block-homepage-key-areas.php'); ?>
    </div>

    <div class="addi-content">
    	<?php include('includes/block-homepage-additional-content.php'); ?>
    </div>

		<div class="benchmark-tool">
    	<?php include('includes/block-benchmark-tool.php'); ?>
		</div>

		<div class="member-benefits">
    	<?php include('includes/block-homepage-member-benefits.php'); ?>
		</div>

		<div class="industry-exp">
			<?php include('includes/block-homepage-learn-from-experts.php'); ?>
    </div>


<?php get_footer();