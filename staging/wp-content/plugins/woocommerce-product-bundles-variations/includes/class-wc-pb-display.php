<?php
/**
 * Product Bundle front-end functions and filters.
 *
 * @class 	WC_PB_Display
 * @version 4.7.6
 * @since   4.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_PB_Display {

	/**
	 * Setup class
	 */
	function __construct() {

		// Single product template for product bundles
		add_action( 'woocommerce_bundle_add_to_cart', array( $this, 'woo_bundles_add_to_cart' ) );

		// Single product add-to-cart button template for product bundles
		add_action( 'woocommerce_bundles_add_to_cart_button', array( $this, 'woo_bundles_add_to_cart_button' ) );

		// Filter add_to_cart_url & add_to_cart_text when product type is 'bundle'
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'woo_bundles_loop_add_to_cart_link' ), 10, 2 );

		// Add preamble info to bundled products
		add_filter( 'woocommerce_cart_item_name', array( $this, 'woo_bundles_in_cart_item_title' ), 10, 3 );
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'woo_bundles_cart_item_item_quantity' ), 10, 3 );

		add_filter( 'woocommerce_order_item_name', array( $this, 'woo_bundles_order_table_item_title' ), 10, 2 );
		add_filter( 'woocommerce_order_item_quantity_html', array( $this, 'woo_bundles_order_table_item_quantity' ), 10, 2 );

		// Change the tr class attributes when displaying bundled items in templates
		add_filter( 'woocommerce_cart_item_class', array( $this, 'woo_bundles_table_item_class' ), 10, 3 );
		add_filter( 'woocommerce_order_item_class', array( $this, 'woo_bundles_table_item_class' ), 10, 3 );

		// Front end variation select box jquery for multiple variable products
		add_action( 'wp_enqueue_scripts', array( $this, 'woo_bundles_frontend_scripts' ), 100 );

		// QuickView support
		add_action( 'wc_quick_view_enqueue_scripts', array( $this, 'woo_bundles_qv' ) );
	}

	/**
	 * Add-to-cart button and quantity template for product bundles.
	 * @return void
	 */
	function woo_bundles_add_to_cart_button() {

		global $woocommerce_bundles;

		wc_get_template( 'single-product/add-to-cart/bundle-quantity-input.php', array(), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );
		wc_get_template( 'single-product/add-to-cart/bundle-button.php', array(), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );
	}

	/**
	 * Add-to-cart template for bundle type products.
	 * @return void
	 */
	function woo_bundles_add_to_cart() {

		global $woocommerce_bundles, $product, $post;

		// Enqueue variation scripts
		wp_enqueue_script( 'wc-add-to-cart-bundle' );

		wp_enqueue_style( 'wc-bundle-css' );

		$bundled_items = $product->get_bundled_items();

		if ( $bundled_items )
			wc_get_template( 'single-product/add-to-cart/bundle.php', array(
				'available_variations' 		=> $product->get_available_bundle_variations(),
				'attributes'   				=> $product->get_bundle_variation_attributes(),
				'selected_attributes' 		=> $product->get_selected_bundle_variation_attributes(),
				'bundle_price_data' 		=> $product->get_bundle_price_data(),
				'bundled_items' 			=> $bundled_items
			), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );

	}

	/**
	 * Replaces add_to_cart button url with something more appropriate.
	 **/
	function woo_bundles_loop_add_to_cart_url( $url ) {

		global $product;

		if ( $product->is_type( 'bundle' ) )
			return $product->add_to_cart_url();

		return $url;
	}

	/**
	 * Adds product_type_simple class for Ajax add to cart when all items are simple.
	 **/
	function woo_bundles_add_to_cart_class( $class ) {

		global $product;

		if ( $product->is_type( 'bundle' ) ) {

			if ( $product->has_variables() )
				return '';
			else
				return $class . ' product_type_simple';
		}

		return $class;
	}

	/**
	 * Replaces add_to_cart text with something more appropriate.
	 **/
	function woo_bundles_add_to_cart_text( $text ) {

		global $product;

		if ( $product->is_type( 'bundle' ) )
			return $product->add_to_cart_text();

		return $text;
	}

	/**
	 * Adds QuickView support
	 */
	function woo_bundles_loop_add_to_cart_link( $link, $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			if ( $product->is_in_stock() && $product->all_items_in_stock() && ! $product->has_variables() )
				return str_replace( 'product_type_bundle', 'product_type_bundle product_type_simple', $link );
			else
				return str_replace( 'add_to_cart_button', '', $link );
		}

		return $link;
	}

	/**
	 * Adds title preambles to cart items.
	 *
	 * @param  string   $content
	 * @param  array    $cart_item_values
	 * @param  string   $cart_item_key
	 * @return string
	 */
	public function woo_bundles_in_cart_item_title( $content, $cart_item_values, $cart_item_key ) {

		if ( ! empty( $cart_item_values[ 'bundled_by' ] ) ) {

			if ( is_checkout() || ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'woocommerce_update_order_review' ) )
				$item_quantity = apply_filters( 'woocommerce_bundled_cart_item_quantity_html', ' <strong class="bundled-product-quantity">' . sprintf( '&times; %s', $cart_item_values[ 'quantity' ] ) . '</strong>', $cart_item_values );
			else
				$item_quantity = '';

			$bundled_product_title = '<span class="bundled-product-name">' . $content . $item_quantity . '</span>';

			return $bundled_product_title;
		}

		return $content;
	}

	/**
	 * Delete bundled item quantity from the review-order.php template. Quantity is inserted into the product name by 'woo_bundles_in_cart_item_title'.
	 *
	 * @param  string 	$quantity
	 * @param  array 	$cart_item
	 * @param  string 	$cart_key
	 * @return string
	 */
	public function woo_bundles_cart_item_item_quantity( $quantity, $cart_item, $cart_key ) {

		if ( ! empty( $cart_item[ 'bundled_by' ] ) ) {
			return '';
		}

		return $quantity;
	}

	/**
	 * Adds bundled item title preambles to order-details template.
	 *
	 * @param  string 	$content
	 * @param  array 	$order_item
	 * @return string
	 */
	public function woo_bundles_order_table_item_title( $content, $order_item ) {

		if ( ! empty( $order_item[ 'bundled_by' ] ) ) {

			$item_quantity = apply_filters( 'woocommerce_bundled_order_item_quantity_html', ' <strong class="bundled-product-quantity">' . sprintf( '&times; %s', $order_item[ 'qty' ] ) . '</strong>', $order_item );

			if ( function_exists( 'is_account_page' ) && is_account_page() || function_exists( 'is_checkout' ) && is_checkout() ) {

				return '<span class="bundled-product-name">' . $content . $item_quantity . '</span>';

			} else {

				return '<small>' . $content . '</small>';
			}
		}

		return $content;
	}

	/**
	 * Delete bundled item quantity from order-details template. Quantity is inserted into the product name by 'woo_bundles_order_table_item_title'.
	 *
	 * @param  string 	$content
	 * @param  array 	$order_item
	 * @return string
	 */
	public function woo_bundles_order_table_item_quantity( $content, $order_item ) {

		if ( ! empty( $order_item[ 'bundled_by' ] ) ) {
			return '';
		}

		return $content;
	}

	/**
	 * Change the tr class of bundled items in all templates to allow their styling.
	 *
	 * @param  string   $classname      original classname
	 * @param  array    $values         cart item data
	 * @param  string   $cart_item_key  cart item key
	 * @return string                   modified class string
	 */
	function woo_bundles_table_item_class( $classname, $values, $cart_item_key ) {

		if ( isset( $values[ 'bundled_by' ] ) )
			return $classname . ' bundled_table_item';
		elseif ( isset( $values[ 'stamp' ] ) )
			return $classname . ' bundle_table_item';

		return $classname;
	}

	/**
	 * Frontend scripts.
	 *
	 * @return void
	 */
	function woo_bundles_frontend_scripts() {

		global $woocommerce_bundles;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-add-to-cart-bundle', $woocommerce_bundles->woo_bundles_plugin_url() . '/assets/js/add-to-cart-bundle' . $suffix . '.js', array( 'jquery', 'wc-add-to-cart-variation' ), $woocommerce_bundles->version, true );
		wp_register_style( 'wc-bundle-css', $woocommerce_bundles->woo_bundles_plugin_url() . '/assets/css/bundles-frontend.css', false, $woocommerce_bundles->version );
		wp_register_style( 'wc-bundle-style', $woocommerce_bundles->woo_bundles_plugin_url() . '/assets/css/bundles-style.css', false, $woocommerce_bundles->version );
		wp_enqueue_style( 'wc-bundle-style' );

		$params = array(
			'i18n_free'                     => __( 'Free!', 'woocommerce' ),
			'i18n_total'                    => __( 'Total', 'woocommerce-product-bundles' ) . ': ',
			'i18n_subtotal'                 => __( 'Subtotal', 'woocommerce-product-bundles' ) . ': ',
			'i18n_partially_out_of_stock'   => __( 'Insufficient stock', 'woocommerce-product-bundles' ),
			'i18n_partially_on_backorder'   => __( 'Available on backorder', 'woocommerce-product-bundles' ),
			'i18n_select_options'           => sprintf( __( '<p class="price"><span class="bundle_error">%s</span></p>', 'woocommerce-product-bundles' ), __( 'To continue, please choose product options&hellip;', 'woocommerce-product-bundles' ) ),
			'i18n_unavailable_text'         => sprintf( __( '<p class="price"><span class="bundle_error">%s</span></p>', 'woocommerce-product-bundles' ), __( 'Sorry, this product cannot be purchased at the moment.', 'woocommerce-product-bundles' ) ),
			'currency_symbol'               => get_woocommerce_currency_symbol(),
			'currency_position'             => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
			'currency_format_num_decimals'  => absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'currency_format_decimal_sep'   => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format_trim_zeros'    => false == apply_filters( 'woocommerce_price_trim_zeros', false ) ? 'no' : 'yes'
		);

		wp_localize_script( 'wc-add-to-cart-bundle', 'wc_bundle_params', $params );

	}

	/**
	 * Load quickview script.
	 */
	function woo_bundles_qv() {

		global $woocommerce_bundles;

		if ( ! is_product() ) {

			$this->woo_bundles_frontend_scripts();

			wp_enqueue_script( 'wc-add-to-cart-bundle' );
			wp_enqueue_style( 'wc-bundle-css' );

		}

	}

}
