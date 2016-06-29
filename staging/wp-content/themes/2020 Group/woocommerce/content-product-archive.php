<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop, $woocommerce;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;



// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';

if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last end';

/*
  echo '<div style="clear: both">';
    print_r($woocommerce_loop);
  echo '</div>';
*/

if (count($woocommerce->query->unfiltered_product_ids) == 4) {
  $gridcol = 'large-3';
} else {
  $gridcol = 'large-4';
}

$counter = 0;
$classes = '';
//	$classes[] = 'small-12 medium-3 column';
?>


<div class="small-12 medium-6 <?php echo $gridcol; ?> columns end">

  	<div class="archive-block block-0<?php echo implode(' ',$classes); ?>" data-equalizer-watch>
      <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
      <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      <div class="excerpt">
        <p><?php // echo $productExcerpt; ?></p>
      </div>

      	<?php
      		/**
      		 * woocommerce_before_shop_loop_item_title hook
      		 *
      		 * @hooked woocommerce_show_product_loop_sale_flash - 10
      		 * @hooked woocommerce_template_loop_product_thumbnail - 10
      		 */
      		do_action( 'woocommerce_before_shop_loop_item_title' );
      	?>

        <?php
          // Set the content vars of the returned matching products
          //$productLink = get_permalink( $product->ID );
          //$productTitle = $product->post_title;
          $today = date('Ymd');
          $eventDate = get_field('date', $product->ID);
          $eventStartTime = get_field('start_time', $product->ID);
          $eventEndTime = get_field('end_time', $product->ID);

          //$productExcerpt = $product->post_excerpt;
        ?>

        <?php if ($eventDate) : ?>
          <?php
          $date_part['year'] = substr($eventDate,0,4);
          $date_part['month'] = substr($eventDate,4,2);
          $date_part['day'] = substr($eventDate,6,2);
          ?>
          <p class="event-date"><span>Date:</span> <?php echo date('j<\s\u\p>S</\s\u\p> F Y', mktime(0, 0, 0, $date_part['month'], $date_part['day'], $date_part['year'])); ?></p>
        <?php endif; ?>

        <?php if($eventDate) : ?>
          <?php if($today <= $eventDate) : ?>
            <?php if ($eventStartTime) : ?>
              <p class="event-time"><?php echo date('G:i', strtotime($eventStartTime)); ?>
                <?php if ($eventEndTime) : ?>
                - <?php echo date('G:i', strtotime($eventEndTime)); ?>
                <?php endif; ?>
              </p>
            <?php endif; ?>
          <?php else : ?>
            <p class="event-date">Access Recording</p>
          <?php endif; ?>
        <?php endif; ?> 
      <?php
      if( $product->product_type == 'bundle' ) { 
          //add_bundle_to_cart ();          
      } else {
        if ($product->product_type != 'variable') {
          //do_action( 'woocommerce_after_shop_loop_item' );
        }
      } 
      ?>
        <a class="gen-btn orange icon right-arrow product_type_simple" href="<?php the_permalink(); ?>">More Details</a>
    </div>

</div>
