<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'woocommerce' ); ?></p>

		<p><?php
			if ( is_user_logged_in() )
				_e( 'Please attempt your purchase again or go to your account page.', 'woocommerce' );
			else
				_e( 'Please attempt your purchase again.', 'woocommerce' );
		?></p>

		<p>
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></p>

		<ul class="order_details">
			<li class="order">
				<?php _e( 'Order Number:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php _e( 'Date:', 'woocommerce' ); ?>
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<li class="total">
				<?php _e( 'Total:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
				<?php _e( 'Payment Method:', 'woocommerce' ); ?>
				<strong><?php echo $order->payment_method_title; ?></strong>
			</li>
			<?php endif; ?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>



	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>

	<?php

	$smartdebit_ref = get_post_meta($order->id,'smartdebit_ref',true);

	if ($smartdebit_ref != false) : ?>
		<?php
		$smartdebit_first_amount = get_post_meta($order->id,'smartdebit_first_amount',true);
		$smartdebit_start = get_post_meta($order->id,'smartdebit_start',true);
		?>
	<h3>Direct Debit Information</h3>
	<p>Your Direct Debit Reference Number is: <?php echo $smartdebit_ref; ?> </p>

	<p>Your Direct Debit will be debited on: <?php echo date('d F Y',strtotime($smartdebit_start)); ?> in the amount of &pound;<?php echo number_format(($smartdebit_first_amount/100),2); ?>. The name that will appear on your bank statement is 2020 Innovation Training.</p>

	<p>Should you have any queries regarding your Direct Debit, please do not hesitate to contact us on 0121 314 2020. </p>

	<p>That completes the Direct Debit Instruction, thank you. An email confirming the details will be sent within 3 working days or not later than 5 working days before the first collection. Please find a copy enclosed a copy of the Direct Debit Guarantee below.</p>

	<div style="border: 1px solid #000; padding: 15px;">

		<div class="row">
			<div class="small-12 medium-6 columns">
				<h1>The Direct Debit Guarantee</h1>
			</div>
			<div class="small-12 medium-6 columns text-right">
				<img src="/wp-content/themes/2020 Group/library/images/general/dd.png" alt="direct debit logo" style="display: inline-block; max-width: 150px;">
			</div>
		</div>

			<ul style="clear: both;">
				<li>This Guarantee is offered by all banks and building societies that accept instructions to pay DirectDebits</li>
				<li>If there are any changes to the amount, date or frequency of your DirectDebit PSL re 2020 Innovation Training Ltd will notify you five (5) working days in advance of your account being debited or as otherwise agreed.
				    If you request PSL re 2020 Innovation Training Ltd to collect a payment, confirmation of the amount and date will be given to you at the time of request.</li>
				<li>If an error is made in the payment of your Direct Debit, by PSL re 2020 Innovation Training Ltd or your bank or building society you are entitled to a full and immediate refund of the amount paid from your bank or building society.<br />
					- If you receive a refund you are not entitled to, you must pay it back when PSL re 2020 Innovation Training Ltd asks you to.</li>
				<li>You can cancel a DirectDebit at any time by simply contacting your bank or building society. Written confirmation may be required. Please also notify us.</li>
			</ul>
	</div>


<?php endif; ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

<?php endif; ?>
