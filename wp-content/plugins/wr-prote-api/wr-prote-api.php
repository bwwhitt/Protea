<?php

/**
 * Plugin Name: Prote Users API
 * Description: Lets developers get users via API
 * Author: Developer
 * Version: 1.0
 * Author URI: https://proteaintelligence.com/
 *
 *  @package ProteUsersAPI
 */
include 'wr-custom-api.php';
$acd_plan_id   = 895;
$enter_plan_id = 897;
include 'wr-class-prote-api.php'; 
include 'stripe/init.php';
/**
 * Enqueue scripts and styles.
 */
function wrpt_enqueue_scripts()
{
	wp_enqueue_script('wrpt_customjs', plugin_dir_url(__FILE__) . 'assets/js/wrpt-custom.js', array('jquery'), time(), true);
	wp_enqueue_style('wrpt_customcss', plugin_dir_url(__FILE__) . 'assets/css/wr_protea.css');
	//wp_localize_script( 'wrpt_customjs', 'wrpt_vars',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
	wp_localize_script('wrpt_customjs', 'frontendajax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'wrpt_enqueue_scripts');

function wr_validate_email($submit_errors, $form_id, $field_data_array)
{
	if ($form_id == '894') {
		$email_field = 'email-1';
		$email = isset($_POST[$email_field]) ? $_POST[$email_field] : false;
		if (!$email) {
			return $submit_errors;
		}

		list($user, $domain) = explode('@', $email);
		list($email_name, $extantion) = explode('.', $domain);

		if (strtolower($extantion) != 'edu') {
			$submit_errors[][$email_field] = __('Not a valid Email Address(".edu is required")');
		}
	}
	return $submit_errors;
}
//add_filter('forminator_custom_form_submit_errors', 'wr_validate_email', 99, 3);

 
/**
 * Proccess form data on submit.
 * 
 * @param entry $entry Entry array of form.
 * @param int   $form_id Entry id of form.
 * @param array $field_data_array array of form data.
 */
function wrpt_registraion_form_proccess_data($entry, $form_id, $field_data_array)
{
	/* echo "<pre>";
	print_r($entry);
	print_r($field_data_array);
	echo "</pre>"; */

	$api_data = array();

	/* if ($form_id == '894') {
		//Academy Form
		$email_field_index = array_search('email-1', array_column($field_data_array, 'name'));
		$email             = $field_data_array[$email_field_index]['value'];
		$orgArr = explode("@", $email);
		$token             = md5($email) . wp_rand(10, 9999);
		$user              = get_user_by('email', $email);
		$user_id           = $user->ID;
		update_user_meta($user_id, 'activation_token', $token);
		update_user_meta($user_id, 'prt_plan_id', $GLOBALS['acd_plan_id']);
		$activation_link      = get_site_url() . '?wrpt_activate=' . $token . '&user_email=' . $email;
		$subject              = 'Activate account on Protea Intelligence';
		$message              = "<b>Please Visit This Links To Activate Your Account</b> <a target='_blank' href='" . $activation_link . "'>Activate</a>";
		$headers              = array('Content-Type: text/html; charset=UTF-8');
		$testemail            = wp_mail($email, $subject, $message, $headers);
		$api_data['UserType'] = 1;
		$api_data['Plan'] = 1;
		//$api_data['Id']       = $user_id;
	} */
	/*if ($form_id == '899') {
		//Enterprice Form 
		$email_field_index = array_search('email-1', array_column($field_data_array, 'name'));
		$email             = $field_data_array[$email_field_index]['value'];
		$user              = get_user_by('email', $email);
		$user_id           = $user->ID;
		update_user_meta($user_id, 'prt_plan_id', $GLOBALS['enter_plan_id']);
		$udata = get_userdata($user->ID);
		$registered = $udata->user_registered;
		$dateafter7 = date('Y-m-d', strtotime('+7 day', strtotime($registered)));
		update_user_meta($user_id, 'wruser_next_week_date', $dateafter7);
		update_user_meta($user_id, 'wruser_next_week_date1', 'tesing');
		$api_data['UserType'] = 2;
		$api_data['Plan'] = 2;
		//$api_data['Id'] = $user_id;
	} */
	if ($form_id == '899' || $form_id == '894') { //not work action link after user create
		/* $email_field_index = array_search('email-1', array_column($field_data_array, 'name'));
		$email             = $field_data_array[$email_field_index]['value'];
		$user              = get_user_by('email', $email);
		$user_id           = $user->ID;
		$password  = isset($_POST['password-1']) ? $_POST['password-1'] : "test123";
		update_user_meta($user_id, 'wruser_pass', $password); */
		//$user_data = wp_get_current_user();
		//echo "<pre>";
		//$user              = get_user_by('email', "tokaj30159@leezro.com");
		//echo "<pre>fffff";print_r($user); echo "</pre>";
		//print_r($field_data_array);
		/*print_r($_POST);
		print_r($user);
		echo "</pre>";
		echo "\nemail : $email";
		echo "\nuser id : $user_id ";
		echo "\npassword : $password";
		echo "<pre>";print_r($user_data); echo "</pre>"; */
	}
	if ($form_id == '916') {
		/**EnterPrice Payment form */
		$user_id = $_GET['user_id'];
		foreach ($field_data_array as $key => $val) {

			if ($val['name'] == 'stripe-1') {
				$stripe_data = $field_data_array[$key];
			}else if ($val['name'] == 'hidden-1') {
				$user_id = $val['value'];
			}else if ($val['name'] == 'email-1') {
				$email = $val['value'];
			}
		}
		
		update_user_meta($user_id, 'wrpt_stripe_data', $stripe_data);
		update_user_meta($user_id, 'wrpt_activation_enterprise_date', date('F j, Y, g:i a'));
		$enterprice_user_payment_date = date('Y-m-d H:i:s');
		update_user_meta($user_id, 'wr_enterprice_user_payment_date', $enterprice_user_payment_date);

		/* $entry_id = $entry->entry_id;
		update_user_meta($user_id, 'wr_entry_id', $entry_id); */

		$enterprice_user_payment_date_obj = new DateTime($enterprice_user_payment_date);
		if (strtolower($stripe_data['value']['product_name']) == "monthly") {
			$enterprice_user_payment_date_obj->modify('+30 days');
		} else {
			$enterprice_user_payment_date_obj->modify('+365 days');
		}
		$expiry_date_obj = $enterprice_user_payment_date_obj->format('Y-m-d H:i:s');
		update_user_meta($user_id, 'wrpt_expiry_enterprise_date', $expiry_date_obj);

		
		$api_data['IsPaid']  = true;
		$api_data['ExpirationDate']  = $enterprice_user_payment_date_obj->format('Y-m-d\TH:i:s\Z');

		$url                   = 'https://protea-api-staging.azurewebsites.net/user/'.trim($email).'/paid';
		$args = array(
			'method' => 'POST',
			'body'   => json_encode($api_data),
			'headers' => array(
				'content-type' => 'application/json'
			),
		);
		$request = wp_remote_post($url, $args);
		if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
			error_log(print_r($request, true));
		}
		$response = wp_remote_retrieve_body($request);
		error_log(print_r($response, true));
	}
	
}
//add_action('forminator_custom_form_submit_before_set_fields', 'wrpt_registraion_form_proccess_data', 99, 3);


/**
 * Custom Yeatly Event
 *
 * @param array $schedules array of schedules.
 */
function wrpt_custom_yearly_event($schedules)
{
	if (!isset($schedules['5min'])) {
		$schedules['5min'] = array(
			'interval' => 25 * 60,
			'display'  => __('Once every 5 minutes'),
		);
	}
	return $schedules;
}
add_filter('cron_schedules', 'wrpt_custom_yearly_event');

function redirect_sub_to_home_wpse_93843($redirect_to, $request, $user)
{
	if (isset($user->roles) && is_array($user->roles)) {
		if (in_array('enterprise_user', $user->roles) || in_array('academic_user', $user->roles)) {
			return home_url();
		}
	}
	return $redirect_to;
}
add_filter('login_redirect', 'redirect_sub_to_home_wpse_93843', 10, 3);

function hide_admin_wpse_93843()
{
	if (current_user_can('academic_user') || current_user_can('enterprise_user')) {
		add_filter('show_admin_bar', 'return_false');
	}
}
add_action('wp_head', 'hide_admin_wpse_93843');

function admin_default_page()
{
	if (!current_user_can('editor') || !current_user_can('administrator')) {
		return '/my-account';
	}
}
add_filter('login_redirect', 'admin_default_page');

function wrpt_change_password()
{
	global $wpdb;

	$passdata = $_POST;

	$user = wp_get_current_user(); //trace($user);
	//print_r($user);
	$x = wp_check_password($passdata['old_password'], $user->user_pass, $user->data->ID);
	$passupdatetype = 'error';
	if ($x) {
		if (!empty($passdata['new_password']) && !empty($passdata['confirm_password'])) {
			if ($passdata['new_password'] == $passdata['confirm_password']) {
				$udata['ID'] = $user->data->ID;
				$udata['user_pass'] = $passdata['new_password'];
				$uid = wp_update_user($udata);
				if ($uid) {
					do_action('wr_porte_api_password_change', $udata['user_pass'], $user->data->user_email);
					$passupdatemsg = "The password has been updated successfully";
					$passupdatetype = 'success';
					unset($passdata);
				} else {
					$passupdatemsg = "Sorry! Failed to update your account details.";
					$passupdatetype = 'error';
				}
			} else {
				$passupdatemsg = "Confirm password doesn't match with new password";
				$passupdatetype = 'error';
			}
		} else {
			$passupdatemsg = "Please enter new password and confirm password";
			$passupdatetype = 'error';
		}
	} else {
		$passupdatemsg = "Old Password doesn't match the existing password";
		$passupdatetype = 'error';
	}
	$arr['message'] = $passupdatemsg;
	$arr['status'] = $passupdatetype;
	//print_r($arr);
	echo json_encode($arr);
	die();
}
add_action('wp_ajax_wrpt_change_password', 'wrpt_change_password');

/**
 * Shortcode for my account
 */
function wrpt_my_account()
{
	ob_start();
	/* if( isset($_GET['stripe_test']) ) {
		echo "<pre>";
		//$stripe = new \Stripe\StripeClient("sk_test_51K1nT2SBem5ZxLU088EwJFDH0hJDr9Bi7x6PHqUl00WbooKwtay2bz6Ju6GpKaVaYpYMJLuAKX69xchyyFVsrJSi00WMTpJyPq");

		$stripe = new \Stripe\StripeClient("sk_test_51H39x4AHrMBxesFq1yFz4xPOHjf8jXmOwFVWH7ZIxyvUCR64vK2g5MuA3dcTXy7VhRR65RmNCnjM7X8QvgEhhPg900JD3bBYKV");
		
		if(false){
			$card_data = $stripe->customers->allSources(
				'cus_KhasNo26d0NsBO',
				['object' => 'card', 'limit' => 3]
			);
			$stripe->paymentMethods->attach(
				'pm_1KHNIgSBem5ZxLU0O5ENRs94',
				['customer' => 'cus_KhasNo26d0NsBO']
			);
			
			$card_data = $stripe->paymentMethods->all([
			'customer' => 'cus_KhasNo26d0NsBO',
			'type' => 'card',
			]);
		}

		if(true) {
			/* echo "<h2>COUPON CODE USING GET COUPON DATA</h2>";
			$card_data = $stripe->customers->allSources(
				'cus_KhasNo26d0NsBO',
				['object' => 'card', 'limit' => 3]
			);
			print_r($card_data); \/////

			echo "<h2>GET COUPON CODE LIST</h2>";
			$coupon_list = $stripe->coupons->all(['limit' => 3]);
			print_r($coupon_list);

			echo "<h2>COUPON CODE USING GET COUPON DATA</h2>";
			$coupon_data = $stripe->coupons->retrieve('OrrVPNEE', []);
			print_r($coupon_data);
		}
		
		//print_r($card_data);
		echo "</pre>";
	} */
	
	//	$SubscriptionService_Obj = new SubscriptionService();
	$user_id = get_current_user_id();
	if (current_user_can('editor') || current_user_can('administrator')) {
		wp_redirect(admin_url());
		return;
	}
	if (!$user_id) { ?>
		<a href="/academy-form" class="elementor-button-link elementor-button elementor-size-md">Academy Plan</a>
		<a href="/enterprise-plan" class="elementor-button-link elementor-button elementor-size-md">Enterprise Plan</a>
	<?php
		return;
	}

	$user       = get_user_by('ID', $user_id);
	$user_meta = get_user_meta( $user_id );
	
	$user_meta  = get_userdata($user_id);
	$user_email = $user->user_email;
	$user_roles = $user_meta->roles;
	$user_role  = $user_roles[0];

	if ($user_role == 'subscriber') {
	?>
		<a href="/academy-form" class="elementor-button-link elementor-button elementor-size-md">Academy Plan</a>
		<a href="/enterprise-plan" class="elementor-button-link elementor-button elementor-size-md">Enterprise Plan</a>
	<?php
		return;
	}
	$token               = get_user_meta($user_id, 'activation_token', true);
	$activation_link     = get_site_url() . '?wrpt_activate=' . $token . '&user_email=' . $email;
	$plan                = get_user_meta($user_id, 'prt_plan_id', true);
	$stripe_detail       = get_user_meta($user_id, 'wrpt_stripe_data', true);
	$stripe_payment_date = get_user_meta($user_id, 'wrpt_activation_enterprise_date', true);
	$current_date        = new DateTime();
	$user_meta = get_userdata($user_id);
	$user_roles = $user_meta->roles;
	$user_role = $user_roles[0];
	$enterprise_payment_entry_id = get_user_meta($user_id, 'wr_entry_id', true);	//KKK
	$wrpt_expiry_enterprise_date = get_user_meta($user_id, 'wrpt_expiry_enterprise_date', true);	//KKK
	//echo "wrpt_expiry_enterprise_date : ".$wrpt_expiry_enterprise_date;

	if ($user_role ==  'academic_user') {
		$activation_date         = $user->user_registered;
		//$activation_date           = get_user_meta($user_id, 'wrpt_activation_date', true);
		$activation_date_obj_org   = new DateTime($activation_date);
		$next_activattion_date_obj = $activation_date_obj_org->modify('+365 days');
	}
	if ($user_role ==  'enterprise_user') {

		/* echo "<pre>";
		print_r($stripe_detail);
		print_r(($wrpt_expiry_enterprise_date));
		print_r($stripe_detail['value']['product_name']);
		echo "</pre>"; */

		if ($stripe_payment_date) {
			/* $activation_date         = $stripe_payment_date;
			$activation_date_obj_org = new DateTime($stripe_payment_date);
			$subscription_type = $stripe_detail['value']['product_name'] . " Subscription";
			if (strtolower($stripe_detail['value']['product_name']) == "monthly") {
				$next_activattion_date_obj = $activation_date_obj_org->modify('+30 days');
			} else {
				$next_activattion_date_obj = $activation_date_obj_org->modify('+365 days');
			} */
			$subscription_type        = $stripe_detail['plan_data']['plan_name'];
			$activation_date         = $stripe_detail['plan_data']['plan_activation_date'];
			$plan_expiry_date         = $stripe_detail['plan_data']['plan_expiry_date'];
			$next_activattion_date_obj = new DateTime($plan_expiry_date);
		} else {
			$activation_date         = $user->user_registered;
			$subscription_type = "Trial (7 Days)";
			$next_activattion_date_obj = new DateTime($activation_date);
			$next_activattion_date_obj->modify('+7 days');
		}
	}

	$intvl     = $current_date->diff($next_activattion_date_obj);

	/* echo "<pre>";
	echo  $next_activattion_date_obj->format('y-m-d');
	echo  $current_date->format('y-m-d');

	print_r($intvl);
	echo $intvl->format('%R%a Days');
	echo "</pre>"; */

	//$expiry_date = $next_activattion_date_obj->format('F j, Y, g:i a'); //F j, Y, g:i a
	$expiry_date = $next_activattion_date_obj->format('F j, Y'); //F j, Y, g:i a
	$days_diff = $intvl->format('%R%a')//$intvl->days;
	?>
	<a href="#" class='wr_change_pwd_btn'>
		<span class="elementor-button-text">Change Your Password?</span>
	</a>
	<div class="wr_change_pwd_wrapper">
		<form name="wr_change_pwd_frm" id="wr_change_pwd_frm" method="post" action="#">
			<h3 class="wr_box_title">Change Password </h3>
			<div class="wr_messagebox"></div>
			<div class="elementor-form-fields-wrapper">
				<div class="elementor-field-type-text elementor-field-group elementor-column elementor-field-group-name elementor-col-50 elementor-field-required elementor-mark-required">
					<label for="wrpt_current_password" class="elementor-field-label">Current Password</label>
					<input size="1" type="password" name="wrpt_current_password" id="wrpt_current_password" class="elementor-field elementor-size-sm  elementor-field-textual" required="required" aria-required="true">
				</div>
				<div class="elementor-field-type-text elementor-field-group elementor-column elementor-field-group-name elementor-col-50 elementor-field-required elementor-mark-required">
					<label for="wrpt_new_password" class="elementor-field-label">New Password</label>
					<input size="1" type="password" name="wrpt_new_password" id="wrpt_new_password" class="elementor-field elementor-size-sm  elementor-field-textual" required="required" aria-required="true">
				</div>
				<div class="elementor-field-type-text elementor-field-group elementor-column elementor-field-group-name elementor-col-50 elementor-field-required elementor-mark-required">
					<label for="wrpt_confirm_password" class="elementor-field-label">Confirm Password</label>
					<input size="1" type="password" name="wrpt_confirm_password" id="wrpt_confirm_password" class="elementor-field elementor-size-sm  elementor-field-textual" required="required" aria-required="true">
				</div>
				<div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100 e-form__buttons">
					<input type="submit" class="wr-elementor-button  wrpt_change_pwd_btn" value="Change Password" name='wrpt_change_pwd_btn' />
				</div>
			</div>
		</form>
	</div>
	<table>
		<tr>
			<th>Email</th>
			<th>Plan Details</th>
			<th>Days remain</th>
			<?php
			if ($user_role ==  'enterprise_user') {
			?>
				<th>Activation Status</th>
			<?php } ?>
		</tr>
		<tr>
			<td><?php echo $user_email ?></td>
			<td>
				<?php
				if ($user_role ==  'academic_user') {
				?>
					<ul>
						<li><b>Plan: Academic </b></li>
						<li><b>Activation Date :</b> <?php echo (new DateTime($activation_date))->format('F j, Y'); //$activation_date; ?></li>
						<li><b>Email :</b> <?php echo $user_email; ?> </li>
					</ul>
				<?php
				}
				if ($user_role ==  'enterprise_user') {
				?>
					<ul>
						<li><b>Plan: Commercial</b></li>
						<li><b>Plan Type:</b> <?php echo $subscription_type; ?></li>
						<li><b>Activation Date :</b> <?php echo (new DateTime($activation_date))->format('F j, Y'); //$activation_date; ?></li>
						<li><b>Expiry Date :</b> <?php echo $expiry_date; ?></li>
						<li><b>Email :</b> <?php echo $user_email; ?> </li>
					</ul>
				<?php
				}
				?>
			</td>
			<td><?php echo $days_diff > 0 ? ($days_diff + 1) : 0; ?></td>
			<?php
			if ($user_role ==  'enterprise_user') { ?>
				<td>
					<?php
					$activation_link = get_site_url() . '/enterprise-plan-payment?user_id=' . $user_id;
					if ($user_role ==  'enterprise_user') {
						if ($days_diff > 0 && $stripe_payment_date) {
							echo "Active";
						} else {
							?>
								Trial<br/>
								<a href='<?php echo $activation_link; ?>'>Pay for Subscription</a>
							<?php
						}
					}
					?>
				</td>
			<?php } ?>
		</tr>
	</table>
		<?php 
			if ($user_role ==  'enterprise_user') {
				if ($days_diff > 0) {
					?> <div class="fullstripe_customer_portal"> <?php 
					echo do_shortcode('[fullstripe_customer_portal authentication="Wordpress"]');
					?> </div> <?php 
				}
			}
		?>
		<div class="we_app_btn_wrapper">
			<!--<a class="wr_footer_sticky_botton" href="https://proteaintelligence.com/wp-content/uploads/2022/01/PROTEA-for-Revit-0.1.8006.0-Setup.exe_.zip">Download</a>-->
			<!--<a href="https://proteaintelligence.com/wp-content/uploads/2022/01/PROTEA-for-Revit-0.1.8006.0-Setup.exe_.zip" class="elementor-button-link elementor-button elementor-size-md wr-elementor-button wr-elementor-fill-button wr_float1">-->
			<a href="https://proteaintelligence.com/wp-content/uploads/safe_dir/PROTEA_for_Revit_0.1.8006.0_Setup.exe" class="elementor-button-link elementor-button elementor-size-md wr-elementor-button wr-elementor-fill-button wr_float1">
				<span class="elementor-button-content-wrapper">
					<span class="elementor-button-icon elementor-align-icon-left"> <i aria-hidden="true" class="fa fa-download"></i> </span>
					<span class="elementor-button-text">Download Revit Plugin</span>
				</span>
			</a>
			<a href="https://app.proteaintelligence.com/" class="elementor-button-link elementor-button elementor-size-md wr-elementor-button wr_float1" role="button">
				<span class="elementor-button-content-wrapper">
					<span class="elementor-button-text">Visit Our Web App</span>
				</span>
			</a>
			<!-- <a href="https://proteaintelligence.com/wp-content/uploads/safe_dir/PROTEA_for_Revit_0.1.8006.0_Setup.exe">Download</a> -->
		</div>
		
	<?php
	return ob_get_clean();
}
add_shortcode('wrpt_my_accont', 'wrpt_my_account');

//add_filter( 'wp_nav_menu_items', 'wrpt_add_login_logout_menu', 10, 2 );
function wrpt_add_login_logout_menu($items, $args)
{
	ob_start();
	wp_loginout('index.php');
	$loginoutlink = ob_get_contents();
	ob_end_clean();
	$items .= '<li>' . $loginoutlink . '</li>';
	return $items;
}

function wrpt_custom_cron_schedule($schedules)
{
	$schedules['every_week'] = array(
		'interval' => 21600, // Every 6 hours /*Seconds * minutes * hours * days */
		'display'  => __('Check Academy Plan Days'),
	);
	return $schedules;
}
//add_filter( 'cron_schedules', 'wrpt_custom_cron_schedule' );

// function for activation hook
function wr_enterprise_plan_notice_after_one_week()
{
	// check if scheduled hook exists
	if (!wp_next_scheduled('wr_enterprise_plan_notice')) {
		wp_schedule_event(time(), 'daily', 'wr_enterprise_plan_notice');
	}
}
add_action('init', 'wr_enterprise_plan_notice_after_one_week');

add_action('wr_enterprise_plan_notice', 'wr_enterprise_plan_notice');

// the code of your hourly event
function wr_enterprise_plan_notice()
{
	$enterprise_users = get_users(array(
		'role' => 'enterprise_user',
	));
	foreach ($enterprise_users as $euser) {
		$user_id = $euser->ID;
		$stripe_data = get_user_meta($user_id, 'wrpt_stripe_data');
		$registered = $udata->user_registered;
		$wruser_next_week_date = get_user_meta($euser->ID, 'wruser_next_week_date', true);
		$udata = get_userdata($euser->ID);

		if (!$stripe_data && $wruser_next_week_date) {
			$registered_date = new DateTime($registered);
			$dateinfuture = new DateTime($wruser_next_week_date);
			$interval = $registered_date->diff($dateinfuture);
			if ($interval <= 0) {
				$to = $euser->user_email;
				$subject = 'Follow-up on payment for the plan';
				$body = "Dear customer,<br/>";
				$body .= 'We hope you’re enjoying your free trial! Our records show that you have not yet paid for Protea yet. If you would like to learn more about our features, please visit this page. If you need any support, please email us at <a href="mailto: support@proteacorp.com">support@proteacorp.com</a>.';
				$body .= 'Please click here to <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
				$body = apply_filters('wr_payment_reminder_body_html', $body, $user_id );
				
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$test = wp_mail($to, $subject, $body, $headers);
			}
		}
		/*If user has not paid */
		if ($stripe_data) {
			$check_firsttime_payment = get_user_meta($euser->ID, 'wr_enterprice_user_payment_date');
			$registered = $udata->user_registered;
			$registered_date = new DateTime($registered);
			$current_date = new DateTime();

			if ($check_firsttime_payment) {
				$last_date = get_user_meta($euser->ID, 'wr_enterprice_user_payment_date', true);
				$futureDate = date('Y-m-d', strtotime('+1 year', strtotime($last_date)));
			} else {
				$futureDate = date('Y-m-d', strtotime('+1 year', strtotime($registered)));
			}
			$dateinfuture = new DateTime($futureDate);
			$interval = $current_date->diff($dateinfuture);
			/*Update current date to usermeta in order to check in future after user make payment after first time(user registered date will not work for second year)*/
			$cdate = date('Y-m-d H:i:s');
			if ($interval->days <= 0) {
				$to = $euser->user_email;
				$subject = 'Pay for the plan';
				$body = "Dear customer,<br/>";
				$body .= 'We hope you’re enjoying your free trial! Our records show that you have not yet paid for Protea yet. If you would like to learn more about our features, please visit this page. If you need any support, please email us at <a href="mailto: support@proteacorp.com">support@proteacorp.com</a>.';
				$body .= 'Please click here to <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
				$body = apply_filters('wr_payment_reminder_body_html', $body, $user_id );
				
				$headers = array('Content-Type: text/html; charset=UTF-8');
				//$test = wp_mail( $to, $subject, $body, $headers );
				//$user_msg = $user_msg . ' sentagain '.$user_id;
			}
		} else {
			$user_email = $euser->user_email;
			$to = $user_email;
			$subject = 'Pay for the plan';
			$body = "Dear customer,<br/>";
			$body .= 'We hope you’re enjoying your free trial! Our records show that you have not yet paid for Protea yet. If you would like to learn more about our features, please visit this page. If you need any support, please email us at <a href="mailto: support@proteacorp.com">support@proteacorp.com</a>.';
			$body .= 'Please click here to <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
			$body = apply_filters('wr_payment_reminder_body_html', $body, $user_id );
			
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$test = wp_mail($to, $subject, $body, $headers);
		}
	}
}

//add_action( 'init', 'wr_test_mail' );
function wr_test_mail(){
	if(isset($_GET['wrmail'])){
		echo "<h2>HELO DEBUG START</h2>";
		$subject = 'Pay for the plan';
		$body = apply_filters('wr_payment_reminder_body_html', "HELLO WORLD", 111 );
		//$body = wr_payment_reminder_body_change_html("HELLO WORLD", 111);
		$to = "webdev@clikitnow.com";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$test = wp_mail($to, $subject, $body, $headers);
		echo "<h2>STOP DEBUG START</h2>";
		exit;
	}
}
//add_action( 'init', 'testaa' );
/* function testaa()
{
	if (isset($_GET['exp'])) {
		$enterprise_users = get_users(array(
			'role' => 'enterprise_user',
		));
		foreach ($enterprise_users as $euser) {
			$user_id = $euser->ID;
			$stripe_data = get_user_meta($user_id, 'wrpt_stripe_data');
			$registered = $udata->user_registered;
			$wruser_next_week_date = get_user_meta($euser->ID, 'wruser_next_week_date', true);
			$udata = get_userdata($euser->ID);

			if (!$stripe_data && $wruser_next_week_date) {
				echo "weekly notive <br>";
				$registered_date = new DateTime($registered);
				$dateinfuture = new DateTime($wruser_next_week_date);
				$interval = $registered_date->diff($dateinfuture);
				if ($interval <= 0) {
					$to = $euser->user_email;
					$subject = 'Pay for the plan';
					$body = 'Please click on below link to make payment <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
					$headers = array('Content-Type: text/html; charset=UTF-8');
					//$test = wp_mail( $to, $subject, $body, $headers );
				}
			}
			/// *If user has not paid 
			if ($stripe_data) {
				$check_firsttime_payment = get_user_meta($euser->ID, 'wr_enterprice_user_payment_date');
				$registered = $udata->user_registered;
				$registered_date = new DateTime($registered);
				$current_date = new DateTime();

				if ($check_firsttime_payment) {
					$last_date = get_user_meta($euser->ID, 'wr_enterprice_user_payment_date', true);
					$futureDate = date('Y-m-d', strtotime('+1 year', strtotime($last_date)));
				} else {
					$futureDate = date('Y-m-d', strtotime('+1 year', strtotime($registered)));
				}
				$dateinfuture = new DateTime($futureDate);
				$interval = $current_date->diff($dateinfuture);
				//Update current date to usermeta in order to check in future after user make payment after first time(user registered date will not work for second year)
				$cdate = date('Y-m-d H:i:s');
				if ($interval->days <= 0) {
					$to = $euser->user_email;
					$subject = 'Pay for the plan';
					$body = 'Please click on below link to make payment <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
					$headers = array('Content-Type: text/html; charset=UTF-8');
					//$test = wp_mail( $to, $subject, $body, $headers );
					//$user_msg = $user_msg . ' sentagain '.$user_id;
				}
			} else {
				$user_email = $euser->user_email;
				$to = $user_email;
				$subject = 'Pay for the plan';
				$body = 'Please click on below link to make payment <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
				$headers = array('Content-Type: text/html; charset=UTF-8');
				//$test = wp_mail( $to, $subject, $body, $headers );
			}
		}
		die();
	}
} */
function wr_footer_button()
{
	if (is_user_logged_in()) {
	?>
		<div>
			<!--<a class="wr_footer_sticky_botton" href="https://proteaintelligence.com/wp-content/uploads/2022/01/PROTEA-for-Revit-0.1.8006.0-Setup.exe_.zip">Download</a>-->
			<a href="https://proteaintelligence.com/wp-content/uploads/2022/01/PROTEA-for-Revit-0.1.8006.0-Setup.exe_.zip" class="wr_float">
				Download APP
				<i class="fa fa-download"></i>
			</a>
		</div>
	<?php
	}
}
//zxadd_action( 'wp_footer', 'wr_footer_button' );

function wr_add_user_meta_on_signup($user_id)
{
	$udata = get_userdata($user_id);
	$registered = $udata->user_registered;
	$dateafter7 = date('Y-m-d', strtotime('+7 day', strtotime($registered)));
	update_user_meta($user_id, 'wruser_next_week_date', $dateafter7);
}
add_action('user_register', 'wr_add_user_meta_on_signup');

function wr_enter_user()
{
	ob_start();
	$enterprise_users = get_users(array(
		'role' => 'enterprise_user',
		'orderby' => 'registered',
		'order' => 'DESC'
	));
	?>
	<table>
		<?php
		foreach ($enterprise_users as $euser) {
			$user_id = $euser->ID;
			$stripe_data = get_user_meta($user_id, 'wrpt_stripe_data');
			$registered = $udata->user_registered;
			$wruser_next_week_date = get_user_meta($euser->ID, 'wruser_next_week_date', true);
			$udata = get_userdata($euser->ID);
		?>
			<tr>
				<td><?php echo $user_id; ?></td>
				<td><?php echo $euser->user_email; ?></td>
				<td>
					<?php //$form_id == '916' 
					if (!$stripe_data && $wruser_next_week_date) {
						echo "User in in weekly notice period";
						$registered_date = new DateTime($registered);
						$dateinfuture = new DateTime($wruser_next_week_date);
						$interval = $registered_date->diff($dateinfuture);
						echo "Days Remain=" . $interval->days;
					} else {
						if ($stripe_data) {
							$check_firsttime_payment = get_user_meta($euser->ID, 'wr_enterprice_user_payment_date');
							$registered = $udata->user_registered;
							$registered_date = new DateTime($registered);
							$current_date = new DateTime();

							if ($check_firsttime_payment) {
								$last_date = get_user_meta($euser->ID, 'wr_enterprice_user_payment_date', true);
								$futureDate = date('Y-m-d', strtotime('+1 year', strtotime($last_date)));
							} else {
								$futureDate = date('Y-m-d', strtotime('+1 year', strtotime($registered)));
							}
							$dateinfuture = new DateTime($futureDate);
							$interval = $current_date->diff($dateinfuture);
						}
						echo " User in in yearly notice period ";
						echo " Days remain = " . $interval->days;
					}
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</table>
<?php
	return ob_get_clean();
}
add_shortcode('wr_enter_user', 'wr_enter_user');

// Update CSS within in Admin
function wr_admin_login_style() {
	echo "<style>
			.login #nav, .login #nav a {
				background-color: #fff;
				box-shadow: 0px 1px 0px 0px rgb(0 0 0 / 13%);
				margin-top: -20px;
			}
			.login #backtoblog, .login #backtoblog a {
				background-color: #fff;
				box-shadow: 0px 1px 0px 0px rgb(0 0 0 / 13%);
				margin-top: 0;
				padding-top: 16px;
				padding-bottom: 16px;
			}
	</style>";
}
add_action('login_form', 'wr_admin_login_style');


function rs_dwnld() {
	if( isset( $_GET['protea_app'] ) ) {
		if( is_user_logged_in(  ) ) {

			$path= WP_CONTENT_DIR . '/uploads/safe_dir/';
			//wp_redirect(home_url());
			$filetype="exe";

			//Get the requested file
			$file = get_option( 'wrpr_uploaded_file', true );//'PROTEA.exe';//$_GET['file'];
			$filePath = $path . $file;
			//Stop the script if file selected is invalid
			if( (!file_exists( $path . $file ) ) ) {
				die( "File wasnt set or it didnt exist" );
			}
			$type = mime_content_type($filePath);			
			if( file_exists( $filePath ) ){
				// Define headers
				
				header("Content-Disposition: attachment; filename=\"$file\"");
				header("Content-Type: application/x-dosexec");
				// Read the file
				readfile($filePath);
				exit;
			}
			
			//echo $filename;die();
			//readfile( $path . $file );
			
			die();
		}  else {
			wp_redirect( wp_login_url() );
		}
	}
	
}
add_action( 'init' ,'rs_dwnld' );



/**
 * Create option page.
 */
function wrpt_exe_file_location() {
	add_menu_page(
		'Exe File Option',
		'Exe File Option',
		'manage_options',
		'wrpt-exe-file-location',
		'wrpt_exe_option_page',
		'dashicons-star-half',
		5
	);
}
add_action( 'admin_menu', 'wrpt_exe_file_location' );

function listFolderFiles($dir){
    $ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    // prevent empty ordered elements
    if (count($ffs) < 1)
        return;

    echo '<ol class="dir">';
    foreach($ffs as $ff){
        echo '<li>'.$ff;
        if(is_dir($dir.'/'.$ff)) {
			listFolderFiles($dir.'/'.$ff);
		}
        echo '</li>';
    }
    echo '</ol>';
}

/**
 * Option page Form
 */
function wrpt_exe_option_page() {
	?>
	
	<?php
	$auth_token = get_option( 'wrpt_api_auth_token' );
	$path    = WP_CONTENT_DIR . '/uploads/safe_dir';
	
	?>
	<div class="wrpt_file_list">
	<?php
	//listFolderFiles($path);
	?>
	</div>
	<?php
	if ( isset( $_POST['wrpt_option_button'] ) ) {
		$file_tmp =$_FILES['wrpt_exe_file']['tmp_name'];
		
		$test = move_uploaded_file($file_tmp, $path.'/'.$_FILES['wrpt_exe_file']['name']);
		if( $test ) {
			update_option('wrpr_uploaded_file',$_FILES['wrpt_exe_file']['name']);
			?>
			<div class="notice notice-success is-dismissible">
				<p>File Uploaded Succesfully</p>	
			</div>
			
			<?php
		} else {
			echo "File Not Uploaded";
		}
	}
	?>
	<form action="#" method="post" enctype="multipart/form-data">

		<table>
			<tr>
				<td><b>Select File Path</b></td>
				<td>
					<input type="file" name='wrpt_exe_file' id="wrpt_exe_file"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php
						submit_button( '', 'primary', 'wrpt_option_button', );
					?>
				</td>
			</tr>
		</table>
		<style>
			.dir{
				
			}
		</style>
		<script>
			jQuery(document).ready(function($){
				$('#wrpt_exe_file').change(function (event) {
					var tmppath = URL.createObjectURL(event.target.files[0]);
    				$("img").fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));
					console.log(tmppath);
				});
				$('.dir').click(function() {
					$(this).children().slideToggle();
				});
			})
		</script>
	</form>
	<?php
}

add_filter('wr_payment_reminder_body_html','wr_payment_reminder_body_change_html', 10, 2 );
function wr_payment_reminder_body_change_html( $body, $user_id ){
	$body = "Dear customer,<br/>";
	$body .= 'We hope you’re enjoying your free trial! Our records show that you have not yet paid for Protea yet. 
	If you would like to learn more about our features, please visit this page. If you need any support, 
	please email us at <a href="mailto: support@proteaintelligence.com">support@proteacorp.com</a>.';
	$body .= 'Please click here to <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id=' . $user_id . '">Pay</a>';
	$headers = array('Content-Type: text/html; charset=UTF-8');	
	return $body;
}