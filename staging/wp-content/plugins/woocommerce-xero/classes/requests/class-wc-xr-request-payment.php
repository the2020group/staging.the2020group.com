<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Payment extends WC_XR_Request {

	public function __construct( WC_XR_Payment $payment ) {

		// Set Endpoint
		$this->set_endpoint( 'Payments' );

		// Set the XML
		$this->set_body( '<Payments>' . $payment->to_xml() .'</Payments>' );

	}

}