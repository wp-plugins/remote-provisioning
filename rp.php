<?php
/*
 Plugin Name: Remote Provisioning
 Plugin URI: http://www.choppedcode.com/Forum
 Description: This plugin allows provisioning of blogs on a Wordpress multi-site installation from external packages and billing systems such as WHMCS.

 Author: EBO
 Version: 0.9.1
 Author URI: http://www.choppedcode.com/
 */

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', '1');

define("CC_RP_VERSION","0.9.1");

// Pre-2.6 compatibility for wp-content folder location
if (!defined("WP_CONTENT_URL")) {
	define("WP_CONTENT_URL", get_option("siteurl") . "/wp-content");
}
if (!defined("WP_CONTENT_DIR")) {
	define("WP_CONTENT_DIR", ABSPATH . "wp-content");
}

if (!defined("CC_RP_PLUGIN")) {
	$cc_rp_plugin=str_replace(realpath(dirname(__FILE__).'/..'),"",dirname(__FILE__));
	$cc_rp_plugin=substr($cc_rp_plugin,1);
	define("CC_RP_PLUGIN", $cc_rp_plugin);
}

define("CC_RP_URL", WP_CONTENT_URL . "/plugins/".CC_RP_PLUGIN."/");

add_action('admin_notices','cc_rp_check');

register_activation_hook(__FILE__,'cc_rp_activate');
register_deactivation_hook(__FILE__,'cc_rp_deactivate');

function cc_rp_check() {
	global $wpdb;
	$errors=array();
	$warnings=array();
	$files=array();
	$dirs=array();

	foreach ($files as $file) {
		if (!is_writable($file)) $warnings[]='File '.$file.' is not writable, please chmod to 666';
	}

	foreach ($dirs as $file) {
		if (!is_writable($file)) $errors[]='Directory '.$file.' is not writable, please chmod to 777';
	}

	if (phpversion() < '5')	$warnings[]="You are running PHP version ".phpversion().". We recommend you upgrade to PHP 5.3 or higher.";
	if (ini_get("zend.ze1_compatibility_mode")) $warnings[]="You are running PHP in PHP 4 compatibility mode. We recommend you turn this option off.";
	if (!function_exists('curl_init')) $errors[]="You need to have cURL installed. Contact your hosting provider to do so.";

	if (count($errors)) {
		foreach ($errors as $message) {
			echo "<div id='zing-warning' style='background-color:greenyellow' class='updated fade'><p><strong>".$message."</strong> "."</p></div>";
		}
	}
	if (count($warnings)) {
		foreach ($warnings as $message) {
			echo "<div id='zing-warning' style='background-color:greenyellow' class='updated fade'><p><strong>".$message."</strong> "."</p></div>";
		}
	}
}


/**
 * Activation: creation of database tables & set up of pages
 * @return unknown_type
 */
function cc_rp_activate() {
	//nothing much to do
}

/**
 * Deactivation: nothing to do
 * @return void
 */
function cc_rp_deactivate() {
	//nothing much to do
}

/**
 * Initialization of page, action & page_id arrays
 * @return unknown_type
 */
function cc_rp_init()
{
	ob_start();
	session_start();
}

function cc_rp_add_admin() {
	add_options_page('Remote provisioning', 'Remote provisioning', 'administrator', 'cc-rp-cp','cc_rp_admin');
}

function cc_rp_admin() {

	global $wpdb,$current_user,$current_site,$base;

	$action=$_POST['action'];
	if ($action=='create') {
		$blog = $_POST['blog'];
		$domain = '';
		if ( ! preg_match( '/(--)/', $blog['domain'] ) && preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) )
		$domain = strtolower( $blog['domain'] );

		// If not a subdomain install, make sure the domain isn't a reserved word
		if ( ! is_subdomain_install() ) {
			$subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
			if ( in_array( $domain, $subdirectory_reserved_names ) )
			wp_die( sprintf( __('The following words are reserved for use by WordPress functions and cannot be used as blog names: <code>%s</code>' ), implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
		}
		$email = sanitize_email( $blog['email'] );
		$title = $blog['title'];

		if ( empty( $domain ) )
		wp_die( __( 'Missing or invalid site address.' ) );
		if ( empty( $email ) )
		wp_die( __( 'Missing email address.' ) );
		if ( !is_email( $email ) )
		wp_die( __( 'Invalid email address.' ) );

		$userName=$_POST['blog']['username'];
		$userName=$email;
		if ( is_subdomain_install() ) {
			$newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
			$path = $base;
		} else {
			$newdomain = $current_site->domain;
			$path = $base . $domain . '/';
		}

		$user_id = email_exists($email);
		if ( !$user_id ) { // Create a new user with a random password
			$password=$blog['password'];
			$user_id = wpmu_create_user( $userName, $blog['password'], $email );
			if ( false == $user_id )
			wp_die( __( 'There was an error creating the user.' ) );
			else
			wp_new_user_notification( $user_id, $password );
		} else {
			$password='[your current password]';
		}

		remove_user_from_blog( $user_id, '1' ); //removes new user from blog_id 1

		$wpdb->hide_errors();
		$blog_id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( 'public' => 1 ), $current_site->id );


		//global $current_user;
		//get_currentuserinfo();
		if ($blog['defaultrole']) {
			$roleName=$blog['defaultrole'];
			$roleSlug=str_replace(' ','_',$roleName);
			$roleSlug=strtolower($roleSlug);
			$roleSlug=preg_replace("/[^a-zA-Z0-9\s]/", "", $roleSlug);
			$roles=new WP_Roles();
			$roles->add_role($roleSlug,$roleName,array($roleSlug));

			remove_user_from_blog($user_id, $blog_id);
			add_user_to_blog($blog_id, $user_id, 'subscriber');
			$user=new WP_User($user_id);
			$user->add_role($roleSlug);
		}


		$wpdb->show_errors();
		if ( !is_wp_error( $blog_id ) ) {
			if ( !is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) )
			update_user_option( $user_id, 'primary_blog', $blog_id, true );
			//$content_mail = sprintf( __( "New site created by %1s\n\nAddress: http://%2s\nName: %3s"), $current_user->user_login , $newdomain . $path, stripslashes( $title ) );
			//wp_mail( get_site_option('admin_email'), sprintf( __( '[%s] New Site Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
			//wpmu_welcome_notification( $blog_id, $user_id, $password, $title, array( 'public' => 1 ) );
			//wp_redirect( add_query_arg( array('update' => 'added'), 'site-new.php' ) );
			//exit;
		} else {
			//wp_die( $blog_id->get_error_message() );
		}
		mkdir(WP_CONTENT_DIR.'/blogs.dir/'.$blog_id);
		mkdir(WP_CONTENT_DIR.'/blogs.dir/'.$blog_id.'/files');
		return;

	} elseif ($action=='suspend') {
		$domain=$_POST['blog']['domain'];
		echo 'suspend '.$domain;
		$id=get_id_from_blogname($domain);
		update_blog_status( $id, 'archived', '1' );
		return;

	} elseif ($action=='unsuspend') {
		$domain=$_POST['blog']['domain'];
		echo 'unsuspend '.$domain;
		$id=get_id_from_blogname($domain);
		update_blog_status( $id, 'archived', '0' );
		return;

	} elseif ($action=='terminate') {
		$domain=$_POST['blog']['domain'];
		echo 'terminate '.$domain;
		$id=get_id_from_blogname($domain);
		update_blog_status( $id, 'deleted', '1' );
		//wpmu_delete_blog($id,true); 
		return;
	}
	?>
<div class="wrap">
<h2><b>Remote provisioning</b></h2>
<p>The Remote Provisioning plugin allows provisioning of blogs on a Wordpress multi-site
installation from external packages and billing systems such as <a href="http://www.whmcs.com"
	target="_blank"
>WHMCS</a>.<br />
Basically this means you can charge for providing Wordpress blogs using your prefered billing
system. It supports creation, (un)suspension and cancellation of Wordpress blogs.<br />
You need to download the matching module for your external package.<br />
We have one available for WHMCS at the moment. Just follow this <a
	href="http://www.clientcentral.info/cart.php?a=add&pid=22"
>link</a>.<br />
<br />
That's it, no other settings.
<hr />
<a href="http://www.choppedcode.com" target="_blank" alt="Chopped Code" title="Chopped Code"><image
	src="<?php echo CC_RP_URL;?>choppedcode.png"
/></a></p>

	<?php
	$cc_ew=cc_rp_check();
	$cc_errors=$cc_ew['errors'];
	$cc_warnings=$cc_ew['warnings'];
	if ($cc_errors) {
		echo '<div style="background-color:pink" id="message" class="updated fade"><p>';
		echo '<strong>Errors - you need to resolve these errors before continuing:</strong><br /><br />';
		foreach ($cc_errors as $cc_error) echo $cc_error.'<br />';
		echo '</p></div>';
	}
	if ($cc_warnings) {
		echo '<div style="background-color:peachpuff" id="message" class="updated fade"><p>';
		echo '<strong>Warnings - you might want to have a look at these issues to avoid surprises or unexpected behaviour:</strong><br /><br />';
		foreach ($cc_warnings as $cc_warning) echo $cc_warning.'<br />';
		echo '</p></div>';
	}

	echo '</div>'; //end wrap
}
add_action('admin_menu', 'cc_rp_add_admin');
