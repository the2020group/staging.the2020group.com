<?php
/*
Plugin Name: InfusedWoo Pro
Plugin URI: http://woo.infusedaddons.com
Description: Integrates WooCommerce with Infusionsoft. You need an Infusionsoft account to make this plugin work.
Version: 999
Author: Mark Joseph
Author URI: http://www.infusedaddons.com
HACK DETAILS:
	
	edited ordercomplete.php to make sure that the webinar, conference, workshops and focus groups don't get tags assigned automatically to the purchaser

*/
define('INFUSEDWOO_PRO_VER', '2.1');
define('INFUSEDWOO_PRO_DIR', WP_PLUGIN_DIR . "/" . plugin_basename( dirname(__FILE__) ) . '/');
define('INFUSEDWOO_PRO_URL', plugins_url() . "/" . plugin_basename( dirname(__FILE__) ) . '/');
define('INFUSEDWOO_PRO_BASE', plugin_basename( __FILE__));
define('INFUSEDWOO_PRO_UPDATER', 'http://downloads.infusedaddons.com/updater/iwpro.php');




$iwpro = 0;
$iw_cache = array();

// INCLUDE CORE FILES

include(INFUSEDWOO_PRO_DIR . 'core/integration.php');
include(INFUSEDWOO_PRO_DIR . 'core/autoupdate.php');
include(INFUSEDWOO_PRO_DIR . 'core/gateway.php');

// InfusedWoo 2.0 = New Admin Menu
include(INFUSEDWOO_PRO_DIR . 'admin-menu/admin.php');

// INCLUDE MODULES :: Note that modules below will only be loaded if Infusionsoft Integration is Enabled

add_action('iwpro_ready', 'iwpro_modules', 10, 1); 

function iwpro_modules($int) {
	global $iwpro;
	$iwpro = $int;
	
	if($iwpro->enabled) {
		// ADD MODULES BELOW
		include(INFUSEDWOO_PRO_DIR . 'modules/login.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/paneledits.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/orderprocess.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/ordercomplete.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/referral.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/registration.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/postfcn.php');

		if(version_compare( WOOCOMMERCE_VERSION, '2.1.0', '>=' )) {
			include(INFUSEDWOO_PRO_DIR . 'modules/subscriptions2.php');
		} else {
			include(INFUSEDWOO_PRO_DIR . 'modules/subscriptions.php');
		}
		
		include(INFUSEDWOO_PRO_DIR . 'modules/woo-subscriptions-actions.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/wooevents.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/checkoutfields.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/typagecontrol.php');
		include(INFUSEDWOO_PRO_DIR . 'modules/save_address.php');
	}
}

?>