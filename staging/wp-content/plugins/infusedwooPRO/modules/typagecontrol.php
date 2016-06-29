<?php
	add_action('template_redirect', 'iw_typagecontrol');

	function iw_typagecontrol() {
		if(is_order_received_page()) {
			$overrides = get_option('iw_ty_ovs' );

			if(!is_array($overrides) || count($overrides) == 0) return false;

			$sorted_overrides = array();
			foreach($overrides as $k => $v) {
				$sorted_overrides[$overrides[$k]['order']] = $v;
			}
			ksort($sorted_overrides);

			// URL Vars:
			$orderid = (int) $_GET['order-received'];
			if($orderid) {
				$order = new WC_Order($orderid);
				if(!$order->key_is_valid($_GET['key'])) {
					return false;
				}
			} else {
				return false;
			}

			$passvars = array(
					'FirstName'			=> stripslashes($order->billing_first_name),
					'LastName'			=> stripslashes($order->billing_last_name),
					'Email'				=> stripslashes($order->billing_email),
					'StreetAddress1' 	=> stripslashes($order->billing_address_1),
					'StreetAddress2' 	=> stripslashes($order->billing_address_2),
					'City' 				=> stripslashes($order->billing_city),
					'State' 			=> stripslashes($order->billing_state),
					'Country' 			=> stripslashes(iw_to_country($order->billing_country)),
					'PostalCode' 		=> stripslashes($order->billing_postcode),
					'Address2Street1' 	=> stripslashes($order->shipping_address_1),
					'Address2Street2' 	=> stripslashes($order->shipping_address_2),
					'City2' 			=> stripslashes($order->shipping_city),
					'State2' 			=> stripslashes($order->shipping_state),
					'Country2' 			=> stripslashes(iw_to_country($order->shipping_country)),
				);

			$getvars = array();
			foreach($_GET as $k => $v) {
				if($k != 'page_id') {
					$passvars[$k] = $v;
					$getvars[$k] = $v;
				}
			}

			$items = $order->get_items();
			$totals = $order->get_total();
			$count = $order->get_item_count();
			$usedcoups = $order->get_used_coupons();

			foreach($sorted_overrides as $o) {
				$redir = false;
				$type = $o['cond'];
				$checks = $o['further'];

				if($type == 'always') {
					$redir = true;
				} else if($type == 'product') {
					foreach($items as $item) {
						if(in_array($item['product_id'], $checks)) {
							$redir = true;
						}
					}
				} else if($type == 'categ') {
					foreach($items as $item) {
						$cats = get_the_terms($item['product_id'], 'product_cat');
						if(is_array($cats)) {
							foreach($cats as $cat) {
								if(in_array($cat->term_id, $checks)) {
									$redir = true;
								}
							}
						}

						if($redir) break;
					}
				} else if($type == 'morevalue') {
					$redir = ($totals > $checks);
				} else if($type == 'lessvalue') {
					$redir = ($totals < $checks);
				} else if($type == 'moreitem') {
					$redir = ($count > $checks);
				} else if($type == 'lessitem') {
					$redir = ($count < $checks);
				} else if($type == 'coupon') {
					if(empty($checks)) {
						$redir = count($usedcoups);
					} else {
						foreach($usedcoups as $coupon) {
							if(strpos($checks, $coupon) !== false) {
								$redir = true;
							}
						}
					}
				} else if($type == 'pg') {
					$redir = ($order->payment_method == $checks);
				}


				if($redir) {
					$ty_uri = $o['url'];
					if($o['passvars'] == 'true') {
						if(strpos($ty_uri, "?") !== false)
							$ty_uri .= "&" . http_build_query($passvars);
						else 
							$ty_uri .= "?" . http_build_query($passvars);
					} else if(count($getvars) > 0) {
						if(strpos($ty_uri, "?") !== false)
							$ty_uri .= "&" . http_build_query($getvars);
						else 
							$ty_uri .= "?" . http_build_query($getvars);
					}

					header("Location: $ty_uri");
					exit();
					break;
				}
			}
		}
	}
?>