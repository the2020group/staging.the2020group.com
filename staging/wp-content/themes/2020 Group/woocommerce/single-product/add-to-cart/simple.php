<?php
/**
 * Simple product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $post;


$current_user = wp_get_current_user();
$email = $current_user->email;

if ( wc_customer_bought_product( $email, $current_user->ID, $post->ID ) ):

	echo 'Already bought';

endif;

	if ( ! $product->is_purchasable() ) return;
	?>

	<?php
		// Availability
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

		echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	?>
	<?php if ( $product->is_in_stock() ) : ?>

		<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

		<form class="cart" method="post" enctype='multipart/form-data'>
			<div class="product_form__wrap">

				<div class="product_form__left">
					<!-- wc_before_add_to_cart_btn -->
				 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
				 	<!-- End: wc_before_add_to_cart_btn -->
				 	<div style="display:none;" class="who_content">You will need to allocate delegate name(s) in My2020Dashboard 'My Purchases' after you have successfully completed the checkout process.</div>
					<div style="display:none;" class="who_content_error">Select who this booking is for from the options above.</div>
				</div>

				<div class="product_form__right">
					<!-- Single Var Wrap -->
					<div class="single_variation_wrap">
						<div class="single_variation"><?php echo fixed_pricing_2($product,true); ?></div>
					</div>
					<!-- End: Single Var Wrap -->
					<!-- Single variation wrap -->
					<div class="single_variation_wrap">

					 	<?php
					 		if ( ! $product->is_sold_individually() )
					 			woocommerce_quantity_input( array(
					 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
					 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
					 			) );
					 	?>

				 		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
				 		<button type="submit" class="single_add_to_cart_button gen-btn orange icon right-arrow"><?php echo $product->single_add_to_cart_text(); ?></button>


				 	</div>
				 	<!-- End: Single variation wrap -->
				 	<div class="currency-label"><?php the_field('currency_label',5); ?></div>
				</div>

			</div>




		 	<!-- After add to cart button -->
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>



		</form>

		<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

	<?php endif; ?>
