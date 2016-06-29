<?php
/**
 * Variable product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post, $_subscription_details;
?>


<?php
  $current_user = wp_get_current_user();
  $email = $current_user->email;

  // Access Control checks
  $access = hasUserAccessTo(get_post_meta($post->ID,'groups-groups_read_post'));

  $account_on_hold = false;

  /* Subscription Checking - does the user have a valid subscription */
    $user_id = $current_user->ID;

  	$parent_user_id = get_user_meta($user_id,'2020_parent_account',true);

    $is_child_user=false;

    if ($parent_user_id>0) {
    	$child_user_id = $user_id;
    	$user_id = $parent_user_id;
    	$is_child_user = true;
    }

    $subscriptions = $_subscription_details;

    if(!empty($subscriptions)) {
    	foreach($subscriptions as $subscription) {
    		if ($subscription['status'] == 'active') {
    			break;
    		}
	      if ($subscription['status'] != 'active') {
	        $account_on_hold = true;
	      }
	    }
    }

    /* END Subscription Checking */

  if ($account_on_hold) : ?>

    <div class="cta-block side-block text-block texturebg add-cart-block">
      <h2>Your subscription is currently inactive</h2>
      <h4>Please contact the primary account holder for more information.</p>
    </div>

  <?php
  elseif (!is_user_logged_in()) : ?>

  	<?php if(!has_term(97,'product_cat')) : ?>

      <div class="cta-block side-block text-block texturebg add-cart-block">
        <h2>Not yet a member?</h2>
        <?php if(	is_single(1028, 1131, 2557, 2801) ) : ?>
        	<h4>Become a 2020 member to download this content at discounted rates</h4>
    	  <?php elseif( is_single(1426) ) : ?>
        	<h4>Become a 2020 member to attend this conference at discounted rates</h4>
    	  <?php elseif(	has_term( array(30, 93, 94, 95, 99, 100, 109), 'product_cat') ) : ?>
        	<h4>Become a 2020 member to access some 2020 webinars as part of your subscription and others at discounted rates</h4>
    	  <?php else : ?>
    	  	<h4>Become a 2020 member to download this content</h4>
    	  <?php endif; ?>

        <p>Join 1,000's of other progressive accountants and tax professionals worldwide who benefit from our innovative solutions.</p>

        <?php if(is_single(48)) : ?>
        	<a href="<?php echo get_option('home'); ?>/product/standard/?attribute_partners=2-5-partners" class="gen-btn orange icon trophy">Join Now</a>
    	  <?php else : ?>
    	  	<a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn orange icon trophy">Join Now</a>
    	  <?php endif; ?>

        <p class="already-member">Already a member? <a href="/login?ref=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="white-link">Login now</a></p>
      </div>

	  <?php endif; ?>

  <?php elseif ($access) : ?>

  	<?php if( has_term(array(101, 102), 'product_cat') ) : ?>

  <?php elseif (userHasSubscription('active')) : ?>

		<?php if(!has_term(97,'product_cat')) : ?>

  		<div class="cta-block side-block text-block texturebg add-cart-block">
        <?php if(	is_single(array(1028, 2557, 2801, 22162, 21019)) || has_term(array(10,30, 99, 100), 'product_cat') ) : ?>
        	<h4>This item is discounted as you are a 2020 member</h4>
        <?php elseif(	has_term(array(94, 109, 1426), 'product_cat') || is_single(1426) ) : ?>
      		<h4>This conference is discounted as you are a 2020 member</h4>
  	  	<?php else : ?>
    	  	<h4>This item is included in your 2020 membership</h4>
  	  	<?php endif; ?>
      </div>

		<?php endif; ?>

	<?php elseif (userHasSubscription(array('pending', 'on-hold', 'expired', 'pending'))) : ?>

    <div class="cta-block side-block text-block texturebg add-cart-block">
      <h4>Your subscription is not currently active. Please resubscribe to get a discounted price.</h4>
    </div>

	<?php endif; ?>

  <?php else : ?>

  	<?php if(!has_term(97,'product_cat')) : ?>

    <div class="cta-block side-block text-block texturebg">
      <h4>Sorry, you currently don't have access to this</h4>
      <p>Upgrade your membership in order to see the rest of this content</p>
      <a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn orange icon trophy">View Membership Options</a>
    </div>

	<?php endif; ?>

  <?php endif;

    if ( wc_customer_bought_product( $email, $current_user->ID, $post->ID ) ):


    	echo 'Already bought';

    endif;
	?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">

	<?php if ( ! empty( $available_variations ) ) : ?>
		<div class="product_form__wrap">

			<div class="product_form__left">
				<!-- Start Variations -->
				<table class="variations" cellspacing="0">
					<tbody>
						<?php $loop = 0; foreach ( $attributes as $name => $options ) : $loop++; ?>
							<tr>
								<td class="label"><label for="<?php echo sanitize_title($name); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
								<td class="value"><select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>">
									<option value=""><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option>
									<?php
										if ( is_array( $options ) ) {

											if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ) {
												$selected_value = $_REQUEST[ 'attribute_' . sanitize_title( $name ) ];
											} elseif ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) {
												$selected_value = $selected_attributes[ sanitize_title( $name ) ];
											} else {
												$selected_value = '';
											}

											// Get terms if this is a taxonomy - ordered
											if ( taxonomy_exists( sanitize_title( $name ) ) ) {

												$orderby = wc_attribute_orderby( sanitize_title( $name ) );

												switch ( $orderby ) {
													case 'name' :
														$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
													break;
													case 'id' :
														$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false, 'hide_empty' => false );
													break;
													case 'menu_order' :
														$args = array( 'menu_order' => 'ASC', 'hide_empty' => false );
													break;
												}

												$terms = get_terms( sanitize_title( $name ), $args );

												foreach ( $terms as $term ) {
													if ( ! in_array( $term->slug, $options ) )
														continue;

													echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
												}
											} else {

												foreach ( $options as $option ) {
													echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
												}

											}
										}
									?>
								</select> <?php
									if ( sizeof( $attributes ) === $loop )
										echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'woocommerce' ) . '</a>';

									?>
								</td>
							</tr>
				        <?php endforeach;?>
					</tbody>
				</table>
				<!-- End Variations -->
				<!-- woocommerce_before_add_to_cart_button -->
				<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
				<!-- end: woocommerce_before_add_to_cart_button -->
				<div style="display:none;" class="who_content">You will need to allocate delegate name(s) in My2020Dashboard 'My Purchases' after you have successfully completed the checkout process.</div>
				<div style="display:none;" class="who_content_error">Select who this booking is for from the options above.</div>
			</div>

			<div class="product_form__right">
				<!-- Start Single Variation wrap -->
				<div class="single_variation_wrap" style="display:none;">
					<!-- woocommerce_before_single_variation -->
					<?php do_action( 'woocommerce_before_single_variation' ); ?>
					<!-- end: woocommerce_before_single_variation -->
					<div class="single_variation"></div>
					<div class="variations_button">
						<!-- woocommerce_quantity_input -->
						<?php woocommerce_quantity_input(); ?>
						<!-- end: woocommerce_quantity_input -->
						<button type="submit" class="single_add_to_cart_button gen-btn orange icon right-arrow"><?php echo $product->single_add_to_cart_text(); ?></button>
					</div>

					<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
					<input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
					<input type="hidden" name="variation_id" class="variation_id" value="" />

					<!-- woocommerce_after_single_variation -->
					<?php do_action( 'woocommerce_after_single_variation' ); ?>
					<!-- end: woocommerce_after_single_variation -->
					<div class="currency-label"><?php the_field('currency_label',5); ?></div>
				</div>

			</div>

		</div>

		<!-- woocommerce_after_add_to_cart_button -->
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php else : ?>

		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
