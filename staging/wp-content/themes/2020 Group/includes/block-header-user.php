<?php
global $woocommerce;

// get cart quantity
$cartQty = $woocommerce->cart->get_cart_contents_count();

// get cart total
$total = $woocommerce->cart->get_cart_total();

// get cart url
$cart_url = $woocommerce->cart->get_cart_url();
?>


<div id="menu-header-wrapper">
  <ul id="menu-header-menu" class="menu<?php if ( is_user_logged_in() ) { echo ' loggedin'; }; ?>">

      <?php
        if ( is_user_logged_in() ) {
          echo '
           <li class="logout menu-item">
             <a href="/logout/">Logout</a>
           </li>
           <li class="mydash menu-item">
             <a href="/dashboard/">my2020</a>
           </li>';
        } else {
          echo '<li class="mydash menu-item"><a href="/login/">Login</a></li>';
        }
      ?>

    <li class="basket menu-item<?php if($cartQty>0) { echo ' active'; } ?>">
      <a href="/basket/"<?php if($cartQty>0) { echo ' class="active"'; } ?>>Basket
        <?php // Output number of items in cart
          if($cartQty>1) {
            echo ': ' . $cartQty .' items';
          }
          else if($cartQty==1) {
            echo ': 1 item';
          }
        ?>
      </a>
    </li>
    <li class="help menu-item">
      <a href="/help/">Help</a>
    </li>
  </ul>
</div>
