<?php
/**
 * Product bundle add to cart template.
 * @version 4.8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

global $woocommerce, $product, $post, $woocommerce_bundles;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form method="post" enctype="multipart/form-data" class="bundle_form" >

  <h3><?php _e( 'Included in this package:' ); ?></h3>

  <?php
	foreach ( $bundled_items as $bundled_item ) {

		$bundled_item_id = $bundled_item->item_id;
		$bundled_product = $bundled_item->product;
		$item_quantity   = $bundled_item->get_quantity();

		?><div class="bundled_product bundled_product_summary product" style="<?php echo ( ! $bundled_item->is_visible() ? 'display:none;' : '' ); ?>" ><?php

			// title template
			wc_get_template( 'single-product/bundled-item-title.php', array(
				'quantity'     => $item_quantity,
				'title'        => $bundled_item->get_title(),
				'optional'     => $bundled_item->is_optional(),
				'bundled_item' => $bundled_item,
			), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );


			if ( $bundled_item->is_visible() ) {

				// image template
				if ( $bundled_item->is_thumbnail_visible() )
					wc_get_template( 'single-product/bundled-item-image.php', array( 'post_id' => $bundled_product->id ), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );
			}

			?><div class="details"><?php

				// description template
				wc_get_template( 'single-product/bundled-item-description.php', array(
					'description' => $bundled_item->get_description()
				), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );

				if ( $bundled_product->is_purchasable() ) {

					// Availability
					$availability = $woocommerce_bundles->helpers->get_bundled_product_availability( $bundled_product, $item_quantity );

					if ( $bundled_product->product_type == 'simple' || $bundled_product->product_type == 'subscription' ) {

						$bundled_item->add_price_filters();

						if ( $bundled_item->is_optional() ) {

							// optional checkbox template
							wc_get_template( 'single-product/bundled-item-optional.php', array(
								'quantity'     => $item_quantity,
								'bundled_item' => $bundled_item,
								'is_in_stock'  => isset( $availability[ 'class' ] ) && $availability[ 'class' ] != 'out-of-stock'
							), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );

						} else {

							wc_get_template( 'single-product/bundled-item-price.php', array(
								'bundled_item' => $bundled_item ), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );
						}

						?><div class="cart" data-title="<?php echo $bundled_item->get_raw_title(); ?>" data-optional="<?php echo $bundled_item->is_optional() ? true : false; ?>" data-type="<?php echo $bundled_product->product_type; ?>" data-bundled-item-id="<?php echo $bundled_item_id; ?>" data-product_id="<?php echo $post->ID . str_replace( '_', '', $bundled_item_id ); ?>" data-bundle-id="<?php echo $post->ID; ?>"><?php

							if ( $availability[ 'availability' ] )
								echo apply_filters( 'woocommerce_stock_html', '<p class="stock '. $availability[ 'class' ] .'">' . $availability[ 'availability' ] . '</p>', $availability[ 'availability' ] );

							?><div class="bundled_item_wrap">
								<div class="bundled_item_optional_content" style="<?php echo $bundled_item->is_optional() && ! $bundled_item->is_optional_checked() ? 'display:none;' : ''; ?>"><?php

									// Compatibility with plugins that normally hook to woocommerce_before_add_to_cart_button
									do_action( 'woocommerce_bundled_product_add_to_cart', $bundled_product->id, $bundled_item );

									$bundled_item->remove_price_filters();

									?><div class="quantity" style="display:none;"><input class="qty" type="hidden" name="bundle_quantity_<?php echo $bundled_item_id; ?>" value="<?php echo $item_quantity; ?>" /></div>
								</div>
							</div>
						</div><?php

					} elseif ( $bundled_product->product_type == 'variable' ) {

						$bundled_item->add_price_filters();

						if ( $bundled_item->is_optional() ) {

							// optional checkbox template
							wc_get_template( 'single-product/bundled-item-optional.php', array(
								'quantity'     => $item_quantity,
								'bundled_item' => $bundled_item,
								'is_in_stock'  => isset( $availability[ 'class' ] ) && $availability[ 'class' ] != 'out-of-stock'
							), false, $woocommerce_bundles->woo_bundles_plugin_path() . '/templates/' );

						}

						?><div class="cart bundled_item_optional_content" data-title="<?php echo $bundled_item->get_raw_title(); ?>" style="<?php echo $bundled_item->is_optional() && ! $bundled_item->is_optional_checked() ? 'display:none;' : ''; ?>" data-optional="<?php echo $bundled_item->is_optional() ? true : false; ?>" data-type="<?php echo $bundled_product->product_type; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations[ $bundled_item_id ] ) ); ?>" data-bundled-item-id="<?php echo $bundled_item_id; ?>" data-product_id="<?php echo $post->ID . str_replace('_', '', $bundled_item_id); ?>" data-bundle-id="<?php echo $post->ID; ?>">
							<table class="variations hidden" cellspacing="0">
								<tbody><?php
								$loop = 0;
								foreach ( $attributes[ $bundled_item_id ] as $name => $options ) {
									$loop++;
									?><tr class="attribute-options" data-attribute_label="<?php echo wc_attribute_label( $name ); ?>">
										<td class="label">
											<label for="<?php echo sanitize_title( $name ) . '_' . $bundled_item_id; ?>"><?php echo wc_attribute_label( $name ); ?> <abbr class="required" title="required">*</abbr></label>
										</td>
										<td class="value">
											<select id="<?php echo esc_attr( sanitize_title( $name ) . '_' . $bundled_item_id ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>">
												<option value=""><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option><?php

												if ( is_array( $options ) ) {

													if ( isset( $_REQUEST[ 'bundle_attribute_' . sanitize_title( $name ) . '_' . $bundled_item_id ] ) ) {
														$selected_value = $_REQUEST[ 'bundle_attribute_' . sanitize_title( $name ) . '_' . $bundled_item_id ];
													} elseif ( isset( $selected_attributes[ $bundled_item_id ][ sanitize_title( $name ) ] ) ) {
														$selected_value = $selected_attributes[ $bundled_item_id ][ sanitize_title( $name ) ];
													} else {
														$selected_value = '';
													}

													// Placeholder: Do not show filtered-out (disabled) options

													if ( taxonomy_exists( sanitize_title( $name ) ) ) {

														$terms = wc_bundles_get_product_terms( $bundled_product->id, $name, array( 'fields' => 'all' ) );

														foreach ( $terms as $term ) {

															if ( ! in_array( $term->slug, $options ) ) {
																continue;
															}

															echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
														}

													} else {

														foreach ( $options as $option ) {
															echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
														}
													}
												}

											?></select> <?php

											if ( sizeof( $attributes[ $bundled_item_id ] ) == $loop ) {
												echo '<a class="reset_variations" href="#reset_' . $bundled_item_id .'">'.__( 'Clear selection', 'woocommerce' ).'</a>';
											}

										?></td>
									</tr><?php

								}

								?></tbody>
							</table><?php

							// Compatibility with plugins that normally hook to woocommerce_before_add_to_cart_button
							do_action( 'woocommerce_bundled_product_add_to_cart', $bundled_product->id, $bundled_item );

							$bundled_item->remove_price_filters();

							?><div class="single_variation_wrap bundled_item_wrap" style="display:none;">
								<div class="single_variation"><?php echo fixed_pricing_2($product,true); ?></div>
								<div class="variations_button">
									<input type="hidden" name="variation_id" value="" />
									<input class="qty" type="hidden" name="bundle_quantity_<?php echo $bundled_item_id; ?>" value="<?php echo $item_quantity; ?>" />
								</div>
							</div>
						</div><?php
					}
				} else {
					echo __( 'Sorry, this item is not available at the moment.', 'woocommerce-product-bundles' );
				}
			?></div>
		</div><?php

	}

	if ( $product->is_purchasable() ) {

		?><div class="cart bundle_data bundle_data_<?php echo $post->ID; ?>" data-button_behaviour="<?php echo esc_attr( apply_filters( 'woocommerce_bundles_button_behaviour', 'new', $product ) ); ?>" data-bundle_price_data="<?php echo esc_attr( json_encode( $bundle_price_data ) ); ?>" data-bundle-id="<?php echo $post->ID; ?>">

			<div class="bundle_wrap" style="<?php echo apply_filters( 'woocommerce_bundles_button_behaviour', 'new', $product ) == 'new' ? '' : 'display:none'; ?>">

				<div class="bundle_price"></div><?php

				// Bundle Availability
				$availability = $product->get_availability();

				if ( $availability[ 'availability' ] )
					echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . $availability[ 'class' ] . '">' . $availability[ 'availability' ] . '</p>', $availability[ 'availability' ] );

				?><div class="bundle_button"><?php

					foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

						$bundled_item_id = $bundled_item->item_id;
						$bundled_product = $bundled_item->product;

						if ( $bundled_product->product_type == 'variable' ) {

							?><input type="hidden" name="bundle_variation_id_<?php echo $bundled_item_id; ?>" class="bundle_variation_id_<?php echo $bundled_item_id; ?>" value="" /><?php
							foreach ( $attributes[ $bundled_item_id ] as $name => $options ) { ?>
								<input type="hidden" name="bundle_attribute_<?php echo sanitize_title( $name ); ?>_<?php echo $bundled_item_id; ?>" class="bundle_attribute_<?php echo sanitize_title( $name ); ?>_<?php echo $bundled_item_id; ?>" value=""><?php
							}
						}
					}
          ?>
          		</div>


       	<div class="product_form__wrap">

			<div class="product_form__left">
						<?php if ( ! empty( $available_variations ) && 1==2 ) : ?>
							<table class="variations" cellspacing="0">
								<tbody>
									<?php

									$loop = 0; foreach ( $product->get_attributes() as $name => $options ) : $loop++;

									if ($name == 'for-who') :
									?>
										<tr>
											<td class="label"><label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
											<td class="value"><select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>" data-attribute_name="attribute_<?php echo sanitize_title( $name ); ?>">
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
														if ( taxonomy_exists( $name ) ) {

															$terms = wc_get_product_terms( $post->ID, $name, array( 'fields' => 'all' ) );

															foreach ( $terms as $term ) {
																if ( ! in_array( $term->slug, $options ) ) {
																	continue;
																}
																echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
															}

														} else {

															if ($options['value']) {

																	$values = explode ('|',$options['value']);

																	foreach ($values as $value) {
																		echo '<option value="' . esc_attr( sanitize_title( $value ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $value ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $value ) ) . '</option>';
																	}

															} else {
																echo '<option value="' . esc_attr( sanitize_title( $option['value'] ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
															}

														}
													}
												?>
											</select> <?php
												if ( sizeof( $attributes ) === $loop ) {
													echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'woocommerce' ) . '</a>';
												}
											?>
											<?php
											//if ( sanitize_title($name) == 'for-who' ) {
											?>

												<!--<div style="display:none;" class="who_content">You will need to allocate their name(s) in My2020Dashboard 'My Purchases' after you have successfully completed the checkout process.</div>
												<div style="display:none;" class="who_content_error">You need to select one of the options for who will be allocated to the product.</div>-->

											<?php //} ?>
										</td>
										</tr>
							        <?php endif; endforeach;?>
								</tbody>
							</table>
							<?php endif ; ?>

							<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
							<div style="display:none;" class="who_content_error">Select who this booking is for from the options above.</div>
							<div style="display:none;" class="who_content">You will need to allocate delegate name(s) in My2020Dashboard 'My Purchases' after you have successfully completed the checkout process.</div>
			</div>

			<div class="product_form__right">
				<div class="single_variation_wrap">
					<div class="single_variation"><?php echo fixed_pricing_2($product); ?></div>
					<?php do_action( 'woocommerce_bundles_add_to_cart_button' ); ?>
				</div>

				<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />

				<div class="currency-label"><?php the_field('currency_label',5); ?></div>
			</div>

		</div>


		</div>
			</div>
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>




		<?php

	}

?></form>







<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
