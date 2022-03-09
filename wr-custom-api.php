<?php

/**
 * Custom Endpoints for users listing.
 */
function wrpt_cusotm_endpoints() {
	register_rest_route(
		'prote/v1',
		'/user/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'wrpt_get_user_callback',
			'permission_callback' => 'wrpt_api_authentication',
		)
	);
	register_rest_route(
		'prote/v1',
		'/user/(?P<email>[\da-zA-Z\@\.-]+)/edit',
		array(
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => 'wrpt_update_user_callback',
			//'permission_callback' => 'wrpt_api_authentication',
			'args'      => array(
				'email'     => array(
					'validate_callback' => function($parameter, $request, $key) {
						return filter_var($parameter, FILTER_VALIDATE_EMAIL);
					},
					'required' => true
				),
				'password'     => array(
					'required' => true
				)
			)
		)
	);
	register_rest_route(
		'prote/v1',
		'/get_all_users/',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'wrpt_get_all_users_callback',
			'permission_callback' => 'wrpt_api_authentication',
		)
	);
	register_rest_route(
		'prote/v1',
		'/get_all_user_roles/',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'wrpt_get_all_user_role_callback',
			'permission_callback' => 'wrpt_api_authentication',
		)
	);
	register_rest_route(
		'prote/v1',
		'/check_academy_status/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'wrpt_check_academy_status',
			'permission_callback' => 'wrpt_api_authentication',
		)
	);
	register_rest_route(
		'prote/v1',
		'/check_plan_status/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'wrpt_plan_status',
			'permission_callback' => 'wrpt_api_authentication',
		)
	);
}
add_action( 'rest_api_init', 'wrpt_cusotm_endpoints' );


/**
 * API callback For Getting Use By ID.
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_get_user_callback( WP_REST_Request $request ) {
	$user_id                   = $request['id'];
	$current_user              = get_user_by( 'ID', $user_id );
	$plan_id                   = get_user_meta( $user_id, 'prt_plan_id', true );
	$activation_date           = get_user_meta( $user_id, 'wrpt_activation_date', true );
	$activation_status         = ( $activation_date ) ? 'Active' : 'Inactive';
	$activation_date_formatted = date( 'D, jS F Y', $activation_date );
	$activation_date_obj_org   = new DateTime( $activation_date );
	$current_date              = new DateTime();
	if ( $plan_id == $GLOBALS['acd_plan_id'] ) {
		$current_date              = new DateTime();
		$activation_date_obj_org   = new DateTime( $activation_date );
		$next_activattion_date_obj = $activation_date_obj_org->modify( '+1 year' );
		$intvl                     = $next_activattion_date_obj->diff( $current_date );
		$days_diff                 = $intvl->days;
	}
	if ( $plan_id == $GLOBALS['enter_plan_id'] ) {
		$current_date              = new DateTime();
		$activation_date_obj_org   = new DateTime( $current_user->user_registered );
		$next_activattion_date_obj = $activation_date_obj_org->modify( '+7 days' );
		$intvl                     = $next_activattion_date_obj->diff( $current_date );
		$days_diff                 = $intvl->days;
	}
	$plan_title = get_the_title( $plan_id );

	$tmp_arr['user_id']                   = $user_id;
	$tmp_arr['user_name']                 = $current_user->display_name;
	$tmp_arr['user_email']                = $current_user->user_email;
	$tmp_arr['user_registered']           = $current_user->user_registered;
	$tmp_arr['user_activation_date']      = $activation_date_formatted;
	$tmp_arr['user_next_activation_date'] = $next_activattion_date_formatted;
	$tmp_arr['activation_status']         = $activation_status;
	$tmp_arr['days_remain']               = $days_diff;
	$tmp_arr['user_plan']                 = $plan_title;
	$allusers_data[]                      = $tmp_arr;

	$result = new WP_REST_Response( $allusers_data );
	$result->set_headers( array( 'Cache-Control' => 'no-cache' ) );
	return $result;
}

/**
 * API callback For Getting Use By ID.
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_update_user_callback( WP_REST_Request $request ) {
	/* echo "<h2>UPDATE USER API </h2>"; */
	$data['flag'] = false;
	$data['status'] = "ERROR";
	$email  = trim($request['email'] );
	$user_data = get_user_by( 'email', $email );

	if($user_data ) {
		if(in_array('enterprise_user', $user_data->roles) || in_array('academic_user', $user_data->roles)) {
			/* echo "<pre>";print_r($user_data);echo "</pre>"; */
			$user_id = $user_data->ID;

			$password = trim($request->get_param('password'));
			if( strlen($password) >= 6  ) {
				/* echo "USER ID : ".$user_id;
				echo "Password : ".$password; */
				wp_set_password( $password, $user_id );
				$data['flag'] = true;
				$data['status'] = "SUCCESS";
				$data['message'] = "Successfully updated user password.";
			} else {
				$data['message'] = "Invalid Password. Password Must Contain At Least 6 Characters!";
			}
		} else {
			$data['message'] = "User not found..";
		}
	} else {
		$data['message'] = "User not found.";
	}
	
	$result = new WP_REST_Response( $data );
	$result->set_headers( array( 'Cache-Control' => 'no-cache' ) );
	return $result;
}


/**
 * API Callback For All USers
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_get_all_users_callback( WP_REST_Request $request ) {
	$allusers_data = array();
	$creds         = array();
	$page          = isset( $request['page'] ) ? $request['page'] : 1;
	$orderby       = isset( $request['orderby'] ) ? $request['orderby'] : 'ID';
	$order         = isset( $request['order'] ) ? $request['order'] : 'ASC';
	$per_page      = isset( $request['limit'] ) ? $request['limit'] : 25;
	$search        = isset( $request['search'] ) ? $request['search'] : '';
	$roles         = isset( $request['roles'] ) ? explode( ',', $request['roles'] ) : '';
	$user_id       = isset( $request['user_id'] ) ? explode( ',', $request['user_id'] ) : '';
	$plan          = isset( $request['plan'] ) ? $request['plan'] : false;
	if ( 'Academy' === $plan ) {
		$plan_id = $GLOBALS['acd_plan_id'];
	}
	if ( 'Enterprise' === $plan ) {
		$plan_id = $GLOBALS['enter_plan_id'];
	}

	$headers                = $request->get_headers();
	$creds['user_login']    = $headers['user_login'];
	$creds['user_password'] = $headers['user_password'];
	$creds['remember']      = false;
	$user                   = wp_signon( $creds, false );  // Verify the user.

	$args = array(
		'fields' => array( 'ID' ),
		'number' => $per_page,
		'paged'  => $page,
	);
	if ( $roles ) {
		$args['role__in'] = $roles;
	}
	if ( $user_id ) {
		$args['include'] = $user_id;
	}
	if ( $orderby ) {
		$args['orderby'] = $orderby;
	}
	if ( $order ) {
		$args['order'] = $order;
	}
	if ( $search ) {
		$args['search']         = '*' . $search . '*';
		$args['search_columns'] = array( 'ID', 'user_login', 'user_nicename', 'user_email', 'user_url' );
	}
	if ( $plan ) {
		$args['meta_query'] = array(
			array(
				'relation' => 'AND',
				array(
					'key'   => 'prt_plan_id',
					'value' => $plan_id,
				),
			),
		);
	}
	$users = get_users( $args );
	foreach ( $users as $user ) {
		$user_id = $user->ID;

		$current_user = get_user_by( 'ID', $user->ID );
		$plan_id      = get_user_meta( $user_id, 'prt_plan_id', true );
		if ( $plan_id == $GLOBALS['acd_plan_id'] ) {
			$activation_date                 = get_user_meta( $user_id, 'wrpt_activation_date', true );
			$activation_status               = ( $activation_date ) ? 'Active' : 'Inactive';
			$activation_date_formatted       = date( 'D, jS F Y', $activation_date );
			$next_activattion_date           = strtotime( '+1 years' );
			$activation_date_obj_org         = new DateTime( $activation_date );
			$current_date                    = new DateTime();
			$next_activattion_date_obj       = $activation_date_obj_org->modify( '+1 year' );
			$intvl                           = $next_activattion_date_obj->diff( $current_date );
			$days_diff                       = $intvl->days;
			$next_activattion_date_formatted = date( 'D, jS F Y', $next_activattion_date );
		} elseif ( $plan_id == $GLOBALS['enter_plan_id'] ) {
			$stripe_payment_date             = get_user_meta( $user_id, 'wrpt_activation_enterprise_date', true );
			$udata                           = get_userdata( $user->ID );
			$activation_date_formatted       = date( 'D, jS F Y', strtotime( $udata->user_registered ) );
			$next_activattion_date           = strtotime( '+7 days' );
			$next_activattion_date_formatted = date( 'D, jS F Y', $next_activattion_date );
			$activation_date                 = $current_user->user_registered;
			$activation_date_obj_org         = new DateTime( $activation_date );
			$activation_status               = 'Trial';
			$current_date = new DateTime();
			if ( $stripe_payment_date ) {
				$next_activattion_date_obj = $activation_date_obj_org->modify( '+365 days' );
			} else {
				$next_activattion_date_obj = $activation_date_obj_org->modify( '+7 days' );
			}
			$intvl     = $next_activattion_date_obj->diff( $current_date );
			$days_diff = $intvl->days;
		}
		$plan_title                 = get_the_title( $plan_id );
		$tmp_arr['user_id']         = $user->ID;
		$tmp_arr['user_name']       = $current_user->display_name;
		$tmp_arr['user_email']      = $current_user->user_email;
		$tmp_arr['user_registered'] = $current_user->user_registered;
		$tmp_arr['user_role']       = $current_user->roles;
		if ( $plan_id ) {
			$tmp_arr['activation_status']         = $activation_status;
			$tmp_arr['user_activation_date']      = $activation_date_formatted;
			$tmp_arr['user_next_activation_date'] = $next_activattion_date_formatted;
			$tmp_arr['days_remain']               = $days_diff;
			$tmp_arr['user_plan']                 = $plan_title;
		}
		$allusers_data[] = $tmp_arr;
	}
	$result = new WP_REST_Response( $allusers_data );
	$result->set_headers( array( 'Cache-Control' => 'no-cache' ) );
	return $result;
}

/**
 * Create plan post type.
 */
function wrpt_protea_plan_init() {
	$labels = array(
		'name'               => 'Protea Plan',
		'singular_name'      => 'Protea Plan',
		'add_new'            => 'Add New Protea Plan',
		'add_new_item'       => 'Add New Protea Plan',
		'edit_item'          => 'Edit Protea Plan',
		'new_item'           => 'New Protea Plan',
		'all_items'          => 'All Protea Plan',
		'view_item'          => 'View Protea Plan',
		'search_items'       => 'Search Protea Plan',
		'not_found'          => 'No Protea Plan Found',
		'not_found_in_trash' => 'No Protea Plan found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Protea Plan',
	);

	$args = array(
		'labels'          => $labels,
		'public'          => true,
		'has_archive'     => true,
		'show_ui'         => true,
		'capability_type' => 'post',
		'hierarchical'    => false,
		'rewrite'         => array( 'slug' => 'Protea Plan' ),
		'query_var'       => true,
		'menu_icon'       => 'dashicons-randomize',
		'supports'        => array(
			'title',
			'editor',
			'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			'revisions',
			'thumbnail',
			'author',
			'page-attributes',
		),
	);
	register_post_type( 'protea_plan', $args );
}
add_action( 'init', 'wrpt_protea_plan_init' );

/**
 * Hide Plan Post type from admni menu
 */
function wrpt_hide_plan_post_type() {
	remove_menu_page( 'edit.php?post_type=protea_plan' );
}
add_action( 'admin_menu', 'wrpt_hide_plan_post_type' );


/**
 * Create option page.
 */
function wrpt_options_page() {
	add_menu_page(
		'Authentication Option',
		'Authentication Option',
		'manage_options',
		'wrpt-auth-option-page',
		'wrpt_auth_option_page',
		'dashicons-star-half',
		5
	);
}
//add_action( 'admin_menu', 'wrpt_options_page' );

/**
 * Option page Form
 */
function wrpt_auth_option_page() {
	$auth_token = get_option( 'wrpt_api_auth_token' );
	if ( isset( $_POST['wrpt_option_button'] ) ) {
		update_option( 'wrpt_api_auth_token', $_POST['wrpt_auth_token'] );
	}
	?>
	<form action="#" method="post">
		<table>
			<tr>
				<td><b>Enter Token Here</b></td>
				<td>
					<input type="text" name='wrpt_auth_token' class="regular-text" value="<?php echo $auth_token; ?>" />
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
	</form>
	<?php
}



/**
 * Activate user once user click the link from email
 */
function wrpt_activate_academy_user() {
	if ( isset( $_GET['wrpt_activate'] ) && isset( $_GET['user_email'] ) ) {
		$email            = sanitize_email( wp_unslash( $_GET['user_email'] ) );
		$activation_token = sanitize_text_field( wp_unslash( $_GET['wrpt_activate'] ) );
		$user             = get_user_by( 'email', $email );
		$user_id          = $user->ID;
		$token            = get_user_meta( $user_id, 'activation_token', true );
		if ( $activation_token === $token ) {
			update_user_meta( $user_id, 'wrpt_activation_date', date( 'F j, Y, g:i a' ) );
			wp_redirect( wp_login_url() );
		}
	}
	if ( isset( $_GET['debug'] ) ) {		
		$userid = $_GET['user_id'];
		$user_meta = get_user_meta( $userid );
		echo "<pre>";
		print_r($user_meta);
		die();
		/*$userid = $_GET['user_id'];
		$user_meta = get_user_meta( $userid );
		$user             = get_user_by( 'ID', $userid );		
		$user_email       = $user->user_email;
		$to = $user_email;
		$subject = 'Pay for the plan';
		$body = 'Please click on below link to make payment <a taget="_blank" href="https://proteaintelligence.com/enterprise-plan-payment/?user_id='.$userid.'">Pay</a>';
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$test = wp_mail( $to, $subject, $body, $headers );*/
	}
	/*if( isset($_GET['test_stripe']) ) {
		$email = 'fogixe4573@simdpi.com';
		$token = md5($email).rand(10,9999);
		$user_email = md5( $email );
		$activation_link = get_site_url() . "?wrpt_activate=" . $token . "&user_email=".$email;
		$subject = 'Activate account on Protea Intelligence';
		$message = "<b>Please Visit This Links To Activate Your Account</b> <a href='" . $activation_link . "'>Activate</a>";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		echo "clear";
		$testemail = wp_mail( $email, $subject, $message, $headers );
		var_dump( $testemail );die();
		/*global $wpdb;
		$table_name = $wpdb->prefix . 'frmt_form_entry_meta WHERE meta_key = "stripe-1" AND entry_id = 27';
		$query = "SELECT meta_value FROM " . $table_name;
		$result = $wpdb->get_row( $query );
		echo "<pre>";
		$meta_arr = maybe_unserialize($result->meta_value );
		$sub_id = $meta_arr['subscription_id'];
		echo $sub_id;
		$obj = new Forminator_Subscriptions_API_Stripe();
		$sub = $obj->get_subscription($sub_id);
		print_r($sub);
		echo $query;die();
	}*/
}
add_action( 'init', 'wrpt_activate_academy_user' );


/**
 * Status of Academy Plan API
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_check_academy_status( WP_REST_Request $request ) {
	$user_id                   = $request['id'];
	$user_meta                 = get_user_meta( $user_id );
	$plan_id                   = get_user_meta( $user_id, 'prt_plan_id', true );
	$activation_date           = get_user_meta( $user_id, 'wrpt_activation_date', true );
	$activation_status         = ( $activation_date ) ? 'Active' : 'Inactive';
	$activation_date           = date_create( $activation_date );
	$current_date              = date_create( gmdate() );
	$activation_date_formatted = gmdate( 'D, jS F Y', $activation_date );
	$date_diff                 = date_diff( $activation_date, $current_date );
	$tmp_arr['days_remaining'] = $date_diff->format( '%a days' );
	$result                    = new WP_REST_Response( $tmp_arr );
	return $result;
}

/**
 * Status of Academy Plan API
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_plan_status( WP_REST_Request $request ) {
	$user_id   = $request['id'];
	$user_meta = get_user_meta( $user_id );
	$user      = get_user_by( 'ID', $user_id );
	$plan_id   = get_user_meta( $user_id, 'prt_plan_id', true );
	if ( $plan_id == $GLOBALS['acd_plan_id'] ) {
		$activation_date           = get_user_meta( $user_id, 'wrpt_activation_date', true );
		$activation_status         = ( $activation_date ) ? 'Active' : 'Inactive';
		$current_date              = new DateTime();
		$activation_date_obj_org   = new DateTime( $activation_date );
		$next_activattion_date_obj = $activation_date_obj_org->modify( '+1 year' );
		$intvl                     = $next_activattion_date_obj->diff( $current_date );
		$days_diff                 = $intvl->days;
		$tmp_arr['plan']           = 'Academy';
	}
	if ( $plan_id == $GLOBALS['enter_plan_id'] ) {
		$current_date            = new DateTime();
		$stripe_payment_date     = get_user_meta( $user_id, 'wrpt_activation_enterprise_date', true );
		$activation_date         = $user->user_registered;
		$activation_date_obj_org = new DateTime( $activation_date );

		if ( $stripe_payment_date ) {
			$stripe_data                        = get_user_meta( $user_id, 'wrpt_stripe_data', true );
			$tmp_arr['stripe_transaction_id']   = $stripe_data['value']['transaction_id'];
			$tmp_arr['stripe_transaction_link'] = $stripe_data['value']['transaction_link'];
			$next_activattion_date_obj          = $activation_date_obj_org->modify( '+365 days' );
		} else {
			$next_activattion_date_obj = $activation_date_obj_org->modify( '+7 days' );
		}

		$intvl           = $next_activattion_date_obj->diff( $current_date );
		$days_diff       = $intvl->days;
		$tmp_arr['plan'] = 'Enterprice';
	}
	$tmp_arr['days_remaining'] = $days_diff;
	$result                    = new WP_REST_Response( $tmp_arr );
	/*$activation_date           = get_user_meta( $user_id, 'wrpt_activation_date', true );
	$activation_status         = ( $activation_date ) ? 'Active' : 'Inactive';
	$activation_date           = date_create( $activation_date );
	$current_date              = date_create( gmdate() );
	$activation_date_formatted = gmdate( 'D, jS F Y', $activation_date );
	$date_diff                 = date_diff( $activation_date, $current_date );
	$tmp_arr['days_remaining'] = $date_diff->format( '%a days' );
	$result                    = new WP_REST_Response( $tmp_arr );*/
	return $result;
}

/**
 * Authentication of API
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_api_authentication( WP_REST_Request $request ) {
	$auth_token = get_option( 'wrpt_api_auth_token' );
	/* echo "<pre>";
	echo "Auth Token : ".$auth_token;
	echo "Token : ".$request->get_param( 'token' );
	echo "</pre>"; */
	if ( $auth_token === $request->get_param( 'token' ) ) {
		return true;
	}
	return false;
}
/**
 * API callback For Getting Use By ID.
 *
 * @param WP_REST_Request $request Request Object.
 */
function wrpt_get_all_user_role_callback( WP_REST_Request $request ) {
	global $wp_roles;
	$all_roles      = $wp_roles->roles;
	$editable_roles = apply_filters( 'editable_roles', $all_roles );
	return $editable_roles;
}
