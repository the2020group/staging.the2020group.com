<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Contact extends WC_XR_Request {

	public function __construct( $email ) {
		$this->set_method( 'GET' );
		$this->set_endpoint( 'Contacts' );
		$this->set_query( array(
			'where' => 'EmailAddress=="' . $email . '"',
		) );
	}

}
