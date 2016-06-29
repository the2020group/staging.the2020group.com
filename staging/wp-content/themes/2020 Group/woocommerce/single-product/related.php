<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

$related = $product->get_related( $posts_per_page );

$cross_sell = $product->get_cross_sells();

// if there are linked products replace related with linked products

if (count($cross_sell) > 0) {
	$args = apply_filters( 'woocommerce_related_products_args', array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'posts_per_page'       => 10,
		'orderby'              => $orderby,
		'post__in'             => $cross_sell,
		'post__not_in'         => array( $product->id )
	) );
}
else {

	if ( sizeof( $related ) == 0 ) return;

	$terms = wp_get_post_terms( $product->id, 'product_cat' );
	$categories = array();
	foreach ( $terms as $term ) { $categories[] = $term->term_id; }

	$cat_count = count($categories);
	$diff_array = array_diff($categories, array(97,11,10));

	if($cat_count != count ($diff_array)) {

		if(	(is_single(1784)) || (is_single(1786))	) {

			$args = apply_filters( 'woocommerce_related_products_args', array(
				'post_type'            => 'product',
				'ignore_sticky_posts'  => 1,
				'no_found_rows'        => 1,
				'posts_per_page'       => $posts_per_page,
				'orderby'              => $orderby,
				//'post__in'             => $related,
				'post__not_in'         => array( $product->id ),
				'tax_query' 		   => array(
											array(
												'taxonomy' => 'product_cat',
												'field'    => 'term_id',
												'terms'    => array(11,10),
												'operator' => 'NOT IN',
											),
										),
			) );

		} else {
			$args = apply_filters( 'woocommerce_related_products_args', array(
				'post_type'            => 'product',
				'ignore_sticky_posts'  => 1,
				'no_found_rows'        => 1,
				'posts_per_page'       => $posts_per_page,
				'orderby'              => $orderby,
				'post__in'             => $related,
				'post__not_in'         => array( $product->id ),
				'meta_query' => array (
			                                array(
			                                        'key' => 'date',
			                                        'value' => date('Ymd'),
			                                        'compare' => '>='
			                                )
			                        )
			) );
		}

	} else {

	$args = apply_filters( 'woocommerce_related_products_args', array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'posts_per_page'       => $posts_per_page,
		'orderby'              => $orderby,
		'post__in'             => $related,
		'post__not_in'         => array( $product->id )
	) );



	}

}

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>

	<div class="related products">

		<h2><?php _e( 'Related Products', 'woocommerce' );?> </h2>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product-related' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
