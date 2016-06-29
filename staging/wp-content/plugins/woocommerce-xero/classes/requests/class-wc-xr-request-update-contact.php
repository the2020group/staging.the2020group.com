<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Update_Contact extends WC_XR_Request {

	public function __construct( $contact_id, $contact ) {
		$contact->set_id( $contact_id );

		$this->set_method( 'POST' );
		$this->set_endpoint( 'Contacts' );
		$this->set_body( $contact->to_xml() );
	}

}
