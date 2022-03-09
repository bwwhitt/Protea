<?php

//API Documents
//http://protea-api-staging.azurewebsites.net/swagger/ui/index#!/Account/Account_GetAllUsers
//http://protea-api.azurewebsites.net/swagger/ui/index#!/Account/Account_UpdateUser_0

//if (!defined('AZURE_API_BASE_URL')) define('AZURE_API_BASE_URL', 'http://protea-api-staging.azurewebsites.net'); //Staging SITE BASE URL API 
if (!defined('AZURE_API_BASE_URL')) define('AZURE_API_BASE_URL', 'http://protea-api.azurewebsites.net'); //Live SITE BASE URL API 


add_action('forminator_activate_user', 'wr_forminator_activate_user',99,2);

function wr_forminator_activate_user( $userID, $signup_meta ) {
        
        $debug = false; // always false;

        $form_id = isset($signup_meta['form_id']) ? $signup_meta['form_id'] : 0;

        if ($form_id == '894' || $form_id == '899') {

            $api_data = array();
            $entry_id = $signup_meta['entry_id'];
            $email = $signup_meta['user_data']['user_email'];
            $current_date = date('Y-m-d H:i:s');
            $userdata = get_userdata($userID);

            switch ($form_id) {
                case '894': /**Academy Form */

                    $academy_expiration_date = date('Y-m-d', strtotime('+365 days', strtotime($current_date)));

                    update_user_meta($userID, 'prt_plan_id', $GLOBALS['acd_plan_id'] );
                    update_user_meta($userID, 'wrpt_activation_date', $current_date);
                    update_user_meta($userID, 'wrpt_expiry_date', $academy_expiration_date );

                    $api_data['UserType'] = 1;
                    //$api_data['Plan'] = 1; 
                    $api_data['LicenseExpiry'] = date('Y-m-d\TH:i:s\Z', strtotime('+365 days', strtotime($current_date) ) ); //$academy_expiration_date;
                    /* $user_name = trim($userdata->data->user_login);
                    $last_name = (strpos($user_name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $user_name);
                    $first_name = trim( preg_replace('#'.preg_quote($last_name,'#').'#', '', $user_name ) );
                    $api_data['FirstName'] = $first_name;
                    $api_data['LastName'] = $last_name; */
                break;

                case '899': /**Enterprice Form */

                    //$form_data = Forminator_API::get_entry( $form_id, $entry_id );
                    //echo "<h2>ACTIVATE DATE : </h2>";print_r($current_date);

                    $tial_expiration_date = date('Y-m-d', strtotime('+7 day', strtotime($current_date)));
                    update_user_meta($userID, 'prt_plan_id', $GLOBALS['enter_plan_id']);
                    update_user_meta($userID, 'wruser_next_week_date', $tial_expiration_date);
                    update_user_meta($userID, 'wrpt_expiry_date', $tial_expiration_date );

                    $api_data['UserType'] = 2;
                    //$api_data['Plan'] = 2; 
                    $api_data['LicenseExpiry'] = date('Y-m-d\TH:i:s\Z', strtotime('+7 day', strtotime($current_date))); //"2022-01-20T00:00:00.670Z";
                    
                    $user_name = trim($userdata->data->user_login);
                    $last_name = (strpos($user_name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $user_name);
                    $first_name = trim( preg_replace('#'.preg_quote($last_name,'#').'#', '', $user_name ) );
                    $api_data['FirstName'] = $first_name;
                    $api_data['LastName'] = $last_name;
                    //echo "<h2>TRIAL DATE :: </h2>";
                    //print_r($tial_expiration_date);
                    /* echo "<pre>";
                    print_r($userdata );
                    echo "</pre>"; */

                default:                    
                break;
            } 

            $api_data['Email'] = $email;
            $api_data['OrganizationName'] = $email;   
           
            //update_user_meta($userID, 'wruser_pass', "user123");
            $user_pass = get_user_meta($userID, 'wruser_pass', true);
            //echo "<h2>USER PASS : </h2>";
            //ar_dump($user_pass);
            $api_data['Password']  = $user_pass!= "" ? $user_pass : $_POST['password-1'];
            delete_user_meta($userID,'wruser_pass');             
            
            $url                   =  AZURE_API_BASE_URL . '/register';  ///'http://protea-api-staging.azurewebsites.net/register';
            echo "Before API data";
            echo "<pre>";
            print_r($api_data);
           
            $args = array(
                'method' => 'POST',
                'body'   => json_encode($api_data),
                'headers' => array(
                    'content-type' => 'application/json'
                ),
            );            
            if($debug){
                echo "<h2>API DATA : </h2>"; print_r($api_data);
                echo "<h2>URL : </h2>"; print_r($url);
                echo "<h2>args : </h2>"; print_r($args);
            }

            $request = wp_remote_post($url, $args);

            if($debug){
                echo "<h2>REQUEST : </h2>"; print_r($request);
            }

            if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
                error_log(print_r($request, true));
            }
            $response = wp_remote_retrieve_body($request);
            error_log(print_r($response, true));

           
            if($debug){
                echo "<h2>RESPONSE : </h2>"; print_r($response);
                echo "</pre>";
                echo "<h1>STOP FORM DATA</h1>";
                exit;
            }
        }
    
}

add_action( 'forminator_cform_user_registered', 'wr_forminator_cform_user_registered', 99, 5 );
function wr_forminator_cform_user_registered( $user_id, $custom_form, $entry, $user_pass, $submitted_data ) {
    update_user_meta($user_id, 'wruser_pass', $user_pass);
}

//WP Stripe Full Plugins Hooks list
//https://paymentsplugin.com/kb/integrating-wp-full-stripe-with-other-systems
add_action('fullstripe_before_subscription_charge', 'wr_full_stripe_before_inline_subscription_charge', 99, 1 );
function wr_full_stripe_before_inline_subscription_charge( $params ) {

    if(isset($params['formName']) && $params['formName'] == 'enterprise-plan-payment') {
        
    
        if( isset( $params['urlParameters'] ) && isset( $params['urlParameters']['user_id'] ) ) {
            $userdata = get_userdata( $params['urlParameters']['user_id'] );
        }else if(isset($params['email']) ) {
            $userdata  = get_user_by( 'email', trim($params['email']) );
        }

        $user_id = $userdata->ID;
        $email = $userdata->data->user_email;
        //Live Plan IDs
        $monthly_plan_ids = array('price_1KK5UtAHrMBxesFqXPGzpyNS', 'price_1KK0bPAHrMBxesFqPoWfYvzy');
        $yearly_plan_ids = array('price_1KK5UeAHrMBxesFqMpjAiypQ', 'price_1KK5R9AHrMBxesFqhahgHz4p');
       
		
        $strip_data = $_POST;
        $plan_activation_date = date('Y-m-d H:i:s');
        $plan_activation_date_obj = new DateTime($plan_activation_date);
        
        
		update_user_meta($user_id, 'wrpt_activation_enterprise_date', $plan_activation_date_obj->format('F j, Y, g:i a'));
        update_user_meta($user_id, 'wr_enterprice_user_payment_date', $plan_activation_date);

		if ( in_array($params['planId'], $monthly_plan_ids) ) {

            $plan_slug = "monthly";
            $plan_name = "Monthly Subscription Plan";
			$expiry_date_obj = $plan_activation_date_obj->modify('+30 days');

		} else if ( in_array($params['planId'], $yearly_plan_ids) ) {

            $plan_slug = "yearly";
            $plan_name = "Yearly Subscription Plan";
			$expiry_date_obj = $plan_activation_date_obj->modify('+365 days');

		} else {

            $expiry_date_obj = $plan_activation_date_obj->modify('+1 days');
            error_log(print_r($_POST, true));

        }

        $plan_expiry_date = $expiry_date_obj->format('Y-m-d H:i:s');
        $strip_data['plan_data']= array(
            'plan_id' => $params['planId'],
            'plan_slug' => $plan_slug,
            'plan_name' => $plan_name,
            'plan_activation_date' => $plan_activation_date,
            'plan_expiry_date' => $plan_expiry_date,
        );

        $strip_data['value']['product_name'] = $plan_slug; //just tempraly used

        update_user_meta($user_id, 'wrpt_stripe_data', $strip_data);		
		update_user_meta($user_id, 'wrpt_expiry_enterprise_date', $plan_expiry_date );
		
		$api_data['IsPaid']  = true;
		$api_data['ExpirationDate']  = $expiry_date_obj->format('Y-m-d\TH:i:s\Z');

		$url                   = AZURE_API_BASE_URL . '/user/'.trim($email).'/paid'; //'https://protea-api-staging.azurewebsites.net/user/'.trim($email).'/paid';
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


//do_actions('wr_porte_api_password_change', $password, $email);
add_action('wr_porte_api_password_change', 'wr_porte_password_change', 10, 2);
function wr_porte_password_change( $new_password, $email ){

    $debug = false; // always false;
 
    $api_data['NewPassword']  = trim($new_password);
    $url                   = AZURE_API_BASE_URL . '/user/'.trim($email).'/password';
    $args = array(
        'method' => 'POST',
        'body'   => json_encode($api_data),
        'headers' => array(
            'content-type' => 'application/json'
        ),
    );

    if($debug) {
        echo "<h2>API DATA : </h2>"; print_r($api_data);
        echo "<h2>URL : </h2>"; print_r($url);
        echo "<h2>args : </h2>"; print_r($args); 
    }

    $request = wp_remote_post($url, $args);
    if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
        error_log(print_r($request, true));
    }

    if($debug) {
        echo "<h2>REQUEST : </h2>"; print_r($request); 
    }

    $response = wp_remote_retrieve_body($request);
    error_log(print_r($response, true));

    if($debug) {
        echo "<h2>RESPONSE : </h2>"; print_r($response);
        echo "</pre>";
        echo "<h1>STOP FORM DATA</h1>";
    }
}

add_action( 'password_reset', 'wr_password_reset',99, 2);
function wr_password_reset( $user, $pass ) 
{
    if(in_array('enterprise_user', $user->roles) || in_array('academic_user', $user->roles)) {
        do_action('wr_porte_api_password_change', trim($pass), $user->data->user_email);
    }
}

//add_action( 'init', 'wr_test' );
function wr_test()
{
	if (isset($_GET['wrtest'])) {
        //if(isset($_GET['teststep']))
        echo "<h2>START DEBUG WR TEST</h2><pre>";

        $url                   = AZURE_API_BASE_URL . '/users/'; //'https://protea-api-staging.azurewebsites.net/user/'.trim($email).'/paid';
		$args = array(
			'method' => 'GET',
			'headers' => array(
				'content-type' => 'application/json'
			),
		);


        //echo "<h2>API DATA : </h2>"; print_r($api_data);
        echo "<h2>URL : </h2>"; print_r($url);
        echo "<h2>args : </h2>"; print_r($args); 

		$request = wp_remote_post($url, $args);
		if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
			error_log(print_r($request, true));
		}

        echo "<h2>REQUEST : </h2>"; print_r($request); 

		$response = wp_remote_retrieve_body($request);
		error_log(print_r($response, true));
        $response = json_decode($response);
        echo "<h2>TOTAL RECORED FOUND : ".count($response)."</h2>";
        echo "<h2>RESPONSE : </h2>"; print_r($response);
        echo "</pre>";
		echo "<h3>STOP DEBUG WR TEST</h3>";
		die();
	}
}
/* function wpdocs_check_user_password_updated( $user_id, $old_user_data ) {
    echo "<h2>DEBUG PASSWORD PROFILESSS</h2>";
    echo "<pre>";
    print_r($user_id);
    print_r($old_user_data);
    print_r($_REQUEST);
    echo "</pre>";
    exit;
    if(isset($_POST['from']) && $_POST['from'] == profile) {
        if(isset($_POST['role']) && ($_POST['role'] == 'enterprise_user' || $_POST['role'] == 'academic_user') ){
            if(isset($_POST['pass1']) && isset($_POST['pass2']) && !empty($_POST['pass1']) ){
            do_action('wr_porte_api_password_change', trim($_POST['pass1']), $old_user_data->data->user_email);
        }
    }
}
//add_action( 'profile_update', 'wpdocs_check_user_password_updated', 10, 2 );*/

add_action( 'init', 'wr_test_api' );
function wr_test_api() {
    if( isset( $_GET['wr_test_api'] ) ) {
        $url = AZURE_API_BASE_URL . '/register';
        $api_data = array(
            'UserType' => 1,
            'LicenseExpiry' => '2023-03-03T10:40:43Z',
            'Email' => 'afteracakow37605@votooe.com',
            'OrganizationName' => 'aftercakow37605@votooe.com',
            'Password' => 'passtestadter'
        );
        $args = array(
            'method' => 'POST',
            'body'   => json_encode($api_data),
            'headers' => array(
                'content-type' => 'application/json'
            ),
        );  
        echo "<pre>";
        $request = wp_remote_post($url, $args);

        if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
            var_dump(wp_remote_retrieve_response_code($request));
        }
        $response = wp_remote_retrieve_body($request);
        error_log(print_r($response, true));   
        echo "<h2>RESPONSE : </h2>"; print_r($response);        
        echo "<h1>STOP FORM DATA</h1>";
        exit;
    }
}

add_filter( 'http_request_timeout', 'wp9838c_timeout_extend' );

function wp9838c_timeout_extend( $time ) {
    // Default timeout is 5
    return 100;
}