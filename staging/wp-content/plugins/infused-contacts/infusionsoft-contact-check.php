<?php

class Infusionsoft_Contact_Check {


	public function get_is_user ($user_id) {

		global $infusionsoft;

		$is_user = array();

		$contact_id = get_user_meta( $user_id, 'is_contact_id', true );

		$data = array('FirstName','LastName','Phone1','Email','PostalCode','StreetAddress1','StreetAddress2', 'City','State','Company','Country');

    if ($infusionsoft !== null) {
		    $is_user = $infusionsoft->contact( 'load', (int) $contact_id , $data);

        return $is_user;
    } else {
      return false;
    }
	}


	public function compare_users ($is_user,$user_id=0) {

		global $infusionsoft;

		$match = true ;

		if (is_user_logged_in()&&$user_id==0) {
			$user = wp_get_current_user();
			$user_id = $user->ID;
		}

		$current_user = array(
			'FirstName' => get_user_meta( $user_id, 'first_name', true ),
			'LastName' 	=> get_user_meta( $user_id, 'last_name', true ),
			'Phone1' 	=> get_user_meta( $user_id, 'billing_phone', true ),
			'Email' 	=> get_user_meta( $user_id, 'billing_email', true ),
			'PostalCode' => get_user_meta( $user_id, 'billing_postcode', true ),
      'StreetAddress1' 		=> get_user_meta( $user_id, 'billing_address_1', true ),
			'StreetAddress2' 		=> get_user_meta( $user_id, 'billing_address_2', true ),
			'City' 		=> get_user_meta( $user_id, 'billing_city', true ),
			'State' 	=> get_user_meta( $user_id, 'billing_state', true ),
			'Company' 	=> get_user_meta( $user_id, 'billing_company', true ),
			'Country' 	=> get_user_meta( $user_id, 'billing_country', true )
		);

		if (is_array($is_user)) {

			// check the last updated date
			// $is_user['LastUpdated'];

			foreach ($is_user as $label => $item) {

				if ($is_user[$label] != $current_user[$label]) {

					$match = false;

				}

			}

		}

		return $match;
	}

	public function update_is_contact($user_id=0) {

		global $infusionsoft;

		if (is_user_logged_in()&&$user_id==0) {
			$user = wp_get_current_user();
			$user_id = $user->ID;
		}

		$current_user = array(
			'FirstName' => get_user_meta( $user_id, 'first_name', true ),
			'LastName' 	=> get_user_meta( $user_id, 'last_name', true ),
			'Phone1' 	=> get_user_meta( $user_id, 'billing_phone', true ),
			'Email' 	=> get_user_meta( $user_id, 'billing_email', true ),
			'PostalCode' => get_user_meta( $user_id, 'billing_postcode', true ),
			'StreetAddress1' 		=> get_user_meta( $user_id, 'billing_address_1', true ),
			'StreetAddress2' 		=> get_user_meta( $user_id, 'billing_address_2', true ),
			'City' 		=> get_user_meta( $user_id, 'billing_city', true ),
			'State' 	=> get_user_meta( $user_id, 'billing_state', true ),
			'Company' 	=> get_user_meta( $user_id, 'billing_company', true ),
			'Country' 	=> get_user_meta( $user_id, 'billing_country', true )
		);


		$contact_id = get_user_meta( $user_id, 'is_contact_id', true );

		$updated_contact_id = $infusionsoft->contact( 'update', (int) $contact_id, $current_user );

		return $updated_contact_id;

	}

	public function update_wp_contact($new_contact,$user_id=0) {

		if (is_array($new_contact)&&$user_id>0) {

      update_user_meta( $user_id, 'first_name',$new_contact['FirstName']);
      update_user_meta( $user_id, 'last_name', $new_contact['LastName'] );
      update_user_meta( $user_id, 'billing_phone', $new_contact['Phone1'] );
      update_user_meta( $user_id, 'billing_email', $new_contact['Email'] );
      update_user_meta( $user_id, 'billing_postcode', $new_contact['PostalCode'] );
      update_user_meta( $user_id, 'billing_address_1', $new_contact['StreetAddress1'] );
      update_user_meta( $user_id, 'billing_address_2', $new_contact['StreetAddress2'] );
      update_user_meta( $user_id, 'billing_city', $new_contact['City'] );
      update_user_meta( $user_id, 'billing_state', $new_contact['State'] );
      update_user_meta( $user_id, 'billing_company', $new_contact['Company'] );
      update_user_meta( $user_id, 'billing_country', $new_contact['Country'] );

		}

		return $updated_user_id = $user_id;

	}



	public function for_cron ($pass=1,$block=0) {

		$settings = (array) get_option( 'infusionsoft_settings' );
		$per_call = $settings['batch_size'];

		$block_start = $block * $per_call + 1 ;
		$block_end  = $block_start + $per_call ;

		$update_all_cron_pass = 1 ;

		$count = $block_start;
		if ($pass == $update_all_cron_pass) {

			$active_users = get_users( array( 'fields' => array( 'id' ) ) );

			foreach ($active_users as $user) {

				if ($count < $block_end) {

					$contact_id = get_user_meta( $user->id, 'is_contact_id', true );

					$is_user = $this->get_is_user($user->id);

					if (!$this->compare_users($is_user,$user->ID)) {
						//$updated_contact=$this->update_wp_contact($is_user,$user->ID);
					}
				}
				$count++;
			}
		}
	}


	public function check_on_login($user_login,$user) {

		$user_id = $user->ID;

		$match = true;

		// get array from infusionsoft
		$is_user = $this->get_is_user($user_id);


		// how does it compare to
		$match = $this->compare_users($is_user,$user_id);


		if (!$match) {
			$updated_contact = $this->update_wp_contact($is_user,$user_id);
		}

	}

}

$infusionsoft_contact_check = new Infusionsoft_Contact_Check;

add_action('wp_login', array( $infusionsoft_contact_check , 'check_on_login'),10,2 );
//add_action( 'user_register', array( $infusionsoft_contact_check, 'user_register' ) );
