<?php
// function f10_add_subscription_headers_to_csv_export($column_headers, $csv_generator) {
//   if ('default_one_row_per_item' === $csv_generator->order_format) {
// 		$new_headers = array(
//       'start_date' => 'start_date',
//       'end_date' => 'end_date',
//       'expiry_date' => 'expiry_date',
//       'renewal_amount' => 'renewal_amount',
//       'next_payment' => 'next_payment',
//       'last_payment' => 'last_payment'
// 		);
//
// 		$column_headers = array_merge($column_headers, $new_headers);
// 	}
//
// 	return $column_headers;
// }
// add_filter('wc_customer_order_csv_export_order_headers', 'f10_add_subscription_headers_to_csv_export', 10, 2);

function f10_add_subscription_fields_to_csv_export($line_item, $item, $product, $order) {
  $line_item['start_date'] = $item['subscription_start_date'];
  $line_item['end_date'] = $item['subscription_end_date'];
  $line_item['expiry_date'] = $item['subscription_expiry_date'];
  $line_item['renewal_amount'] = $item['subscription_recurring_amount'];
  $line_item['next_payment'] = WC_Subscriptions_Order::get_next_payment_date($order, $item['product_id']);
  $line_item['last_payment'] = WC_Subscriptions_Order::get_last_payment_date($order, $item['product_id']);

	return $line_item;
}
add_filter('wc_customer_order_csv_export_order_line_item', 'f10_add_subscription_fields_to_csv_export', 10, 4);
