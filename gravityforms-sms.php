<?php
/*
Plugin Name: Gravity Forms - SMS Notifications
Plugin URI: http://www.mediaburst.co.uk/
Description: Receive an SMS text notification on your mobile phone when someone submits a Gravity Form on your website.
Version: 1.0.1
Author: Mediaburst
Author URI: http://www.mediaburst.co.uk/
*/

// Try to avoid clashes with anything else using MB
if ( ! class_exists( 'WPmediaburstSMS' ) ) {
	require_once( 'classes/WPmediaburstSMS.class.php' );
}
if ( ! class_exists( 'WPWordPressMBHTTP' ) ) {
	require_once( 'classes/wordpress-mb-http.class.php' );
}

// Setup GravityForms classes
if ( ! class_exists( 'RGForms' ) ) {
  require_once( dirname( dirname( __FILE__ ) ) . '/gravityforms/gravityforms.php' );  
}

require_once( 'classes/sms.php' );

new GravityFormsSMS();
?>