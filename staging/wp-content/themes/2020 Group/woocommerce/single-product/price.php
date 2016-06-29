<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<?php
		$price = $product->get_price_html();


		$showprice = array(101,102,96); //filter for showing price above the line on specific products

		$terms = get_the_terms( $product->ID, 'product_cat' );
		foreach ($terms as $term) {
    		$product_cat_id = $term->term_id;

    		if (in_array($product_cat_id,$showprice)) {
    			break;
    		} else {
    			$price = '';
    		}
		}
	?>
	<p class="price"><?php echo $price; ?></p>
	<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>