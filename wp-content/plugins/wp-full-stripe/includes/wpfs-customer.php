<?php

/**
 * Class MM_WPFS_Customer deals with customer front-end input i.e. payment forms submission
 */
class MM_WPFS_Customer {
	use MM_WPFS_DonationTools;

	const DEFAULT_CHECKOUT_LINE_ITEM_IMAGE = 'https://stripe.com/img/documentation/checkout/marketplace.png';

	/** @var $stripe MM_WPFS_Stripe */
	private $stripe = null;
	/** @var $db MM_WPFS_Database */
	private $db = null;
	/** @var $mailer MM_WPFS_Mailer */
	private $mailer = null;
	/** @var MM_WPFS_TransactionDataService */
	private $transactionDataService = null;
	/** @var MM_WPFS_CheckoutSubmissionService */
	private $checkoutSubmissionService = null;

	private $debugLog = false;

	public function __construct() {
		$this->setup();
		$this->hooks();
	}

	private function setup() {
		$this->stripe                    = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
		$this->db                        = new MM_WPFS_Database();
		$this->mailer                    = new MM_WPFS_Mailer();
		$this->transactionDataService    = new MM_WPFS_TransactionDataService();
		$this->checkoutSubmissionService = new MM_WPFS_CheckoutSubmissionService();
	}

	private function hooks() {
		add_action( 'wp_ajax_wp_full_stripe_subscription_charge', array( $this, 'fullstripe_subscription_charge' ) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_subscription_charge', array(
			$this,
			'fullstripe_subscription_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_check_coupon', array( $this, 'fullstripe_check_coupon' ) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_check_coupon', array( $this, 'fullstripe_check_coupon' ) );
		add_action( 'wp_ajax_wp_full_stripe_inline_payment_charge', array(
			$this,
			'fullstripe_inline_payment_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_inline_donation_charge', array(
			$this,
			'fullstripe_inline_donation_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_inline_payment_charge', array(
			$this,
			'fullstripe_inline_payment_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_inline_donation_charge', array(
			$this,
			'fullstripe_inline_donation_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_inline_subscription_charge', array(
			$this,
			'fullstripe_inline_subscription_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_inline_subscription_charge', array(
			$this,
			'fullstripe_inline_subscription_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_popup_payment_charge', array(
			$this,
            'fullstripe_checkout_payment_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_popup_payment_charge', array(
			$this,
            'fullstripe_checkout_payment_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_popup_donation_charge', array(
			$this,
            'fullstripe_checkout_donation_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_popup_donation_charge', array(
			$this,
            'fullstripe_checkout_donation_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_popup_subscription_charge', array(
			$this,
            'fullstripe_checkout_subscription_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_popup_subscription_charge', array(
			$this,
            'fullstripe_checkout_subscription_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_handle_checkout_session', array(
			$this,
			'fullstripe_handle_checkout_session'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_handle_checkout_session', array(
			$this,
			'fullstripe_handle_checkout_session'
		) );

        // actions for pricing/tax calculations
        add_action( 'wp_ajax_wpfs-calculate-pricing', array( $this, 'calculatePricing' ) );
        add_action( 'wp_ajax_nopriv_wpfs-calculate-pricing', array( $this, 'calculatePricing' ) );
    }

	/**
	 * @param $transactionResult MM_WPFS_TransactionResult
	 *
	 * @return array
	 */
	static function generate_return_value_from_transaction_result( $transactionResult ) {
		$returnValue = array(
			'success'      => $transactionResult->isSuccess(),
			'messageTitle' => $transactionResult->getMessageTitle(),
			'message'      => $transactionResult->getMessage(),
			'redirect'     => $transactionResult->isRedirect(),
			'redirectURL'  => $transactionResult->getRedirectURL()
		);

		return $returnValue;
	}

	function fullstripe_handle_checkout_session() {

		try {

			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): CALLED' );
			}

			$submitHash      = isset(
				$_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ]
			) ? sanitize_text_field( $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ] ) : null;
			$submitStatus    = isset(
				$_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_STATUS ]
			) ? sanitize_text_field( $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_STATUS ] ) : null;
			$popupFormSubmit = null;

			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( "fullstripe_handle_checkout_session(): submitHash=$submitHash" );
				MM_WPFS_Utils::log( "fullstripe_handle_checkout_session(): submitStatus=$submitStatus" );
			}

			if ( ! empty( $submitHash ) && ! empty( $submitStatus ) ) {

				$popupFormSubmit = $this->checkoutSubmissionService->retrieveSubmitEntry( $submitHash );

				if ( ! is_null( $popupFormSubmit ) && isset( $popupFormSubmit->checkoutSessionId ) ) {

					$checkoutSession = $this->checkoutSubmissionService->retrieveCheckoutSession( $popupFormSubmit->checkoutSessionId );

					if ( $this->debugLog ) {
						MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
						MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): checkoutSession=' . print_r( $checkoutSession, true ) );
					}

					if ( MM_WPFS_CheckoutSubmissionService::CHECKOUT_SESSION_STATUS_SUCCESS === $submitStatus ) {

						/**
						 * @var MM_WPFS_CheckoutChargeHandler
						 */
						$checkoutChargeHandler = null;
						$formModel             = null;
						if ( MM_WPFS_Utils::isCheckoutPaymentFormType( $popupFormSubmit->formType ) ||
                             MM_WPFS_Utils::isCheckoutSaveCardFormType( $popupFormSubmit->formType )
                        ) {
							$formModel             = new MM_WPFS_Public_CheckoutPaymentFormModel();
							$checkoutChargeHandler = new MM_WPFS_CheckoutPaymentChargeHandler();
						} elseif ( MM_WPFS_Utils::isCheckoutSubscriptionFormType( $popupFormSubmit->formType ) ) {
							$formModel             = new MM_WPFS_Public_CheckoutSubscriptionFormModel();
							$checkoutChargeHandler = new MM_WPFS_CheckoutSubscriptionChargeHandler();
						} elseif ( MM_WPFS_Utils::isCheckoutDonationFormType( $popupFormSubmit->formType ) ) {
							$formModel             = new MM_WPFS_Public_CheckoutDonationFormModel();
							$checkoutChargeHandler = new MM_WPFS_CheckoutDonationChargeHandler();
						}
						if ( ! is_null( $formModel ) && ! is_null( $checkoutChargeHandler ) ) {
							$postData            = $formModel->extractFormModelDataFromPopupFormSubmit( $popupFormSubmit );
							$checkoutSessionData = $formModel->extractFormModelDataFromCheckoutSession( $checkoutSession );
							$postData            = array_merge( $postData, $checkoutSessionData );
							$formModel->bindByArray(
								$postData
							);
							if ( $this->debugLog ) {
								MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): formModel=' . print_r( $formModel, true ) );
							}
							$chargeResult = $checkoutChargeHandler->handle( $formModel, $checkoutSession );
							if ( $chargeResult->isSuccess() ) {
								$this->checkoutSubmissionService->updateSubmitEntryWithSuccess( $popupFormSubmit, $chargeResult->getMessageTitle(), $chargeResult->getMessage() );
								$redirectURL = $popupFormSubmit->referrer;
								if ( $chargeResult->isRedirect() ) {
									$redirectURL = $chargeResult->getRedirectURL();
								}
								wp_redirect( $redirectURL );
								if ( $this->debugLog ) {
									MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): Submit entry successfully processed, redirect to=' . $redirectURL );
								}
							} else {
								$this->checkoutSubmissionService->updateSubmitEntryWithFailed( $popupFormSubmit );
								wp_redirect( $popupFormSubmit->referrer );
								if ( $this->debugLog ) {
									MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): Submit entry failed, redirect to=' . $popupFormSubmit->referrer );
								}
							}
						} else {
							if ( $this->debugLog ) {
								MM_WPFS_Utils::log( __CLASS__ . __FUNCTION__ . "(): Cannot find handler and form model for form type '" . $popupFormSubmit->formType . "'." );
							}
						}
					} else {
						// tnagy mark submission as failed
						$this->checkoutSubmissionService->updateSubmitEntryWithCancelled( $popupFormSubmit );
						wp_redirect( $popupFormSubmit->referrer );
						if ( $this->debugLog ) {
							MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): Submit entry cancelled, redirect to=' . $popupFormSubmit->referrer );
						}
					}
				} else {
					// tnagy submit entry not found
					MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): ERROR: Submit entry not found: submitHash=' . $submitHash . ', submitStatus=' . $submitStatus );
					status_header( 500 );
				}

			} else {
				// tnagy submit hash and/or submit status is empty
				MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): ERROR: submitHash and/or submitStatus is empty: submitHash=' . $submitHash . ', submitStatus=' . $submitStatus );
				status_header( 500 );
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			if ( isset( $popupFormSubmit ) ) {
				$this->checkoutSubmissionService->updateSubmitEntryWithFailed( $popupFormSubmit, __( 'Internal Error', 'wp-full-stripe' ), MM_WPFS_Localization::translateLabel( $e->getMessage() ) );
				wp_redirect( $popupFormSubmit->referrer );
			} else {
				status_header( 500 );
			}
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): FINISHED' );
		}

		exit;
	}

	function fullstripe_inline_payment_charge() {

		try {

			$paymentFormModel = new MM_WPFS_Public_InlinePaymentFormModel();
			$bindingResult    = $paymentFormModel->bind();
			if ($this->debugLog) {
				MM_WPFS_Utils::log( 'coupon code=' . $paymentFormModel->getCouponCode() );
				MM_WPFS_Utils::log( 'stripe coupon=' . print_r( $paymentFormModel->getStripeCoupon(), true ) );
			}

			if ( $bindingResult->hasErrors() ) {
				$return = MM_WPFS_Utils::generateReturnValueFromBindings( $bindingResult );
			} else {
				if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $paymentFormModel->getForm()->customAmount ) {
					$result = $this->processSetupIntent( $paymentFormModel );
				} else {
					$result = $this->processPaymentIntentCharge( $paymentFormModel );
				}
				$return = $result->getAsArray();
			}
		} catch ( WPFS_UserFriendlyException $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
			$messageTitle = is_null( $ex->getTitle() ) ?
				/* translators: Banner title of an error returned from an extension point by a developer */
				__( 'Internal Error', 'wp-full-stripe' ) :
				$ex->getTitle();
			$message      = $ex->getMessage();
			$return       = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $ex->getMessage()
			);
		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', 'wp-full-stripe' );
			$message      = $this->stripe->resolveErrorMessageByCode( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', 'wp-full-stripe' ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_inline_payment_charge_return_message', $return ) );
		exit;

	}

	function fullstripe_inline_donation_charge() {

		try {

			$donationFormModel = new MM_WPFS_Public_InlineDonationFormModel();
			$bindingResult     = $donationFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = MM_WPFS_Utils::generateReturnValueFromBindings( $bindingResult );
			} else {
				$result = $this->processDonationPaymentIntentCharge( $donationFormModel );
				$return = $this->generateReturnValueFromTransactionResult( $result );
			}
		} catch ( WPFS_UserFriendlyException $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
			$messageTitle = is_null( $ex->getTitle() ) ?
				/* translators: Banner title of an error returned from an extension point by a developer */
				__( 'Internal Error', 'wp-full-stripe' ) :
				$ex->getTitle();
			$message      = $ex->getMessage();
			$return       = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $ex->getMessage()
			);
		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', 'wp-full-stripe' );
			$message      = $this->stripe->resolveErrorMessageByCode( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', 'wp-full-stripe' ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_inline_donation_charge_return_message', $return ) );
		exit;

	}

    /**
     * @param $saveCardFormModel MM_WPFS_Public_InlinePaymentFormModel
     * @param $transactionData MM_WPFS_PaymentTransactionData
     */
    protected function fireBeforeInlineSaveCardAction( $saveCardFormModel, $transactionData ) {
        $params = array(
            'email'         => $saveCardFormModel->getCardHolderEmail(),
            'urlParameters' => $saveCardFormModel->getFormGetParametersAsArray(),
            'formName'      => $saveCardFormModel->getFormName(),
            'stripeClient'  => $this->stripe->getStripeClient(),
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_SAVE_CARD, $params );
    }

    /**
     * @param $saveCardFormModel MM_WPFS_Public_PaymentFormModel
     * @param $transactionData MM_WPFS_SaveCardTransactionData
     * @param $stripeCustomer \StripeWPFS\Customer
     */
    protected function fireAfterInlineSaveCardAction( $saveCardFormModel, $transactionData, $stripeCustomer ) {
        $replacer = new MM_WPFS_SaveCardMacroReplacer( $saveCardFormModel->getForm(), $transactionData );

        $params = array(
            'email'                   => $saveCardFormModel->getCardHolderEmail(),
            'urlParameters'           => $saveCardFormModel->getFormGetParametersAsArray(),
            'formName'                => $saveCardFormModel->getFormName(),
            'stripeClient'            => $this->stripe->getStripeClient(),
            'stripeCustomer'          => $stripeCustomer,
            'rawPlaceholders'         => $replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'   => $replacer->getDecoratedKeyValuePairs(),
        );

        do_action( MM_WPFS::ACTION_NAME_AFTER_SAVE_CARD, $params );
    }

    /**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return MM_WPFS_ChargeResult
	 */
	private function processSetupIntent( $paymentFormModel ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( __FUNCTION__ . '(): CALLED' );
		}

		$setupIntentResult = new MM_WPFS_SetupIntentResult();

		if ( empty( $paymentFormModel->getStripeSetupIntentId() ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( __FUNCTION__ . '(): Creating SetupIntent...' );
			}
			$setupIntent = $this->stripe->createSetupIntentWithPaymentMethod( $paymentFormModel->getStripePaymentMethodId() );
			$setupIntent->confirm();
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( __FUNCTION__ . '(): Retrieving SetupIntent...' );
			}

			$setupIntent = $this->stripe->retrieveSetupIntent( $paymentFormModel->getStripeSetupIntentId() );
		}

		$transactionData = null;
		if ( $setupIntent instanceof \StripeWPFS\SetupIntent ) {
			if (
				\StripeWPFS\SetupIntent::STATUS_REQUIRES_ACTION === $setupIntent->status
				&& 'use_stripe_sdk' === $setupIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( __FUNCTION__ . '(): SetupIntent requires action...' );
				}
				$setupIntentResult->setSuccess( false );
				$setupIntentResult->setRequiresAction( true );
				$setupIntentResult->setSetupIntentClientSecret( $setupIntent->client_secret );
				$setupIntentResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', 'wp-full-stripe' ) );
				$setupIntentResult->setMessage(
				/* translators: Banner message of a pending card saving transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Saving this card requires additional action before completion!', 'wp-full-stripe' ) );
			} elseif ( \StripeWPFS\SetupIntent::STATUS_SUCCEEDED === $setupIntent->status ) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( __FUNCTION__ . '(): SetupIntent succeeded.' );
				}

				$this->fireBeforeInlineSaveCardAction( $paymentFormModel, $transactionData );

                $this->setCustomerAndPaymentMethodByFormModel( $paymentFormModel, true);

				$transactionData            = MM_WPFS_TransactionDataService::createSaveCardDataByModel( $paymentFormModel );
				$stripeCardSavedDescription = MM_WPFS_Utils::prepareStripeCardSavedDescription( $paymentFormModel, $transactionData );

				$stripeCustomer              = $paymentFormModel->getStripeCustomer();
				$stripeCustomer->description = $stripeCardSavedDescription;
				$stripeCustomer->save();

                $paymentFormModel->setTransactionId( $paymentFormModel->getStripeCustomer()->id );
                $transactionData->setTransactionId( $paymentFormModel->getTransactionId() );

                $this->db->insertSavedCard( $paymentFormModel, $transactionData );

                $this->fireAfterInlineSaveCardAction( $paymentFormModel, $transactionData, $stripeCustomer );

				$setupIntentResult->setRequiresAction( false );
				$setupIntentResult->setSuccess( true );
				$setupIntentResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', 'wp-full-stripe' ) );
				$setupIntentResult->setMessage(
				/* translators: Banner message of saving card successfully */
					__( 'Saving Card Successful!', 'wp-full-stripe' ) );
			} else {
				$setupIntentResult->setSuccess( false );
				$setupIntentResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', 'wp-full-stripe' ) );
				$setupIntentResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid SetupIntent status '%'.", $setupIntent->status ) );
			}
		}

		$this->handleRedirect( $paymentFormModel, $transactionData, $setupIntentResult );

		if ( $setupIntentResult->isSuccess() ) {
		    if ( MM_WPFS_Mailer::canSendSaveCardPluginReceipt( $paymentFormModel->getForm() )) {
                $this->mailer->sendSaveCardNotification( $paymentFormModel->getForm(), $transactionData );
            }
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( __FUNCTION__ . '(): Returning paymentIntentResult=' . print_r( $setupIntentResult, true ) );
		}

		return $setupIntentResult;
	}

    protected function determineTaxCountry( $taxCountry, $billingAddress  ) {
        $result = null;
        $billingCountry = ! is_null( $billingAddress ) ? $billingAddress['country_code'] : null;

        if ( ! empty( $billingCountry ) ) {
            $result = $billingCountry;
        }
        if ( is_null( $result ) && ! empty( $taxCountry ) ) {
            $result = $taxCountry;
        }

        return $result;
    }

    /**
	 * This function creates or retrieves a Stripe Customer. As a first step it validates the given PaymentMethod's CVC
	 * check then tries to retrieve an existing Stripe Customer by the given email address.
	 * If no Stripe Customer has been found then it tries to retrieve a Customer stored by the PaymentMethod and if
	 * there is no Stripe Customer at all then creates one.
	 * If an existing Stripe Customer was found then it tries to attach the given PaymentMethod if the PaymentMethod's
	 * card fingerprint is not currently found in the list of PaymentMethods currently attached to this Customer to
	 * avoid the duplication of identical PaymentMethods.
	 *
	 * @param $paymentMethodId
	 * @param $paymentIntentId
	 * @param $cardHolderName
	 * @param $cardHolderEmail
	 * @param $cardHolderPhone
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $metadata
	 *
	 * @return MM_WPFS_CreateOrRetrieveCustomerResult
	 * @throws Exception
	 */
	private function createOrRetrieveCustomer( $paymentMethodId, $paymentIntentId, $cardHolderName, $cardHolderEmail, $cardHolderPhone, $businessName, $taxId, $taxCountry, $billingName, $billingAddress, $shippingName, $shippingAddress, $metadata ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( "createOrRetrieveCustomer(): CALLED, params: paymentMethodId=$paymentMethodId, paymentIntentId=$paymentIntentId, cardHolderEmail=$cardHolderEmail" );
		}
		$result        = new MM_WPFS_CreateOrRetrieveCustomerResult();
		$paymentMethod = $this->stripe->validatePaymentMethodCVCCheck( $paymentMethodId );
		$result->setPaymentMethod( $paymentMethod );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): paymentMethod=' . print_r( $paymentMethod, true ) );
		}
		$stripeCustomer = MM_WPFS_Utils::findExistingStripeCustomerAnywhereByEmail( $this->db, $this->stripe, $cardHolderEmail );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): stripeCustomer by email=' . print_r( $stripeCustomer, true ) );
		}
		if ( ! isset( $stripeCustomer ) && isset( $paymentMethod->customer ) ) {
			$stripeCustomer = $this->stripe->retrieveCustomer( $paymentMethod->customer );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe Customer by PaymentMethod=' . print_r( $stripeCustomer, true ) );
			}
		}
        $taxIdType = MM_WPFS_Pricing::determineTaxIdType($taxId, $this->determineTaxCountry( $taxCountry, $billingAddress));
		if ( ! isset( $stripeCustomer ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Creating Stripe Customer with PaymentMethod...' );
			}
			$stripeCustomer = $this->stripe->createCustomerWithPaymentMethod(
			    $paymentMethod->id,
                MM_WPFS_Utils::determineCustomerName($cardHolderName, $businessName, $billingName),
                $cardHolderEmail,
                $metadata,
                $taxIdType,
                is_null( $taxIdType ) ? null : $taxId
            );
			$result->setCustomer( $stripeCustomer );
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Attaching PaymentMethod to existing Stripe Customer...' );
			}
			$attachedPaymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
				$stripeCustomer,
				$paymentMethod,
				/* set to default */
				true
			);
			$result->setPaymentMethod( $attachedPaymentMethod );

			$stripeCustomer->name       = MM_WPFS_Utils::determineCustomerName($cardHolderName, $businessName, $billingName);
            $stripeCustomer->metadata   = $metadata;
			$stripeCustomer->save();
		}
		if ( ! is_null( $billingAddress ) ) {
			$this->stripe->updateCustomerBillingAddress( $stripeCustomer, $billingName, $billingAddress );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe customer\'s billing address updated with=' . print_r( $billingAddress, true ) );
			}
		}
		if ( ! is_null( $shippingAddress ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe Customer\'s shipping address will be updated with=' . print_r( $shippingAddress, true ) );
			}
			$this->stripe->updateCustomerShippingAddress( $stripeCustomer, $shippingName, $cardHolderPhone, $shippingAddress );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe Customer\'s shipping address updated with=' . print_r( $shippingAddress, true ) );
			}
		}

		$stripeCustomer = $this->stripe->retrieveCustomer( $stripeCustomer->id );
		$result->setCustomer( $stripeCustomer );

		return $result;
	}

	/**
	 * @param MM_WPFS_Public_FormModel $formModel
	 * @param MM_WPFS_FormTransactionData $transactionData
	 * @param MM_WPFS_TransactionResult $transactionResult
	 */
	private function handleRedirect( $formModel, $transactionData, $transactionResult ) {
		if ( $transactionResult->isSuccess() ) {
			if ( 1 == $formModel->getForm()->redirectOnSuccess ) {
				if ( 1 == $formModel->getForm()->redirectToPageOrPost ) {
					if ( 0 != $formModel->getForm()->redirectPostID ) {
						$transactionDataKey = $this->transactionDataService->store( $transactionData );

						$pageOrPostUrl = get_page_link( $formModel->getForm()->redirectPostID );
						$pageOrPostUrl = add_query_arg(
							array(
								MM_WPFS_TransactionDataService::REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY => $transactionDataKey
							),
							$pageOrPostUrl
						);
						$transactionResult->setRedirect( true );
						$transactionResult->setRedirectURL( $pageOrPostUrl );
					} else {
						MM_WPFS_Utils::log( "handleRedirect(): Inconsistent form data: formName={$formModel->getFormName()}, doRedirect={$formModel->getForm()->redirectOnSuccess}, redirectPostID={$formModel->getForm()->redirectPostID}" );
					}
				} else {
					$transactionResult->setRedirect( true );
					$transactionResult->setRedirectURL( $formModel->getForm()->redirectUrl );
				}
			}
		}
	}

	/**
	 * @param $paymentIntentResult MM_WPFS_DonationPaymentIntentResult
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 * @param $title string
	 * @param $message string
	 *
	 * @return MM_WPFS_DonationPaymentIntentResult
	 */
	protected function createPaymentIntentResultActionRequired( &$paymentIntentResult, $paymentIntent, $title, $message ) {
		$paymentIntentResult->setSuccess( false );
		$paymentIntentResult->setRequiresAction( true );
		$paymentIntentResult->setPaymentIntentClientSecret( $paymentIntent->client_secret );
		$paymentIntentResult->setMessageTitle( $title );
		$paymentIntentResult->setMessage( $message );

		return $paymentIntentResult;
	}

	/**
	 * @param $paymentIntentResult MM_WPFS_DonationPaymentIntentResult
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 * @param $title string
	 * @param $message string
	 *
	 * @return MM_WPFS_DonationPaymentIntentResult
	 */
	protected function createPaymentIntentResultSuccess( &$paymentIntentResult, $title, $message ) {
		$paymentIntentResult->setRequiresAction( false );
		$paymentIntentResult->setSuccess( true );
		$paymentIntentResult->setMessageTitle( $title );
		$paymentIntentResult->setMessage( $message );

		return $paymentIntentResult;
	}

	/**
	 * @param $paymentIntentResult MM_WPFS_DonationPaymentIntentResult
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 * @param $title string
	 * @param $message string
	 *
	 * @return MM_WPFS_DonationPaymentIntentResult
	 */
	protected function createPaymentIntentResultFailed( &$paymentIntentResult, $title, $message ) {
		$paymentIntentResult->setSuccess( false );
		$paymentIntentResult->setMessageTitle( $title );
		$paymentIntentResult->setMessage( $message );

		return $paymentIntentResult;
	}

	/**
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 * @param $formName
	 */
	protected function addFormNameToPaymentIntent( &$paymentIntent, $formName ) {
		$paymentIntent->wpfs_form = $formName;
	}

	/**
	 * @param $formModel MM_WPFS_Public_FormModel
	 *
	 * @return boolean
	 */
	protected function modelNeedsPaymentIntent( $formModel ) {
		return empty( $formModel->getStripePaymentIntentId() );
	}

	/**
	 * @param $donationFormModel MM_WPFS_Public_DonationFormModel
	 * @param $transactionData MM_WPFS_DonationTransactionData
	 *
	 * @return \StripeWPFS\PaymentIntent
	 * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	protected function createPaymentIntentForDonation( $donationFormModel, $transactionData ) {
		$donationDescription = MM_WPFS_Utils::prepareStripeDonationDescription( $donationFormModel, $transactionData );

		$paymentIntent = $this->stripe->createPaymentIntent(
			$donationFormModel->getStripePaymentMethod()->id,
			$donationFormModel->getStripeCustomer()->id,
			$donationFormModel->getForm()->currency,
			$donationFormModel->getAmount(),
			true,
			$donationDescription,
			$donationFormModel->getMetadata(),
			MM_WPFS_Mailer::canSendDonationStripeReceipt( $donationFormModel->getForm() ) ? $donationFormModel->getCardHolderEmail() : null
		);

		return $paymentIntent;
	}

	/**
	 * @param $donationFormModel MM_WPFS_Public_DonationFormModel
	 * @param $transactionData MM_WPFS_DonationTransactionData
	 *
	 * @return \StripeWPFS\PaymentIntent
	 * @throws Exception
	 */
	protected function createOrRetrievePaymentIntent( $donationFormModel, $transactionData ) {
		$paymentIntent = null;

		if ( $this->modelNeedsPaymentIntent( $donationFormModel ) ) {
			$paymentIntent = $this->createPaymentIntentForDonation( $donationFormModel, $transactionData );

			$donationFormModel->setTransactionId( $paymentIntent->id );
			$transactionData->setTransactionId( $donationFormModel->getTransactionId() );
		} else {
			$paymentIntent = $this->stripe->retrievePaymentIntent( $donationFormModel->getStripePaymentIntentId() );

			if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
				$paymentIntent->confirm();

				$donationFormModel->setTransactionId( $paymentIntent->id );
				$transactionData->setTransactionId( $donationFormModel->getTransactionId() );
			}
		}

		return $paymentIntent;
	}

	/**
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 *
	 * @return boolean
	 */
	protected function paymentIntentRequiresAction( $paymentIntent ) {
		return \StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
		       && 'use_stripe_sdk' === $paymentIntent->next_action->type;
	}

	/**
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 *
	 * @return boolean
	 */
	protected function paymentIntentSucceeded( $paymentIntent ) {
		return \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
		       || \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status;
	}

	/**
	 * @param $formModel MM_WPFS_Public_FormModel
	 *
	 * @return string
	 */
	protected function findBillingName( $formModel ) {
		return is_null( $formModel->getBillingName() ) ? $formModel->getCardHolderName() : $formModel->getBillingName();
	}

	/**
	 * @param $donationFormModel MM_WPFS_Public_DonationFormModel
	 *
	 * @return MM_WPFS_CreateOrRetrieveCustomerResult
	 * @throws Exception
	 */
	protected function createOrRetrieveCustomerForDonation( $donationFormModel ) {
		$billingName = $this->findBillingName( $donationFormModel );

		$createOrRetrieveResult = $this->createOrRetrieveCustomer(
			$donationFormModel->getStripePaymentMethodId(),
			$donationFormModel->getStripePaymentIntentId(),
			$donationFormModel->getCardHolderName(),
			$donationFormModel->getCardHolderEmail(),
			$donationFormModel->getCardHolderPhone(),
            null,
			null,
			null,
			$billingName,
			$donationFormModel->getBillingAddress(),
			$donationFormModel->getShippingName(),
			$donationFormModel->getShippingAddress(),
			null
		);

		return $createOrRetrieveResult;
	}

	/**
	 * @param $donationFormModel MM_WPFS_Public_DonationFormModel
	 */
	protected function fireBeforeInlineDonationAction( $donationFormModel, $transactionData ) {
        $params = array(
            'email'                   => $donationFormModel->getCardHolderEmail(),
            'urlParameters'           => $donationFormModel->getFormGetParametersAsArray(),
            'formName'                => $donationFormModel->getFormName(),
            'currency'                => $transactionData->getCurrency(),
            'frequency'               => $donationFormModel->getDonationFrequency(),
            'amount'                  => $donationFormModel->getAmount(),
            'stripeClient'            => $this->stripe->getStripeClient()
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_DONATION_CHARGE, $params );
	}

	/**
	 * @param $donationFormModel MM_WPFS_Public_DonationFormModel
	 * @param $paymentIntent \StripeWPFS\PaymentIntent
	 */
	protected function fireAfterInlineDonationAction( $donationFormModel, $transactionData, $paymentIntent ) {
        $replacer = new MM_WPFS_DonationMacroReplacer( $donationFormModel->getForm(), $transactionData );

        $params = array(
            'email'                   => $donationFormModel->getCardHolderEmail(),
            'urlParameters'           => $donationFormModel->getFormGetParametersAsArray(),
            'formName'                => $donationFormModel->getFormName(),
            'currency'                => $transactionData->getCurrency(),
            'frequency'               => $donationFormModel->getDonationFrequency(),
            'amount'                  => $donationFormModel->getAmount(),
            'stripeClient'            => $this->stripe->getStripeClient(),
            'stripePaymentIntent'     => $paymentIntent,
            'stripeSubscription'      => $donationFormModel->getStripeSubscription(),
            'rawPlaceholders'         => $replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'   => $replacer->getDecoratedKeyValuePairs(),
        );

        do_action( MM_WPFS::ACTION_NAME_AFTER_DONATION_CHARGE, $params );
	}

	/**
	 * @param $donationFormModel MM_WPFS_Public_DonationFormModel
	 *
	 * @return MM_WPFS_ChargeResult
	 * @throws Exception
	 */
	private function processDonationPaymentIntentCharge( $donationFormModel ) {
		$paymentIntentResult = new MM_WPFS_DonationPaymentIntentResult();
		$paymentIntentResult->setNonce( $donationFormModel->getNonce() );

		$createOrRetrieveResult = $this->createOrRetrieveCustomerForDonation( $donationFormModel );
		$donationFormModel->setStripeCustomer( $createOrRetrieveResult->getCustomer() );
		$donationFormModel->setStripePaymentMethod( $createOrRetrieveResult->getPaymentMethod() );

		$transactionData = MM_WPFS_TransactionDataService::createDonationDataByFormModel( $donationFormModel );

		$this->fireBeforeInlineDonationAction( $donationFormModel, $transactionData );

		$paymentIntent = $this->createOrRetrievePaymentIntent( $donationFormModel, $transactionData );

		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if ( $this->paymentIntentRequiresAction( $paymentIntent ) ) {

				$this->createPaymentIntentResultActionRequired( $paymentIntentResult, $paymentIntent,
					/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', 'wp-full-stripe' ),
					/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The donation needs additional action before completion!', 'wp-full-stripe' ) );

			} else if ( $this->paymentIntentSucceeded( $paymentIntent ) ) {

				$this->addFormNameToPaymentIntent( $paymentIntent, $donationFormModel->getFormName() );

				$subscription = null;
				if ( $this->isRecurringDonation( $donationFormModel ) ) {
					$subscription = $this->createSubscriptionForDonation( $donationFormModel );
					$donationFormModel->setStripeSubscription( $subscription );
				}
				$this->db->insertInlineDonation( $donationFormModel, $paymentIntent, $subscription );

				$this->fireAfterInlineDonationAction( $donationFormModel, $transactionData, $paymentIntent );

				$this->createPaymentIntentResultSuccess( $paymentIntentResult,
					/* translators: Banner title of successful transaction */
					__( 'Success', 'wp-full-stripe' ),
					/* translators: Banner message of successful payment */
					__( 'Donation Successful!', 'wp-full-stripe' ) );

			} else {

				$this->createPaymentIntentResultFailed( $paymentIntentResult,
					/* translators: Banner title of failed transaction */
					__( 'Failed', 'wp-full-stripe' ),
					// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $paymentIntent->status ) );
			}
		} else {
			$this->createPaymentIntentResultFailed( $paymentIntentResult,
				/* translators: Banner title of failed transaction */
				__( 'Failed', 'wp-full-stripe' ),
				// This is an internal error, no need to localize it
				"PaymentIntent was neither created nor retrieved." );
		}

		$this->handleRedirect( $donationFormModel, $transactionData, $paymentIntentResult );

		if ( $paymentIntentResult->isSuccess() ) {
			if ( MM_WPFS_Mailer::canSendDonationPluginReceipt( $donationFormModel->getForm() )) {
                $this->mailer->sendDonationEmailReceipt( $donationFormModel->getForm(), $transactionData );
			}
		}

		return $paymentIntentResult;
	}


    /**
     * @param $formModel MM_WPFS_Public_PaymentFormModel|MM_WPFS_Public_SubscriptionFormModel
     */
	private function setCustomerAndPaymentMethodByFormModel( $formModel, $addMetadata ) {
        $billingName = $this->findBillingName( $formModel );

        $createOrRetrieveResult = $this->createOrRetrieveCustomer(
            $formModel->getStripePaymentMethodId(),
            $formModel->getStripePaymentIntentId(),
            $formModel->getCardHolderName(),
            $formModel->getCardHolderEmail(),
            $formModel->getCardHolderPhone(),
            $formModel->getBusinessName(),
            $formModel->getTaxId(),
            $formModel->getTaxCountry(),
            $billingName,
            $formModel->getBillingAddress(),
            $formModel->getShippingName(),
            $formModel->getShippingAddress(),
            $addMetadata ? $formModel->getMetadata() : null
        );

        $formModel->setStripeCustomer( $createOrRetrieveResult->getCustomer() );
        $formModel->setStripePaymentMethod( $createOrRetrieveResult->getPaymentMethod() );
    }

    /**
     * @param $paymentFormModel MM_WPFS_Public_PaymentFormModel
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     */
    private function fireBeforeInlinePaymentAction( $paymentFormModel, $transactionData ) {
        $params = array(
            'email'         => $paymentFormModel->getCardHolderEmail(),
            'urlParameters' => $paymentFormModel->getFormGetParametersAsArray(),
            'formName'      => $paymentFormModel->getFormName(),
            'priceId'       => $paymentFormModel->getPriceId(),
            'productName'   => $paymentFormModel->getProductName(),
            'currency'      => $transactionData->getCurrency(),
            'amount'        => $paymentFormModel->getAmount(),
            'stripeClient'  => $this->stripe->getStripeClient(),
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_PAYMENT_CHARGE, $params );
    }

    /**
     * @param $paymentFormModel MM_WPFS_Public_PaymentFormModel
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     */
    private function fireAfterInlinePaymentAction( $paymentFormModel, $transactionData, $paymentIntent ) {
        $replacer = new MM_WPFS_OneTimePaymentMacroReplacer( $paymentFormModel->getForm(), $transactionData );

        $params = array(
            'email'                   => $paymentFormModel->getCardHolderEmail(),
            'urlParameters'           => $paymentFormModel->getFormGetParametersAsArray(),
            'formName'                => $paymentFormModel->getFormName(),
            'priceId'                 => $paymentFormModel->getPriceId(),
            'productName'             => $paymentFormModel->getProductName(),
            'currency'                => $transactionData->getCurrency(),
            'amount'                  => $transactionData->getAmount(),
            'stripeClient'            => $this->stripe->getStripeClient(),
            'stripePaymentIntent'     => $paymentIntent,
            'rawPlaceholders'         => $replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'   => $replacer->getDecoratedKeyValuePairs(),
        );

        do_action( MM_WPFS::ACTION_NAME_AFTER_PAYMENT_CHARGE, $params );
    }

    /**
     * @param $formModel MM_WPFS_Public_PaymentFormModel
     * @return mixed
     */
    protected function getTaxCountry( $formModel ) {
        $result = null;

        $result = $formModel->getBillingAddressCountry();
        if ( empty( $result ) ) {
            $result = $formModel->getTaxCountry();
        }

        return $result;
    }

    /**
     * @param $formModel MM_WPFS_Public_PaymentFormModel
     * @return null
     */
    protected function getTaxState( $formModel ) {
        $result = null;

        $result = $formModel->getBillingAddressState();
        if ( empty( $result ) ) {
            $result = $formModel->getTaxState();
        }

        return $result;
    }

    /**
     * @param $formModel MM_WPFS_Public_PaymentFormModel|MM_WPFS_Public_SubscriptionFormModel
     */
    protected function getApplicableTaxRates( $formModel ) {
        $result = [];

        if ( $formModel->getForm()->vatRateType === MM_WPFS::FIELD_VALUE_TAX_RATE_FIXED) {
            $result = json_decode( $formModel->getForm()->vatRates );
        } else if ($formModel->getForm()->vatRateType === MM_WPFS::FIELD_VALUE_TAX_RATE_DYNAMIC) {
            $result = MM_WPFS_PriceCalculator::filterApplicableTaxRatesStatic(
                $this->getTaxCountry( $formModel ),
                $this->getTaxState( $formModel ),
                json_decode( $formModel->getForm()->vatRates ),
                $formModel->getTaxId()
            );
        }

        return $result;
    }

    /**
     * @param $transactionData MM_WPFS_PaymentTransactionData
     * @param $invoice \StripeWPFS\Invoice
     */
    private function updatePaymentTransactionDataPricing($transactionData, $invoice ) {
        $pricingDetails = MM_WPFS_Pricing::extractSimplifiedPricingFromInvoiceLineItems( $invoice->lines->data );

        $transactionData->setProductAmountDiscount( $pricingDetails->discountAmount );
        $transactionData->setProductAmountNet( $pricingDetails->totalAmount - $pricingDetails->discountAmount );
        $transactionData->setProductAmountTax( $pricingDetails->taxAmount );
        $transactionData->setProductAmountGross( $transactionData->getProductAmountNet() + $transactionData->getProductAmountTax() );
        $transactionData->setAmount( $transactionData->getProductAmountGross() );
    }

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return MM_WPFS_ChargeResult
	 * @throws Exception
	 */
	private function processPaymentIntentCharge( $paymentFormModel ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): ' . 'CALLED' );
		}

		$paymentIntentResult = new MM_WPFS_PaymentIntentResult();
		$paymentIntentResult->setNonce( $paymentFormModel->getNonce() );

		$this->setCustomerAndPaymentMethodByFormModel( $paymentFormModel, false);

		$transactionData         = MM_WPFS_TransactionDataService::createOneTimePaymentDataByModel( $paymentFormModel );
		$stripeChargeDescription = MM_WPFS_Utils::prepareStripeChargeDescription( $paymentFormModel, $transactionData );

		$this->fireBeforeInlinePaymentAction( $paymentFormModel, $transactionData );

		$paymentIntent = null;
		if ( empty( $paymentFormModel->getStripePaymentIntentId() ) ) {
		    $taxRateIds = MM_WPFS_Pricing::extractTaxRateIdsStatic( $this->getApplicableTaxRates( $paymentFormModel ));

            if ( $paymentFormModel->getForm()->generateInvoice == 1 ) {
                if ( MM_WPFS_Utils::hasToCapturePaymentIntentByFormModel( $paymentFormModel ) ) {
                    $stripeInvoice = $this->stripe->createInvoiceForOneTimePayment(
                        $paymentFormModel->getStripeCustomer()->id,
                        $paymentFormModel->getPriceId(),
                        $paymentFormModel->getForm()->currency,
                        $paymentFormModel->getAmount(),
                        $paymentFormModel->getProductName(),
                        is_null ( $paymentFormModel->getStripeCoupon() ) ? null : $paymentFormModel->getStripeCoupon()->id,
                        true,
                        $taxRateIds
                    );
                    $finalizedInvoice = $this->stripe->finalizeInvoice( $stripeInvoice->id );

                    $this->updatePaymentTransactionDataPricing( $transactionData, $finalizedInvoice );
                    $transactionData->setStripeInvoiceId( $finalizedInvoice->id );
                    $transactionData->setInvoiceUrl( $finalizedInvoice->invoice_pdf );
                    $transactionData->setInvoiceNumber( $finalizedInvoice->number );

                    $this->stripe->updatePaymentIntentByInvoice(
                        $finalizedInvoice,
                        $paymentFormModel->getStripePaymentMethodId(),
                        $stripeChargeDescription,
                        $paymentFormModel->getMetadata(),
                        MM_WPFS_Mailer::canSendPaymentStripeReceipt( $paymentFormModel->getForm() ) ? $paymentFormModel->getCardHolderEmail() : null
                    );

                    $paymentIntent = $this->stripe->retrievePaymentIntent( $finalizedInvoice->payment_intent );
                    $paymentFormModel->setTransactionId( $paymentIntent->id );
                    $transactionData->setTransactionId( $paymentFormModel->getTransactionId() );
                } else {
                    $stripeInvoice = $this->stripe->createInvoiceForOneTimePayment(
                        $paymentFormModel->getStripeCustomer()->id,
                        $paymentFormModel->getPriceId(),
                        $paymentFormModel->getForm()->currency,
                        $paymentFormModel->getAmount(),
                        $paymentFormModel->getProductName(),
                        is_null ( $paymentFormModel->getStripeCoupon() ) ? null : $paymentFormModel->getStripeCoupon()->id,
                        false,
                        $taxRateIds
                    );
                    $paidStripeInvoice = $this->stripe->payInvoiceOutOfBand( $stripeInvoice->id );

                    $this->updatePaymentTransactionDataPricing( $transactionData, $paidStripeInvoice );
                    $transactionData->setStripeInvoiceId( $paidStripeInvoice->id );
                    $transactionData->setInvoiceUrl( $paidStripeInvoice->invoice_pdf );
                    $transactionData->setInvoiceNumber( $paidStripeInvoice->number );

                    $paymentIntent = $this->stripe->createPaymentIntent(
                        $paymentFormModel->getStripePaymentMethod()->id,
                        $paymentFormModel->getStripeCustomer()->id,
                        $paymentFormModel->getForm()->currency,
                        $transactionData->getProductAmountGross(),
                        false,
                        $stripeChargeDescription,
                        $paymentFormModel->getMetadata(),
                        MM_WPFS_Mailer::canSendPaymentStripeReceipt( $paymentFormModel->getForm() ) ? $paymentFormModel->getCardHolderEmail() : null
                    );
                    $paymentFormModel->setTransactionId( $paymentIntent->id );
                    $transactionData->setTransactionId( $paymentFormModel->getTransactionId() );
                }
            } else {
                $previewInvoice = $this->stripe->createPreviewInvoiceForOneTimePayment(
                    $paymentFormModel->getTaxCountry(),
                    $paymentFormModel->getTaxState(),
                    $paymentFormModel->getPriceId(),
                    $paymentFormModel->getForm()->currency,
                    $paymentFormModel->getAmount(),
                    $paymentFormModel->getProductName(),
                    is_null ( $paymentFormModel->getStripeCoupon() ) ? null : $paymentFormModel->getStripeCoupon()->id,
                    $taxRateIds
                );

                $this->updatePaymentTransactionDataPricing( $transactionData, $previewInvoice );
                $transactionData->setStripeInvoiceId( null );
                $transactionData->setInvoiceUrl( null );
                $transactionData->setInvoiceNumber( null );

                $paymentIntent = $this->stripe->createPaymentIntent(
                    $paymentFormModel->getStripePaymentMethod()->id,
                    $paymentFormModel->getStripeCustomer()->id,
                    $paymentFormModel->getForm()->currency,
                    $transactionData->getProductAmountGross(),
                    MM_WPFS_Utils::hasToCapturePaymentIntentByFormModel( $paymentFormModel ),
                    $stripeChargeDescription,
                    $paymentFormModel->getMetadata(),
                    MM_WPFS_Mailer::canSendPaymentStripeReceipt( $paymentFormModel->getForm() ) ? $paymentFormModel->getCardHolderEmail() : null
                );
                $paymentFormModel->setTransactionId( $paymentIntent->id );
                $transactionData->setTransactionId( $paymentFormModel->getTransactionId() );
            }
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'processPaymentIntentCharge(): Retrieving PaymentIntent...' );
			}
			$paymentIntent = $this->stripe->retrievePaymentIntent( $paymentFormModel->getStripePaymentIntentId() );
			if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
                if ( \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CONFIRMATION === $paymentIntent->status ) {
                    $paymentIntent->confirm();
                }

				$paymentFormModel->setTransactionId( $paymentIntent->id );
				$transactionData->setTransactionId( $paymentFormModel->getTransactionId() );
			}
		}
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): paymentIntent=' . print_r( $paymentIntent, true ) );
		}

		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
				&& 'use_stripe_sdk' === $paymentIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'processPaymentIntentCharge(): PaymentIntent requires action...' );
				}
				$paymentIntentResult->setSuccess( false );
                $paymentIntentResult->setIsManualConfirmation(  $paymentIntent->confirmation_method === 'manual' );
				$paymentIntentResult->setRequiresAction( true );
				$paymentIntentResult->setPaymentIntentClientSecret( $paymentIntent->client_secret );
				$paymentIntentResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', 'wp-full-stripe' ) );
				$paymentIntentResult->setMessage(
				/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The payment needs additional action before completion!', 'wp-full-stripe' ) );
			} elseif (
				\StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'processPaymentIntentCharge(): PaymentIntent succeeded.' );
				}

                $paymentIntent->wpfs_form = $paymentFormModel->getFormName();
                $paymentFormModel->setStripePaymentIntent( $paymentIntent );

				$this->db->insertPayment( $paymentFormModel, $transactionData );

				$this->fireAfterInlinePaymentAction( $paymentFormModel, $transactionData, $paymentIntent );

				$paymentIntentResult->setRequiresAction( false );
				$paymentIntentResult->setSuccess( true );
				$paymentIntentResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', 'wp-full-stripe' ) );
				$paymentIntentResult->setMessage(
				/* translators: Banner message of successful payment */
					__( 'Payment Successful!', 'wp-full-stripe' ) );
			} else {
				$paymentIntentResult->setSuccess( false );
				$paymentIntentResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', 'wp-full-stripe' ) );
				$paymentIntentResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $paymentIntent->status ) );
			}
		}

		$this->handleRedirect( $paymentFormModel, $transactionData, $paymentIntentResult );

		if ( $paymentIntentResult->isSuccess() ) {
            if ( MM_WPFS_Mailer::canSendPaymentPluginReceipt( $paymentFormModel->getForm() )) {
				$this->mailer->sendOneTimePaymentReceipt( $paymentFormModel->getForm(), $transactionData );
            }
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): Returning paymentIntentResult=' . print_r( $paymentIntentResult, true ) );
		}

		return $paymentIntentResult;
	}

	/**
	 * @param MM_WPFS_TransactionResult $transactionResult
	 *
	 * @return array
	 */
	private function generateReturnValueFromTransactionResult( $transactionResult ) {
		$returnValue = array(
			'success'                   => $transactionResult->isSuccess(),
			'messageTitle'              => $transactionResult->getMessageTitle(),
			'message'                   => $transactionResult->getMessage(),
			'redirect'                  => $transactionResult->isRedirect(),
			'redirectURL'               => $transactionResult->getRedirectURL(),
			'requiresAction'            => $transactionResult->isRequiresAction(),
			'paymentIntentClientSecret' => $transactionResult->getPaymentIntentClientSecret(),
			'setupIntentClientSecret'   => $transactionResult->getSetupIntentClientSecret(),
			'formType'                  => $transactionResult->getFormType(),
			'nonce'                     => $transactionResult->getNonce(),
		);

		return $returnValue;
	}

	function fullstripe_inline_subscription_charge() {

		try {

			$subscriptionFormModel = new MM_WPFS_Public_InlineSubscriptionFormModel();
			$bindingResult         = $subscriptionFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = MM_WPFS_Utils::generateReturnValueFromBindings( $bindingResult );
			} else {
				$subscriptionResult = $this->processSubscription( $subscriptionFormModel );
				$return             = self::generateReturnValueFromTransactionResult( $subscriptionResult );
			}
		} catch ( WPFS_UserFriendlyException $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
			$messageTitle = is_null( $ex->getTitle() ) ?
				/* translators: Banner title of an error returned from an extension point by a developer */
				__( 'Internal Error', 'wp-full-stripe' ) :
				$ex->getTitle();
			$message      = $ex->getMessage();
			$return       = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $ex->getMessage()
			);
		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', 'wp-full-stripe' );
			$message      = $this->stripe->resolveErrorMessageByCode( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* Banner title of internal error */
					__( 'Internal Error', 'wp-full-stripe' ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_inline_subscription_charge_return_message', $return ) );
		exit;

	}

    /**
     * @param $subscriptionFormModel MM_WPFS_Public_SubscriptionFormModel
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     */
	protected function fireBeforeInlineSubscriptionAction( $subscriptionFormModel, $transactionData ) {
        $params = array(
            'email'                   => $subscriptionFormModel->getCardHolderEmail(),
            'urlParameters'           => $subscriptionFormModel->getFormGetParametersAsArray(),
            'formName'                => $subscriptionFormModel->getFormName(),
            'productName'             => $subscriptionFormModel->getProductName(),
            'planId'                  => $subscriptionFormModel->getStripePlanId(),
            'currency'                => $transactionData->getCurrency(),
            'amount'                  => $subscriptionFormModel->getPlanAmount(),
            'setupFee'                => $subscriptionFormModel->getSetupFee(),
            'quantity'                => $subscriptionFormModel->getStripePlanQuantity(),
            'stripeClient'            => $this->stripe->getStripeClient(),
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_SUBSCRIPTION_CHARGE, $params );
    }

    /**
     * @param $subscriptionFormModel MM_WPFS_Public_SubscriptionFormModel
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $subscription \StripeWPFS\Subscription
     */
    protected function fireAfterInlineSubscriptionAction( $subscriptionFormModel, $transactionData, $subscription) {
        $replacer = new MM_WPFS_SubscriptionMacroReplacer( $subscriptionFormModel->getForm(), $transactionData );

        $params = array(
            'email'                   => $subscriptionFormModel->getCardHolderEmail(),
            'urlParameters'           => $subscriptionFormModel->getFormGetParametersAsArray(),
            'formName'                => $subscriptionFormModel->getFormName(),
            'productName'             => $subscriptionFormModel->getProductName(),
            'planId'                  => $subscriptionFormModel->getStripePlanId(),
            'currency'                => $transactionData->getCurrency(),
            'amount'                  => $transactionData->getAmount(),
            'setupFee'                => $subscriptionFormModel->getSetupFee(),
            'quantity'                => $subscriptionFormModel->getStripePlanQuantity(),
            'stripeClient'            => $this->stripe->getStripeClient(),
            'stripeSubscription'      => $subscription,
            'rawPlaceholders'         => $replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'   => $replacer->getDecoratedKeyValuePairs(),
        );

        do_action( MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_CHARGE, $params );
    }

    /**
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $invoice \StripeWPFS\Invoice
     */
    private function updateSubscriptionTransactionDataPricing( & $transactionData, $invoice ) {
        $pricingDetails = MM_WPFS_Pricing::extractSubscriptionPricingFromInvoiceLineItems( $invoice->lines->data );

        $setupFeeAmount     = 0;
        $setupFeeTax        = 0;
        $setupFeeDiscount   = 0;
        if ( $pricingDetails->setupFee !== null ) {
            $setupFeeAmount     = $pricingDetails->setupFee->amount;
            $setupFeeTax        = $pricingDetails->setupFee->tax;
            $setupFeeDiscount   = $pricingDetails->setupFee->discount;
        }

        $transactionData->setPlanQuantity( $pricingDetails->product->quantity );

        $transactionData->setPlanNetAmountTotal( $pricingDetails->product->amount - $pricingDetails->product->discount );
        $transactionData->setPlanTaxAmountTotal( $pricingDetails->product->tax );
        $transactionData->setPlanGrossAmountTotal( $transactionData->getPlanNetAmountTotal() + $transactionData->getPlanTaxAmountTotal() );
        $transactionData->setPlanNetAmount( $transactionData->getPlanNetAmountTotal() / $transactionData->getPlanQuantity() );
        $transactionData->setPlanTaxAmount( $transactionData->getPlanTaxAmountTotal() / $transactionData->getPlanQuantity() );
        $transactionData->setPlanGrossAmount( $transactionData->getPlanGrossAmountTotal() / $transactionData->getPlanQuantity() );

        $transactionData->setSetupFeeNetAmountTotal( $setupFeeAmount - $setupFeeDiscount );
        $transactionData->setSetupFeeTaxAmountTotal( $setupFeeTax );
        $transactionData->setSetupFeeGrossAmountTotal( $transactionData->getSetupFeeNetAmountTotal() + $transactionData->getSetupFeeTaxAmountTotal() );
        $transactionData->setSetupFeeNetAmount( $transactionData->getSetupFeeNetAmountTotal() / $transactionData->getPlanQuantity() );
        $transactionData->setSetupFeeTaxAmount( $transactionData->getSetupFeeTaxAmountTotal() / $transactionData->getPlanQuantity() );
        $transactionData->setSetupFeeGrossAmount( $transactionData->getSetupFeeGrossAmountTotal() / $transactionData->getPlanQuantity() );

        $transactionData->setAmount( $transactionData->getPlanGrossAmountTotal() + $transactionData->getSetupFeeGrossAmountTotal() );
    }

    /**
	 * @param MM_WPFS_Public_SubscriptionFormModel $subscriptionFormModel
	 *
	 * @return MM_WPFS_SubscriptionResult
	 */
	private function processSubscription( $subscriptionFormModel ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processSubscription(): CALLED, subscriptionFormModel=' . print_r( $subscriptionFormModel, true ) );
		}

		$subscriptionResult = new MM_WPFS_SubscriptionResult();
		$subscriptionResult->setNonce( $subscriptionFormModel->getNonce() );

        $this->setCustomerAndPaymentMethodByFormModel( $subscriptionFormModel, false);
        $transactionData = MM_WPFS_TransactionDataService::createSubscriptionDataByModel( $subscriptionFormModel );

		$stripeSubscription  = null;
		$stripePaymentIntent = null;
		$stripeSetupIntent   = null;
		if (
			empty( $subscriptionFormModel->getStripePaymentIntentId() )
			&& empty( $subscriptionFormModel->getStripeSetupIntentId() )
		) {
            if ( $this->debugLog ) {
                MM_WPFS_Utils::log( 'processSubscription(): Creating Subscription...' );
            }

            $this->fireBeforeInlineSubscriptionAction( $subscriptionFormModel, $transactionData );

            $taxRateIds = MM_WPFS_Pricing::extractTaxRateIdsStatic( $this->getApplicableTaxRates( $subscriptionFormModel ));
            $stripeSubscription = $this->stripe->subscribe( $transactionData, $taxRateIds );

            $stripeCustomer = $this->stripe->retrieveCustomer( $stripeSubscription->customer );
            $subscriptionFormModel->setStripeCustomer( $stripeCustomer );
            $subscriptionFormModel->setStripeSubscription( $stripeSubscription );
            $subscriptionFormModel->setTransactionId( $stripeSubscription->id );
            $transactionData->setTransactionId( $stripeSubscription->id );
            if ( isset( $stripeSubscription->latest_invoice ) ) {
                $transactionData->setInvoiceUrl( $stripeSubscription->latest_invoice->invoice_pdf );
                $transactionData->setInvoiceNumber( $stripeSubscription->latest_invoice->number );
                $transactionData->setReceiptUrl( isset( $stripeSubscription->latest_invoice->charge ) ? $stripeSubscription->latest_invoice->charge->receipt_url : null );

                $this->updateSubscriptionTransactionDataPricing( $transactionData, $stripeSubscription->latest_invoice );
            }
            $transactionData->setStripeCustomerId( $stripeCustomer->id );

			if ( isset( $stripeSubscription ) ) {
				if ( isset( $stripeSubscription->latest_invoice ) && isset( $stripeSubscription->latest_invoice ) ) {
					if ( $stripeSubscription->latest_invoice instanceof \StripeWPFS\Invoice ) {
						$stripePaymentIntent = $stripeSubscription->latest_invoice->payment_intent;
                        $subscriptionFormModel->setStripePaymentIntent( $stripePaymentIntent );
						$subscriptionFormModel->setTransactionId( $stripeSubscription->id );
						$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
					} else {
						// todo tnagy retrieve if not expanded
					}
				}
				if ( isset( $stripeSubscription->pending_setup_intent ) ) {
					$stripeSetupIntent = $stripeSubscription->pending_setup_intent;
                    $subscriptionFormModel->setStripeSetupIntent( $stripeSetupIntent );
					$subscriptionFormModel->setTransactionId( $stripeSubscription->id );
					$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
				}
			}

			// tnagy insert subscriber
			$this->db->insertSubscriber( $subscriptionFormModel, $transactionData );

		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'processSubscription(): Retrieving Subscription...' );
			}
			if ( ! empty( $subscriptionFormModel->getStripePaymentIntentId() ) ) {
				$stripePaymentIntent = $this->stripe->retrievePaymentIntent( $subscriptionFormModel->getStripePaymentIntentId() );
				if ( $stripePaymentIntent instanceof \StripeWPFS\PaymentIntent ) {
					$stripeCustomer = $this->stripe->retrieveCustomer( $stripePaymentIntent->customer );
					$subscriptionFormModel->setStripeCustomer( $stripeCustomer );
					// tnagy update transaction id
					$wpfsSubscriber = $this->db->findSubscriberByPaymentIntentId( $stripePaymentIntent->id );
					if ( isset( $wpfsSubscriber ) && isset( $wpfsSubscriber->stripeSubscriptionID ) ) {
						$subscriptionFormModel->setTransactionId( $wpfsSubscriber->stripeSubscriptionID );
						$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
					}
				}
			}
			if ( ! empty( $subscriptionFormModel->getStripeSetupIntentId() ) ) {
				$stripeSetupIntent = $this->stripe->retrieveSetupIntent( $subscriptionFormModel->getStripeSetupIntentId() );
				if ( $stripeSetupIntent instanceof \StripeWPFS\SetupIntent ) {
					$stripeCustomer = $this->stripe->retrieveCustomer( $stripeSetupIntent->customer );
					$subscriptionFormModel->setStripeCustomer( $stripeCustomer );
					// tnagy update transaction id
					$wpfsSubscriber = $this->db->findSubscriberBySetupIntentId( $stripeSetupIntent->id );
					if ( isset( $wpfsSubscriber ) && isset( $wpfsSubscriber->stripeSubscriptionID ) ) {
						$subscriptionFormModel->setTransactionId( $wpfsSubscriber->stripeSubscriptionID );
						$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
					}
				}
			}
		}
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processSubscription(): paymentIntent=' . print_r( $stripePaymentIntent, true ) );
			MM_WPFS_Utils::log( 'processSubscription(): setupIntent=' . print_r( $stripeSetupIntent, true ) );
		}
		$this->handleIntent( $subscriptionResult, $stripeSubscription, $stripePaymentIntent, $stripeSetupIntent );
		$this->handleRedirect( $subscriptionFormModel, $transactionData, $subscriptionResult );
		if ( $subscriptionResult->isSuccess() ) {
            $this->fireAfterInlineSubscriptionAction( $subscriptionFormModel, $transactionData, $stripeSubscription );

            if ( MM_WPFS_Mailer::canSendSubscriptionPluginReceipt( $subscriptionFormModel->getForm() )) {
                $this->mailer->sendSubscriptionStartedEmailReceipt( $subscriptionFormModel->getForm(), $transactionData );
            }
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processSubscription(): FINISHED, result=' . print_r( $subscriptionResult, true ) );
		}

		return $subscriptionResult;
	}

	/**
	 * Updates the given result by the given PaymentIntent or SetupIntent. When no PaymentIntent nor
	 * SetupIntent are given, we consider the subscription as successful.
	 *
	 * @param MM_WPFS_SubscriptionResult $subscriptionResult
	 * @param \StripeWPFS\Subscription $subscription
	 * @param \StripeWPFS\PaymentIntent $paymentIntent
	 * @param \StripeWPFS\SetupIntent $setupIntent
	 */
	private function handleIntent( $subscriptionResult, $subscription, $paymentIntent, $setupIntent ) {
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
				&& 'use_stripe_sdk' === $paymentIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): PaymentIntent requires action...' );
				}
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setRequiresAction( true );
				$subscriptionResult->setPaymentIntentClientSecret( $paymentIntent->client_secret );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', 'wp-full-stripe' ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The payment needs additional action before completion!', 'wp-full-stripe' ) );
			} elseif (
				\StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): PaymentIntent succeeded.' );
				}
				$this->db->updateSubscriptionByPaymentIntentToRunning( $paymentIntent->id );
				$subscriptionResult->setRequiresAction( false );
				$subscriptionResult->setSuccess( true );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', 'wp-full-stripe' ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of successful payment */
					__( 'Payment Successful!', 'wp-full-stripe' ) );
			} else {
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', 'wp-full-stripe' ) );
				$subscriptionResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $paymentIntent->status ) );
			}
		} elseif ( $setupIntent instanceof \StripeWPFS\SetupIntent ) {
			if (
				\StripeWPFS\SetupIntent::STATUS_REQUIRES_ACTION === $setupIntent->status
				&& 'use_stripe_sdk' === $setupIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): SetupIntent requires action...' );
				}
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setRequiresAction( true );
				$subscriptionResult->setSetupIntentClientSecret( $setupIntent->client_secret );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', 'wp-full-stripe' ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The payment needs additional action before completion!', 'wp-full-stripe' ) );
			} elseif (
				\StripeWPFS\SetupIntent::STATUS_SUCCEEDED === $setupIntent->status
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): SetupIntent succeeded.' );
				}
				$this->db->updateSubscriptionBySetupIntentToRunning( $setupIntent->id );
				$subscriptionResult->setRequiresAction( false );
				$subscriptionResult->setSuccess( true );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', 'wp-full-stripe' ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of successful payment */
					__( 'Payment Successful!', 'wp-full-stripe' ) );
			} else {
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', 'wp-full-stripe' ) );
				$subscriptionResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $setupIntent->status ) );
			}
		} else {
			/*
			 * WPFS-1012: When a Subscription has a trial period without a setup fee then the Invoice has no
			 * PaymentIntent. When SCA is not triggered then the pending SetupIntent is also missing.
			 * In these cases the PaymentIntent and SetupIntent are both null.
			 * We consider these subscriptions as successful.
			 */
			$this->db->updateSubscriptionToRunning( $subscription->id );
			$subscriptionResult->setRequiresAction( false );
			$subscriptionResult->setSuccess( true );
			$subscriptionResult->setMessageTitle(
			/* translators: Banner title of successful transaction */
				__( 'Success', 'wp-full-stripe' ) );
			$subscriptionResult->setMessage(
			/* translators: Banner message of successful payment */
				__( 'Payment Successful!', 'wp-full-stripe' ) );
		}
	}

    /**
     * @param $saveCardFormModel MM_WPFS_Public_CheckoutPaymentFormModel
     */
    protected function fireBeforeCheckoutSaveCardAction( $saveCardFormModel ) {
        $params = array(
            'urlParameters' => $saveCardFormModel->getFormGetParametersAsArray(),
            'formName'      => $saveCardFormModel->getFormName(),
            'stripeClient'  => $this->stripe->getStripeClient(),
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_SAVE_CARD, $params );
    }

    /**
     * @param $paymentFormModel MM_WPFS_Public_CheckoutPaymentFormModel
     * @param $transactionData MM_WPFS_PaymentTransactionData
     */
    private function fireBeforeCheckoutPaymentAction( $paymentFormModel, $transactionData ) {
        $params = array(
            'urlParameters' => $paymentFormModel->getFormGetParametersAsArray(),
            'formName'      => $paymentFormModel->getFormName(),
            'priceId'       => $paymentFormModel->getPriceId(),
            'productName'   => $paymentFormModel->getProductName(),
            'currency'      => $transactionData->getCurrency(),
            'amount'        => $paymentFormModel->getAmount(),
            'stripeClient'  => $this->stripe->getStripeClient(),
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_PAYMENT_CHARGE, $params );
    }

    function fullstripe_checkout_payment_charge() {
		try {
			$paymentFormModel = new MM_WPFS_Public_CheckoutPaymentFormModel();
			$bindingResult    = $paymentFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = MM_WPFS_Utils::generateReturnValueFromBindings( $bindingResult );
			} else {
                if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $paymentFormModel->getForm()->customAmount ) {
                    $this->fireBeforeCheckoutSaveCardAction( $paymentFormModel );
                } else {
                    $transactionData = MM_WPFS_TransactionDataService::createOneTimePaymentDataByModel( $paymentFormModel );
                    $this->fireBeforeCheckoutPaymentAction( $paymentFormModel, $transactionData );
                }

				$checkoutSession = $this->checkoutSubmissionService->createCheckoutSessionByPaymentForm( $paymentFormModel );
				$return          = $this->generateReturnValueFromCheckoutSession( $checkoutSession );
			}
		} catch ( WPFS_UserFriendlyException $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
			$messageTitle = is_null( $ex->getTitle() ) ?
				/* translators: Banner title of an error returned from an extension point by a developer */
				__( 'Internal Error', 'wp-full-stripe' ) :
				$ex->getTitle();
			$message      = $ex->getMessage();
			$return       = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $ex->getMessage()
			);
		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', 'wp-full-stripe' );
			$message      = $this->stripe->resolveErrorMessageByCode( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', 'wp-full-stripe' ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_checkout_payment_charge_return_message', $return ) );
		exit;

	}

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     */
    protected function fireBeforeCheckoutDonationAction( $donationFormModel, $transactionData ) {
        $params = array(
            'urlParameters'           => $donationFormModel->getFormGetParametersAsArray(),
            'formName'                => $donationFormModel->getFormName(),
            'currency'                => $transactionData->getCurrency(),
            'frequency'               => $donationFormModel->getDonationFrequency(),
            'amount'                  => $donationFormModel->getAmount(),
            'stripeClient'            => $this->stripe->getStripeClient()
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_DONATION_CHARGE, $params );
    }

	function fullstripe_checkout_donation_charge() {
		try {

			$donationFormModel = new MM_WPFS_Public_CheckoutDonationFormModel();
			$bindingResult     = $donationFormModel->bind();
			if ( $bindingResult->hasErrors() ) {
				$return = MM_WPFS_Utils::generateReturnValueFromBindings( $bindingResult );
			} else {
			    $transactionData = MM_WPFS_TransactionDataService::createDonationDataByFormModel( $donationFormModel );
                $this->fireBeforeCheckoutDonationAction( $donationFormModel, $transactionData );

				$checkoutSession = $this->checkoutSubmissionService->createCheckoutSessionByDonationForm( $donationFormModel );
				$return          = $this->generateReturnValueFromCheckoutSession( $checkoutSession );
			}
		} catch ( WPFS_UserFriendlyException $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
			$messageTitle = is_null( $ex->getTitle() ) ?
				/* translators: Banner title of an error returned from an extension point by a developer */
				__( 'Internal Error', 'wp-full-stripe' ) :
				$ex->getTitle();
			$message      = $ex->getMessage();
			$return       = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $ex->getMessage()
			);
		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', 'wp-full-stripe' );
			$message      = $this->stripe->resolveErrorMessageByCode( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', 'wp-full-stripe' ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_checkout_donation_charge_return_message', $return ) );
		exit;

	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return array
	 */
	private function generateReturnValueFromCheckoutSession( $checkoutSession ) {
		return array(
			'success'           => true,
			'checkoutSessionId' => $checkoutSession->id
		);
	}

    /**
     * @param $subscriptionFormModel MM_WPFS_Public_SubscriptionFormModel
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     */
    protected function fireBeforeCheckoutSubscriptionAction( $subscriptionFormModel, $transactionData ) {
        $params = array(
            'urlParameters'           => $subscriptionFormModel->getFormGetParametersAsArray(),
            'formName'                => $subscriptionFormModel->getFormName(),
            'productName'             => $subscriptionFormModel->getProductName(),
            'planId'                  => $subscriptionFormModel->getStripePlanId(),
            'currency'                => $transactionData->getCurrency(),
            'amount'                  => $subscriptionFormModel->getPlanAmount(),
            'setupFee'                => $subscriptionFormModel->getSetupFee(),
            'quantity'                => $subscriptionFormModel->getStripePlanQuantity(),
            'stripeClient'            => $this->stripe->getStripeClient(),
        );

        do_action( MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_SUBSCRIPTION_CHARGE, $params );
    }

    function fullstripe_checkout_subscription_charge() {
		try {

			$subscriptionFormModel = new MM_WPFS_Public_CheckoutSubscriptionFormModel();
			$bindingResult         = $subscriptionFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = MM_WPFS_Utils::generateReturnValueFromBindings( $bindingResult );
			} else {
			    $this->fireBeforeCheckoutSubscriptionAction(
			        $subscriptionFormModel,
                    MM_WPFS_TransactionDataService::createSubscriptionDataByModel( $subscriptionFormModel )
                );

				$checkoutSession = $this->checkoutSubmissionService->createCheckoutSessionBySubscriptionForm( $subscriptionFormModel );
				$return          = $this->generateReturnValueFromCheckoutSession( $checkoutSession );
			}
		} catch ( WPFS_UserFriendlyException $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
			$messageTitle = is_null( $ex->getTitle() ) ?
				/* translators: Banner title of an error returned from an extension point by a developer */
				__( 'Internal Error', 'wp-full-stripe' ) :
				$ex->getTitle();
			$message      = $ex->getMessage();
			$return       = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $ex->getMessage()
			);
		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', 'wp-full-stripe' );
			$message      = $this->stripe->resolveErrorMessageByCode( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', 'wp-full-stripe' ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_checkout_subscription_charge_return_message', $return ) );
		exit;

	}

	public function retrieveCoupon( $code ) {
		try {
			return $this->stripe->retrieveCoupon( $code );
		} catch ( Exception $e ) {
			MM_WPFS_Utils::log( sprintf( 'Message=%s, Stack=%s ', $e->getMessage(), $e->getTraceAsString() ) );

			return null;
		}
	}

	function retrievePromotionalCode( $code ) {
		try {
			return $this->stripe->retrievePromotionalCode( $code );
		} catch ( Exception $e ) {
			return null;
		}
	}

    /**
     * @param $code string
     * @return \StripeWPFS\Coupon|null
     */
	function retrieveCouponByPromotionalCodeOrCouponCode($code ) {
        $result = null;

        try {
            $promotionalCode = $this->retrievePromotionalCode( $code );

            if ( ! is_null( $promotionalCode ) ) {
                if ( false == $promotionalCode->active ) {
                    $result = $this->retrieveCoupon( $code );
                } else {
                    $result = $promotionalCode->coupon;
                }
            } else {
                $result = $this->retrieveCoupon( $code );
            }
        } catch ( Exception $e ) {
            MM_WPFS_Utils::log( sprintf( 'Message=%s, Stack=%s ', $e->getMessage(), $e->getTraceAsString() ) );
        }

        return $result;
    }

	function fullstripe_check_coupon() {
        $return = [];
		$code = $_POST['code'];

        if ( empty( $code ) ) {
            $return = array(
                'msg_title' =>
                /* translators: Banner title for messages related to applying a coupon */
                    __( 'Coupon redemption', 'wp-full-stripe' ),
                'msg'       =>
                /* translators: Banner message of expired coupon */
                    __( 'Please enter a coupon code', 'wp-full-stripe' ),
                'valid'     => false
            );
        } else {
            $coupon = $this->retrieveCouponByPromotionalCodeOrCouponCode( $code );

            if ( is_null( $coupon ) || false == $coupon->valid ) {
                $return = array(
                    'msg_title' =>
                    /* translators: Banner title for messages related to applying a coupon */
                        __( 'Coupon redemption', 'wp-full-stripe' ),
                    'msg'       =>
                    /* translators: Banner message of expired coupon */
                        __( 'This coupon has expired', 'wp-full-stripe' ),
                    'valid'     => false
                );
            } else {
                $pricingData = new \StdClass;
                $pricingData->formType = $_POST['taxData']['formType'];
                $pricingData->formId = $_POST['taxData']['formId'];
                $pricingData->country = $_POST['taxData']['country'];
                $pricingData->state = $_POST['taxData']['state'];
                $pricingData->taxId = $_POST['taxData']['taxId'];
                $pricingData->couponCode = $coupon->id;
                $pricingData->couponPercentOff = ! ( $coupon->amount_off > 0 );
                $pricingData->customAmount = ! empty( $_POST['taxData']['customAmount'] ) ? $_POST['taxData']['customAmount'] : null;
                $pricingData->quantity = $_POST['taxData']['quantity'];

                $productPricing = MM_WPFS_Pricing::createFormPriceCalculator( $pricingData )->getProductPrices();

                $return = array(
                    'msg_title' =>
                    /* translators: Banner title for messages related to applying a coupon */
                        __( 'Coupon redemption', 'wp-full-stripe' ),
                    'msg'       =>
                    /* translators: Banner message of successfully applying a coupon */
                        __( 'The coupon has been applied successfully', 'wp-full-stripe' ),
                    'coupon'    => array(
                        'id'          => $coupon->id,
                        'name'        => $code,
                        'currency'    => $coupon->currency,
                        'percent_off' => $coupon->percent_off,
                        'amount_off'  => $coupon->amount_off
                    ),
                    'valid'          => true,
                    'productPricing' => $productPricing
                );
            }
        }

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

    public function calculatePricing() {
        try {
            $couponId = null;
            $coupon = null;

            if ( ! empty( $_POST['coupon'] ) ) {
                $coupon = $this->retrieveCouponByPromotionalCodeOrCouponCode( $_POST['coupon'] );
                $couponId = ! is_null( $coupon ) ? $coupon->id : null;
            }

            $pricingData = new \StdClass;
            $pricingData->formType = $_POST['formType'];
            $pricingData->formId = $_POST['formId'];
            $pricingData->country = $_POST['country'];
            $pricingData->state = $_POST['state'];
            $pricingData->taxId = $_POST['taxId'];
            $pricingData->couponCode = $couponId;
            $pricingData->customAmount = ! empty( $_POST['customAmount'] ) ? $_POST['customAmount'] : null;
            $pricingData->quantity = $_POST['quantity'];

            if ( ! empty( $pricingData->couponCode ) ) {
                $pricingData->couponPercentOff = ! ( $coupon->amount_off > 0 );
            } else {
                $pricingData->couponPercentOff = true;
            }

            $pricing = MM_WPFS_Pricing::createFormPriceCalculator( $pricingData )->getProductPrices();

            $return = array(
                'success'           => true,
                'productPricing'    => $pricing
            );
        } catch ( Exception $e ) {
            $return = array(
                'success' => false,
                'msg'     => __( 'There was an error calculating product pricing: ', 'wp-full-stripe-admin' ) . $e->getMessage()
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }
}

class MM_WPFS_TransactionResult {

	/**
	 * @var boolean
	 */
	protected $success = false;
	/**
	 * @var string
	 */
	protected $messageTitle;
	/**
	 * @var string
	 */
	protected $message;
	/**
	 * @var boolean
	 */
	protected $redirect = false;
	/**
	 * @var string
	 */
	protected $redirectURL;
	/**
	 * @var boolean
	 */
	protected $requiresAction = false;
	/**
	 * @var string
	 */
	protected $paymentIntentClientSecret;
	/**
	 * @var string
	 */
	protected $setupIntentClientSecret;
	/**
	 * @var string
	 */
	protected $formType;
	/**
	 * @var string
	 */
	protected $nonce;

    /**
     * @var boolean
     */
    protected $isManualConfirmation = false;

    /**
	 * @param boolean $success
	 */
	public function setSuccess( $success ) {
		$this->success = $success;
	}

	/**
	 * @return string
	 */
	public function getMessageTitle() {
		return $this->messageTitle;
	}

	/**
	 * @param string $messageTitle
	 */
	public function setMessageTitle( $messageTitle ) {
		$this->messageTitle = $messageTitle;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage( $message ) {
		$this->message = $message;
	}

	/**
	 * @return boolean
	 */
	public function isRedirect() {
		return $this->redirect;
	}

	/**
	 * @param boolean $redirect
	 */
	public function setRedirect( $redirect ) {
		$this->redirect = $redirect;
	}

	/**
	 * @return string
	 */
	public function getRedirectURL() {
		return $this->redirectURL;
	}

	/**
	 * @param string $redirectURL
	 */
	public function setRedirectURL( $redirectURL ) {
		$this->redirectURL = $redirectURL;
	}

	/**
	 * @return boolean
	 */
	public function isRequiresAction() {
		return $this->requiresAction;
	}

	/**
	 * @param boolean $requiresAction
	 */
	public function setRequiresAction( $requiresAction ) {
		$this->requiresAction = $requiresAction;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentIntentClientSecret() {
		return $this->paymentIntentClientSecret;
	}

	/**
	 * @param mixed $paymentIntentClientSecret
	 */
	public function setPaymentIntentClientSecret( $paymentIntentClientSecret ) {
		$this->paymentIntentClientSecret = $paymentIntentClientSecret;
	}

	/**
	 * @return string
	 */
	public function getSetupIntentClientSecret() {
		return $this->setupIntentClientSecret;
	}

	/**
	 * @param string $setupIntentClientSecret
	 */
	public function setSetupIntentClientSecret( $setupIntentClientSecret ) {
		$this->setupIntentClientSecret = $setupIntentClientSecret;
	}

	/**
	 * @return string
	 */
	public function getFormType() {
		return $this->formType;
	}

	/**
	 * @param string $formType
	 */
	public function setFormType( $formType ) {
		$this->formType = $formType;
	}

	/**
	 * @return string
	 */
	public function getNonce() {
		return $this->nonce;
	}

	/**
	 * @param string $nonce
	 */
	public function setNonce( $nonce ) {
		$this->nonce = $nonce;
	}

    /**
     * @return bool
     */
    public function isManualConfirmation(): bool {
        return $this->isManualConfirmation;
    }

    /**
     * @param bool $isManualConfirmation
     */
    public function setIsManualConfirmation( bool $isManualConfirmation ) {
        $this->isManualConfirmation = $isManualConfirmation;
    }

    /**
     * @return boolean
     */
    public function isSuccess() {
        return $this->success;
    }
}

class MM_WPFS_PaymentIntentResult extends MM_WPFS_ChargeResult {

    /**
	 * MM_WPFS_PaymentIntentResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
	}

    public function getAsArray() {
        return array(
            'success'                   => $this->success,
            'messageTitle'              => $this->messageTitle,
            'message'                   => $this->message,
            'redirect'                  => $this->redirect,
            'redirectURL'               => $this->redirectURL,
            'requiresAction'            => $this->requiresAction,
            'paymentIntentClientSecret' => $this->paymentIntentClientSecret,
            'setupIntentClientSecret'   => $this->setupIntentClientSecret,
            'formType'                  => $this->formType,
            'nonce'                     => $this->nonce,
            'isManualConfirmation'      => $this->isManualConfirmation,
        );
    }
}

class MM_WPFS_DonationPaymentIntentResult extends MM_WPFS_ChargeResult {

	/**
	 * MM_WPFS_DonationPaymentIntentResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_DONATION;
	}
}

class MM_WPFS_DonationCheckoutResult extends MM_WPFS_ChargeResult {

	/**
	 * MM_WPFS_DonationCheckoutResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_CHECKOUT_DONATION;
	}
}

class MM_WPFS_SetupIntentResult extends MM_WPFS_ChargeResult {

	/**
	 * MM_WPFS_PaymentIntentResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD;
	}

    public function getAsArray() {
        return array(
            'success'                   => $this->success,
            'messageTitle'              => $this->messageTitle,
            'message'                   => $this->message,
            'redirect'                  => $this->redirect,
            'redirectURL'               => $this->redirectURL,
            'requiresAction'            => $this->requiresAction,
            'paymentIntentClientSecret' => $this->paymentIntentClientSecret,
            'setupIntentClientSecret'   => $this->setupIntentClientSecret,
            'formType'                  => $this->formType,
            'nonce'                     => $this->nonce,
        );
    }
}


class MM_WPFS_ChargeResult extends MM_WPFS_TransactionResult {

	/**
	 * @var string
	 */
	protected $paymentType;

	/**
	 * @return string
	 */
	public function getPaymentType() {
		return $this->paymentType;
	}

	/**
	 * @param string $paymentType
	 */
	public function setPaymentType( $paymentType ) {
		$this->paymentType = $paymentType;
	}

}

class MM_WPFS_SubscriptionResult extends MM_WPFS_TransactionResult {

	/**
	 * MM_WPFS_SubscriptionResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
	}
}

class MM_WPFS_CreateOrRetrieveCustomerResult {

	/**
	 * @var \StripeWPFS\Customer
	 */
	private $customer;
	/**
	 * @var \StripeWPFS\PaymentMethod
	 */
	private $paymentMethod;

	/**
	 * @return \StripeWPFS\Customer
	 */
	public function getCustomer() {
		return $this->customer;
	}

	/**
	 * @param \StripeWPFS\Customer $customer
	 */
	public function setCustomer( $customer ) {
		$this->customer = $customer;
	}

	/**
	 * @return \StripeWPFS\PaymentMethod
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * @param \StripeWPFS\PaymentMethod $paymentMethod
	 */
	public function setPaymentMethod( $paymentMethod ) {
		$this->paymentMethod = $paymentMethod;
	}

}
