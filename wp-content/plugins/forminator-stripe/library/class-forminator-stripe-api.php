<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Wrapper Stripe Subscription class
 * Class Forminator_Subscriptions_Gateway_Stripe
 *
 * @since 1.0
 */
class Forminator_Subscriptions_API_Stripe extends Forminator_Gateway_Stripe {
	/**
	 * Plugin instance
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0
	 * @return Forminator_Stripe_Subscription
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set Stripe API key and App info
	 *
	 * @since 1.0
	 */
	public function set_stripe_key( $mode = 'test' ) {
        if ( 'live' === $mode || $this->is_live() ) {
            $api_key = $this->live_secret;
        } else {
            $api_key = $this->test_secret;
        }
		\Forminator\Stripe\Stripe::setApiKey( $api_key );

		if ( method_exists( 'Forminator_Stripe_Gateway', 'set_stripe_app_info' ) ) {
			self::set_stripe_app_info();
		}
	}

	/**
	 * Create Stripe customer
	 *
	 * @since 1.0
	 *
	 * @param array $data Customer data.
	 *
	 * @return mixed
	 */
	public function create_customer( $data ) {
		try {
			return \Forminator\Stripe\Customer::create( $data );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Get Stripe customer
	 *
	 * @since 1.0
	 *
	 * @param string $id Customer ID.
	 *
	 * @return mixed
	 */
	public function get_customer( $id ) {
		try {
			return \Forminator\Stripe\Customer::retrieve( $id );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Save Stripe customer
	 *
	 * @since 1.0
	 *
	 * @param object $customer Customer object.
	 *
	 * @return mixed
	 */
	public function save_customer( $customer ) {
		try {
			return $customer->save();
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Update Stripe customers
	 *
	 * @since 1.0
	 *
	 * @param string $id   Customer ID.
	 * @param array  $data Customer data.
	 *
	 * @return mixed
	 */
	public function update_customer( $id, $data ) {
		try {
			return \Forminator\Stripe\Customer::update( $id, $data );	  	 	  			 		 	 		    
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Delete Stripe customer
	 *
	 * @since 1.0
	 *
	 * @param string $id Customer ID.
	 *
	 * @return mixed
	 */
	public function delete_customer( $id ) {
		try {
			return \Forminator\Stripe\Customer::delete( $id );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Create Stripe product
	 *
	 * @since 1.0
	 *
	 * @param array $data Product data.
	 *
	 * @return mixed
	 */
	public function create_product( $data ) {
		try {
			return \Forminator\Stripe\Product::create( $data );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Get Stripe product
	 *
	 * @since 1.0
	 *
	 * @param string $id Product ID.
	 *
	 * @return mixed
	 */
	public function get_product( $id ) {
		try {
			return \Forminator\Stripe\Product::retrieve( $id );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Update Stripe product
	 *
	 * @since 1.0
	 *
	 * @param string $id   Product ID.
	 * @param array  $data Product data.
	 *
	 * @return mixed
	 */
	public function update_product( $id, $data ) {
		try {
			return \Forminator\Stripe\Product::update( $id, $data );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Delete Stripe product
	 *
	 * @since 1.0
	 *
	 * @param string $id Product ID.
	 *
	 * @return mixed
	 */
	public function delete_product( $id ) {
		try {
			return \Forminator\Stripe\Product::delete( $id );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Create Stripe subscription
	 *
	 * @since 1.0
	 *
	 * @param array $data Subscription data.
	 *
	 * @return mixed
	 */
	public function create_subscription( $data ) {
		try {
			return \Forminator\Stripe\Subscription::create( $data );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Get Stripe subscription
	 *
	 * @since 1.0
	 *
	 * @param string $id Subscription ID.
	 *
	 * @return mixed
	 */
	public function get_subscription( $id ) {
		try {
			return \Forminator\Stripe\Subscription::retrieve( $id );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}

	/**
	 * Get Stripe invoice
	 *
	 * @since 1.0
	 *
	 * @param string $id Invoice ID.
	 *
	 * @return mixed
	 */
	public function get_invoice( $id ) {
		try {
			return \Forminator\Stripe\Invoice::retrieve( $id );
		} catch ( \Exception $e ) {
			return $this->get_error( $e );
		}
	}
}