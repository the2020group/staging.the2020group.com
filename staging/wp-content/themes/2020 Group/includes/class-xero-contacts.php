<?php
/**
 * Xero Class to connect to API and update contact details
 */

 // Includes and defines

include 'XeroOAuth-PHP/lib/XeroOAuth.php';

define ( 'BASE_PATH', dirname(__FILE__) );
define ( "XRO_APP_TYPE", "Private" );
define ( "OAUTH_CALLBACK", "oob" );


class Xero_Update_Contact_Details
{
	public $contacts_xml = '';
	public $useragent = "XeroOAuth-PHP Private App Test";

	public $signatures = array(
		'core_version' => '2.0',
		'payroll_version' => '1.0',
		'file_version' => '1.0',
	);
	private $XeroOAuth = array();

	function __construct (){

		$keys = array(
			'consumer_key' => get_option('wc_xero_consumer_key'),
			'shared_secret' => get_option('wc_xero_consumer_secret'),
			'rsa_private_key' => get_option('wc_xero_private_key'),
			'rsa_public_key' => get_option('wc_xero_public_key')
		);

		$this->signatures = array_merge($this->signatures, $keys);

		//connect to API
		$this->XeroOAuth = new XeroOAuth(array_merge(array(
			'application_type' => XRO_APP_TYPE,
			'oauth_callback' => OAUTH_CALLBACK,
			'user_agent' => $this->useragent
		), $this->signatures));

		$initialCheck = $this->XeroOAuth->diagnostics();

		$checkErrors = count ( $initialCheck );

		if ($checkErrors > 0) {
			foreach ( $initialCheck as $check ) {
			}
		} else {
			$session = $this->persistSession( array (
				'oauth_token' => $this->XeroOAuth->config ['consumer_key'],
				'oauth_token_secret' => $this->XeroOAuth->config ['shared_secret'],
				'oauth_session_handle' => ''
		  ));
			$oauthSession = $this->retrieveSession();

			if (isset ( $oauthSession ['oauth_token'] )) {
				$this->XeroOAuth->config ['access_token'] = $oauthSession ['oauth_token'];
				$this->XeroOAuth->config ['access_token_secret'] = $oauthSession ['oauth_token_secret'];
			}
		}
	}

	//get contact ID
	public function getContactID($email){
    $response = $this->XeroOAuth->request('GET', $this->XeroOAuth->url('Contacts', 'core'), array('Where' => 'EmailAddress=="$email"'));

    if ($this->XeroOAuth->response['code'] == 200) {
      $contact = $this->XeroOAuth->parseResponse($this->XeroOAuth->response['response'], $this->XeroOAuth->response['format']);
      return $contact->Id;
    } else {
      $this->outputError($this->XeroOAuth);
    }
	}

	//update contact details in batches
	public function updateContacts($contacts_xml){
		$response = $this->XeroOAuth->request('POST', $this->XeroOAuth->url('Contacts', 'core'), array(), $contacts_xml);

	    if ($this->XeroOAuth->response['code'] != 200) {
	      $this->outputError($XeroOAuth);
	    }
	}


	//functions
	 //Store the OAuth access token this should be a storage in DB not session
	function persistSession($response)
	{
		if (isset($response)) {
			$_SESSION['access_token']       = $response['oauth_token'];
			$_SESSION['oauth_token_secret'] = $response['oauth_token_secret'];
			if(isset($response['oauth_session_handle']))  $_SESSION['session_handle']     = $response['oauth_session_handle'];
		} else {
			return false;
		}
	}

	//get back access token
	function retrieveSession()
	{
		if (isset($_SESSION['access_token'])) {
			$response['oauth_token']            =    $_SESSION['access_token'];
			$response['oauth_token_secret']     =    $_SESSION['oauth_token_secret'];
			$response['oauth_session_handle']   =    $_SESSION['session_handle'];
			return $response;
		} else {
			return false;
		}

	}

	function outputError($XeroOAuth)
	{
		echo 'Error: ' . $this->XeroOAuth->response['response'] . PHP_EOL;
		$this->pr($this->XeroOAuth);
	}


	//Debug function for printing the content of an object

	function pr($obj)
	{

		if (!$this->is_cli())
			echo '<pre style="word-wrap: break-word">';
		if (is_object($obj))
			print_r($obj);
		elseif (is_array($obj))
			print_r($obj);
		else
			echo $obj;
		if (!$this->is_cli())
			echo '</pre>';
	}

	function is_cli()
	{
		return (PHP_SAPI == 'cli' && empty($_SERVER['REMOTE_ADDR']));
	}

}
