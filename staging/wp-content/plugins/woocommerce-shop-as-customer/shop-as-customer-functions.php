<?php

/**
 * Sets an authorisation cookie containing the originating user, or appends it if there's more than one.
 *
 * @param int $old_user_id The ID of the originating user, usually the current logged in user.
 * @return null
 */
if ( !function_exists( 'wp_set_old_user_cookie' ) ) {
	function wp_set_old_user_cookie( $old_user_id ) {
		$expiration = time() + 172800; # 48 hours
		$cookie = wp_get_old_user_cookie();
		$cookie[] = wp_generate_auth_cookie( $old_user_id, $expiration, 'originating_user' );
		setcookie( ORIGINATION_COOKIE, json_encode( $cookie ), $expiration, COOKIEPATH, COOKIE_DOMAIN, false );
	}
}

/**
 * Clears the cookie containing the originating user, or pops the latest item off the end if there's more than one.
 *
 * @param bool $clear_all Whether to clear the cookie or just pop the last user information off the end.
 * @return null
 */
if ( !function_exists( 'wp_clear_old_user_cookie' ) ) {
	function wp_clear_old_user_cookie( $clear_all = true ) {
		$cookie = wp_get_old_user_cookie();
		if ( $clear_all or empty( $cookie ) ) {
			setcookie( ORIGINATION_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN );
		} else {
			array_pop( $cookie );
			$expiration = time() + 172800; # 48 hours
			setcookie( ORIGINATION_COOKIE, json_encode( $cookie ), $expiration, COOKIEPATH, COOKIE_DOMAIN, false );
		}
	}
}

/**
 * Gets the value of the cookie containing the list of originating users.
 *
 * @return array Array of originating user authentication cookies. @see wp_generate_auth_cookie()
 */
if ( !function_exists( 'wp_get_old_user_cookie' ) ) {
	function wp_get_old_user_cookie() {
		if ( isset( $_COOKIE[ORIGINATION_COOKIE] ) )
			$cookie = json_decode( stripslashes( $_COOKIE[ORIGINATION_COOKIE] ) );
		if ( !isset( $cookie ) or !is_array( $cookie ) )
			$cookie = array();
		return $cookie;
	}
}

/**
 * Switches the current logged in user to the specified user.
 *
 * @param int  $user_id      The ID of the user to switch to.
 * @param bool $remember     Whether to 'remember' the user in the form of a persistent browser cookie. Optional.
 * @param bool $set_old_user Whether to set the old user cookie. Optional.
 * @return bool True on success, false on failure.
 */
if ( !function_exists( 'shop_as_customer' ) ) {
	function shop_as_customer( $user_id, $remember = false, $set_old_user = true ) {
		
		if ( !$user = get_userdata( $user_id ) )
			return false;
		
		
		//don't switch if main user is shop_manager and attempt to swicth to administrator
		if ( ! WC_Shop_As_Customer::test_user_role( 'administrator' ) && $user->roles[0] == 'administrator' )
		 	return false;
		
		
		if ( $set_old_user and is_user_logged_in() ) {
			$cookie = wp_get_old_user_cookie();
			$old_user_already_set = false;
			if ( !empty( $cookie ) ) {
				if ( $old_user_id = wp_validate_auth_cookie( end( $cookie ), 'originating_user' ) )
					$old_user_already_set = get_userdata( $old_user_id );
			}
			if (!$old_user_already_set) {
				$old_user_id = get_current_user_id();
				wp_set_old_user_cookie( $old_user_id );
			}
			//wp_clear_previous_switched_cookie();
		} else {
			$old_user_id = get_current_user_id();
			wp_set_previous_switched_cookie( $old_user_id, $user_id );
			$old_user_id = false;
			wp_clear_old_user_cookie( false );
		}

		wp_clear_auth_cookie();
		wp_set_auth_cookie( $user_id, $remember );
		wp_set_current_user( $user_id );

		if ( $set_old_user )
			do_action( 'shop_as_customer', $user_id, $old_user_id );
		else
			do_action( 'switch_back_user', $user_id, $old_user_id );

		return true;
	}
}


/**
 * Gets the value of the cookie containing the list of switched users.
 *
 * @return array Array of originating user authentication cookies. @see wp_generate_auth_cookie()
 */
if ( !function_exists( 'wp_get_previous_switched_cookie' ) ) {
	function wp_get_previous_switched_cookie() {
		if ( isset( $_COOKIE[SWITCHED_COOKIE] ) )
			$cookie = json_decode( stripslashes( $_COOKIE[SWITCHED_COOKIE] ) );
		if ( !isset( $cookie ) or !is_array( $cookie ) )
			$cookie = array();
		return $cookie;
	}
}

/**
 * Sets an authorisation cookie containing the previous user switched, or appends it if there's more than one.
 *
 * @param int $old_user_id The ID of the originating user, usually the current logged in user.
 * @return null
 */
if ( !function_exists( 'wp_set_previous_switched_cookie' ) ) {
	function wp_set_previous_switched_cookie( $old_user_id, $original_user_id ) {
		$expiration = time() + 172800; # 48 hours
		$cookie = wp_get_previous_switched_cookie();
		if ( !empty( $cookie ) ) {
			
			$user_cookies = array();
			foreach ($cookie as $user_cookie) {
				$user_id = wp_validate_auth_cookie( $user_cookie, 'switched_user' );
				
				if ( ( $user_id != $old_user_id ) && ( $user_id != $original_user_id ) ) {
					$user_cookies[] = $user_cookie;
				}
			}
			if ( count($user_cookies) > 2) {
				$user_cookies = array_splice($user_cookies, -2);
			}
			$user_cookies[] = wp_generate_auth_cookie( $old_user_id, $expiration, 'switched_user' );
			
		} else {
			$user_cookies[] = wp_generate_auth_cookie( $old_user_id, $expiration, 'switched_user' );
		}
		setcookie( SWITCHED_COOKIE, json_encode( $user_cookies ), $expiration, COOKIEPATH, COOKIE_DOMAIN, false );
	}
}

/**
 * Clears the cookie containing the switched user, or pops the latest item off the end if there's more than one.
 *
 * @param bool $clear_all Whether to clear the cookie or just pop the last user information off the end.
 * @return null
 */
if ( !function_exists( 'wp_clear_previous_switched_cookie' ) ) {
	function wp_clear_previous_switched_cookie( ) {
		$cookie = wp_get_previous_switched_cookie();
		
		setcookie( SWITCHED_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN );
		
	}
}