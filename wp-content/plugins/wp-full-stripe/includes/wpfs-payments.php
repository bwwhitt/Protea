<?php

/**
 * Class MM_WPFS_Stripe
 *
 * deals with calls to Stripe API
 *
 */
class MM_WPFS_Stripe {

	const DESIRED_STRIPE_API_VERSION = '2020-08-27';

	/**
	 * @var string
	 */
	const INVALID_NUMBER_ERROR = 'invalid_number';
	/**
	 * @var string
	 */
	const INVALID_NUMBER_ERROR_EXP_MONTH = 'invalid_number_exp_month';
	/**
	 * @var string
	 */
	const INVALID_NUMBER_ERROR_EXP_YEAR = 'invalid_number_exp_year';
	/**
	 * @var string
	 */
	const INVALID_EXPIRY_MONTH_ERROR = 'invalid_expiry_month';
	/**
	 * @var string
	 */
	const INVALID_EXPIRY_YEAR_ERROR = 'invalid_expiry_year';
	/**
	 * @var string
	 */
	const INVALID_CVC_ERROR = 'invalid_cvc';
	/**
	 * @var string
	 */
	const INCORRECT_NUMBER_ERROR = 'incorrect_number';
	/**
	 * @var string
	 */
	const EXPIRED_CARD_ERROR = 'expired_card';
	/**
	 * @var string
	 */
	const INCORRECT_CVC_ERROR = 'incorrect_cvc';
	/**
	 * @var string
	 */
	const INCORRECT_ZIP_ERROR = 'incorrect_zip';
	/**
	 * @var string
	 */
	const CARD_DECLINED_ERROR = 'card_declined';
	/**
	 * @var string
	 */
	const MISSING_ERROR = 'missing';
	/**
	 * @var string
	 */
	const PROCESSING_ERROR = 'processing_error';
	/**
	 * @var string
	 */
	const MISSING_PAYMENT_INFORMATION = 'missing_payment_information';
	/**
	 * @var string
	 */
	const COULD_NOT_FIND_PAYMENT_INFORMATION = 'Could not find payment information';

	private $debugLog = false;

	/* @var $stripe \StripeWPFS\StripeClient */
	private $stripe;

	/**
	 * MM_WPFS_Stripe constructor.
	 *
	 * @param $token
	 *
	 * @throws Exception
	 */
	public function __construct( $token ) {
        try {
            $this->stripe = self::createStripeClient( $token );
        } catch ( Exception $e ) {
            MM_WPFS_Utils::logException( $e, $this );
        }
	}

    /**
     * @param $token string
     * @return \StripeWPFS\StripeClient
     */
	protected static function createStripeClient( $token ) {
        return new \StripeWPFS\StripeClient([
            "api_key"           => $token,
            "stripe_version"    => self::DESIRED_STRIPE_API_VERSION
        ]);
    }

    function getErrorCodes() {
		return array(
			self::INVALID_NUMBER_ERROR,
			self::INVALID_NUMBER_ERROR_EXP_MONTH,
			self::INVALID_NUMBER_ERROR_EXP_YEAR,
			self::INVALID_EXPIRY_MONTH_ERROR,
			self::INVALID_EXPIRY_YEAR_ERROR,
			self::INVALID_CVC_ERROR,
			self::INCORRECT_NUMBER_ERROR,
			self::EXPIRED_CARD_ERROR,
			self::INCORRECT_CVC_ERROR,
			self::INCORRECT_ZIP_ERROR,
			self::CARD_DECLINED_ERROR,
			self::MISSING_ERROR,
			self::PROCESSING_ERROR,
			self::MISSING_PAYMENT_INFORMATION
		);
	}

    /**
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $taxRateIds array
     * @return \StripeWPFS\Subscription
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function subscribe( $transactionData, $taxRateIds ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( __FUNCTION__ . '(): transactionData=' . print_r( $transactionData, true ) );
		}

        $subscription = $this->createSubscriptionForCustomer( $transactionData, $taxRateIds );

        return $subscription;
	}


    /**
     * @param string $stripeCustomerId
     * @param string $stripePlanId
     *
     * @return \StripeWPFS\Subscription
     * @throws Exception
     */
	public function subscribeCustomerToPlan($stripeCustomerId, $stripePlanId ) {
        $subscriptionData = array(
            'customer'        => $stripeCustomerId,
            'items'           => array(
                array(
                    'price'     => $stripePlanId,
                )
            )
        );

        $stripeSubscription = $this->stripe->subscriptions->create( $subscriptionData );

        return $stripeSubscription;
    }

    /**
     * @param \StripeWPFS\Subscription $stripeSubscription
     * @param string $quantity
     *
     * @throws Exception
     */
    public function createUsageRecordForSubscription( $stripeSubscription, $quantity ) {
        $stripeSubscriptionItem = $stripeSubscription->items->data[0];

        $this->stripe->subscriptionItems->createUsageRecord(
            $stripeSubscriptionItem->id,
            [
                'quantity' => $quantity,
                /*
                 * We add 5 minutes to avoid the following Stripe error message:
                 * "Cannot create the usage record with this timestamp because timestamps must be after
                 *  the subscription's last invoice period (or current period start time)."
                 */
                'timestamp' => time() + 5 * 60,
                'action' => 'set',
            ]
        );
    }

    /**
     * @param $stripePaymentMethodId
     *
     * @return \StripeWPFS\PaymentMethod
     * @throws \StripeWPFS\Exception\ApiErrorException
     * @throws Exception
     */
	public function validatePaymentMethodCVCCheck( $stripePaymentMethodId ) {
		/* @var $paymentMethod \StripeWPFS\PaymentMethod */
		$paymentMethod = $this->stripe->paymentMethods->retrieve( $stripePaymentMethodId );

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'validatePaymentMethodCVC(): stripePaymentMethod=' . print_r( $paymentMethod, true ) );
		}

		if ( is_null( $paymentMethod->card->checks->cvc_check ) ) {
			throw new Exception(
			    /* translators: Validation error message for a card number without a CVC code */
			    __( 'Please enter a CVC code', 'wp-full-stripe' ) );
		}

		return $paymentMethod;
	}

	/**
	 * Updates the \StripeWPFS\Customer object's address property with an appropriate address array.
	 *
	 * @param $stripeCustomer \StripeWPFS\Customer
	 * @param $billingName
	 * @param $billingAddress array
	 *
	 * @return \StripeWPFS\Customer
	 */
	public function updateCustomerBillingAddress($stripeCustomer, $billingName, $billingAddress ) {
		$stripeArrayHash = MM_WPFS_Utils::prepare_stripe_address_hash_from_array( $billingAddress );
		if ( isset( $stripeArrayHash ) ) {
			$stripeCustomer->address = $stripeArrayHash;
		}
		if ( ! empty( $billingName ) ) {
			$stripeCustomer->name = $billingName;
		}
		$stripeCustomer->save();

		return $stripeCustomer;
	}

    /**
     * Updates the \StripeWPFS\Customer object's shipping property with an appropriate address array.
     *
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $shippingName
     * @param $shippingPhone
     * @param $shippingAddress array
     *
     * @return \StripeWPFS\Customer
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function updateCustomerShippingAddress( $stripeCustomer, $shippingName, $shippingPhone, $shippingAddress ) {
		$stripeShippingHash = MM_WPFS_Utils::prepareStripeShippingHashFromArray( $shippingName, $shippingPhone, $shippingAddress );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'update_customer_shipping_address(): stripe_shipping_hash=' . print_r( $stripeShippingHash, true ) );
		}
		$stripeCustomer->shipping = $stripeShippingHash;
		$stripeCustomer->save();

		return $stripeCustomer;
	}

    /**
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @return \StripeWPFS\Subscription
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	private function createSubscriptionForCustomer( $transactionData, $taxRateIds ) {
        $stripeCustomer = $this->retrieveCustomer( $transactionData->getStripeCustomerId() );

        $stripePriceId          = $transactionData->getPlanId();
        $setupFee               = $transactionData->getSetupFeeNetAmount();
        $trialPeriodDays        = $transactionData->getTrialPeriodDays();
        $stripePaymentMethodId  = $transactionData->getStripePaymentMethodId();
        $stripePlanQuantity     = $transactionData->getPlanQuantity();
        $billingCycleAnchorDay  = $transactionData->getBillingCycleAnchorDay();
        $prorateUntilAnchorDay  = $transactionData->getProrateUntilAnchorDay();
        $metadata               = $transactionData->getMetadata();
        $couponId               = $transactionData->getCouponId();

		$recurringPrice = $this->stripe->prices->retrieve( $stripePriceId );
		if ( ! isset( $recurringPrice ) ) {
			throw new Exception( "Recurring price with id '${stripePriceId}' doesn't exist." );
		}

        // tnagy attach payment method to customer
		if ( ! is_null( $stripePaymentMethodId ) ) {
			$paymentMethod = $this->retrievePaymentMethod($stripePaymentMethodId );
			$paymentMethod->attach( array( 'customer' => $stripeCustomer->id ) );
			// tnagy set as default payment method on the customer
			$this->stripe->customers->update(
				$stripeCustomer->id,
				array(
					'invoice_settings' => array(
						'default_payment_method' => $stripePaymentMethodId
					)
				)
			);
		}

		if ( $setupFee > 0 ) {
			// tnagy add setup fee as invoice item
            $this->stripe->invoiceItems->create( array(
                    'customer'    => $stripeCustomer->id,
					'currency'    => $recurringPrice->currency,
					'description' => sprintf(
                        /* translators: It's a line item for the initial payment of a subscription */
                        __( 'One-time setup fee (plan: %s)', 'wp-full-stripe' ), MM_WPFS_Localization::translateLabel( $transactionData->getProductName() )),
                    'quantity'    => $stripePlanQuantity,
                    'unit_amount' => $setupFee,
                    'tax_rates'   => $taxRateIds,
                    'metadata'    => [
                            'type'      => 'setupFee'
                    ]
				)
			);
		}

        $hasBillingCycleAnchor          = $billingCycleAnchorDay > 0;
        $hasMonthlyBillingCycleAnchor   = $recurringPrice->recurring['interval'] === 'month' && $hasBillingCycleAnchor;
        $hasTrialPeriod                 = $trialPeriodDays > 0;

        // tnagy create subscription
		$subscriptionData = array(
			'customer'        => $stripeCustomer->id,
			'items'           => array(
				array(
					'price'     => $recurringPrice->id,
					'quantity'  => $stripePlanQuantity,
                    'tax_rates' => $taxRateIds
				)
			),
			'expand'          => array(
			    'latest_invoice',
				'latest_invoice.payment_intent',
				'latest_invoice.charge',
				'pending_setup_intent'
			)
		);
		if ( ! empty( $couponId ) ) {
			$subscriptionData['coupon'] = $couponId;
		}
        if ( $hasTrialPeriod ) {
            $subscriptionData['trial_period_days'] = $trialPeriodDays;
        }
		if ( $hasMonthlyBillingCycleAnchor ) {
		    if ( $hasTrialPeriod ) {
                $subscriptionData['billing_cycle_anchor'] = MM_WPFS_Utils::calculateBillingCycleAnchorFromTimestamp( $billingCycleAnchorDay, MM_WPFS_Utils::calculateTrialEndFromNow( $trialPeriodDays ));
            } else {
                $subscriptionData['billing_cycle_anchor'] = MM_WPFS_Utils::calculateBillingCycleAnchorFromNow( $billingCycleAnchorDay );
            }

            if ( $prorateUntilAnchorDay === 1 ) {
                $subscriptionData['proration_behavior'] = 'create_prorations';
            } else {
                $subscriptionData['proration_behavior'] = 'none';
            }
        }
		if ( ! is_null( $metadata ) ) {
			$subscriptionData['metadata'] = $metadata;
		}
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createSubscriptionForCustomer(): subscriptionData=' . print_r( $subscriptionData, true ) );
		}
		$stripeSubscription = $this->stripe->subscriptions->create( $subscriptionData );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createSubscriptionForCustomer(): created subscription=' . print_r( $stripeSubscription, true ) );
		}

		return $stripeSubscription;
	}

	/**
	 * @param $customerId
	 *
	 * @return \StripeWPFS\Customer
	 */
	public function retrieveCustomer( $customerId ) {
		return $this->stripe->customers->retrieve( $customerId );
	}

    /**
     * @param $customerId
     * @param $params
     *
     * @return \StripeWPFS\Customer
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function retrieveCustomerWithParams( $customerId, $params ) {
        return $this->stripe->customers->retrieve( $customerId, $params );
    }

    /**
     * @param $productId
     *
     * @return \StripeWPFS\Product
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function retrieveProduct( $productId ) {
        return $this->stripe->products->retrieve( $productId );
    }

	/**
	 * @param $code
	 *
	 * @return string|void
	 */
	function resolveErrorMessageByCode($code ) {
		if ( $code === self::INVALID_NUMBER_ERROR ) {
			$resolved_message =  /* translators: message for Stripe error code 'invalid_number' */
				__( 'Your card number is invalid.', 'wp-full-stripe' );
		} elseif ( $code === self::INVALID_EXPIRY_MONTH_ERROR || $code === self::INVALID_NUMBER_ERROR_EXP_MONTH ) {
			$resolved_message = /* translators: message for Stripe error code 'invalid_expiry_month' */
				__( 'Your card\'s expiration month is invalid.', 'wp-full-stripe' );
		} elseif ( $code === self::INVALID_EXPIRY_YEAR_ERROR || $code === self::INVALID_NUMBER_ERROR_EXP_YEAR ) {
			$resolved_message = /* translators: message for Stripe error code 'invalid_expiry_year' */
				__( 'Your card\'s expiration year is invalid.', 'wp-full-stripe' );
		} elseif ( $code === self::INVALID_CVC_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'invalid_cvc' */
				__( 'Your card\'s security code is invalid.', 'wp-full-stripe' );
		} elseif ( $code === self::INCORRECT_NUMBER_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'incorrect_number' */
				__( 'Your card number is incorrect.', 'wp-full-stripe' );
		} elseif ( $code === self::EXPIRED_CARD_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'expired_card' */
				__( 'Your card has expired.', 'wp-full-stripe' );
		} elseif ( $code === self::INCORRECT_CVC_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'incorrect_cvc' */
				__( 'Your card\'s security code is incorrect.', 'wp-full-stripe' );
		} elseif ( $code === self::INCORRECT_ZIP_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'incorrect_zip' */
				__( 'Your card\'s zip code failed validation.', 'wp-full-stripe' );
		} elseif ( $code === self::CARD_DECLINED_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'card_declined' */
				__( 'Your card was declined.', 'wp-full-stripe' );
		} elseif ( $code === self::MISSING_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'missing' */
				__( 'There is no card on a customer that is being charged.', 'wp-full-stripe' );
		} elseif ( $code === self::PROCESSING_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'processing_error' */
				__( 'An error occurred while processing your card.', 'wp-full-stripe' );
		} elseif ( $code === self::MISSING_PAYMENT_INFORMATION ) {
			$resolved_message = /* translators: Stripe error message 'Missing payment information' */
				__( 'Missing payment information', 'wp-full-stripe' );
		} elseif ( $code === self::COULD_NOT_FIND_PAYMENT_INFORMATION ) {
			$resolved_message = /* translators: Stripe error message 'Could not find payment information' */
				__( 'Could not find payment information', 'wp-full-stripe' );
		} else {
			$resolved_message = null;
		}

		return $resolved_message;
	}

	function createPlan($id, $name, $currency, $amount, $setupFee, $interval, $trialDays, $intervalCount, $cancellationCount ) {
        $planData = array(
            "amount"         => $amount,
            "interval"       => $interval,
            "nickname"       => $id,
            "product"        => array(
                "name" => $name,
            ),
            "currency"       => $currency,
            "interval_count" => $intervalCount,
            "id"             => $id,
            "metadata"       => array(
                "cancellation_count" => $cancellationCount,
                "setup_fee"          => $setupFee
            )
        );

        if ( $trialDays != 0 ) {
            $planData['metadata']['trial_period_days'] = $trialDays;
        }

        $this->stripe->plans->create( $planData );
	}

    /**
     * @param $id
     * @param $name
     * @param $currency
     * @param $interval
     * @param $intervalCount
     *
     * @return \StripeWPFS\Price
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    function createRecurringDonationPlan($id, $name, $currency, $interval, $intervalCount ) {
        $planData = array(
            "currency"        => $currency,
            "unit_amount"     => "1",
            "nickname"        => $name,
            "recurring"       => array(
                "interval"        => $interval,
                "interval_count"  => $intervalCount,
                "usage_type"      => "metered",
                "aggregate_usage" => "last_ever"
            ),
            "product_data"        => array(
                "name" => $name
            ),
            "lookup_key"      => $id,
        );

        return $this->stripe->prices->create( $planData );
    }


    /**
     * @param $planId
     *
     * @return \StripeWPFS\Price|null
     */
	public function retrievePlan($planId ) {
        $plan = null;
		try {
			$plan = $this->stripe->prices->retrieve( $planId, array( "expand" => array( "product" ) ));
		} catch ( Exception $e ) {
            // plan not found, let's fall through
		}

		return $plan;
	}

    /**
     * @param $planId
     *
     * @return \StripeWPFS\Collection
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function retrieveDonationPlansWithLookupKey( $planId ) {
	    $prices = $this->stripe->prices->all([
	        'active'      => true,
            'lookup_keys' => [ $planId ]
        ]);

	    return $prices;
    }

	public function getCustomersByEmail($email ) {
		$customers = array();

		try {
			do {
				$params        = array( 'limit' => 100, 'email' => $email );
				$last_customer = end( $customers );
				if ( $last_customer ) {
					$params['starting_after'] = $last_customer['id'];
				}
				$customer_collection = $this->stripe->customers->all( $params );
				$customers           = array_merge( $customers, $customer_collection['data'] );
			} while ( $customer_collection['has_more'] );
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$customers = array();
		}

		return $customers;
	}

    /**
     * @param $params
     * @return \StripeWPFS\Collection
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function getCustomersWithParams( $params ) {
        return $this->stripe->customers->all( $params );
    }

	/**
	 * @return array|\StripeWPFS\Collection
	 */
	public function getSubscriptionPlans() {
		$plans = array();
		try {
			do {
				$params    = array( 'type' => 'recurring', 'limit' => 100, 'include[]' => 'total_count', 'expand' => array( 'data.product' ) );
				$last_plan = end( $plans );
				if ( $last_plan ) {
					$params['starting_after'] = $last_plan['id'];
				}
				$plan_collection = $this->stripe->prices->all( $params );
				$plans           = array_merge( $plans, $plan_collection['data'] );
			} while ( $plan_collection['has_more'] );
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$plans = array();
		}

		return $plans;
	}

    /**
     * @return array|\StripeWPFS\Collection
     */
    public function getOnetimePrices() {
        $prices = array();
        do {
            $params = array(
                'active' => true,
                'type' => 'one_time',
                'limit' => 100,
                'include[]' => 'total_count',
                'expand' => array( 'data.product' )
            );

            $lastPrice = end( $prices );
            if ( $lastPrice ) {
                $params['starting_after'] = $lastPrice['id'];
            }
            $priceCollection = $this->stripe->prices->all( $params );
            $prices          = array_merge( $prices, $priceCollection['data'] );
        } while ( $priceCollection['has_more'] );

        return $prices;
    }

    /**
     * @return array|\StripeWPFS\Collection
     */
    public function getRecurringPrices() {
        $prices = array();
        do {
            $params = array(
                'active' => true,
                'type' => 'recurring',
                'limit' => 100,
                'include[]' => 'total_count',
                'expand' => array( 'data.product' )
            );

            $lastPrice = end( $prices );
            if ( $lastPrice ) {
                $params['starting_after'] = $lastPrice['id'];
            }
            $priceCollection = $this->stripe->prices->all( $params );
            $prices          = array_merge( $prices, $priceCollection['data'] );
        } while ( $priceCollection['has_more'] );

        return $prices;
    }

    /**
     * @return array|\StripeWPFS\Collection
     */
    public function getTaxRates() {
        $taxRates = array();
        do {
            $params = array(
                'active' => true,
                'inclusive' => false,
                'limit' => 100,
                'include[]' => 'total_count'
            );

            $lastTaxRate = end( $taxRates );
            if ( $lastTaxRate ) {
                $params['starting_after'] = $lastTaxRate['id'];
            }
            $taxRateCollection = $this->stripe->taxRates->all( $params );
            $taxRates          = array_merge( $taxRates, $taxRateCollection['data'] );
        } while ( $taxRateCollection['has_more'] );

        return $taxRates;
    }

    /**
	 * @param $code
	 *
	 * @return \StripeWPFS\Coupon
     * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	public function retrieveCoupon( $code ) {
	    return $this->stripe->coupons->retrieve( $code );
	}

	/**
	 * @param $code
	 *
	 * @return \StripeWPFS\PromotionCode
     * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	public function retrievePromotionalCode( $code ) {
		$promotionalCodesCollection = $this->stripe->promotionCodes->all( array( 'code' => $code ) );
        $result = null;

        foreach ( $promotionalCodesCollection->autoPagingIterator() as $promotionCode ) {
            if ( strcmp( $code, $promotionCode->code ) === 0 ) {
                $result = $promotionCode;
                break;
            }
        }

		return $result;
	}

    /**
     * @param $invoiceId
     *
     * @return \StripeWPFS\Invoice
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    function retrieveInvoice( $invoiceId ) {
        return $this->stripe->invoices->retrieve( $invoiceId );
    }

    /**
	 * @param $paymentMethodId
	 * @param $customerName
	 * @param $customerEmail
	 * @param $metadata
	 *
	 * @return \StripeWPFS\Customer
     *
     * @throws StripeWPFS\Exception\ApiErrorException
	 */
	function createCustomerWithPaymentMethod( $paymentMethodId, $customerName, $customerEmail, $metadata, $taxIdType = null, $taxId = null ) {
		$customer = array(
			'payment_method'   => $paymentMethodId,
			'email'            => $customerEmail,
			'invoice_settings' => array(
				'default_payment_method' => $paymentMethodId
			)
		);

		if ( ! is_null( $customerName ) ) {
			$customer['name'] = $customerName;
		}

		if ( ! is_null( $metadata ) ) {
			$customer['metadata'] = $metadata;
		}

		if ( ! is_null( $taxIdType ) ) {
		    $customer['tax_id_data'] = [
		        [
                    'type'  =>  $taxIdType,
                    'value' =>  $taxId
                ]
            ];
        }

		return $this->stripe->customers->create( $customer );
	}

	/**
	 * @param $paymentMethodId
	 * @param $customerId
	 * @param $currency
	 * @param $amount
	 * @param $capture
	 * @param null $description
	 * @param null $metadata
	 * @param null $stripeEmail
	 *
	 * @return \StripeWPFS\PaymentIntent
     *
     * @throws StripeWPFS\Exception\ApiErrorException
	 */
	function createPaymentIntent( $paymentMethodId, $customerId, $currency, $amount, $capture, $description, $metadata = null, $stripeEmail = null ) {
		$paymentIntentParameters = array(
            'amount'              => $amount,
            'currency'            => $currency,
            'confirm'             => true,
            'customer'            => $customerId,
			'payment_method'      => $paymentMethodId,
			'confirmation_method' => 'manual',
		);
		if ( ! empty( $description ) ) {
			$paymentIntentParameters['description'] = $description;
		}
		if ( false === $capture ) {
			$paymentIntentParameters['capture_method'] = 'manual';
		}
		if ( isset( $stripeEmail ) ) {
			$paymentIntentParameters['receipt_email'] = $stripeEmail;
		}
		if ( isset( $metadata ) ) {
			$paymentIntentParameters['metadata'] = $metadata;
		}

		$intent = $this->stripe->paymentIntents->create( apply_filters( 'fullstripe_payment_intent_parameters', $paymentIntentParameters ) );

		return $intent;
	}

    /**
     * @param $stripeCustomerId
     * @param $priceId
     * @param $productCurrency
     * @param $productAmount
     * @param $productName
     * @param $stripeCouponId
     * @param $autoAdvance
     * @param $taxRateIds
     * @return \StripeWPFS\Invoice
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function createInvoiceForOneTimePayment(
        $stripeCustomerId,
        $priceId,
        $productCurrency,
        $productAmount,
        $productName,
        $stripeCouponId,
        $autoAdvance,
        $taxRateIds
	) {
		$params = array(
            'customer'    => $stripeCustomerId,
        );

		if ( $priceId !== null ) {
            $params['price'] = $priceId;
        } else {
            $params['amount'] = $productAmount;
            $params['currency'] = $productCurrency;
            $params['description'] = __( $productName, 'wp-full-stripe' );
        }

		if ( isset( $stripeCouponId ) ) {
			$params['discounts'] = array(
				array( 'coupon' => $stripeCouponId )
			);
		}

		if ( isset( $taxRateIds ) && count( $taxRateIds ) > 0 ) {
            $params['tax_rates'] = $taxRateIds;
        }

		$invoiceItem = $this->stripe->invoiceItems->create( $params );
		$createdInvoice = $this->stripe->invoices->create(
			array(
				'customer'     => $stripeCustomerId,
				'auto_advance' => $autoAdvance
			)
		);

        return $createdInvoice;
	}

    /**
     * @param $taxCountry
     * @param $taxState
     * @param $priceId
     * @param $productCurrency
     * @param $productAmount
     * @param $productName
     * @param $stripeCouponId
     * @param $taxRateIds
     * @return \StripeWPFS\Invoice
     */
    function createPreviewInvoiceForOneTimePayment(
        $taxCountry,
        $taxState,
        $priceId,
        $productCurrency,
        $productAmount,
        $productName,
        $stripeCouponId,
        $taxRateIds
    ) {
        $invoiceParams = [];

        $address = [
            'country'   => $taxCountry
        ];
        if ( ! empty( $taxState ) ) {
            $address['state'] = $taxState;
        }
        $invoiceParams['customer_details'] = [
            'address'   => $address
        ];

        $itemParams = [];
        if ( $priceId !== null ) {
            $itemParams['price'] = $priceId;
        } else {
            $itemParams['amount'] = $productAmount;
            $itemParams['currency'] = $productCurrency;
            $itemParams['description'] = __( $productName, 'wp-full-stripe' );
        }

        if ( isset( $stripeCouponId ) ) {
            $itemParams['discounts'] = array(
                array( 'coupon' => $stripeCouponId )
            );
        }

        if ( isset( $taxRateIds ) && count( $taxRateIds ) > 0 ) {
            $itemParams['tax_rates'] = $taxRateIds;
        }

        $invoiceParams[ 'invoice_items' ] = [
            $itemParams
        ];

        return $this->getUpcomingInvoice( $invoiceParams );
    }

    /**
     * @param $finalizedInvoice
     * @param $stripePaymentMethodId
     * @param $stripeChargeDescription
     * @param $stripeReceiptEmailAddress
     *
     * @return \StripeWPFS\Invoice
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function updatePaymentIntentByInvoice( $finalizedInvoice, $stripePaymentMethodId, $stripeChargeDescription, $metadata, $stripeReceiptEmailAddress  ) {
        $generatedPaymentIntent = $this->stripe->paymentIntents->retrieve( $finalizedInvoice->payment_intent );
        $paymentIntentParameters = array();
        if ( ! empty( $stripeChargeDescription ) ) {
            $paymentIntentParameters['description'] = $stripeChargeDescription;
        }
        if ( isset( $stripeReceiptEmailAddress ) ) {
            $paymentIntentParameters['receipt_email'] = $stripeReceiptEmailAddress;
        }
        if ( isset( $metadata ) ) {
            $paymentIntentParameters['metadata'] = $metadata;
        }
        $updatedPaymentIntent = $this->stripe->paymentIntents->update( $generatedPaymentIntent->id, apply_filters( 'fullstripe_payment_intent_parameters', $paymentIntentParameters ));
        $this->stripe->paymentIntents->confirm( $updatedPaymentIntent->id, array( 'payment_method' => $stripePaymentMethodId ) );

        return $this->stripe->invoices->update( $finalizedInvoice->id );
    }

	/**
	 * @param $paymentIntentId
	 *
	 * @return \StripeWPFS\PaymentIntent
     * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	function retrievePaymentIntent( $paymentIntentId ) {
		$intent = $this->stripe->paymentIntents->retrieve( $paymentIntentId );

		return $intent;
	}

    /**
     * @param $invoiceId
     * @param $params
     *
     * @return \StripeWPFS\Invoice
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function retrieveInvoiceWithParams( $invoiceId, $params ) {
	    $invoice = $this->stripe->invoices->retrieve( $invoiceId, $params );

        return $invoice;
    }

    /**
     * @param $sessionId
     * @param $params
     *
     * @return \StripeWPFS\Checkout\Session
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function retrieveCheckoutSessionWithParams( $sessionId, $params ) {
	    $checkoutSession = $this->stripe->checkout->sessions->retrieve( $sessionId, $params );

	    return $checkoutSession;
    }

    /**
     * @param $paymentMethodId
     *
     * @return \StripeWPFS\PaymentMethod
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function retrievePaymentMethod( $paymentMethodId ) {
	    $paymentMethod = $this->stripe->paymentMethods->retrieve( $paymentMethodId );

	    return $paymentMethod;
    }

    /**
     * @param $eventID
     *
     * @return \StripeWPFS\Event
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function retrieveEvent( $eventID ) {
        $event = $this->stripe->events->retrieve( $eventID );

        return $event;
    }


    /**
     * @param $stripePaymentMethodId
     *
     * @return \StripeWPFS\SetupIntent
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function createSetupIntentWithPaymentMethod( $stripePaymentMethodId ) {
		$params = array(
			'usage'                => 'off_session',
			'payment_method_types' => [ 'card' ],
			'payment_method'       => $stripePaymentMethodId,
			'confirm'              => false
		);
		$intent = $this->stripe->setupIntents->create( $params );

		return $intent;
	}

    /**
     * @param $stripeSetupIntentId
     *
     * @return \StripeWPFS\SetupIntent
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function retrieveSetupIntent( $stripeSetupIntentId ) {
		$intent = $this->stripe->setupIntents->retrieve( $stripeSetupIntentId );

		return $intent;
	}

	/**
	 * Attaches the given PaymentMethod to the given Customer if the Customer do not have an identical PaymentMethod
	 * by card fingerprint.
	 *
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param \StripeWPFS\PaymentMethod $currentPaymentMethod
	 * @param bool $setToDefault
	 *
	 * @return \StripeWPFS\PaymentMethod the attached PaymentMethod or the existing one
     *
     * @throws StripeWPFS\Exception\ApiErrorException
	 */
	function attachPaymentMethodToCustomerIfMissing( $stripeCustomer, $currentPaymentMethod, $setToDefault = false ) {
		$attachedPaymentMethod = null;
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log(
				"attachPaymentMethodToCustomerIfMissing(): CALLED, params: "
				. 'stripeCustomer=' . print_r( $stripeCustomer, true )
				. ', stripePaymentMethodId=' . print_r( $currentPaymentMethod, true )
				. ', setToDefault=' . ( $setToDefault ? 'true' : 'false' )
			);
		}
		if ( $stripeCustomer instanceof \StripeWPFS\Customer && $currentPaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
			// WPFS-983: tnagy find existing PaymentMethod with identical fingerprint and reuse it
			$existingStripePaymentMethod = $this->findExistingPaymentMethodByFingerPrintAndExpiry(
				$stripeCustomer,
				$currentPaymentMethod->card->fingerprint,
				$currentPaymentMethod->card->exp_year,
				$currentPaymentMethod->card->exp_month
			);
			if ( $existingStripePaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log(
						'attachPaymentMethodToCustomerIfMissing(): '
						. 'PaymentMethod with identical card fingerprint exists, won\'t attach.'
					);
				}
				$attachedPaymentMethod = $existingStripePaymentMethod;
			} else {
				if ( is_null( $currentPaymentMethod->customer ) ) {
					$currentPaymentMethod->attach( array( 'customer' => $stripeCustomer->id ) );
					if ( $this->debugLog ) {
						MM_WPFS_Utils::log( 'attachPaymentMethodToCustomerIfMissing(): PaymentMethod attached.' );
					}
				}
				$attachedPaymentMethod = $currentPaymentMethod;
			}
			if ( $setToDefault ) {
				$this->stripe->customers->update(
					$stripeCustomer->id,
					array(
						'invoice_settings' => array(
							'default_payment_method' => $attachedPaymentMethod->id
						)
					)
				);
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'attachPaymentMethodToCustomerIfMissing(): Default PaymentMethod updated.' );
				}
			}

		}

		return $attachedPaymentMethod;
	}

	/**
	 * Find a Customer's PaymentMethod by fingerprint if exists.
	 *
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param string $paymentMethodCardFingerPrint
	 * @param $expiryYear
	 * @param $expiryMonth
	 *
	 * @return null|\StripeWPFS\PaymentMethod the existing PaymentMethod
	 * @throws StripeWPFS\Exception\ApiErrorException
	 */
	public function findExistingPaymentMethodByFingerPrintAndExpiry( $stripeCustomer, $paymentMethodCardFingerPrint, $expiryYear, $expiryMonth ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log(
				'findExistingPaymentMethodByFingerPrint(): CALLED, params: stripeCustomer='
				. print_r( $stripeCustomer, true ) . ', paymentMethodCardFingerPrint=' . $paymentMethodCardFingerPrint
			);
		}
		if ( empty( $paymentMethodCardFingerPrint ) ) {
			return null;
		}
		$paymentMethods        = $this->stripe->paymentMethods->all( array(
				'customer' => $stripeCustomer->id,
				'type'     => 'card'
			)
		);
		$existingPaymentMethod = null;
		if ( $paymentMethods instanceof \StripeWPFS\Collection ) {
			foreach ( $paymentMethods['data'] as $paymentMethod ) {
				/**
				 * @var \StripeWPFS\PaymentMethod $paymentMethod
				 */
				if ( is_null( $existingPaymentMethod ) ) {
					if ( isset( $paymentMethod ) && isset( $paymentMethod->card ) && isset( $paymentMethod->card->fingerprint ) ) {
						if ( $paymentMethod->card->fingerprint == $paymentMethodCardFingerPrint &&
						     $paymentMethod->card->exp_year == $expiryYear &&
						     $paymentMethod->card->exp_month == $expiryMonth
						) {
							$existingPaymentMethod = $paymentMethod;
							if ( $this->debugLog ) {
								MM_WPFS_Utils::log(
									'findExistingPaymentMethodByFingerPrint(): Identical PaymentMethod found='
									. print_r( $existingPaymentMethod, true )
								);
							}
						}
					}
				}
			}
		}

		return $existingPaymentMethod;
	}

    /**
     * @deprecated
     *
     * @param $planId
     * @param $planData
     *
     * @return \StripeWPFS\Plan|null
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function updatePlan($planId, $planData ) {
		if ( isset( $planId ) ) {
			/**
			 * @var \StripeWPFS\Plan
			 */
			$plan = $this->stripe->plans->retrieve( $planId, array( "expand" => array( "product" ) ) );
			if ( isset( $planData ) ) {
				if ( array_key_exists( 'name', $planData ) && ! empty( $planData['name'] ) ) {
					$plan->product->name = $planData['name'];
				}
				if ( array_key_exists( 'statement_descriptor', $planData ) && ! empty( $planData['statement_descriptor'] ) ) {
					$plan->product->statement_descriptor = $planData['statement_descriptor'];
				} else {
					$plan->product->statement_descriptor = null;
				}
				if ( array_key_exists( 'setup_fee', $planData ) && ! empty( $planData['setup_fee'] ) ) {
					$plan->metadata->setup_fee = $planData['setup_fee'];
				} else {
					$plan->metadata->setup_fee = 0;
				}

				$plan->product->save();

				return $plan->save();
			}
		}

		return null;
	}

    /**
     * @deprecated
     *
     * @param $planId
     *
     * @return null
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function deletePlan($planId ) {
		if ( isset( $planId ) ) {
			$plan = $this->stripe->plans->retrieve( $planId );

			return $plan->delete();
		}

		return null;
	}

    /**
     * @param $subscriptionId string
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function activateCancelledSubscription( $subscriptionId ) {
	    $subscription = $this->retrieveSubscription( $subscriptionId );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_SUBSCRIPTION_ACTIVATION, $subscriptionId );

	    $subscription->cancel_at_period_end = false;
        $subscription->save();

        do_action( MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_ACTIVATION, $subscriptionId );
    }

    /**
     * @param $stripeSubscriptionId
     */
    private function fireBeforeSubscriptionCancellationAction( $stripeSubscriptionId ) {
        do_action( MM_WPFS::ACTION_NAME_BEFORE_SUBSCRIPTION_CANCELLATION, $stripeSubscriptionId );
    }

    /**
     * @param $stripeSubscriptionId
     */
    private function fireAfterSubscriptionCancellationAction( $stripeSubscriptionId ) {
        do_action( MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_CANCELLATION, $stripeSubscriptionId );
    }

    /**
     * @param $stripeCustomerId
     * @param $stripeSubscriptionId
     * @param bool $atPeriodEnd
     *
     * @return bool
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function cancelSubscription( $stripeCustomerId, $stripeSubscriptionId, $atPeriodEnd = false ) {
		if ( isset( $stripeCustomerId ) && isset( $stripeSubscriptionId ) ) {
			if ( ! empty( $stripeCustomerId ) && ! empty( $stripeSubscriptionId ) ) {
				$subscription = $this->retrieveSubscription( $stripeSubscriptionId );
				if ( $subscription ) {
				    $this->fireBeforeSubscriptionCancellationAction( $stripeSubscriptionId );

					/** @noinspection PhpUnusedLocalVariableInspection */
                    if ( $atPeriodEnd ) {
                        $cancellationResult = $this->stripe->subscriptions->update(
                            $stripeSubscriptionId,
                            array (
                                'cancel_at_period_end' => true
                            )
                        );
                    } else {
                        $cancellationResult = $subscription->cancel();
                    }

                    $this->fireAfterSubscriptionCancellationAction( $stripeSubscriptionId );

					if ( $cancellationResult instanceof \StripeWPFS\Subscription ) {
						return true;
					}
				}
			}
		}

		return false;
	}

    /**
     * @param $params array
     *
     * @return \StripeWPFS\Collection
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function listSubscriptionsWithParams( $params ) {
        return $this->stripe->subscriptions->all( $params );
    }

    /**
     * @param $params array
     *
     * @return \StripeWPFS\Collection
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function listInvoicesWithParams( $params ) {
        return $this->stripe->invoices->all( $params );
    }

    /**
     * @param $params array
     *
     * @return \StripeWPFS\Collection
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function listPaymentMethodsWithParams( $params ) {
        return $this->stripe->paymentMethods->all( $params );
    }

    /**
     * @param $customerID
     * @param $subscriptionID
     *
     * @return array|\StripeWPFS\StripeObject
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function retrieveSubscriptionByCustomer( $customerID, $subscriptionID ) {
		$cu = $this->stripe->customers->retrieve( $customerID, [ 'expand' => [ 'subscriptions' ] ] );

		return $cu->subscriptions->retrieve( $subscriptionID );
	}

	/**
	 * @param $subscriptionID
	 *
	 * @return \StripeWPFS\Subscription
	 * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	function retrieveSubscription( $subscriptionID ) {
		return $this->stripe->subscriptions->retrieve( $subscriptionID );
	}

    /**
     * @param $subscriptionId string
     * @param $params array
     *
     * @return \StripeWPFS\Subscription
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function retrieveSubscriptionWithParams($subscriptionId, $params ) {
	    $stripeSubscription = $this->stripe->subscriptions->retrieve( $subscriptionId, $params );

	    return $stripeSubscription;
    }

    /**
     * @param $stripeSubscriptionId
     * @param $newPlanId
     * @param $newQuantity
     */
    protected function fireBeforeSubscriptionUpdateAction($stripeSubscriptionId, $newPlanId, $newQuantity) {
        $params = [
            'stripeSubscriptionId' => $stripeSubscriptionId,
            'planId' => $newPlanId,
            'quantity' => $newQuantity
        ];

        do_action(MM_WPFS::ACTION_NAME_BEFORE_SUBSCRIPTION_UPDATE, $params);
    }

    /**
     * @param $stripeSubscriptionId
     * @param $newPlanId
     * @param $newQuantity
     */
    protected function fireAfterSubscriptionUpdateAction($stripeSubscriptionId, $newPlanId, $newQuantity) {
        $params = [
            'stripeSubscriptionId' => $stripeSubscriptionId,
            'planId' => $newPlanId,
            'quantity' => $newQuantity
        ];

        do_action(MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_UPDATE, $params);
    }

    /**
	 * @param $stripeCustomerId
	 * @param $stripeSubscriptionId
	 * @param $planId
	 * @param $newPlanQuantity
	 *
	 * @return bool
	 * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	public function updateSubscriptionPlanAndQuantity( $stripeCustomerId, $stripeSubscriptionId, $planId, $planQuantity = null ) {
        if ( ! empty( $stripeCustomerId ) && ! empty( $stripeSubscriptionId ) && ! empty( $planId ) ) {
            /* @var $subscription \StripeWPFS\Subscription */
            $subscription = $this->retrieveSubscriptionByCustomer( $stripeCustomerId, $stripeSubscriptionId );

            if ( ! empty( $planQuantity ) && is_numeric( $planQuantity ) ) {
                $newPlanQuantity = intval( $planQuantity );
            } else {
                $newPlanQuantity = $subscription->quantity;
            }

            if ( isset( $subscription ) ) {
                $parameters    = array();
                $performUpdate = false;
                $planUpdated   = false;
                // tnagy update subscription plan
                if ( $subscription->plan != $planId ) {
                    $parameters    = array_merge( $parameters, array( 'plan' => $planId ) );
                    $planUpdated   = true;
                    $performUpdate = true;
                }
                // tnagy update subscription quantity
                $allowMultipleSubscriptions = false;
                if ( isset( $subscription->metadata ) && isset( $subscription->metadata->allow_multiple_subscriptions ) ) {
                    $allowMultipleSubscriptions = boolval( $subscription->metadata->allow_multiple_subscriptions );
                }
                $maximumQuantity = 0;
                if ( isset( $subscription->metadata ) && isset( $subscription->metadata->maximum_quantity_of_subscriptions ) ) {
                    $maximumQuantity = $subscription->metadata->maximum_quantity_of_subscriptions;
                }
                if ( $allowMultipleSubscriptions ) {
                    if ( $maximumQuantity > 0 && $newPlanQuantity > $maximumQuantity ) {
                        throw new Exception( sprintf(
                        /* translators: Error message displayed when subscriber tries to set a quantity for a subscription which is beyond allowed value */
                            __( "Subscription quantity '%d' is not allowed for this subscription!", 'wp-full-stripe' ), $newPlanQuantity ) );
                    }
                    if ( $subscription->quantity != intval( $newPlanQuantity ) || $planUpdated ) {
                        $parameters    = array_merge( $parameters, array( 'quantity' => $newPlanQuantity ) );
                        $performUpdate = true;
                    }
                } elseif ( $newPlanQuantity > 1 ) {
                    throw new Exception(
                    /* translators: Error message displayed when subscriber tries to set a quantity for a
                     * subscription where quantity other than one is not allowed.
                     */
                        __( 'Quantity update is not allowed for this subscription!', 'wp-full-stripe' ) );
                }
            } else {
                throw new Exception( sprintf(
                /* translators: Error message displayed when a subscription is not found.
                 * p1: Subscription identifier
                 */
                    __( "Subscription '%s' not found!", 'wp-full-stripe' ), $stripeSubscriptionId ) );
            }
            if ( $performUpdate ) {
                $this->fireBeforeSubscriptionUpdateAction( $stripeSubscriptionId, $planId, $newPlanQuantity );
                $this->stripe->subscriptions->update( $stripeSubscriptionId, $parameters );
                $this->fireAfterSubscriptionUpdateAction( $stripeSubscriptionId, $planId, $newPlanQuantity );
            }

            return true;
        } else {
            // This is an internal error, no need to localize it
            throw new Exception( 'Invalid parameters!' );
        }
	}

	function getProducts($associativeArray = false, $productIds = null ) {
		$products = array();
		try {

			$params = array(
				'limit'     => 100,
				'include[]' => 'total_count'
			);
			if ( ! is_null( $productIds ) && count( $productIds ) > 0 ) {
				$params['ids'] = $productIds;
			}
			$params            = array( 'active' => 'false', 'limit' => 100 );
			$productCollection = $this->stripe->products->all( $params );
			foreach ( $productCollection->autoPagingIterator() as $product ) {
				if ( $associativeArray ) {
					$products[ $product->id ] = $product;
				} else {
					array_push( $products, $product );
				}
			}

			// MM_WPFS_Utils::log( 'params=' . print_r( $params, true ) );
			// MM_WPFS_Utils::log( 'productCollection=' . print_r( $productCollection, true ) );

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$products = array();
		}

		return $products;
	}

    /**
     * @param $chargeId
     *
     * @return \StripeWPFS\Charge
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function captureCharge($chargeId ) {
        $charge = $this->stripe->charges->retrieve( $chargeId );
        if ( $charge instanceof \StripeWPFS\Charge ) {
            /* @var $charge \StripeWPFS\Charge */
			return $charge->capture();
		}

		return $charge;
	}

    /**
     * @param $paymentIntentId
     *
     * @return \StripeWPFS\PaymentIntent
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function capturePaymentIntent( $paymentIntentId ) {
		$paymentIntent = $this->stripe->paymentIntents->retrieve( $paymentIntentId );
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
            /* @var $charge \StripeWPFS\PaymentIntent */
			return $paymentIntent->capture();
		}

		return $paymentIntent;
	}

    /**
     * @param $chargeId
     *
     * @return \StripeWPFS\Refund
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	function refundCharge( $chargeId ) {
        $refund = $this->stripe->refunds->create( [
            'charge' => $chargeId
        ] );

		return $refund;
	}

    /**
     * @param $paymentIntentId
     *
     * @return \StripeWPFS\PaymentIntent|\StripeWPFS\Refund
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function cancelOrRefundPaymentIntent( $paymentIntentId ) {
		$paymentIntent = $this->stripe->paymentIntents->retrieve( $paymentIntentId );
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
		    /* @var $paymentIntent \StripeWPFS\PaymentIntent */
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CONFIRMATION === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
			) {
				return $paymentIntent->cancel();
			} elseif (
				\StripeWPFS\PaymentIntent::STATUS_PROCESSING === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
			) {
				/** @var \StripeWPFS\Charge $lastCharge */
				$lastCharge = $paymentIntent->charges->data[0];

				return $this->refundCharge( $lastCharge->id );
			}
		}

		return $paymentIntent;
	}

	public function updateCustomerBillingAddressByPaymentMethod( $stripeCustomer, $stripePaymentMethod ) {
		if ( $stripeCustomer instanceof \StripeWPFS\Customer && $stripePaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
			$address = $this->fetchBillingAddressFromPaymentMethod( $stripePaymentMethod );
			if ( count( $address ) > 0 ) {
				$this->stripe->customers->update(
					$stripeCustomer->id,
					array(
						'address' => $address
					)
				);
			}
		}
	}

	/**
	 * @param $stripePaymentMethod
	 *
	 * @return array
	 */
	private function fetchBillingAddressFromPaymentMethod( $stripePaymentMethod ) {
		$address = array();
		if (
			isset( $stripePaymentMethod->billing_details )
			&& isset( $stripePaymentMethod->billing_details->address )
			&& $this->isRealBillingAddressInPaymentMethod( $stripePaymentMethod )
		) {
			$billingDetailsAddress = $stripePaymentMethod->billing_details->address;
			if ( isset( $billingDetailsAddress->city ) ) {
				$address['city'] = $billingDetailsAddress->city;

			}
			if ( isset( $billingDetailsAddress->country ) ) {
				$address['country'] = $billingDetailsAddress->country;

			}
			if ( isset( $billingDetailsAddress->line1 ) ) {
				$address['line1'] = $billingDetailsAddress->line1;

			}
			if ( isset( $billingDetailsAddress->line2 ) ) {
				$address['line2'] = $billingDetailsAddress->line2;

			}
			if ( isset( $billingDetailsAddress->postal_code ) ) {
				$address['postal_code'] = $billingDetailsAddress->postal_code;

			}
			if ( isset( $billingDetailsAddress->state ) ) {
				$address['state'] = $billingDetailsAddress->state;

				return $address;

			}

			return $address;
		}

		return $address;
	}

	private function isRealBillingAddressInPaymentMethod( $stripePaymentMethod ) {
		$res = false;

		$billingDetailsAddress = $stripePaymentMethod->billing_details->address;
		if ( ! empty( $billingDetailsAddress->city )
		     && ! empty( $billingDetailsAddress->country )
		     && ! empty( $billingDetailsAddress->line1 )
		) {
			$res = true;
		}

		return $res;
	}

	public function updateCustomerShippingAddressByPaymentMethod( $stripeCustomer, $stripePaymentMethod ) {
		if ( $stripeCustomer instanceof \StripeWPFS\Customer && $stripePaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
			$address = $this->fetchBillingAddressFromPaymentMethod( $stripePaymentMethod );
			if ( count( $address ) > 0 ) {
				$this->stripe->customers->update(
					$stripeCustomer->id,
					array(
						'shipping' => array(
							'address' => $address
						)
					)
				);
			}
		}
	}

    /**
     * @param $parameters array
     * 
     * @return \StripeWPFS\Checkout\Session
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function createCheckoutSession( $parameters ) {
        return $this->stripe->checkout->sessions->create( apply_filters( 'fullstripe_checkout_session_parameters', $parameters ));
    }

	/**
	 * @param string $stripeInvoiceId
	 */
	public function payInvoiceOutOfBand( $stripeInvoiceId ) {
		return $this->stripe->invoices->pay( $stripeInvoiceId, array( 'paid_out_of_band' => true )) ;
	}

    /**
     * @param $stripeInvoice
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	public function finalizeInvoice( $stripeInvoiceId ) {
        return $this->stripe->invoices->finalizeInvoice( $stripeInvoiceId );
    }

    public function getUpcomingInvoice( $invoiceParams ) {
	    return $this->stripe->invoices->upcoming( $invoiceParams );
    }

    /**
     * @return \StripeWPFS\StripeClient
     */
    public function getStripeClient() {
	    return $this->stripe;
    }

}