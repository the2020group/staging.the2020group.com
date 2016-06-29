<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice extends WC_XR_Request {

	public function __construct( WC_XR_Invoice $invoice ) {

		// Set Endpoint
		$this->set_endpoint( 'Invoices' );

		// Set the XML
		$this->set_body( '<Invoices>' . $invoice->to_xml() .'</Invoices>' );

	}

}