<?php
/*
Plugin Name: Gravity Forms - SMS Notifications
Plugin URI: http://www.mediaburst.co.uk/
Description: Receive an SMS text notification on your mobile phone when someone submits a Gravity Form on your website.
Version: 0.0.1
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

require_once( 'classes/sms.php' );

new GravityFormsSMS();
?>