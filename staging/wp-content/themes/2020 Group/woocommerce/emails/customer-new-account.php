<?php
/**
 * Customer new account email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>


<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php

$l_user = get_user_by('login',$user_login); 

?>

<p><?php printf( __( 'Thank you for registering on the 2020 website.  You will find your login details below.') ); ?><p>

<p><?php printf( __( "Email Address: <strong>%s</strong>.", 'woocommerce' ), esc_html( $l_user->data->user_email ) ); ?><br />

<?php if ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated ) : ?>
	<?php printf( __( "Password: <strong>%s</strong>", 'woocommerce' ), esc_html( $user_pass ) ); ?>
<?php endif; ?>

</p>

<p><?php printf( __( 'To login to the site visit <a href="http://www.the2020group.com/login/">www.the2020group.com/login/</a>, enter your email address and your new password details in the appropriate fields. Once you have successfully logged in you can change your password in My2020Dashboard.') ); ?></p>
<p><?php printf( __( 'If you require further assistance please call the main 2020 office on +44(0) 121 314 2020 or email <a href="mailto:admin@the2020group.com">admin@the2020group.com</a>.') ); ?></p>
<p><?php printf( __( 'Best wishes<br />2020 Innovation') ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>