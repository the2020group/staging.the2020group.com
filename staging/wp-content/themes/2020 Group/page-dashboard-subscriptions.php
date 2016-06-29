<?php

/*
 * Template Name: Dashboard - My Subscriptions
 */

get_header(); ?>

    <div class="dash-wrap">

    <div class="row collapse">

        <div class="small-1 medium-1 columns">
            <?php get_sidebar('dashboard'); ?>
        </div>


        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div class="small-11 medium-11 columns" role="main">

	        <div id="dash-main">
            <style>
            .error {
                border-color: #ff0000;
            }
            </style>
            <h2><?php the_title(); ?></h2>


				<div class="woocommerce">
          <?php
            $subscriptions = wcs_get_users_subscriptions();
            $user_id       = get_current_user_id();
          ?>

          <?php if ( ! empty( $subscriptions ) ) : ?>
          <table class="shop_table my_account_subscriptions my_account_orders">

          <thead>
            <tr>
              <th class="subscription-id"><span class="nobr"><?php esc_html_e( 'Order', 'woocommerce-subscriptions' ); ?></span></th>
              <th class="subscription-title"><span class="nobr"><?php esc_html_e( 'Subscription', 'woocommerce-subscriptions' ); ?></span></th>
              <th class="subscription-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></span></th>
              <th class="subscription-next-payment"><span class="nobr"><?php esc_html_e( 'Next Payment', 'woocommerce-subscriptions' ); ?></span></th>
              <th class="subscription-end-date"><span class="nobr"><?php esc_html_e( 'End Date', 'woocommerce-subscriptions' ); ?></span></th>
              <th class="subscription-total"><span class="nobr"><?php esc_html_e( 'Total', 'woocommerce-subscriptions' ); ?></span></th>
              <th class="subscription-actions"><span class="nobr"><?php esc_html_e( 'Actions', 'woocommerce-subscriptions' ); ?></span></th>
            </tr>
          </thead>

          <tbody>
          <?php
            $display_renewal_button = false;
            foreach ( $subscriptions as $subscription_id => $subscription ) : ?>
            <tr class="order">
              <td class="subscription-id order-number" data-title="<?php esc_attr_e( 'ID', 'woocommerce-subscriptions' ); ?>">
                <a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . esc_html( $subscription->get_order_number() ) . '&my-account'), 'generate_wpo_wcpdf' ); ?>">
                  <?php echo esc_html( $subscription->get_order_number() ); ?>
                </a>
                <?php do_action( 'woocommerce_my_subscriptions_after_subscription_id', $subscription ); ?>
              </td>
              <td class="subscription-title order-title">
                <?php
                if ( sizeof( $subscription->get_items() ) > 0 ) {

                  foreach ( $subscription->get_items() as $item_id => $item ) {
                    $_product  = apply_filters( 'woocommerce_subscriptions_order_item_product', $subscription->get_product_from_item( $item ), $item );
            				$item_meta = wcs_get_order_item_meta( $item, $_product );

                    if ( $_product && ! $_product->is_visible() ) {
                      echo esc_html( apply_filters( 'woocommerce_order_item_name', $item['name'], $item ) );
                    } else {
                      echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item ) );
                    }

                    // Allow other plugins to add additional product information here
                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $subscription );

                    if(!empty($item_meta->display(true, true))) {
                      echo '<p>';
                      $item_meta->display(true, false);
                      echo '</p>';
                    }

                    // Allow other plugins to add additional product information here
                    do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $subscription );

                  }

            		}
                ?>
              </td>
              <td class="subscription-status order-status" style="text-align:left; white-space:nowrap;" data-title="<?php esc_attr_e( 'Status', 'woocommerce-subscriptions' ); ?>">
                <?php $status = esc_attr( wcs_get_subscription_status_name( $subscription->get_status() ) ); echo $status;
                 ?>
              </td>
              <td class="subscription-next-payment order-date" data-title="<?php esc_attr_e( 'Next Payment', 'woocommerce-subscriptions' ); ?>">
                <?php echo esc_attr( $subscription->get_date_to_display( 'next_payment' ) ); ?>
                <?php if ( ! $subscription->is_manual() && $subscription->has_status( 'active' ) && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
                  <?php $payment_method_to_display = sprintf( __( 'Via %s', 'woocommerce-subscriptions' ), $subscription->get_payment_method_to_display() ); ?>
                  <?php $payment_method_to_display = apply_filters( 'woocommerce_my_subscriptions_payment_method', $payment_method_to_display, $subscription ); ?>
                &nbsp;<small><?php echo esc_attr( $payment_method_to_display ); ?></small>
                <?php endif; ?>
              </td>
              <td class="subscription-next-payment order-date" data-title="<?php esc_attr_e( 'End Date', 'woocommerce-subscriptions' ); ?>">
                <?php echo esc_attr( $subscription->get_date_to_display( 'end' ) ); ?>
                <?php if ( ! $subscription->is_manual() && $subscription->has_status( 'active' ) && $subscription->get_time( 'end' ) > 0 ) : ?>
          				<td><?php echo esc_html( $subscription->get_date_to_display( $date_type ) ); ?></td>
                <?php endif; ?>
              </td>
              <td class="subscription-total order-total">
                <?php $total = wp_kses_post( $subscription->get_formatted_order_total() ); echo $total; ?>
              </td>
              <td class="subscription-actions order-actions">
                <?php $actions = wcs_get_all_user_actions_for_subscription( $subscription, get_current_user_id() ); ?>
                <?php if ( ! empty( $actions ) ) : ?>
                  <?php foreach ( $actions as $key => $action ) :
                    if(sanitize_html_class($key) == 'change_address') continue; ?>
                    <a href="<?php echo esc_url( $action['url'] ); ?>" class="button <?php echo sanitize_html_class( $key ) ?>"><?php echo esc_html( $action['name'] ); ?></a>
                  <?php endforeach;
                    else :
                      if($display_renewal_button == false && $status == 'On hold' && $total == '<span class="amount">£0.00</span>') { ?>
                        <a href="/?ord=<?php echo $subscription->get_order_number(); ?>" class="button <?php echo sanitize_html_class( $key ) ?>">Renew subscription</a>
                      <?php $display_renewal_button = true;
                      }
                  ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php
            $display_renewal_button = true;
            endforeach; ?>
          </tbody>

          </table>
          <?php else : ?>

            <p class="no_subscriptions"><?php printf( esc_html__( 'You have no active subscriptions.', 'woocommerce-subscriptions' ) ); ?></p>

          <?php endif; ?>
        </div>

        <?php endwhile; ?>
        <?php endif; ?>

				</div>
    </div>

    </div>

<?php get_footer();
