=== WHMCS Multi-Site Provisioning ===
Contributors: Zingiri
Donate link: http://www.zingiri.net/
Tags: WHMCS, hosting, support, billing, integration, provisioning
Requires at least: 2.1.7
Tested up to: 3.2.1
Stable tag: 1.2.1

This plugin allows provisioning of blogs on a Wordpress multi-site installation from external packages and billing systems such as WHMCS.

== Description ==

This plugin allows provisioning of blogs on a Wordpress multi-site installation from external packages and billing systems. It adds an API to Wordpress that allows the external system to call Wordpress to create, suspend, unsuspend and terminate blogs on a Wordpress multi-site installation.

A commercial [WHMCS addon](http://www.clientcentral.info/cart.php?a=add&pid=22 "WHMCS addon") is currently available for a recurring fee. This allows you to set up a product in WHMCS which will automatically provision Wordpress blogs for your customers. It works just like a WHMCS server addon.

== Installation ==

1. Upload the `remote-provisioning` folder to the `/wp-content/plugins/` directory
2. Activate the plugin on the main blog of your multi-site Wordpress installation through the 'Plugins' menu in WordPress.

Please visit our [Zingiri](http://www.zingiri.net/products/remote-provisioning/ "setup instructions") for more information and support and instructions on how to install available 3rd party addons.

== Frequently Asked Questions ==

Please visit the [Zingiri](http://forums.zingiri.net/forumdisplay.php?fid=56 "Zingiri Support Forum") for more information and support.

== Upgrade Notice ==

Simply upgrade from the plugins page.

= WHMCS addoncompatibility =
* Plugin v1.1.x compatibile with WHMCS addon v1.1.x
* Plugin v1.0.x compatibile with WHMCS addon v1.0.x
* Plugin v0.9.x compatibile with WHMCS addon v0.9.x
 
== Coming soon ==
* Add mb size to API
* Add privacy option to API (to avoid search engines crawling the site before being built)

== Changelog ==

= 1.2.1 =
* Fixed issue where new blog user is left linked to main blog if that blog id is different from one
* Fixed issue with user role not being set correctly

= 1.2.0 =
* Added 'blog upload space' configuration option to enable setting the upload space to be allocated at account creation

= 1.1.1 =
* Edited description and installation instructions

= 1.1.0 =
* Added support for sub domains
* Updated Description and installation instructions
* Removed local license key display
* Pick up user names as created by Wordpress

= 1.0.3 =
* Fixed issue with packaging

= 1.0.2 =
* Added option to use email as WP user name

= 1.0.1 =
* Rebranding
 
= 1.0.0 =
* Added option to set default title
* Added option to let the user choose the blog title

= 0.9.2 =
* Updated installation instructions

= 0.9.1 =
* Added suspend/unsuspend option

= 0.9.0 =
* Alpha release

