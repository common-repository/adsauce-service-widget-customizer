<?php

	/**
	* Plugin Name: AdSauce Service Widget and Plugin
	* Plugin URI: http://www.adsauce.co/
	* Description: Easily accessible plugin for AdSauce Services.  This plugin accesses the AdSauce server to grab your information in order to place adspots, social message boards, and business directories.  You must provide login data for this service.  This plugin specifically is designed to link to external sites: app.adsauce.co, ad.adsauce.co and www.adsauce.co.
	* Version: 1.1.0
	* Author: AdSauce
	* Author URI: http://www.adsauce.co/
	* License: AdSauce
	*/

//-------------------START INSTANCE WIDGET
	class adsauce_widget extends WP_Widget {
		function __construct() {
			parent::__construct('adsauce_widget',__('AdSauce Ads Plugin', 'adsauce_widget_domain'), array( 'description' => __( 'Simple Widget to ad AdSauce services to your wordpress site', 'adsauce_widget_domain' ), ));
		} 

		public $bearerToken = '';

		// WIDGET
		public function widget( $args, $instance ) {
			//Grab Variables
			if(isset($instance['adSizeTypeName']))
				$adSizeTypeName = $instance['adSizeTypeName'];
			else
				$adSizeTypeName = '';
			if (isset($instance['websiteLocationId']))
				$websiteLocationId = $instance['websiteLocationId'];
			else
				$websiteLocationId = '';
			if(isset($instance['width']))
				$width = $instance['width'];
			else 
				$width = 0;
			if(isset($instance['height']))
				$height = $instance['height'];
			else
				$height = 0;

			$dimensions = '';

			if($adSizeTypeName != '') {
				//Draw Widget
				echo $args['before_widget'];
			?>
				<div style="text-align: center;"><?php
					if ($adSizeTypeName == 'Business Directory') {
						$dimensions = 'width: 100%; height: 650px;';
			?>
						<iframe style="border: none; padding: 0; margin: 0; <?php echo esc_attr( $dimensions ); ?>"
								src="https://tad.adsauce.co/adindex.html#/servicewebsitelocation/directory/<?php echo esc_attr( $websiteLocationId ); ?>">
						</iframe>
			<?php
					} else if ($adSizeTypeName == 'Social Message Board') {
						$dimensions = 'width: 100%; height: 987px; min-width: 315px; max-width: 1260px;';
			?>
						<iframe style="border: none; padding: 0; margin: 0; <?php echo esc_attr( $dimensions ); ?>"
								src="https://tad.adsauce.co/adindex.html#/servicewebsitelocation/messagewall/<?php echo esc_attr( $websiteLocationId ); ?>">
						</iframe>
			<?php
					} else {
						$maxDimensions = 'width: ' . $width . 'px; height: ' . $height . 'px;';
						$dimensions = 'width: ' . $width . 'px; height: ' . $height . 'px;';
						wp_enqueue_script('AdSauceResponsiveScript', 'http://app.adsauce.co/app/scripts/responsivescript.js');
			?>
						<div style="<?php echo esc_attr( $maxDimensions ); ?>">
							<iframe style="border: none; padding: 0; margin: 0; <?php echo esc_attr( $dimensions ); ?>"
									src="https://tad.adsauce.co/adindex.html#/servicewebsitelocation/displayad/<?php echo esc_attr( $websiteLocationId ); ?>">
							</iframe>
						</div>div>
			<?php
					}
			?>
				</div>
			<?php
				echo $args['after_widget'];
			}

		}

		// Widget Backend
		public function form( $instance ) {
			//Include Javascript library
			wp_enqueue_script('adsauce_widget', plugins_url( '/adSauceWidgetForm.js' , __FILE__ ) , array( 'jquery' ));
			// including ajax script in the plugin Myajax.ajaxurl
			wp_localize_script( 'adsauce_widget', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));

			$password = '';
			
			//Grab stored values
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __( 'New title', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'username' ] ) ) {
				$username = $instance[ 'username' ];
			}
			else {
				$username = __( '', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'websiteLocationNameType' ] ) ) {
				$websiteLocationNameType = $instance[ 'websiteLocationNameType' ];
			}
			else {
				$websiteLocationNameType = __( '', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'websiteName' ] ) ) {
				$websiteName = $instance[ 'websiteName' ];
			}
			else {
				$websiteName = __( '', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'websiteLocationId' ] ) ) {
				$websiteLocationId = $instance[ 'websiteLocationId' ];
			}
			else {
				$websiteLocationId = __( '', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'adSizeTypeName' ] ) ) {
				$adSizeTypeName = $instance[ 'adSizeTypeName' ];
			}
			else {
				$adSizeTypeName = __( '', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'width' ] ) ) {
				$width = $instance[ 'width' ];
			}
			else {
				$width = __( '', 'adsauce_widget_domain' );
			}
			if ( isset( $instance[ 'height' ] ) ) {
				$height = $instance[ 'height' ];
			}
			else {
				$height = __( '', 'adsauce_widget_domain' );
			}

			// Widget admin form
			?>
				<p>
					<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username:' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
					<label for="<?php echo $this->get_field_id( 'password' ); ?>"><?php _e( 'Password:' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'password' ); ?>" name="<?php echo $this->get_field_name( 'password' ); ?>" type="password" value="<?php echo esc_attr( $password ); ?>" />
					<div style='text-align: right;'>
						<button id="<?php echo $this->get_field_id( 'submitButton' ); ?>" name="<?php echo $this->get_field_name( 'submitButton' ); ?>" type="button" 
							onclick="setData('<?php echo $this->get_field_id( 'username' ); ?>','<?php echo $this->get_field_id( 'password' ); ?>','<?php echo $this->get_field_id( 'websiteName' ); ?>','<?php echo $this->get_field_id( 'websiteLocationNameType' ); ?>','<?php echo $this->get_field_id( 'websiteLocationId' ); ?>','<?php echo $this->get_field_id( 'adSizeTypeName' ); ?>','<?php echo $this->get_field_id( 'height' ); ?>','<?php echo $this->get_field_id( 'width' ); ?>')">Log In</button>
					</div>

					<label for="<?php echo $this->get_field_id( 'websiteName' ); ?>"><?php _e( 'Website:' ); ?></label><br />
					<select id="<?php echo $this->get_field_id( 'websiteName' ); ?>" name="<?php echo $this->get_field_name( 'websiteName' ); ?>"  style="min-width: 150px;" onchange="updateWebsiteLocations()">
			<?php if(isset($websiteName) && $websiteName != '') { ?>
						<option value="<?php echo esc_attr( $websiteName ); ?>"><?php echo esc_attr( $websiteName ); ?></option>
			<?php } ?>
					</select><br />

					<label for="<?php echo $this->get_field_id( 'websiteLocationNameType' ); ?>"><?php _e( 'Location:' ); ?></label><br />
					<select id="<?php echo $this->get_field_id( 'websiteLocationNameType' ); ?>" name="<?php echo $this->get_field_name( 'websiteLocationNameType' ); ?>"  style="min-width: 150px;" onchange="websiteLocationChanged()">
			<?php if(isset($websiteLocationNameType) && $websiteLocationNameType != '') { ?>
						<option value="<?php echo esc_attr( $websiteLocationNameType ); ?>"><?php echo esc_attr( $websiteLocationNameType ); ?></option>
			<?php } ?>
					</select><br />

					<br />

					<input type="hidden"  id="<?php echo $this->get_field_id( 'websiteLocationId' ); ?>" name="<?php echo $this->get_field_name( 'websiteLocationId' ); ?>" type="text" value="<?php echo esc_attr( $websiteLocationId ); ?>" />
					<input type="hidden"  id="<?php echo $this->get_field_id( 'adSizeTypeName' ); ?>" name="<?php echo $this->get_field_name( 'adSizeTypeName' ); ?>" type="text" value="<?php echo esc_attr( $adSizeTypeName ); ?>" />
					<input type="hidden"  id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />
					<input type="hidden"  id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" />
				</p>
			<?php
		}		

		// Updating widget replacing old instances with new
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['username'] = ( ! empty( $new_instance['username'] ) ) ? strip_tags( $new_instance['username'] ) : '';

			$instance['websiteName'] = ( ! empty( $new_instance['websiteName'] ) ) ? strip_tags( $new_instance['websiteName'] ) : '';
			$instance['websiteLocationNameType'] = ( ! empty( $new_instance['websiteLocationNameType'] ) ) ? strip_tags( $new_instance['websiteLocationNameType'] ) : '';

			$instance['websiteLocationId'] = ( ! empty( $new_instance['websiteLocationId'] ) ) ? strip_tags( $new_instance['websiteLocationId'] ) : '';
			$instance['adSizeTypeName'] = ( ! empty( $new_instance['adSizeTypeName'] ) ) ? strip_tags( $new_instance['adSizeTypeName'] ) : '';
			$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
			$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';

			return $instance;
		}
	}
//-----------END INSTANCE WIDGET


	// Register and load the widget
	function adsauce_load_widget() {
	    register_widget( 'adsauce_widget' );
	}

//------------START API CALLS FOR WIDGET
	add_action( 'widgets_init', 'adsauce_load_widget' );

	add_action( 'wp_ajax_nopriv_getBearerToken', 'getBearerToken' );
	add_action( 'wp_ajax_getBearerToken', 'getBearerToken' );

	add_action( 'wp_ajax_nopriv_getUserInfo', 'getUserInfo' );
	add_action( 'wp_ajax_getUserInfo', 'getUserInfo' );

	add_action( 'wp_ajax_nopriv_getWebsites', 'getWebsites' );
	add_action( 'wp_ajax_getWebsites', 'getWebsites' );

	add_action( 'wp_ajax_nopriv_getWebsiteLocations', 'getWebsiteLocations' );
	add_action( 'wp_ajax_getWebsiteLocations', 'getWebsiteLocations' );

	//WORDPRESS API
	function getWebsites() {
		if(isset($_POST['bearerToken']) && $_POST['bearerToken'] != '' && isset($_POST['userPK']) && $_POST['userPK'] != '')
		{
			$bearerToken = $_POST['bearerToken'];
			$userPK = $_POST['userPK'];
		} else {
			echo 'error: User not loaded!';

			die();
			return;
		}

		try {
			$websites = callGetWebsites($bearerToken, $userPK);
		} catch (Exception $ex)
		{
			echo 'error: '.$ex;
			die();
			return;
		}

		echo json_decode($websites, true);
		die();
	}

	function callGetWebsites($bearerToken, $userPK) {
		$url = 'https://app.adsauce.co/api/Websites?SearchEnabled=true&SearchUserFK='.$userPK;
		                                                                                                                     
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$bearerToken));     
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);                                                           
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   

		$auth = curl_exec($curl);
		$secret = json_encode($auth, true);

		return $secret;
	}

	function getWebsiteLocations() {
		if(isset($_POST['bearerToken']) && $_POST['bearerToken'] != '' && isset($_POST['websitePK']) && $_POST['websitePK'] != '')
		{
			$bearerToken = $_POST['bearerToken'];
			$websitePK = $_POST['websitePK'];
		} else {
			echo 'error: Website not loaded!';

			die();
			return;
		}

		try {
			$websiteLocations = callGetWebsiteLocations($bearerToken, $websitePK);
		} catch (Exception $ex)
		{
			echo 'error: '.$ex;
			die();
			return;
		}

		echo json_decode($websiteLocations, true);
		die();
	}

	function callGetWebsiteLocations($bearerToken, $websitePK) {
		$url = 'https://app.adsauce.co/api/WebsiteLocations?SearchEnabled=true&SearchWebsiteFK='.$websitePK;
		                                                                                                                     
		$curl = curl_init($url);                                                                      
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$bearerToken));     
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);                                                           
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   

		$auth = curl_exec($curl);
		$secret = json_encode($auth, true);

		return $secret;
	}

	function  getBearerToken() {
		if(isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] != '' && $_POST['password'] != '')
		{
			$username = $_POST['username'];
			$password = $_POST['password'];
		} else {
			echo 'error: Please enter username and password!';

			die();
			return;
		}

		try {
			$token = getAdSauceBearerToken($username, $password);
		} catch (Exception $ex)
		{
			echo 'error: '.$ex;
			die();
			return;
		}

		echo $token;
		die();
	}

	function  getUserInfo() {
		if(isset($_POST['bearerToken']) && $_POST['bearerToken'] != '')
		{
			$bearerToken = $_POST['bearerToken'];
		} else {
			echo 'error: Bearer Token Not Set!';

			die();
			return;
		}

		try {
			$user = callGetUserInfo($bearerToken);
		} catch (Exception $ex)
		{
			echo 'error: '.$ex;
			die();
			return;
		}

		echo json_decode($user, true);
		die();
	}

	function callGetUserInfo($bearerToken) {
		$url = 'https://app.adsauce.co/api/Users/GetShort/0';
		                                                                                                                     
		$curl = curl_init($url);                                           
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$bearerToken));     
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);                                                           
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   

		$auth = curl_exec($curl);
		$secret = json_encode($auth, true);

		return $secret;
	}

	function getAdSauceBearerToken($username, $password) {
		$url = 'https://app.adsauce.co/Token';
		$postData = array("grant_type" => "password", "username" => $username, "password" => $password);
		                                                                                                                     
		$curl = curl_init($url);                                                                      
		curl_setopt($curl, CURLOPT_POST, true);                                                                     
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));     
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);                                                           
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   

		$auth = curl_exec($curl);
		$secret = json_decode($auth, true);

		if(isset($secret["access_token"])) {
			$result = $secret["access_token"];
		} else {
			$result = 'error: Username or password incorrect!';
		}

		return $result;
	}

//-------------------END API CALLS FOR WIDGET

//--------------------------------------
	//Iframe functionality
//--------------------------------------
	function adSauce_embed_iFrame_shortcode( $atts ) {

		$defaults = array(

			'src' => 'http://www.youtube.com/embed/4qsGTXLnmKs',

			'width' => '100%',

			'height' => '500',

			'scrolling' => 'yes',

			'class' => 'iframe-class',

			'frameborder' => '0'

		);



		foreach ( $defaults as $default => $value ) { // add defaults

			if ( ! @array_key_exists( $default, $atts ) ) { // mute warning with "@" when no params at all

				$atts[$default] = $value;

			}

		}



		$html = "\n".'<!-- iframe plugin v.4.2 wordpress.org/plugins/iframe/ -->'."\n";

		$html .= '<iframe';

		foreach( $atts as $attr => $value ) {

			if ( strtolower($attr) != 'same_height_as' AND strtolower($attr) != 'onload'

				AND strtolower($attr) != 'onpageshow' AND strtolower($attr) != 'onclick') { // remove some attributes

				if ( $value != '' ) { // adding all attributes

					$html .= ' ' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';

				} else { // adding empty attributes

					$html .= ' ' . esc_attr( $attr );

				}

			}

		}

		$html .= '></iframe>'."\n";



		if ( isset( $atts["same_height_as"] ) ) {

			$html .= '

				<script>

				document.addEventListener("DOMContentLoaded", function(){

					var target_element, iframe_element;

					iframe_element = document.querySelector("iframe.' . esc_attr( $atts["class"] ) . '");

					target_element = document.querySelector("' . esc_attr( $atts["same_height_as"] ) . '");

					iframe_element.style.height = target_element.offsetHeight + "px";

				});

				</script>

			';

		}



		return $html;

	}

	add_shortcode( 'iframe', 'adSauce_embed_iFrame_shortcode' );





	function adSauce_embed_iFrame_plugin_meta( $links, $file ) { // add 'Plugin page' and 'Donate' links to plugin meta row

		if ( strpos( $file, 'iframe/iframe.php' ) !== false ) {

			$links = array_merge( $links, array( '<a href="http://web-profile.com.ua/wordpress/plugins/iframe/" title="Plugin page">Iframe</a>' ) );

			$links = array_merge( $links, array( '<a href="http://web-profile.com.ua/donate/" title="Support the development">Donate</a>' ) );

			$links = array_merge( $links, array( '<a href="http://codecanyon.net/item/advanced-iframe-pro/5344999?ref=webvitaly">Advanced iFrame Pro</a>' ) );

		}

		return $links;

	}

	add_filter( 'plugin_row_meta', 'adSauce_embed_iFrame_plugin_meta', 10, 2 );
//------------------END IFRAME FUNCTIONALITY

	/* AdSauce TinyMCE Button */
	add_action( 'admin_init', 'adSauce_tinymce_button' );

	function adSauce_tinymce_button() {
	     if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
	          add_filter( 'mce_buttons', 'my_register_tinymce_button' );
	          add_filter( 'mce_external_plugins', 'adSauce_add_tinymce_button' );
	     }
	}

	function my_register_tinymce_button( $buttons ) {
	     array_push( $buttons, "adSauce_toolBar_button", "button_green" );
	     return $buttons;
	}

	function adSauce_add_tinymce_button( $plugin_array ) {
	     $plugin_array['adSauce_button_script'] = plugins_url( '/adSauceWordpressbutton.js', __FILE__ ) ;
	     return $plugin_array;
	}

	add_action( 'wp_ajax_adSauce_plugin_slug_insert_dialog', 'adSauce_plugin_slug_insert_dialog' );

	function adSauce_plugin_slug_insert_dialog() {
		echo '<div style="padding: 20 px; margin: 20px; width: 100%; max-width: 380px;">';
		echo 	'<label for="adSauce_username" style="margin: 5px;">Username:</label><br/>';
		echo 	'<input class="widefat" id="adSauce_username" name="adSauce_username" type="text" style="margin: 5px; width: 250px;"/><br/>';
		echo 	'<label for="adSauce_password" style="margin: 5px;">Password:</label><br/>';
		echo 	'<input class="widefat" id="adSauce_password" name="adSauce_password" type="password" style="margin: 5px;"/><br/>';
		echo 	'<label id="adSauce_loggedInAs" style="margin: 5px; visibility: hidden;"></label><br/>';
		echo 	'<div style="text-align: right; width: calc(100% - 40px);"><button id="adSauce_SubmitButton" name="adSauce_SubmitButton" type="button" style="margin: 5px; padding: 4px 8px;" class="mce-btn mce-btn-has-text"';
		echo 	'	onclick="getAdSauceBearerToken()">Log In</button></div><br/>';
		
		echo 	'<label for="adSauce_WebsiteName" style="margin: 5px;">Website:</label><br />';
		echo 	'<select id="adSauce_WebsiteName" name="adSauce_WebsiteName" style="min-width: 150px; max-width: 380px; margin: 5px;" onchange="updateWebsiteLocations()">';

		echo 	'</select><br />';

		echo 	'<label for="adSauce_websiteLocationNameType" style="margin: 5px;">Location:</label><br />';
		echo 	'<select id="adSauce_websiteLocationNameType" name="adSauce_websiteLocationNameType"  style="min-width: 150px; max-width: 380px; margin: 5px;" onchange="websiteLocationChanged()">';

		echo 	'</select><br />';

		echo 	'<br />';

		echo 	'<input type="hidden"  id="adSauce_WebsiteLocationId" name="adSauce_WebsiteLocationId" type="text" />';
		echo 	'<input type="hidden"  id="adSauce_AdSizeTypeName" name="adSauce_AdSizeTypeName" type="text" />';
		echo 	'<input type="hidden"  id="adSauce_Ad_Width" name="adSauce_Ad_Width" type="text" />';
		echo 	'<input type="hidden"  id="adSauce_Ad_Height" name="adSauce_Ad_Height" type="text" />';
		echo '</div>';
		echo '<script>';
		echo '	var adSauceCookie=getCookie();';
		echo '	if(adSauceCookie.adSauceBearerToken)';
		echo '	{';
		echo '		mieAdSauceAdSetupObject.bearerToken=adSauceCookie.adSauceBearerToken;';
		echo '		jQuery("#adSauce_username").val(adSauceCookie.username);';
		echo '		jQuery("#adSauce_loggedInAs").html("Logged in as: " + adSauceCookie.username);';
		echo '		jQuery("#adSauce_loggedInAs").css("visibility", "visible");';
		echo '		getUserInfo();';
		echo '	}';
		echo '</script>';

		die();
	}
?>
