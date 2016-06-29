<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Tax_Rate extends WC_XR_Request {

	public function __construct( $rate, $name ) {
		$this->set_method( 'GET' );
		$this->set_endpoint( 'TaxRates' );
		$this->set_query( array(
			'where' => 'EffectiveRate==' . $rate . '&&Name==' . $name . '&&TaxType.StartsWith("TAX")'
		) );
	}

}
