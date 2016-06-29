<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wc_print_notices();
?>

<p class="cart-empty"><?php _e( 'Your basket is currently empty.', 'woocommerce' ) ?></p>

<?php do_action( 'woocommerce_cart_is_empty' ); 
if ($redirect_product != '' ) {
?>	
<p class="return-to-shop"><a class="button wc-backward" href="<?php echo get_permalink( $redirect_product ); ?>/"><?php _e( 'Back to your new product', 'woocommerce' ) ?></a></p>
<?php
} else {
?>
<p class="return-to-shop"><a class="button wc-backward" href="<?php echo get_option('home'); ?>/"><?php _e( 'Continue Shopping', 'woocommerce' ) ?></a></p>
<?php 
}
?>
