<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); ?>



<?php if (is_product_category('acca-webinars') || is_product_category('acca-events')):
	$accaContent = get_post(842, ARRAY_A);

?>
<div class="row">
	<div class="small-12 large-8 columns columns">
  	<h1 class="acca-title"><?php echo $accaContent['post_title']; ?></h1>
  </div>
</div>
<header class="acca-head">
  <div class="row">
      <div class="small-12 large-8 columns">
        <div class="intro-copy">
	        <?php echo $accaContent['post_content']; ?>
	      </div>
      </div>
      <div class="small-12 large-4 columns end webinar-img">
        <?php
				$image = get_field('acca_main_download', 842);

				if( !empty($image) ): ?>

					<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />

				<?php endif; ?>
				<p><a href="<?php the_field('acca_main_download_text', 842); ?>" class="download">Download the 2015 Webinar Programme <span class="icon-down-arrow"></span></a></p>
      </div>
   </div>
</header>
<?php endif; ?>


<div class="row">
  <div class="small-12 columns end">
    <div class="archive-wrap">

		<?php if ( have_posts() ) : ?>

      <header class="article-head">
		<?php
			/**
			 * woocommerce_before_main_content hook
			 *
			 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked woocommerce_breadcrumb - 20
			 */
			do_action( 'woocommerce_before_main_content' );
		?>

		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>



		<?php do_action( 'woocommerce_archive_description' ); ?>


		<?php
			/**
			 * woocommerce_before_shop_loop hook
			 *
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );
		?>


      </header>

<?php if((is_tax('product_cat',10)) || (term_is_ancestor_of( 10, get_queried_object()->term_id, 'product_cat' ))) : ?>

	<div class="row">
		<div class="small-12 columns end">
			<h2>Which Webinars do you want to see?</h2>
			<p>Please select from the dropdown list below</p>
		</div>
	</div>
	<div class="row">
		<form class="product-filter small-12 medium-6 large-4 columns">
			<select class="redirectme">
				<option value="#">Filter by Webinar type</option>
				<?php wc_subcats_from_parentcat_by_ID_option(10, '', 'filter_name'); ?>
			</select>
		</form>
	</div>

<?php endif; ?>

<?php if((is_tax('product_cat',97)) || (term_is_ancestor_of( 97, get_queried_object()->term_id, 'product_cat' ))) : ?>
	<div class="row">
		<div class="small-12 columns end">
			<h2>Which Events do you want to see?</h2>
			<p>Please select from the dropdown list below</p>
		</div>
	</div>
	<div class="row">
		<form class="product-filter small-12 medium-6 large-4 columns">
			<select class="redirectme">
				<!--<option value="#">Filter by ACCA Webinar type</option>-->
				<?php wc_subcats_from_parentcat_by_ID_option(97, '', 'filter_name'); ?>
			</select>
		</form>

	</div>
<?php endif; ?>

    <?php  woocommerce_product_loop_start(); ?>

        <?php //woocommerce_product_subcategories(); ?>
        
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					if ( is_product_category('acca-webinars') ) {

						
						$terms = get_the_terms( get_the_ID(), 'product_cat' );
						
						foreach($terms as $term) {
							if ($term->term_id == '103') {
								wc_get_template_part( 'content', 'product-archive' );
							}
						}
					} else {
						wc_get_template_part( 'content', 'product-archive' );
					}
					?>
				<?php endwhile; // end of the loop. ?>

      <?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

      <?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

    </div>
  </div>
</div>

	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		// do_action( 'woocommerce_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>