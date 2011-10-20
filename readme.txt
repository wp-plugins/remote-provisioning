=== WHMCS Multi-Site Provisioning ===
Contributors: Zingiri
Donate link: http://www.zingiri.net/
Tags: WHMCS, hosting, support, billing, integration, provisioning
Requires at least: 2.1.7
Tested up to: 3.2.1
Stable tag: 1.1.0

This plugin allows provisioning of blogs on a Wordpress multi-site installation from external packages and billing systems such as WHMCS.

== Description ==

This plugin allows provisioning of blogs on a Wordpress multi-site installation from external packages and billing systems such as WHMCS. 

It consists of a Wordpress plugin and a WHMCS addon. You can order the WHMCS addon [here](http://www.clientcentral.info/cart.php?a=add&pid=22 "here").

== Installation ==

1. Upload the `remote-provisioning` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Order a [license](http://www.clientcentral.info/cart.php?a=add&pid=22 "license").
4. Enter the license key in the Wordpress plugin options panel.
5. Download and install the WHMCS Wordpress provisioning module from the [portal](http://www.clientcentral.info "portal").

Please visit our [Zingiri](http://www.zingiri.net/products/remote-provisioning/ "setup instructions") for more information and support.

== Frequently Asked Questions ==

Please visit the [Zingiri](http://forums.zingiri.net/forumdisplay.php?fid=56 "Zingiri Support Forum") for more information and support.

== Upgrade Notice ==

Simply go to the Wordpress Settings page for the plugins and click the Upgrade button.

== Coming soon ==
* Support for sub domains

== Changelog ==

= 1.1.0 =
* Added support for sub domains
* Improved usage of WHMCS domain and user fields
* Updated Description and installation instructions
* Removed local license key display

= 1.0.3 =
* Fixed issue with packaging

= 1.0.2 =
* Added option to use WHMCS client email as WP user name

= 1.0.1 =
* Moved text for client area control panel button to language file
* Language file is no longer encoded and can be customised
* Copy WHMCS username to WP username when creating
* Rebranding
 
= 1.0.0 =
* Added option to set default title in WHMCS addon set up
* Added option to let the user choose the blog title
* Added option to let the user choose the blog (sub)directory name
* Fixed 'go to control panel' button link on user WHMCS account

= 0.9.2 =
* Updated installation instructions

= 0.9.1 =
* Added suspend/unsuspend option

= 0.9.0 =
* Alpha release

== FAQ ==
= To do =
* Check duplicate user names
* Add support for WPMU installations with sub domains
* Defined mb size (make it configurable in WHMCS)
* Option for privacy (checkbox - so search engines don’t crawl site before built)