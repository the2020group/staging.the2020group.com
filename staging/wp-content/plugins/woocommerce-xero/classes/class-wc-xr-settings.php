<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Settings {

	const OPTION_PREFIX = 'wc_xero_';

	// Settings defaults
	private $settings = array();

	public function __construct() {

		// Set the settings
		$this->settings = array(

			// API keys
			'consumer_key'       => array(
				'title'       => __( 'Consumer Key', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.',
			),
			'consumer_secret'    => array(
				'title'       => __( 'Consumer Secret', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.',
			),
			// SSH key files
			'private_key'        => array(
				'title'       => __( 'Private Key', 'wc-xero' ),
				'default'     => '',
				'type'        => 'file',
				'description' => 'Path to the private key file created to authenticate this site with Xero.',
			),
			'public_key'         => array(
				'title'       => __( 'Public Key', 'wc-xero' ),
				'default'     => '',
				'type'        => 'file',
				'description' => 'Path to the public key file created to authenticate this site with Xero.',
			),
			// Invoice Prefix
			'invoice_prefix'     => array(
				'title'       => __( 'Invoice Prefix', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'Allow you to prefix all your invoices.',
			),
			// Accounts
			'sales_account'      => array(
				'title'       => __( 'Sales Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'Code for Xero account to track sales.',
			),
			'discount_account'   => array(
				'title'       => __( 'Discount Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'Code for Xero account to track customer discounts.',
			),
			'shipping_account'   => array(
				'title'       => __( 'Shipping Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'Code for Xero account to track shipping charges.',
			),
			'payment_account'    => array(
				'title'       => __( 'Payment Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'Code for Xero account to track payments received.',
			),
			'rounding_account'   => array(
				'title'       => __( 'Rounding Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => 'Code for Xero account to allow an adjustment entry for rounding',
			),
			// Misc settings
			'export_zero_amount' => array(
				'title'       => __( 'Orders with zero total', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => 'Export orders with zero total.',
			),
			'send_invoices'      => array(
				'title'       => __( 'Auto Send Invoices', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => 'Send Invoices to Xero automatically when order is generated as completed, processing, pending payment, or on hold.',
			),
			'send_payments'      => array(
				'title'       => __( 'Auto Send Payments', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => 'Send Payments to Xero automatically when order is set to completed. This may need to be turned off if you sync via a separate integration such as PayPal.',
			),
			'send_inventory'     => array(
				'title'       => __( 'Send Inventory Items', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => 'Send Item Code field with invoices. If this is enabled then each product must have a SKU defined and be setup as an <a href="https://help.xero.com/us/#Settings_PriceList" target="_blank">inventory item</a> in Xero.',
			),
			'debug'              => array(
				'title'       => __( 'Debug', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => 'Enable logging.  Log file is located at: /wc-logs/',
			),
		);
	}

	/**
	 * Setup the required settings hooks
	 */
	public function setup_hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
	}

	/**
	 * Get an option
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get_option( $key ) {
		return get_option( self::OPTION_PREFIX . $key, $this->settings[ $key ]['default'] );
	}

	/**
	 * settings_init()
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {

		// Add section
		add_settings_section( 'wc_xero_settings', __( 'Xero Settings', 'wc-xero' ), array(
			$this,
			'settings_intro'
		), 'woocommerce_xero' );

		// Add setting fields
		foreach ( $this->settings as $key => $option ) {

			// Add setting fields
			add_settings_field( self::OPTION_PREFIX . $key, $option['title'], array(
				$this,
				'input_' . $option['type']
			), 'woocommerce_xero', 'wc_xero_settings', array( 'key' => $key, 'option' => $option ) );

			// Register setting
			register_setting( 'woocommerce_xero', self::OPTION_PREFIX . $key );

		}

	}

	/**
	 * Add menu item
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$sub_menu_page = add_submenu_page( 'woocommerce', __( 'Xero', 'wc-xero' ), __( 'Xero', 'wc-xero' ), 'manage_woocommerce', 'woocommerce_xero', array(
			$this,
			'options_page'
		) );

		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_style' ) );
	}

	public function enqueue_style() {
		global $woocommerce;
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
	}

	/**
	 * The options page
	 */
	public function options_page() {
		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" action="options.php">
				<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
				<h2><?php _e( 'Xero for WooCommerce', 'wc-xero' ); ?></h2>

				<?php
				if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] == 'true' ) ) {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'wc-xero' ) . '</strong></p></div>';

				} else if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] == 'false' ) ) {
					echo '<div id="message" class="error fade"><p><strong>' . __( 'There was an error saving your settings.', 'wc-xero' ) . '</strong></p></div>';
				}
				?>

				<?php settings_fields( 'woocommerce_xero' ); ?>
				<?php do_settings_sections( 'woocommerce_xero' ); ?>
				<p class="submit"><input type="submit" class="button-primary" value="Save"/></p>
			</form>
		</div>
	<?php
	}

	/**
	 * Settings intro
	 */
	public function settings_intro() {
		echo '<p>' . __( 'Settings for your Xero account including security keys and default account numbers.<br/> <strong>All</strong> text fields are required for the integration to work properly.', 'wc-xero' ) . '</p>';
	}

	/**
	 * File setting field
	 *
	 * @param $args
	 */
	public function input_file( $args ) {

		// Default text field
		$this->input_text( $args );

		if ( is_file( $this->get_option( $args['key'] ) ) ) {
			echo '<p style="margin-top:15px;"><span style="padding: .5em; background-color: #4AB915; color: #fff; font-weight: bold;">' . __( 'Key file found.', 'wc-xero' ) . '</span></p>';
		} else {
			echo '<p style="margin-top:15px;"><span style="padding: .5em; background-color: #bc0b0b; color: #fff; font-weight: bold;">' . __( 'Key file not found.', 'wc-xero' ) . '</span></p>';
			$working_dir = str_replace( 'wp-admin', '', getcwd() );
			echo '<p>' . __( '  This setting should include the absolute path to the file which might include working directory: ', 'wc-xero' ) . '<span class="code" style="background: #efefef;">' . $working_dir . '</span></p>';
		}
	}

	/**
	 * Text setting field
	 *
	 * @param array $args
	 */
	public function input_text( $args ) {
		echo '<input type="text" name="' . self::OPTION_PREFIX . $args['key'] . '" id="' . self::OPTION_PREFIX . $args['key'] . '" value="' . $this->get_option( $args['key'] ) . '" />';
		echo '<p class="description">' . $args['option']['description'] . '</p>';
	}

	/**
	 * Checkbox setting field
	 *
	 * @param array $args
	 */
	public function input_checkbox( $args ) {
		echo '<input type="checkbox" name="' . self::OPTION_PREFIX . $args['key'] . '" id="' . self::OPTION_PREFIX . $args['key'] . '" ' . checked( 'on', $this->get_option( $args['key'] ), false ) . ' /> ';
		echo '<p class="description">' . $args['option']['description'] . '</p>';
	}

}
