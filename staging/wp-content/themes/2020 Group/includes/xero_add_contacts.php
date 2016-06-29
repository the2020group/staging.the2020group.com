<?php
/**
 * Xero API add contacts 
 */
 
 // Includes and defines

include 'XeroOAuth-PHP/lib/XeroOAuth.php';

define ( 'BASE_PATH', dirname(__FILE__) );
define ( "XRO_APP_TYPE", "Private" );
define ( "OAUTH_CALLBACK", "oob" );
$useragent = "XeroOAuth-PHP Private App Test";
 
 
$signatures = array (
        'consumer_key' => 'IEQGZSSHGZG6UJTL7Z5WMK7OFYPMIH',
        'shared_secret' => 'XZCARCN7F1Z3WPHG8RPIBYOM54DXP6',
		// API versions
		'core_version' => '2.0',
		'payroll_version' => '1.0',
		'file_version' => '1.0' ,
		'rsa_private_key'=> 'xero-certs2/privatekey.pem' ,
		'rsa_public_key'=> 'xero-certs2/publickey.cer' 
);

echo '<a href="'. $signatures['rsa_private_key'] .'">'. $signatures['rsa_private_key'] .'</a>';

$XeroOAuth = new XeroOAuth ( array_merge ( array (
		'application_type' => XRO_APP_TYPE,
		'oauth_callback' => OAUTH_CALLBACK,
		'user_agent' => $useragent 
), $signatures ) );


$initialCheck = $XeroOAuth->diagnostics ();
$checkErrors = count ( $initialCheck );
if ($checkErrors > 0) {
	foreach ( $initialCheck as $check ) {
		echo 'Error: ' . $check . PHP_EOL;
	}
} else {
	$session = persistSession ( array (
			'oauth_token' => $XeroOAuth->config ['consumer_key'],
			'oauth_token_secret' => $XeroOAuth->config ['shared_secret'],
			'oauth_session_handle' => '' 
	) );
	$oauthSession = retrieveSession ();
	
	if (isset ( $oauthSession ['oauth_token'] )) {
		$XeroOAuth->config ['access_token'] = $oauthSession ['oauth_token'];
		$XeroOAuth->config ['access_token_secret'] = $oauthSession ['oauth_token_secret'];
		
	}
	
           $xml = "<Contacts>
                     <Contact>
					   <ContactID>51CBBFB0-8DC9-41AA-AAD6-EB93B3CC40C6</ContactID>
                       <Name>Capital Cab Co</Name>
                       <EmailAddress>emailaddress@yourdomain.com</EmailAddress>
                       <SkypeUserName>leanneskype10111</SkypeUserName>
                       <FirstName>Leanne</FirstName>
                       <LastName>O'Leary</LastName>
                     </Contact>
                   </Contacts>
                   ";
           $response = $XeroOAuth->request('POST', $XeroOAuth->url('Contacts', 'core'), array(), $xml);

           if ($XeroOAuth->response['code'] == 200) {  

				// post returns 200 ok but entry doesn't exist in account
				echo '<pre>';
				print_r($XeroOAuth->response);
				echo '</pre>';

           } else {
               outputError($XeroOAuth);
           }
		   
		   $XeroOAuth->diagnostics();
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
    echo 'Error: ' . $XeroOAuth->response['response'] . PHP_EOL;
    pr($XeroOAuth);
}


//Debug function for printing the content of an object

function pr($obj)
{

    if (!is_cli())
        echo '<pre style="word-wrap: break-word">';
    if (is_object($obj))
        print_r($obj);
    elseif (is_array($obj))
        print_r($obj);
    else
        echo $obj;
    if (!is_cli())
        echo '</pre>';
}

function is_cli()
{
    return (PHP_SAPI == 'cli' && empty($_SERVER['REMOTE_ADDR']));
}