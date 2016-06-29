<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( is_product() ) : ?>

  <div class="row collapse">

<?php elseif (is_product_category() || is_page(842)) : ?>

  <div class="row" data-equalizer>

<?php else : ?>

  <ul class="products product-list small-block-grid-1 medium-block-grid-2 large-block-grid-3" data-equalizer>

<?php endif; ?>