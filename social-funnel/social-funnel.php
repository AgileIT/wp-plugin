<?php
/**
* Plugin Name: Social Funnels
* Plugin URI: http://www.social-funnels.com
* Description: Social Funnels will allow you to build an email list quickly and easily using the power of social traffic.
* Author: Revolution Labs
* Author URI: http://www.revolutionlabs.io
* Version: 2.0
*/
@ob_start();
define( 'SF_URL', plugin_dir_url( __FILE__ )  );
define( 'SF_PATH', plugin_dir_path( __FILE__ ) );
define( 'SF_VERSION', '0.1.0');
define( 'SF_AJAX', false);
// create database tables on plugin activation
//define('WP_DEBUG_DISPLAY', false);

$videos['dash'] = "//player.vimeo.com/video/134090795";
$videos['import'] = "//player.vimeo.com/video/134090869";
$videos['step1'] = "//player.vimeo.com/video/134090798";
$videos['step2'] = "//player.vimeo.com/video/134090794";
$videos['step3'] = "//player.vimeo.com/video/134090799";
$videos['step4'] = "//player.vimeo.com/video/134090868";
$videos['bookmarklet'] = "//player.vimeo.com/video/134090797";

$sfk = "YTo1OntzOjE2OiIxIGRvbWFpbiBsaWNlbnNlIjtzOjI5OiJMMzgxLVNIMjgtMzhETi00OE5TLTgzTkEtNDhTTiI7czoxNjoiNSBkb21haW4gbGljZW5zZSI7czoyOToiSDIzOC0zVDJOLTMxOTgtRE5TRi1PSVNHLTIzT0kiO3M6MTc6IjEwIGRvbWFpbiBsaWNlbnNlIjtzOjI5OiIxOTBOLTIzOUQtT1FXOS05NFRYLTQyOVQtRFNPTiI7czoxNzoidW5saW1pdGVkIGxpY2Vuc2UiO3M6Mjk6IjE4Rk4tRk4yMS01MzJOLVNES0gtRk5PMy1ET1NHIjtzOjEwOiJKViBsaWNlbnNlIjtzOjI5OiIzODFCLTI4M04tRk5RUC05ODJLLUdSMFItOVNEMCI7fQ==";

function socialfunnel_init() {

	// @ini_set( 'upload_max_size' , '64M' );
	// @ini_set( 'post_max_size', '64M');
	// @ini_set( 'max_execution_time', '300' );
	
	add_filter( 'social_funnel_development_mode', '__return_true' );
	add_action( 'admin_enqueue_scripts', 'sf_backend_enqueue_scripts' );
	//add_action( 'wp_enqueue_scripts', 'cf_frontend_enqueue_scripts' );
	add_action('admin_menu','sf_menu');
	//ini_rules();
	/*EMAIL HTML FORMAT*/
	add_filter( 'wp_mail_content_type','sf_set_content_type' );
	$args = array(
		'public' => true,
		'query_var' => 'socialfunnel',
		'capability_type' => 'page',
		'rewrite' => array( 'slug' => 'socialfunnel', 'with_front' => true )
	);
	register_post_type( 'socialfunnel',$args);
	if(!is_admin()){
		add_filter('wp_redirect', 'sf_disable_redirect');
	}
}	

if ( function_exists('w3tc_pgcache_flush') ) {
  w3tc_pgcache_flush();
} else if ( function_exists('wp_cache_clear_cache') ) {
  wp_cache_clear_cache();
}

function sf_theme_setup(){
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'popup-image',200,230,true);
	add_image_size( 'feature-image',280,210,true);
}

function sf_custom_image_sizes_choose( $sizes ) {
    $custom_sizes = array(
        'popup-image' => 'Popup Image',
        'feature-image' => 'Feature Image'
    );
    return array_merge( $sizes, $custom_sizes );
}

/*function custom_remove_cpt_slug( $post_link, $post, $leavename ) {

    if ( 'socialfunnel' != $post->post_type || 'publish' != $post->post_status ) {
        return $post_link;
    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
}

function custom_parse_request_tricksy( $query ) {

    // Only noop the main query
    if ( ! $query->is_main_query() )
        return;

    // Only noop our very specific rewrite rule match
    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'socialfunnel', 'page' ) );
    }
}*/
/*
if (  is_admin() ) {
add_action( 'admin_init','wpse_60168_custom_menu_class' );
}
function wpse_60168_custom_menu_class() 
{
    global $menu;

    foreach( $menu as $key => $value )
    {
        if( 'Social Funnels' == $value[0] )
            $menu[$key][4] .= " social_icon_image";

       
    }
}*/

require_once('wp-updates-plugin.php');
new WPUpdatesPluginUpdater_1116( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

require_once('views/frontend.php');

register_activation_hook( __FILE__, 'sf_activate' );

/* Uninstall Database */

register_uninstall_hook('uninstall.php', '');

/* Uninstall Database End */

add_filter( 'image_size_names_choose', 'sf_custom_image_sizes_choose' );
add_action( 'after_setup_theme', 'sf_theme_setup' );
add_action( 'init', 'socialfunnel_init' );
add_action('wp_ajax_delete_campaign','delete_campaign');
add_action('wp_ajax_nopriv_delete_campaign','delete_campaign');
add_action('wp_ajax_copy_campaign','copy_campaign');
add_action('wp_ajax_nopriv_copy_campaign','copy_campaign');
add_action('wp_ajax_sf_subscribe','sf_subscribe');
add_action('wp_ajax_nopriv_sf_subscribe','sf_subscribe');
add_action('wp_ajax_export_campaign','export_campaign');
add_action('wp_ajax_nopriv_export_campaign','export_campaign');



//add_filter( 'post_type_link', 'custom_remove_cpt_slug', 10, 3 );
//add_action( 'pre_get_posts', 'custom_parse_request_tricksy' );


function sf_backend_enqueue_scripts() {
  	// Load All Css Files
  	if(isset($_GET['page']) == 'social-funnel'){
	    wp_enqueue_style( 'bootstrap-min', SF_URL . 'assets/css/bootstrap.min.css', array(), SF_VERSION );
	    wp_enqueue_style('thickbox');	
	    wp_enqueue_script('thickbox');
	    wp_enqueue_script('media-upload');
	    //wp_enqueue_script('sf-thickbox',includes_url('js/thickbox/thickbox.js'));
	   // wp_enqueue_script('sf-media-upload',admin_url('js/media-upload.min.js'));
	    //wp_enqueue_style( 'bootstrap', SF_URL . 'assets/css/bootstrap.css', array(), SF_VERSION );
		
	    wp_enqueue_style( 'dashboard-custom', SF_URL . 'assets/css/dashboard_custom.css', array(), SF_VERSION );
		wp_enqueue_style( 'dashboar-responsive', SF_URL . 'assets/css/dash_responsive.css', array(), SF_VERSION );
		
		wp_enqueue_style( 'font-awesome', SF_URL . 'assets/css/font-awesome.min.css', array(), SF_VERSION );
		wp_enqueue_style( 'jasny', SF_URL . 'assets/css/jasny-bootstrap.css', array(), SF_VERSION );
	 	// Load all script files
		//wp_enqueue_script( 'sf-bootstrap', SF_URL . 'assets/js/bootstrap.js', array('jquery'), SF_VERSION );
		}else{
			wp_enqueue_style( 'hover-icon', SF_URL . 'assets/css/hover_icon.css', array(), SF_VERSION );
		}
		if(isset($_GET['page']) == 'social-funnel'){
		wp_enqueue_script( 'sf-jquery-min', SF_URL . 'assets/js/jquery.min.js', array(), SF_VERSION );
		wp_enqueue_script( 'sf-bootstrap-min', SF_URL . 'assets/js/bootstrap.min.js', array(), SF_VERSION );
		wp_enqueue_script( 'sf-custom', SF_URL . 'assets/js/custom.js', array(), SF_VERSION );
		wp_enqueue_script( 'sf-checkradios', SF_URL . 'assets/js/jquery.checkradios.js', array(), SF_VERSION );
		wp_enqueue_script( 'sf-nicescroll', SF_URL . 'assets/js/jquery.nicescroll.min.js', array(), SF_VERSION );
		wp_enqueue_script( 'sf-modern', SF_URL . 'assets/js/modernizr.custom.28101.js', array(), SF_VERSION );
		wp_enqueue_script( 'sf-jasny-bootstrap', SF_URL . 'assets/js/jasny-bootstrap.js', array(), SF_VERSION );
		wp_localize_script( 'function', 'my_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}


function sf_menu() {
	//add_menu_page('Social Funnels', 'Social Funnels', 'manage_options', 'social-funnel', 'sf_main_page', SF_URL.'assets/images/social_funnels_icon.svg');
	add_menu_page('Social Funnels', 'Social Funnels', 'manage_options', 'social-funnel', 'sf_main_page', '');
}

function sf_main_page(){
	if(!sf_has_valid_license()) {
		require_once('views/settings.php'); 
	} elseif(isset($_GET['sf']) AND $_GET['sf'] == "add-compaign") {
		require_once('views/new-campaign.php'); 
	} elseif(isset($_GET['sf']) AND $_GET['sf'] == "import") {
		require_once('views/import.php');
	}elseif(isset($_GET['sf']) AND $_GET['sf'] == "bookmarklet") {
		require_once('views/bookmarklet.php');
	}elseif(isset($_GET['sf']) AND $_GET['sf'] == "bookmarklet_action") {
		ob_clean();
		require_once('views/bookmarklet_action.php');
		
		exit;
	}else{
		require_once('views/funnel_dashboard.php');
    }
}

function sf_has_valid_license() {
	$license = get_option("sf_license");
	if(!$license) {
		return false;
	} else {
		if(sf_validate_license($license)) {
			return true;
		} else {
			return false;
		}
	}
}
function sf_validate_license($license) {
	global $sfk;
	$k = unserialize(base64_decode($sfk));
	
	
	if(in_array($license,$k)) {
		return true;
	} else {
		return false;
	}
	return false;
}
function sf_activate(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	
	$campaigns     = $wpdb->prefix .'campaigns';
	$clicks        = $wpdb->prefix .'clicks';
	$optins        = $wpdb->prefix .'optins';


	$campaigns_table = "
					  CREATE TABLE IF NOT EXISTS $campaigns  (
					  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
					  `unique_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `post_id` int(11) NOT NULL,
					  `campaign_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `campaign_slug` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `autoresponder` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `autoresponder_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
					  `include_field` tinyint(1) NOT NULL,
					  `fb_retarget_pixel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
					  `source_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `popup_style` tinyint(1) NOT NULL,
					  `delay` int(11) NOT NULL,
					  `closable` tinyint(1) NOT NULL,
					  `popup_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
					  `content_headline` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `enable_video` tinyint(1) NOT NULL,
					  `video_embed_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
					  `content_subheadline` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `support_email` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `gift_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `unlock_after` int(11) NOT NULL,
					  `button_text` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `page_title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `feature_title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `feature_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
					  `feature_image` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `feature_image_align` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  `image_align` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					   PRIMARY KEY (`id`)
					  ) $charset_collate;";

	$clicks_table = "
					CREATE TABLE IF NOT EXISTS $clicks (
					`id` mediumint(9) NOT NULL AUTO_INCREMENT,
					`camp_id` int(10) NOT NULL,
					`slug` varchar(20) NOT NULL,
					`clicks` int(20) NOT NULL,
					`optins` int(20) NOT NULL,
					`socail_clicks` int(20) NOT NULL,
					`socail_optins` int(20) NOT NULL,
					 PRIMARY KEY (`id`)
					) $charset_collate;";

	$optins_table = "
					CREATE TABLE IF NOT EXISTS $optins (
					`id` mediumint(9) NOT NULL AUTO_INCREMENT,
					`name` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
					`email` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
					`host` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					`url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					`unique_id` varchar(128) NOT NULL,
					`optin_unique` varchar(128) NOT NULL,
					`reference` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					PRIMARY KEY (`id`)
					) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $campaigns_table );
	dbDelta( $clicks_table );
	dbDelta( $optins_table );
}

function delete_campaign(){
	global $wpdb;
	$postid = $wpdb->get_var('select `post_id` from `'.$wpdb->prefix.'campaigns` where `id` = '.$_POST['delid']);
	$query = "delete from `".$wpdb->prefix."campaigns` where `id`=".$_POST['delid'];
	if($wpdb->query($query)){
		wp_delete_post($postid);
		$social_clicks = $wpdb->prefix."clicks";
		$social_user = $wpdb->prefix."optins";
		$camp_id = $_POST['delid'];
		
		$camp_slug = $wpdb->get_results(" SELECT slug FROM $social_clicks WHERE camp_id = $camp_id ");
		$slug = $camp_slug[0]->slug;
		$query_clicks = " delete from $social_clicks where camp_id =".$_POST['delid'];
		$wpdb->query($query_clicks);
		
		$camp_url = get_site_url()."/".$slug;
		$query_optins = " delete from $social_user where url = '$camp_url'";
		$wpdb->query($query_optins);
		echo 'success';
	}else{
		echo 'fail';
	}
	die; 
}

function copy_campaign(){
	global $wpdb; 
	$row = $wpdb->get_row("select * from `".$wpdb->prefix."campaigns` where `id` = ".$_POST['id'], ARRAY_A);
	unset($row['id']);
	$slug = $row['campaign_slug'];
	
	$i=0;
	$count=0;
	while($i<=0)
	{
	 $slugn = check_duplicate_slug($slug);		
	 if(empty($slugn))
		{	$i++;
		}
		else
		{
			$slug = $slug."2";

		}
	 $count++;	
	}
	
	$slug = $slug."2";
	$name = $row['campaign_name'];
	while($count>0)
	{
		$name = $name."2";
		$count--;
	}
	
    $row['campaign_slug'] = $slug;
	$row['campaign_name'] = $name;
	$row['unique_id'] = randomPassword();
	$post_info = array(
        'post_status' => 'publish', 
        'post_type' => 'socialfunnel',
        'post_name' => $slug,
        'post_title' => $name
    );
    $new_postid = wp_insert_post($post_info);
   /* $slug = get_post($new_postid); 
    $slug = $slug->post_name;*/
    if($new_postid){
    	$row['post_id'] = $new_postid;
    	$camp_table = $wpdb->prefix."campaigns";
    	$insert = array(
                    "unique_id" => $row['unique_id'],
                    "post_id" => $row['post_id'],
                    "campaign_name" => $row['campaign_name'],
                    "campaign_slug" => $row['campaign_slug'],
                    "autoresponder" => $row['autoresponder'],
                    "autoresponder_code" => $row['autoresponder_code'],
                    "include_field" =>  $row["include_field"],
                    "fb_retarget_pixel" => $row['fb_retarget_pixel'],
                    "source_url" => $row['source_url'],
                    "popup_style" => $row['popup_style'],
                    "delay" => $row['delay'],
                    "closable" =>  $row['closable'],
                    "popup_details" => $row["popup_details"],
                    "content_headline" => $row['content_headline'],
                    "enable_video" => $row["enable_video"],
                    "video_embed_code" => $row['video_embed_code'],
                    "content_subheadline" => $row['content_subheadline'],
                    "support_email" => $row['support_email'],
                    "gift_url" => $row['gift_url'],
                    "unlock_after" => $row['unlock_after'],
                    "button_text" => $row['button_text'],
                    "page_title" => $row['page_title'],
                    "feature_title" => $row["feature_title"],
                    "feature_text" => $row["feature_text"],
                    "feature_image" => $row["feature_image"],
                    "feature_image_align" => $row["feature_image_align"]
            );

		$query_insert = $wpdb->insert($camp_table,$insert);

		$data = array(); 
		if($query_insert){
			$lid = $wpdb->insert_id;
			$social_clicks = $wpdb->prefix."clicks";
            $wpdb->query(" INSERT INTO $social_clicks (camp_id,slug) VALUES ($lid, '$slug') ");
			$data['msg'] = 'success';
			$data['cpname'] = $name;
			$data['id'] = $lid;
			$data['slug'] = $slug;
			$data['unique'] = $row['unique_id'];
		}else{
			$data['msg'] = 'fail';
		}
    }else{
    	$data['msg'] = 'fail';
    }
	echo json_encode($data);
	die; 
}

function export_campaign(){
	global $wpdb; 
	$query = $wpdb->get_row("select * from `".$wpdb->prefix."campaigns` where `id`= ".$_POST['id'],ARRAY_A);
	$data = array(); 
	foreach ($query as $key => $value) {
		$data[$key] = $value; 
	}
	$filename = $query['campaign_slug'];
	/*if($data['popup_style'] ==1){
		if(function_exists('curl_version')) {
			$json = json_decode($data['popup_details']);
			$url = $json->p1s1image;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url
			));
			$resp = curl_exec($curl);
			curl_close($curl);
			$base64 = base64_encode($resp);
			$ext = end(explode('.',$url));
			$arr = array('ext' => $ext, 'data' => $base64);
			print_r($arr); die ;
		} else {
			return file_get_contents($url);
		}
	}*/
	echo json_encode($data);
	die; 
}

function sf_subscribe(){
	global $wpdb;
	if(!empty($_POST['email']))
	{
		$checkemail = $wpdb->query("select * from `".$wpdb->prefix."optins` where `email` = '".$_POST['email']."' and `unique_id` = '".$_POST['id']."'");
	}else{
		$checkemail = false;
	}
	if($checkemail){
		echo 'email_exists';
	}else{
		$social_clicks = $wpdb->prefix."clicks";
		$social_user = $wpdb->prefix."optins";
		$unique_id = $_POST['optinid'];
		$slug = $_POST["slug"];
		$userid = $_POST["userid"];
		if($_POST['optin'] == 't'){
			$optin = 1;
			$optinid = $_POST['optinid'];
			if(!empty($userid))
			{	
				$get_clicks = $wpdb->get_results(" SELECT socail_optins FROM $social_clicks WHERE slug = '$slug' ");
                $new_optins = (int)$get_clicks[0]->socail_optins + 1;
               	$jay = $wpdb->update($social_clicks, array('socail_optins' => $new_optins), array('slug' => $slug));
			}
		}elseif($_POST['optin'] == 'n'){
			$optin = 0;
			$optinid = '';
			if($userid != "")
			{	
				$get_clicks = $wpdb->get_results(" SELECT optins FROM $social_clicks WHERE slug = '$slug' ");
                $new_optins = (int)$get_clicks[0]->optins + 1;
               	$jay = $wpdb->update($social_clicks, array('optins' => $new_optins), array('slug' => $slug));
			}
		}

		//$unique_id = randomPassword();
		
		/* Update Obtains */
		if($userid != "")
		{	
			$unique_id = randomPassword();
			$optin_unique = randomPassword();
			$options = array(
							 'unique_id' => $unique_id,
							 'optin_unique' => $optin_unique
							);
			
			if(isset($_POST['name']))
			{
				$options["name"] = $_POST['name'];
			}

			if(isset($_POST['email']))
			{
				$options["email"] = $_POST['email'];
			}
			
			$query = $wpdb->update($social_user, $options , array('id' => $userid));	
			
			//$query = $wpdb->query("insert into `".$wpdb->prefix."optins`(`name`,`email`,`unique_id`,`is_optin`,`optin_unique`,`optin_id`) values('".$_POST['name']."','".$_POST['email']."','".$unique_id."',$optin,'".$optin_unique."','".$optinid."')");
			if($query){
				//$uid = $wpdb->get_var("select `unique_id` from `".$wpdb->prefix."optins` where `id` = ".$wpdb->insert_id);
				echo $unique_id;
			}else{
				echo 'fail';
			}
		}else{
			echo $unique_id;
		}
	}
	die; 
}

function randomPassword() {
	global $wpdb; 
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    $pass = implode($pass);
    $query = $wpdb->query("select * from `".$wpdb->prefix."optins` where `unique_id` = '".$pass."' || `optin_unique` == '".$pass."'" );
    if($query){
    	randomPassword();
    }else{
    	return $pass; 
    }
}


function check_duplicate_slug($sluga)
{
	global $wpdb;
	$campaigns = $wpdb->prefix."campaigns";
	$slug = $sluga."2";
	$data = $wpdb->get_results(" SELECT campaign_slug FROM  $campaigns WHERE campaign_slug = '$slug' ");
	if(empty($data))
	{
	  return "";
	}
	else
	{
	 return $slug;	
	}
}

// function ini_rules( $options = null ) {
// 		$rules = '';
// 		$msg = '';

// 		$d = ini_get('upload_max_filesize');
// 		$e = rtrim($d, 'M');
// 		$f = intval($e);
// 		$x = ini_get('post_max_size');
// 		$y = rtrim($x, 'M');
// 		$z = intval($y);
		
// 		if ( $e < 128 OR $y < 128 ) {
// 			$rules .= "upload_max_filesize = 128M\n";
// 			$rules .= "post_max_size = 128M\n";
		

// 		// get url of site root.
// 		$host_path = explode("/", $_SERVER["REQUEST_URI"]);
// 		$host_path = $host_path[1];
// 		$root = $_SERVER["DOCUMENT_ROOT"];
// 		if($host_path != "wp-admin")
// 		{
// 			$root .= "/".$host_path; 
// 		}
		
// 		$filename = $root . '/wp-admin/php.ini';
		
// 		if ( file_exists( $filename ) ) {
		
// 			$editini = file_put_contents( $filename, $rules, FILE_APPEND | LOCK_EX );
				
// 			if (	false === $editini ) {
	
// 				// file_put_contents did not work so try fwrite()
						
// 				if (is_writable($filename)) {
				
// 				    // open in append mode with file pointer at the bottom of the file
// 				    if (!$handle = fopen($filename, 'a')) {
// 				         $error_msg = sprintf( __( 'Cannot open file (%s), so no changes will be made. Please deactivate the plugin and try again. If it still does not work after trying again, then this plugin may not be for you.', 'increase-upload-max-filesize' ), $filename );
// 				    }
					
// 				    // Write $rules to our opened file.
// 				    if (fwrite($handle, $rules) === FALSE) {
// 				         $error_msg = sprintf( __( 'Cannot write to file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then ask your web host to grant you access to write to your <code>%s</code> file.', 'increase-upload-max-filesize' ), $filename );
					
// 					}
				
// 				    fclose($handle);
					
// 				} else {
// 				    $error_msg = "The file $filename is not writable.";
// 				} // end	if (is_writable($filename)
					
// 			} // end if (	false === $editini ) 


// 		} else { 
		
// 			// file does not exist so create it
	
// 			if (!$handlec = fopen($filename, 'a')) {
// 		         $error_msg = sprintf( __( 'Could not create file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then this plugin may not be for you.', 'increase-upload-max-filesize' ), $filename );
// 			}
						
// 		    if (fwrite($handlec , $rules) === FALSE) {
						       
// 		         $error_msg = sprintf( __( 'Cannot write to newly created file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then ask your web host to grant you access to write to your php5.ini file.', 'increase-upload-max-filesize' ), $filename );
					
// 		    }
// 		    fclose($handlec);
				
// 		}  // end if (file_exists($filename)
// 	}
// }



?>