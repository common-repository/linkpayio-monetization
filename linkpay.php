<?php
/*
Plugin Name: LinkPay.io
Plugin URI: https://linkpay.io
Description: The best, fastest, and easiest way to monetize your traffic.
Version: 1.0
Author: LinkPay.io
Author URI: https://linkpay.io
License: GPL v2

This plugin is based on AutoShortLink, a WP plugin by Prasad Kirpekar.  It has been modified to work with LinkPay.io's API system for URL shortening.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'PLUGIN_PATH', plugins_url( __FILE__ ) );

//Detect and convert links from post/page before saving it.
function pay_translinks($content) {
	$string = $content;
	preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
	$out=$match[0];
	for($i=0;$i<count($out);$i++)
	{
		if(strpos($out[$i],'linkpay.io')==false){
		$string=str_replace($out[$i],pay_shst($out[$i]),$string);
		}	
	}

		return $string;
}

add_filter('content_save_pre','pay_translinks');

//Get LinkPay.io URL using API Key
function pay_shst($surl){
	$apikey=get_option('pay_api_key');
	$url = "https://json.linkpay.io/?";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"key=$apikey&url=$surl");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
	$result = curl_exec($ch);
	$statusCode = curl_getInfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($statusCode == 200){
		$json = json_decode($result, true);
		if($json["status"] == "ok"){
		    $shortUrl = $json["shortenedUrl"];
			return $shortUrl;	   
		}
		else{
			return "LinkPay.io URL Failed";
		}
	}
}

if( strlen(get_option('pay_api_key'))!=32 ) {
  add_action( 'admin_notices', 'pay_notice' );
}
function pay_notice()
{
?>
  <div class="update-nag notice">
      <p><?php _e( 'Please <a href="options-general.php?page=linkpay">click here</a> to enter your LinkPay.io API Key.'); ?></p>
  </div>
<?php
}

function pay_settings(){
    if(current_user_can('manage_options')){
	    $pay_key=get_option('pay_api_key');
	    if(isset($_POST['submitted'])&&check_admin_referer( 'pay_nonce_action', 'pay_nonce_field' )){
		if(isset($_POST['service_key'])){
		    $pay_key=sanitize_text_field($_POST['service_key']);
			//validation of key
			if(strlen($pay_key)==32){
			update_option('pay_api_key',$pay_key);
		echo "<div class='updated fade'><p>Updated! Any new links you post will automatically be shortened by LinkPay.io!</p></div>";
			}
			else{
echo "<div class='error fade'><p>Invalid Key</p></div>";
}
		}
		
		
	    }
		$action_url = $_SERVER['REQUEST_URI'];
		include "admin/options.php";
}

}

function pay_add_settings(){

 
    add_option('pay_api_key');
	
}

function pay_admin_settings()
{
			add_options_page('LinkPay.io', 'LinkPay.io', 10, 'linkpay', 'pay_settings');
	
}

register_activation_hook(__FILE__, 'pay_add_settings');
add_action('admin_menu','pay_admin_settings');

?>