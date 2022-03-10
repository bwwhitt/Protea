<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.08.16.
 * Time: 14:50
 */

trait MM_WPFS_DonationTools {
    /* @var stripe MM_WPFS_Database */
    /* @var mailer MM_WPFS_Mailer */

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return boolean
     */
    private function isRecurringDonation( $donationFormModel ) {
        $res = false;

        $donationFrequencies = array(
            MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_DAILY,
            MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_WEEKLY,
            MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_MONTHLY,
            MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ANNUAL );

        if ( false === array_search( $donationFormModel->getDonationFrequency(), $donationFrequencies ) ) {
            $res = false;
        } else {
            $res = true;
        }

        return $res;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     */
    protected function sendDonationEmailReceipt ( $donationFormModel, $transactionData ) {
        $this->mailer->sendDonationEmailReceipt( $donationFormModel->getForm(), $transactionData );
    }

    /**
     * @param $currency string
     * @param $donationFrequency string
     *
     * @return string
     */
    protected function constructDonationPlanID( $currency, $donationFrequency ) {
        return MM_WPFS::DONATION_PLAN_ID_PREFIX . ucfirst( $currency ) . ucfirst( $donationFrequency );
    }

    protected function localizeDonationPlanName( $frequency ) {
        $res = "";

        switch( $frequency ) {
            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_DAILY:
                $res = __( 'Daily donation (%s)' );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_WEEKLY:
                $res = __( 'Weekly donation (%s)' );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_MONTHLY:
                $res = __( 'Monthly donation (%s)' );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ANNUAL:
                $res = __( 'Annual donation (%s)' );
                break;
        }

        return $res;

    }

    /**
     * @param $donationInterval string
     *
     * @return string
     */
    protected function translateFrequencyToInterval( $donationFrequency ) {
        $res = 'month';

        switch( $donationFrequency ) {
            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_DAILY:
                $res = 'day';
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_WEEKLY:
                $res = 'week';
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_MONTHLY:
                $res = 'month';
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ANNUAL:
                $res = 'year';
                break;
        }

        return $res;
    }

    /**
     * @param $planID string
     * @param $currency string
     * @param $donationFrequency string
     *
     * @return \StripeWPFS\Plan
     * @throws Exception
     */
    protected function createDonationPlan( $planID, $currency, $donationFrequency ) {
        $planName = sprintf( $this->localizeDonationPlanName( $donationFrequency ), strtoupper( $currency ));
        $interval = $this->translateFrequencyToInterval( $donationFrequency );

        $plan = $this->stripe->createRecurringDonationPlan( $planID, $planName, $currency, $interval, 1 );

        return $plan;
    }

    /**
     * @param $planId
     *
     * @return \StripeWPFS\Price
     */
    protected function retrieveDonationPlan( $planId ) {
        $plan = $this->stripe->retrievePlan( $planId );

        if ( is_null( $plan ) || ! $plan->active ) {
            $plan = null;
            $plans = $this->stripe->retrieveDonationPlansWithLookupKey( $planId );

            if ( count( $plans->data ) > 0 ) {
                $plan = $plans->data[0];
            }
        }

        return $plan;
    }

    /**
     * @param $currency string
     * @param $donationFrequency string
     *
     * @return \StripeWPFS\Plan
     * @throws Exception
     */
    protected function createOrRetrieveDonationPlan( $currency, $donationFrequency ) {
        $planId = $this->constructDonationPlanID( $currency, $donationFrequency );

        $plan = $this->retrieveDonationPlan( $planId );
        if ( is_null( $plan )) {
            $plan = $this->createDonationPlan( $planId, $currency, $donationFrequency );
        }

        return $plan;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return \StripeWPFS\Subscription
     * @throws Exception
     */
    protected function createSubscriptionForDonation( $donationFormModel ) {
        $plan = $this->createOrRetrieveDonationPlan( $donationFormModel->getForm()->currency, $donationFormModel->getDonationFrequency() );
        $subscription = $this->stripe->subscribeCustomerToPlan( $donationFormModel->getStripeCustomer()->id, $plan->id );

        $this->stripe->createUsageRecordForSubscription( $subscription, $donationFormModel->getAmount() );

        return $subscription;
    }
}

trait MM_WPFS_CheckoutTaxTools {
    /**
     * @param $stripeCustomer \StripeWPFS\Customer
     */
    protected function getTaxIdFromCustomer( $stripeCustomer ) {
        $taxIds = $stripeCustomer->tax_ids->data;

        if ( count( $taxIds ) > 0 ) {
            $customerTaxId = $taxIds[0]['value'];
        } else {
            $customerTaxId = null;
        }

        return $customerTaxId;
    }

    /**
     * @param $transactionData MM_WPFS_PaymentTransactionData|MM_WPFS_SubscriptionTransactionData
     * @param $stripeInvoice \StripeWPFS\Invoice
     */
    protected function setTransactionDataFromInvoice(& $transactionData, $invoice ) {
        $transactionData->setStripeInvoiceId( $invoice->id );
        $transactionData->setInvoiceUrl( $invoice->invoice_pdf );
        $transactionData->setInvoiceNumber( $invoice->number );
    }
}

abstract class MM_WPFS_CheckoutChargeHandler {

	/**
	 * @var bool
	 */
	protected $debugLog = false;
	/**
	 * @var MM_WPFS_Database
	 */
	protected $db;
	/**
	 * @var MM_WPFS_Stripe
	 */
	protected $stripe;
	/**
	 * @var MM_WPFS_CheckoutSubmissionService
	 */
	protected $checkoutSubmissionService;
	/**
	 * @var MM_WPFS_TransactionDataService
	 */
	protected $transactionDataService;
	/**
	 * @var MM_WPFS_Mailer
	 */
	protected $mailer;
	/**
	 * @var MM_WPFS_EventHandler
	 */
	protected $eventHandler;
	/** @var MM_WPFS_LoggerService */
	private $loggerService = null;

	/**
	 * MM_WPFS_CheckoutChargeHandler constructor.
	 */
	public function __construct() {
		$this->db                        = new MM_WPFS_Database();
		$this->stripe                    = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
		$this->checkoutSubmissionService = new MM_WPFS_CheckoutSubmissionService();
		$this->transactionDataService    = new MM_WPFS_TransactionDataService();
		$this->mailer                    = new MM_WPFS_Mailer();
		$this->loggerService             = new MM_WPFS_LoggerService();
		$this->eventHandler              = new MM_WPFS_EventHandler(
			$this->db,
			$this->mailer,
			$this->loggerService
		);
	}

	/**
	 * @param MM_WPFS_Public_CheckoutPaymentFormModel|MM_WPFS_Public_CheckoutSubscriptionFormModel $formModel
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return MM_WPFS_ChargeResult
	 */
	public abstract function handle( $formModel, $checkoutSession );

	/**
	 * @param MM_WPFS_Public_FormModel $formModel
	 * @param MM_WPFS_FormTransactionData $transactionData
	 * @param MM_WPFS_TransactionResult $transactionResult
	 */
	protected function handleRedirect( $formModel, $transactionData, $transactionResult ) {
		if ( $transactionResult->isSuccess() ) {
			if ( 1 == $formModel->getForm()->redirectOnSuccess ) {
				if ( 1 == $formModel->getForm()->redirectToPageOrPost ) {
					if ( 0 != $formModel->getForm()->redirectPostID ) {
                        $transactionDataKey = $this->transactionDataService->store( $transactionData );

						$pageOrPostUrl = get_page_link( $formModel->getForm()->redirectPostID );
                        $pageOrPostUrl      = add_query_arg(
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
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $paymentMethod \StripeWPFS\PaymentMethod
     */
    protected function setBillingAddress( & $stripeCustomer, & $paymentMethod ) {
        $stripeCustomer->name = $paymentMethod->billing_details->name;

        if ( isset( $paymentMethod->billing_details->address ) ) {
            $stripeCustomer->address = $paymentMethod->billing_details->address;
        }
    }

    /**
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $checkoutSession \StripeWPFS\Checkout\Session
     */
    protected function setShippingAddress( & $stripeCustomer, & $checkoutSession ) {
        if ( isset( $checkoutSession->shipping->address )) {
            $shipping = array(
                'name'      => $checkoutSession->shipping->name,
                'address'   => $checkoutSession->shipping->address
            );

            $stripeCustomer->shipping = $shipping;
        }
    }

    /**
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $paymentMethod \StripeWPFS\PaymentMethod
     * @param $checkoutSession \StripeWPFS\Checkout\Session
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    protected function fixCustomerNamesAndAddresses(  & $stripeCustomer, & $paymentMethod, & $checkoutSession ) {
        $this->setBillingAddress( $stripeCustomer, $paymentMethod );
        $this->setShippingAddress( $stripeCustomer, $checkoutSession );
        $stripeCustomer->save();
    }
}

class MM_WPFS_CheckoutPaymentChargeHandler extends MM_WPFS_CheckoutChargeHandler {
    use MM_WPFS_CheckoutTaxTools;

    /**
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $formModel MM_WPFS_Public_CheckoutPaymentFormModel
     * @param $transactionData MM_WPFS_SaveCardTransactionData
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    private function setMetadataAndDescriptionForStripeCustomer(& $stripeCustomer, $formModel, $transactionData ) {
        $stripeCardSavedDescription  = MM_WPFS_Utils::prepareStripeCardSavedDescription( $formModel, $transactionData );

        $stripeCustomer->description = empty( $stripeCardSavedDescription ) ? null : $stripeCardSavedDescription;
        $stripeCustomer->metadata    = array_merge( $formModel->getMetadata(), $stripeCustomer->metadata->toArray() );
        $stripeCustomer->save();
    }

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $formModel MM_WPFS_Public_CheckoutPaymentFormModel
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    private function setMetadataAndDescriptionForPaymentIntent(& $paymentIntent, $formModel, $transactionData ) {
        $stripePaymentIntentDescription = MM_WPFS_Utils::prepareStripeChargeDescription( $formModel, $transactionData );

        $paymentIntent->description = empty( $stripePaymentIntentDescription ) ? null : $stripePaymentIntentDescription;
        $paymentIntent->metadata    = array_merge( $formModel->getMetadata(), $paymentIntent->metadata->toArray() );;
        $paymentIntent->save();

        $paymentIntent = $this->stripe->retrievePaymentIntent( $paymentIntent->id );
        $paymentIntent->wpfs_form = $formModel->getFormName();
    }

    /**
     * @param $paymentMethod \StripeWPFS\PaymentMethod
     * @param $formModel MM_WPFS_Public_CheckoutPaymentFormModel
     *
     * @return \StripeWPFS\Customer
     */
    private function findOrCreateStripeCustomer( & $paymentMethod, $formModel ) {
        $stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByPaymentMethod( $paymentMethod );

        if ( is_null( $stripeCustomer ) ) {
            $stripeCustomer = MM_WPFS_Utils::findExistingStripeCustomerAnywhereByEmail(
                $this->db,
                $this->stripe,
                $paymentMethod->billing_details->email
            );
        }
        if ( is_null( $stripeCustomer ) ) {
            $stripeCustomer = $this->stripe->createCustomerWithPaymentMethod(
                $paymentMethod->id,
                $paymentMethod->billing_details->name,
                $paymentMethod->billing_details->email,
                $formModel->getMetadata()
            );
        } else {
            $paymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
                $stripeCustomer,
                $paymentMethod,
                /* set to default */
                true
            );
        }

        return $stripeCustomer;
    }

    /**
     * @param $saveCardFormModel MM_WPFS_Public_CheckoutPaymentFormModel
     * @param $transactionData MM_WPFS_SaveCardTransactionData
     * @param $stripeCustomer \StripeWPFS\Customer
     */
    protected function fireAfterCheckoutSaveCardAction( $saveCardFormModel, $transactionData, $stripeCustomer ) {
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

        do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_SAVE_CARD, $params );
    }

    /**
     * @param $paymentFormModel MM_WPFS_Public_PaymentFormModel
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     */
    private function fireAfterCheckoutPaymentAction( $paymentFormModel, $transactionData, $paymentIntent ) {
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

        do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_PAYMENT_CHARGE, $params );
    }

    /**
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     * @param $stripeCustomer \StripeWPFS\Customer
     */
    private function setTransactionDataFromContext( & $transactionData, $context ) {
        $transactionData->setCustomerTaxId( $context->customerTaxId );

        $transactionData->setAmount( $context->invoiceData->amount );
        $transactionData->setProductAmountGross( $context->invoiceData->amount );
        $transactionData->setProductAmountTax( $context->invoiceData->taxAmount );
        $transactionData->setProductAmountNet( $context->invoiceData->amount - $context->invoiceData->taxAmount );
        $transactionData->setProductAmountDiscount( $context->invoiceData->discountAmount );
    }

    /**
     * @param $checkoutSession \StripeWPFS\Checkout\Session
     */
    protected function getInvoiceDataFromCheckoutSession( $checkoutSession ) {
        $invoiceData = new \StdClass;

        if ( count( $checkoutSession->line_items->data ) > 0 ) {
            $lineItem = $checkoutSession->line_items->data[0];

            $invoiceData->amount        = $lineItem['amount_total'];
            $invoiceData->currency      = $lineItem['currency'];
            $invoiceData->description   = $lineItem['description'];

            if ( count( $lineItem['discounts'] ) > 0 ) {
                $discountItem = $lineItem['discounts'][0];

                $invoiceData->discountAmount = $discountItem->amount;
                $invoiceData->couponCode = $discountItem->discount->coupon['name'];
            } else {
                $invoiceData->discountAmount = 0;
                $invoiceData->couponCode = '';
            }

            $invoiceData->unitAmount = $lineItem['price']->unit_amount;
            $invoiceData->quantity = $lineItem['quantity'];

            if ( count( $lineItem['taxes'] ) > 0 ) {
                $taxRates = array();
                $taxAmount = 0;

                foreach ( $lineItem['taxes'] as $tax ) {
                    $taxAmount += $tax->amount;
                    array_push( $taxRates, $tax->rate['id'] );
                }

                $invoiceData->taxRates = $taxRates;
                $invoiceData->taxAmount = $taxAmount;
            } else {
                $invoiceData->taxRates = array();
                $invoiceData->taxAmount = 0;
            }
        } else {
            $invoiceData->amount = 0;
            $invoiceData->currency = 'usd';
            $invoiceData->description = '';
            $invoiceData->discountAmount = 0;
            $invoiceData->couponCode = '';
            $invoiceData->taxRates = array();
            $invoiceData->taxAmount = 0;
        }

        return $invoiceData;
    }

    /**
     * @param $checkoutSession \StripeWPFS\Checkout\Session
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $paymentMethod \StripeWPFS\PaymentMethod
     *
     * @returns \StdClass
     */
    protected function getContextDataFromStripeObjects( $checkoutSession, $stripeCustomer, $paymentIntent, $paymentMethod ) {
        $ctx = new \StdClass;

        $ctx->customerTaxId = $this->getTaxIdFromCustomer( $stripeCustomer );
        $ctx->invoiceData   = $this->getInvoiceDataFromCheckoutSession( $checkoutSession );

        return $ctx;
    }

    /**
     * @param MM_WPFS_Public_CheckoutPaymentFormModel $formModel
     * @param \StripeWPFS\Checkout\Session $checkoutSession
     * @return MM_WPFS_ChargeResult
     * @throws \StripeWPFS\Exception\ApiErrorException
     *
     */
	public function handle( $formModel, $checkoutSession ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): CALLED' );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): formModel=' . print_r( $formModel, true ) );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): checkoutSession=' . print_r( $checkoutSession, true ) );
		}
		$chargeResult    = new MM_WPFS_ChargeResult();

		$transactionData = null;
		if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $formModel->getForm()->customAmount ) {
			// tnagy update result with payment type
			$chargeResult->setPaymentType( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE );

			$setupIntent = $this->checkoutSubmissionService->retrieveStripeSetupIntentByCheckoutSession( $checkoutSession );
			$paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodBySetupIntent( $setupIntent );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): paymentMethod=' . print_r( $paymentMethod, true ) );
			}

            if ( isset( $paymentMethod ) && $paymentMethod instanceof \StripeWPFS\PaymentMethod ) {
                $stripeCustomer = $this->findOrCreateStripeCustomer( $paymentMethod, $formModel );

                $formModel->setTransactionId( $stripeCustomer->id );
                $formModel->setStripePaymentMethod( $paymentMethod );

                $this->fixCustomerNamesAndAddresses( $stripeCustomer, $paymentMethod, $checkoutSession );
                $formModel->setStripeCustomer( $stripeCustomer, true );

                $transactionData = MM_WPFS_TransactionDataService::createSaveCardDataByModel( $formModel );
                $this->setMetadataAndDescriptionForStripeCustomer( $stripeCustomer, $formModel, $transactionData );

				$this->db->insertSavedCard( $formModel, $transactionData );

				$this->fireAfterCheckoutSaveCardAction( $formModel, $transactionData, $stripeCustomer );

				do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_SAVE_CARD, $stripeCustomer );

                if ( MM_WPFS_Mailer::canSendSaveCardPluginReceipt( $formModel->getForm() )) {
                    $this->mailer->sendSaveCardNotification( $formModel->getForm(), $transactionData );
                }

                $chargeResult->setSuccess( true );
				$chargeResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', 'wp-full-stripe' ) );
				$chargeResult->setMessage(
				/* translators: Banner message of saving card successfully */
					__( 'Card saved successfully!', 'wp-full-stripe' ) );
			} else {
				$chargeResult->setSuccess( false );
				$chargeResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', 'wp-full-stripe' ) );
				$chargeResult->setMessage(
				/* It's an internal error, no need to localize it */
					'Cannot find PaymentMethod!' );
			}
		} else {
			// tnagy retrieve Stripe Customer and update form model
			$stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByCheckoutSession( $checkoutSession );
			$paymentIntent = $this->checkoutSubmissionService->retrieveStripePaymentIntentByCheckoutSession( $checkoutSession );
            $paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent( $paymentIntent );

            if ( $this->debugLog ) {
                MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): paymentIntent=' . print_r( $paymentIntent, true ) );
                MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): paymentMethod=' . print_r( $paymentMethod, true ) );
            }

            $ctx = $this->getContextDataFromStripeObjects( $checkoutSession, $stripeCustomer, $paymentIntent, $paymentMethod );

            $this->fixCustomerNamesAndAddresses( $stripeCustomer, $paymentMethod, $checkoutSession );
            $formModel->setStripeCustomer( $stripeCustomer, true );
            $formModel->setStripePaymentMethod( $paymentMethod );
            $formModel->setTransactionId( $paymentIntent->id );

            $transactionData = MM_WPFS_TransactionDataService::createOneTimePaymentDataByModel( $formModel );
            $this->setTransactionDataFromContext( $transactionData, $ctx );

			$this->setMetadataAndDescriptionForPaymentIntent( $paymentIntent, $formModel, $transactionData );
            $formModel->setStripePaymentIntent( $paymentIntent );

			$this->db->insertPayment( $formModel, $transactionData );

            if ( $formModel->getForm()->generateInvoice == 1 ) {
                $stripeInvoice = $this->stripe->createInvoiceForOneTimePayment(
                    $stripeCustomer->id,
                    $formModel->getPriceId(),
                    $formModel->getForm()->currency,
                    $formModel->getAmount(),
                    $formModel->getProductName(),
                    is_null ( $formModel->getStripeCoupon() ) ? null : $formModel->getStripeCoupon()->id,
                    false,
                    $ctx->invoiceData->taxRates
                );
                $paidStripeInvoice = $this->stripe->payInvoiceOutOfBand( $stripeInvoice->id );
                $this->setTransactionDataFromInvoice( $transactionData, $paidStripeInvoice );
            }

            $this->fireAfterCheckoutPaymentAction( $formModel, $transactionData, $paymentIntent );

            if ( MM_WPFS_Mailer::canSendPaymentPluginReceipt( $formModel->getForm() )) {
                $this->mailer->sendOneTimePaymentReceipt( $formModel->getForm(), $transactionData );
            }

            $chargeResult->setSuccess( true );
			$chargeResult->setMessageTitle(
			/* translators: Banner title of successful transaction */
				__( 'Success', 'wp-full-stripe' ) );
			$chargeResult->setMessage(
			/* translators: Banner message of successful payment */
				__( 'Payment Successful!', 'wp-full-stripe' ) );
		}

		$this->handleRedirect( $formModel, $transactionData, $chargeResult );

		return $chargeResult;
	}
}

class MM_WPFS_CheckoutDonationChargeHandler extends MM_WPFS_CheckoutChargeHandler {
    use MM_WPFS_DonationTools;

    /**
     * @param $formModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     *
     * @throws Exception
     */
    private function updatePaymentIntent($formModel, $transactionData, & $paymentIntent ) {
        $paymentIntent->description = MM_WPFS_Utils::prepareStripeDonationDescription($formModel, $transactionData);
        $paymentIntent->metadata = array_merge( $formModel->getMetadata(), $paymentIntent->metadata->toArray() );;
        $paymentIntent->save();
        $paymentIntent = $this->stripe->retrievePaymentIntent( $paymentIntent->id );
    }

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $formName
     */
    protected function addFormNameToPaymentIntent( & $paymentIntent, $formName ) {
        $paymentIntent->wpfs_form = $formName;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     */
    protected function fireAfterCheckoutDonationAction ( $donationFormModel, $transactionData, $paymentIntent ) {
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

        do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_DONATION_CHARGE, $params );
    }

    /**
     * @param $donationResult MM_WPFS_DonationCheckoutResult
     * @param $title string
     * @param $message string
     *
     * @return MM_WPFS_DonationCheckoutResult
     */
    protected function createDonationResultSuccess( & $donationResult, $title, $message ) {
        $donationResult->setSuccess( true );
        $donationResult->setMessageTitle( $title );
        $donationResult->setMessage( $message );

        return $donationResult;
    }


    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $stripeCustomer \StripeWPFS\Customer
     *
     * @return \StripeWPFS\PaymentMethod
     */
    protected function setDefaultPaymentMethodFromPaymentIntent( $paymentIntent, & $stripeCustomer ) {
        $paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent( $paymentIntent );
        if ( ! is_null( $paymentMethod ) ) {
            $paymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
                $stripeCustomer,
                $paymentMethod,
                /* set to default */
                true
            );
        }

        return $paymentMethod;
    }

    /**
     * @param MM_WPFS_Public_DonationFormModel $formModel
     * @param \StripeWPFS\Checkout\Session $checkoutSession
     *
     * @return MM_WPFS_ChargeResult
     * @throws Exception
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function handle( $formModel, $checkoutSession) {

        if ($this->debugLog) {
            MM_WPFS_Utils::log(__CLASS__ . __FUNCTION__ . '(): CALLED');
            MM_WPFS_Utils::log(__CLASS__ . __FUNCTION__ . '(): formModel=' . print_r($formModel, true));
            MM_WPFS_Utils::log(__CLASS__ . __FUNCTION__ . '(): checkoutSession=' . print_r($checkoutSession, true));
        }

        $chargeResult = new MM_WPFS_DonationCheckoutResult();

        $stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByCheckoutSession( $checkoutSession );
        $paymentIntent = $this->checkoutSubmissionService->retrieveStripePaymentIntentByCheckoutSession( $checkoutSession );
        $paymentMethod = $this->setDefaultPaymentMethodFromPaymentIntent( $paymentIntent, $stripeCustomer );

        $this->fixCustomerNamesAndAddresses(  $stripeCustomer, $paymentMethod, $checkoutSession );

        $formModel->setTransactionId( $paymentIntent->id );
        $formModel->setStripePaymentMethod( $paymentMethod );
        $formModel->setStripeCustomer( $stripeCustomer, true );

        $transactionData = MM_WPFS_TransactionDataService::createDonationDataByFormModel($formModel);

        $this->updatePaymentIntent( $formModel, $transactionData, $paymentIntent );
        $this->addFormNameToPaymentIntent( $paymentIntent, $formModel->getFormName() );

        $subscription = null;
        if ( $this->isRecurringDonation( $formModel )) {
            $subscription = $this->createSubscriptionForDonation( $formModel );
        }
        $this->db->insertCheckoutDonation( $formModel, $paymentIntent, $subscription );

        $this->fireAfterCheckoutDonationAction( $formModel, $transactionData, $paymentIntent );

        // tnagy update result
        $this->createDonationResultSuccess( $chargeResult,
                                            /* translators: Banner title of successful transaction */
                                            __('Success', 'wp-full-stripe'),
                                            /* translators: Banner message of successful payment */
                                            __('Donation Successful!', 'wp-full-stripe'));

        $this->handleRedirect($formModel, $transactionData, $chargeResult);

        if ( MM_WPFS_Mailer::canSendDonationPluginReceipt( $formModel->getForm() )) {
            $this->mailer->sendDonationEmailReceipt( $formModel->getForm(), $transactionData );
        }

        return $chargeResult;
    }
}


class MM_WPFS_CheckoutSubscriptionChargeHandler extends MM_WPFS_CheckoutChargeHandler {
    use MM_WPFS_CheckoutTaxTools;

    /**
     * @param $subscription \StripeWPFS\Subscription
     *
     * @return string
     */
    private function getCouponCode( $subscription ) {
        $res = null;

        if ( isset( $subscription->discount ) ) {
            $discount = $subscription->discount;

            if ( isset( $discount->promotion_code ) && isset( $discount->promotion_code->code ) ) {
                $res = $discount->promotion_code->code;
            } elseif ( isset( $discount->coupon ) && isset( $discount->coupon->name ) ) {
                $res = $discount->coupon->name;
            }
        }

        return $res;
    }

    /**
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $subscription \StripeWPFS\Subscription
     * @param $formModel MM_WPFS_Public_CheckoutSubscriptionFormModel
     */
    private function updateTransactionData(& $transactionData, $subscription, $formModel ) {
	    $latestInvoice = $this->getLatestInvoice( $subscription );
        $this->setTransactionDataFromInvoice( $transactionData, $latestInvoice );

	    $transactionData->setReceiptUrl( isset( $latestInvoice->charge ) ? $latestInvoice->charge->receipt_url : null );
    }

    /**
     * @param $stripeSubscription \StripeWPFS\Subscription
     *
     * @return array
     */
    private function extractPopupFormSubmit( $stripeSubscription ) {
        $popupFormSubmit = null;
        // tnagy retrieve Stripe Subscription and update form model
        if ( isset( $stripeSubscription )) {
            if ( isset( $stripeSubscription->metadata ) && isset( $stripeSubscription->metadata->client_reference_id ) ) {
                $submitHash      = $stripeSubscription->metadata->client_reference_id;
                $popupFormSubmit = $this->db->findPopupFormSubmitByHash( $submitHash );
            }
        }

        return $popupFormSubmit;
    }

    /**
     * @param $subscription \StripeWPFS\Subscription
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    private function processStripeEvents( $subscription ) {
        $popupFormSubmit = $this->extractPopupFormSubmit( $subscription );

        if ( isset( $popupFormSubmit ) && isset( $popupFormSubmit->relatedStripeEventIDs ) ) {
            $relatedStripeEventIDs = $this->retrieveStripeEventIDs( $popupFormSubmit->relatedStripeEventIDs );
            if ( $this->debugLog ) {
                MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): Processing related Stripe events=' . print_r( $relatedStripeEventIDs, true ) );
            }
            foreach ( $relatedStripeEventIDs as $relatedStripeEventID ) {
                $stripeEvent = $this->retrieveStripeEvent( $relatedStripeEventID );
                if ( $this->debugLog ) {
                    MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): stripeEvent=' . print_r( $stripeEvent, true ) );
                }
                if ( isset( $stripeEvent ) ) {
                    $this->eventHandler->handle( $stripeEvent );
                }
            }
        }
    }


    /**
     * @param $stripePaymentIntent \StripeWPFS\PaymentIntent
     * @param $stripeSetupIntent \StripeWPFS\SetupIntent
     *
     * @return string|\StripeWPFS\PaymentMethod|null
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    private function extractPaymentMethod( $stripePaymentIntent, $stripeSetupIntent ) {
        $paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent( $stripePaymentIntent );
        if ( is_null( $paymentMethod ) ) {
            $paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodBySetupIntent( $stripeSetupIntent );
        }

        return $paymentMethod;
    }


    /**
     * @param $stripePaymentIntent \StripeWPFS\PaymentIntent
     */
    private function updateSubscriptionToRunning( $stripePaymentIntent ) {
        if ( isset( $stripePaymentIntent ) && $stripePaymentIntent instanceof \StripeWPFS\PaymentIntent ) {
            if (
                \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $stripePaymentIntent->status
                || \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $stripePaymentIntent->status
            ) {
                $this->db->updateSubscriptionByPaymentIntentToRunning( $stripePaymentIntent->id );
            }
        }
    }

    /**
     * @param $stripeSubscription \StripeWPFS\Subscription
     * @param $formModel MM_WPFS_Public_CheckoutSubscriptionFormModel
     */
    private function setMetadataForSubscription( $stripeSubscription, $formModel ) {
        $stripeSubscription->metadata = array_merge( $formModel->getMetadata(), $stripeSubscription->metadata->toArray() );
        $stripeSubscription->save();
    }

    /**
     * @param $formModel MM_WPFS_Public_CheckoutSubscriptionFormModel
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $subscription \StripeWPFS\Subscription
     */
    private function fireAfterSubscriptionAction( $subscriptionFormModel, $transactionData, $subscription ) {
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

        do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_SUBSCRIPTION_CHARGE, $params );
    }

    /**
     * @param $lineItem
     * @return bool
     */
    protected function isSetupFeeLineItem( $lineItem ) {
        $result   = false;
        $metaData = $lineItem['price']['product']->metadata;

        if ( $metaData !== null &&
            $metaData['type'] === 'setupFee' ) {
            $result = true;
        }

        return $result;
    }

    protected function extractPricingDataFromLineItem( $lineItem ) {
        $result = new \StdClass;

        $result->amount        = $lineItem['amount_total'];
        $result->currency      = $lineItem['currency'];
        $result->description   = $lineItem['description'];

        if ( count( $lineItem['discounts'] ) > 0 ) {
            $discountItem = $lineItem['discounts'][0];

            $result->discountAmount = $discountItem->amount;
            $result->couponCode = $discountItem->discount->coupon['name'];
        } else {
            $result->discountAmount = 0;
            $result->couponCode = '';
        }

        $result->unitAmount = $lineItem['price']->unit_amount;
        $result->quantity = $lineItem['quantity'];

        if ( count( $lineItem['taxes'] ) > 0 ) {
            $taxRates = array();
            $taxAmount = 0;

            foreach ( $lineItem['taxes'] as $tax ) {
                $taxAmount += $tax->amount;
                array_push( $taxRates, $tax->rate['id'] );
            }

            $result->taxRates = $taxRates;
            $result->taxAmount = $taxAmount;
        } else {
            $result->taxRates = array();
            $result->taxAmount = 0;
        }

        return $result;
    }

    /**
     * @param $checkoutSession \StripeWPFS\Checkout\Session
     */
    protected function getInvoiceDataFromCheckoutSession( $checkoutSession ) {
        $invoiceData    = new \StdClass;
        $currency       = null;

        if ( count( $checkoutSession->line_items->data ) > 0 ) {
            foreach ( $checkoutSession->line_items->data as $lineItem ) {
                if ( $this->isSetupFeeLineItem( $lineItem )) {
                    $invoiceData->setupFee  = $this->extractPricingDataFromLineItem( $lineItem );
                } else {
                    $invoiceData->plan      = $this->extractPricingDataFromLineItem( $lineItem );
                }
            }
        } else {
            $result = new \StdClass;

            $result->currency = 'usd';
            $result->quantity = 0;
            $result->unitAmount = 0;
            $result->amount = 0;
            $result->discountAmount = 0;
            $result->taxAmount = 0;
            $result->description = '';
            $result->couponCode = '';
            $result->taxRates = array();

            $invoiceData->plan = $result;
        }

        return $invoiceData;
    }

    /**
     * @param $checkoutSession \StripeWPFS\Checkout\Session
     * @param $stripeCustomer \StripeWPFS\Customer
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $paymentMethod \StripeWPFS\PaymentMethod
     *
     * @returns \StdClass
     */
    protected function getContextDataFromStripeObjects( $checkoutSession, $stripeCustomer, $paymentIntent, $paymentMethod ) {
        $ctx = new \StdClass;

        $ctx->customerTaxId = $this->getTaxIdFromCustomer( $stripeCustomer );
        $ctx->invoiceData   = $this->getInvoiceDataFromCheckoutSession( $checkoutSession );

        return $ctx;
    }

    /**
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $data
     */
    protected function setPricingPlaceholders( & $transactionData, $data ) {
        $setupFeeAmount         = 0;
        $setupFeeDiscountAmount = 0;
        $setupFeeTaxAmount      = 0;
        $setupFeeUnitAmount     = 0;
        if ( property_exists( $data, 'setupFee' ) ) {
            $setupFeeAmount         = $data->setupFee->amount;
            $setupFeeDiscountAmount = $data->setupFee->discountAmount;
            $setupFeeTaxAmount      = $data->setupFee->taxAmount;
            $setupFeeUnitAmount     = $data->setupFee->unitAmount;
        }

        $transactionData->setAmount( $setupFeeAmount + $data->plan->amount );
        $transactionData->setPlanQuantity( $data->plan->quantity );
        $transactionData->setPlanCurrency( $data->plan->currency );
        if ( $data->plan->quantity == 1 ) {
            $transactionData->setPlanGrossAmountTotal( $data->plan->amount );
            $transactionData->setPlanGrossAmount( $transactionData->getPlanGrossAmountTotal() );

            $transactionData->setPlanTaxAmountTotal( $data->plan->taxAmount );
            $transactionData->setPlanTaxAmount( $transactionData->getPlanTaxAmountTotal() );

            $transactionData->setPlanNetAmountTotal( $transactionData->getPlanGrossAmountTotal() - $transactionData->getPlanTaxAmountTotal() );
            $transactionData->setPlanNetAmount( $transactionData->getPlanNetAmountTotal() );

            $transactionData->setSetupFeeGrossAmountTotal( $setupFeeAmount );
            $transactionData->setSetupFeeGrossAmount( $transactionData->getSetupFeeGrossAmountTotal() );

            $transactionData->setSetupFeeTaxAmountTotal( $setupFeeTaxAmount );
            $transactionData->setSetupFeeTaxAmount( $transactionData->getSetupFeeTaxAmountTotal() );

            $transactionData->setSetupFeeNetAmountTotal( $transactionData->getSetupFeeGrossAmountTotal() - $transactionData->getSetupFeeTaxAmountTotal() );
            $transactionData->setSetupFeeNetAmount( $transactionData->getSetupFeeNetAmountTotal() );
        } else {
            $transactionData->setPlanGrossAmountTotal( $data->plan->amount );
            $planUnitAmount = (int)round( $transactionData->getPlanGrossAmountTotal() / $data->plan->quantity );
            $transactionData->setPlanGrossAmount( $planUnitAmount );

            $transactionData->setPlanTaxAmountTotal( $data->plan->taxAmount );
            $planTaxUnitAmount = (int)round( $transactionData->getPlanTaxAmountTotal() / $data->plan->quantity );
            $transactionData->setPlanTaxAmount( $planTaxUnitAmount );

            $transactionData->setPlanNetAmountTotal( $transactionData->getPlanGrossAmountTotal() - $transactionData->getPlanTaxAmountTotal() );
            $planNetUnitAmount = (int)round( $transactionData->getPlanNetAmountTotal() / $data->plan->quantity );
            $transactionData->setPlanNetAmount( $planNetUnitAmount );

            $transactionData->setSetupFeeGrossAmountTotal( $setupFeeAmount );
            $setupFeeUnitAmount = (int)round( $transactionData->getSetupFeeGrossAmountTotal() / $data->plan->quantity );
            $transactionData->setSetupFeeGrossAmount( $setupFeeUnitAmount );

            $transactionData->setSetupFeeTaxAmountTotal( $setupFeeTaxAmount );
            $setupFeeTaxUnitAmount = (int)round( $transactionData->getSetupFeeTaxAmountTotal() / $data->plan->quantity );
            $transactionData->setSetupFeeTaxAmount( $setupFeeTaxUnitAmount );

            $transactionData->setSetupFeeNetAmountTotal( $transactionData->getSetupFeeGrossAmountTotal() - $transactionData->getSetupFeeTaxAmountTotal() );
            $setupFeeNetUnitAmount = (int)round( $transactionData->getSetupFeeNetAmountTotal() / $data->plan->quantity );
            $transactionData->setSetupFeeNetAmount( $setupFeeNetUnitAmount );
        }
    }

    /**
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     * @param $stripeCustomer \StripeWPFS\Customer
     */
    private function setTransactionDataFromContext( & $transactionData, $context ) {
        $transactionData->setCustomerTaxId( $context->customerTaxId );

        $this->setPricingPlaceholders( $transactionData, $context->invoiceData );
    }

    public function handle( $formModel, $checkoutSession ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): CALLED' );
			MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): formModel=' . print_r( $formModel, true ) );
			MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): checkoutSession=' . print_r( $checkoutSession, true ) );
		}

		$chargeResult = new MM_WPFS_ChargeResult();

		$stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByCheckoutSession( $checkoutSession );
        $stripeSubscription = $this->checkoutSubmissionService->retrieveStripeSubscriptionByCheckoutSession( $checkoutSession );
        $stripePaymentIntent = $this->checkoutSubmissionService->findPaymentIntentInCheckoutSession( $checkoutSession );
        $stripeSetupIntent = $this->checkoutSubmissionService->findSetupIntentInCheckoutSession( $checkoutSession );
        $paymentMethod = $this->extractPaymentMethod( $stripePaymentIntent, $stripeSetupIntent );

        if ( ! is_null( $paymentMethod ) ) {
            $paymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
                $stripeCustomer,
                $paymentMethod,
                /* set to default */
                true
            );
        }

        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): stripeCustomer=' . print_r( $stripeCustomer, true ) );
            MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): paymentIntent=' . print_r( $stripePaymentIntent, true ) );
            MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): setupIntent=' . print_r( $stripeSetupIntent, true ) );
        }

        $ctx = $this->getContextDataFromStripeObjects( $checkoutSession, $stripeCustomer, $stripePaymentIntent, $paymentMethod );

        $this->fixCustomerNamesAndAddresses( $stripeCustomer, $paymentMethod, $checkoutSession );
        $formModel->setStripeCustomer( $stripeCustomer, true );
        $formModel->setStripePaymentMethod( $paymentMethod );
        $formModel->setStripePaymentIntent( $stripePaymentIntent );
        $formModel->setStripeSetupIntent( $stripeSetupIntent );
        $formModel->setStripeSubscription( $stripeSubscription );
        $formModel->setTransactionId( $stripeSubscription->id );

        $formModel->setCouponCode( $this->getCouponCode( $stripeSubscription ) );

        $transactionData = MM_WPFS_TransactionDataService::createSubscriptionDataByModel( $formModel );
        $this->setTransactionDataFromContext( $transactionData, $ctx );
        $this->updateTransactionData( $transactionData, $stripeSubscription, $formModel );

        $this->setMetadataForSubscription( $stripeSubscription, $formModel );

        $this->db->insertSubscriber( $formModel, $transactionData );
        $this->updateSubscriptionToRunning( $stripePaymentIntent );

        $this->processStripeEvents( $stripeSubscription );

        $this->fireAfterSubscriptionAction( $formModel, $transactionData, $stripeSubscription );

		$chargeResult->setSuccess( true );
		$chargeResult->setMessageTitle(
		/* translators: Banner title of successful transaction */
			__( 'Success', 'wp-full-stripe' ) );
		$chargeResult->setMessage(
		/* translators: Banner message of successful payment */
			__( 'Payment Successful!', 'wp-full-stripe' ) );

		$this->handleRedirect( $formModel, $transactionData, $chargeResult );

        if ( MM_WPFS_Mailer::canSendSubscriptionPluginReceipt( $formModel->getForm() )) {
            $this->mailer->sendSubscriptionStartedEmailReceipt( $formModel->getForm(), $transactionData );
        }

		return $chargeResult;
	}

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param \StripeWPFS\Subscription $stripeSubscription
	 * @param \StripeWPFS\PaymentIntent $stripePaymentIntent
	 * @param \StripeWPFS\SetupIntent $stripeSetupIntent
	 * @param MM_WPFS_Public_CheckoutSubscriptionFormModel $subscriptionFormModel
	 * @param $vatPercent
	 */
	private function insertSubscriber( $stripeCustomer, $stripeSubscription, $stripePaymentIntent, $stripeSetupIntent, $subscriptionFormModel, $vatPercent ) {
		$this->db->insertSubscriber(
			$stripeCustomer,
			$stripeSubscription,
			$stripePaymentIntent,
			$stripeSetupIntent,
			$subscriptionFormModel->getCardHolderName(),
			$subscriptionFormModel->getBillingName(),
			$subscriptionFormModel->getBillingAddress( false ),
			$subscriptionFormModel->getShippingName(),
			$subscriptionFormModel->getShippingAddress( false ),
			$subscriptionFormModel->getForm()->checkoutSubscriptionFormID,
			$subscriptionFormModel->getForm()->name,
			$vatPercent,
            $subscriptionFormModel->getCouponCode()
		);
	}

	/**
	 * @param $encodedStripeEventIDs
	 *
	 * @return array|mixed|object
	 */
	protected function retrieveStripeEventIDs( $encodedStripeEventIDs ) {
		$decodedStripeEventIDs = json_decode( $encodedStripeEventIDs );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$decodedStripeEventIDs = array();
		}
		if ( ! is_array( $decodedStripeEventIDs ) ) {
			$decodedStripeEventIDs = array();
		}

		return $decodedStripeEventIDs;
	}

    /**
     * @param $stripeEventID
     *
     * @return \StripeWPFS\Event
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
	protected function retrieveStripeEvent( $stripeEventID ) {
		return $this->stripe->retrieveEvent( $stripeEventID );
	}

	/**
	 * @param \StripeWPFS\Subscription $stripeSubscription
	 *
	 * @return \StripeWPFS\Invoice
	 * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	protected function getLatestInvoice( \StripeWPFS\Subscription $stripeSubscription ) {
        $result = null;

        if ( $stripeSubscription->latest_invoice instanceof \StripeWPFS\Invoice ) {
            $result = $stripeSubscription->latest_invoice;
        } else {
            $params = array(
                'expand' => array(
                    'payment_intent',
                    'charge'
                )
            );
            $result = $this->stripe->retrieveInvoiceWithParams( $stripeSubscription->latest_invoice, $params );
        }

		return $result;
	}

	protected function getLatestInvoiceUrl( \StripeWPFS\Subscription $stripeSubscription ) {
		$latestInvoice = $this->stripe->retrieveInvoice( $stripeSubscription->latest_invoice );

		return $latestInvoice->invoice_pdf;
	}

}
