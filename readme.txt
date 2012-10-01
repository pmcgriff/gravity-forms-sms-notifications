=== Gravity Forms - Clockwork SMS ===
Author: Clockwork
Website: http://www.clockworksms.com/platforms/wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=contact-form-7-sms
Contributors: mediaburst, jamesinman
Tags: SMS, Clockwork, Clockwork SMS, Mediaburst, Contact Form 7, Text Message
Text Domain: wpcf7_sms
Requires at least: 3.0.0
Tested up to: 3.4.2
Stable tag: 2.1.0

Works with the Gravity Forms plugin to send SMS notifications when somebody 
submits your contact form, using the Clockwork API. 

== Description ==

Adds an SMS box to your Gravity Forms options pages, fill this in and you'll 
get a text message each time somebody fills out one of your forms.

You need a [Clockwork SMS account](http://www.clockworksms.com/platforms/wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=gravityforms) and some Clockwork credit to use this plugin.

= Requires =

* Wordpress 3 or higher

* [Gravity Forms](http://www.gravityforms.com/) 1.6.3 or higher

* A [Clockwork SMS account](http://www.clockworksms.com/platforms/wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=gravityforms)

== Installation ==

1. Upload the 'gravity-forms-sms-notifications' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter your Clockwork API key in the 'Clockwork Options' page under 'Clockwork SMS'
4. Set your SMS options for each Grabity Form

== Frequently Asked Questions ==

= How do I upgrade if I use an old version of this plugin with a Mediaburst username and password? =

If you already have forms set up using your Mediaburst username and password, just upgrade this
plugin from inside your Wordpress admin panel, or delete your existing 'gravity-forms-sms-notifications'
directory and follow the installation instructions above. Your API key will automatically be set
up for you.

= What is a Clockwork API key? = 

To send SMS you will need to sign up for a [Clockwork SMS account][1]
and purchase some SMS credit. When you sign up you'll be given an API key.

= Can I send to multiple mobile numbers? =

Yes, separate each mobile number with a comma.

= What format should the mobile number be in? =

All mobile numbers should be entered in international format without a 
leading + symbol or international dialing prefix.  

For example a UK number should be entered 447123456789, and a Republic 
of Ireland number would be entered 353870123456.

== Screenshots ==

1. SMS options for Gravity Forms.

== Changelog ==

= 2.1.0 = 
* Added the ability to set the 'From' sender of SMS messages.

= 2.0.3 = 
* Update to use latest version of Clockwork wrappers.

= 2.0.2 = 
* Fixed an issue where main Clockwork settings would not load if you didn't have any other Clockwork plugins installed.

= 2.0.1 =
* Fixed issue with setting Clockwork options when your Wordpress installation is not in the root directory.

= 2.0.0 =
* Compatible with Clockwork API.
* Now adds ability to set a custom message for each form, with included field values.

= 1.0.1 =
* Fixed an error where sometimes the plugin would fail on a missing class 'RGForms' if Gravity Forms was not loaded.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

Install this version for compatibility with the new Clockwork SMS API.