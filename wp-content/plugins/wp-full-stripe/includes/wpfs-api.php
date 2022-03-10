<?php

class MM_WPFS_PhonyTransactionData {
    protected $customerEmail;

    public function __construct( $emailAddress ) {
        $this->customerEmail = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail() {
        return $this->customerEmail;
    }
}

class MM_WPFS_API_v1 {

    protected static $instance;

    /** @var MM_WPFS_Database */
    private $db = null;
    /** @var MM_WPFS_Stripe */
    protected $stripe;

    protected function __construct() {
        $this->db       = new MM_WPFS_Database();
        $this->stripe   = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
    }

    /**
     * @return MM_WPFS_API_v1
     */
    protected static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new MM_WPFS_API_v1();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public static function getPluginVersion() {
        return MM_WPFS::VERSION;
    }

    /**
     * @return \StripeWPFS\StripeClient
     */
    public static function getStripeClient() {
        return self::getInstance()->stripe->getStripeClient();
    }

    /**
     * @return array
     */
    public static function getStripeSubscriptionPlans() {
        return self::getInstance()->stripe->getSubscriptionPlans();
    }

    /**
     * @param $subscriptionId string
     * @return \StripeWPFS\Subscription
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public static function getStripeSubscription($subscriptionId ) {
        return self::getInstance()->stripe->retrieveSubscription($subscriptionId );
    }

    /**
     * @param $subscriptionId
     * @param $params
     * @return \StripeWPFS\Subscription
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public static function getStripeSubscriptionWithParams( $subscriptionId, $params ) {
        return self::getInstance()->stripe->retrieveSubscriptionWithParams( $subscriptionId, $params );
    }

    /**
     * @param $customerId
     * @return \StripeWPFS\Customer
     */
    public static function getStripeCustomer( $customerId ) {
        return self::getInstance()->stripe->retrieveCustomer( $customerId );
    }

    /**
     * @param $customerId
     * @param $params
     * @return \StripeWPFS\Customer
     */
    public static function getStripeCustomerWithParams( $customerId, $params ) {
        return self::getInstance()->stripe->retrieveCustomerWithParams( $customerId, $params );
    }

    /**
     * @param $params
     * @return \StripeWPFS\Collection
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public static function getStripeCustomersWithParams( $params )  {
        return self::getInstance()->stripe->getCustomersWithParams( $params );
    }

    /**
     * @param $planId
     * @return \StripeWPFS\Price|null
     */
    public static function getStripePlan( $planId ) {
        return self::getInstance()->stripe->retrievePlan( $planId );
    }

    /**
     * @param $stripeCustomerId
     * @param $stripeSubscriptionId
     * @param $stripePlanId
     *
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public static function changeSubscriptionPlan( $stripeCustomerId, $stripeSubscriptionId, $stripePlanId ) {
        $success = self::getInstance()->stripe->updateSubscriptionPlanAndQuantity( $stripeCustomerId, $stripeSubscriptionId, $stripePlanId );
        if ( $success ) {
            self::getInstance()->db->updateSubscriptionPlanByStripeSubscriptionId( $stripeSubscriptionId, $stripePlanId );
        }
    }

    /**
     * @param $stripeCustomerId
     * @param $stripeSubscriptionId
     * @return bool
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public static function cancelSubscription( $stripeCustomerId, $stripeSubscriptionId ) {
        return self::getInstance()->stripe->cancelSubscription(
            $stripeCustomerId,
            $stripeSubscriptionId,
            MM_WPFS_Utils::getCancelSubscriptionsAtPeriodEnd()
        );
    }

    /**
     * @param $currency string
     * @return string
     */
    public static function getCurrencySymbolFor( $currency ) {
        return MM_WPFS_Currencies::getCurrencySymbolFor( $currency );
    }

    /**
     * @param $emailType string
     * @param $decoratedKeyValuePairs array
     */
    public static function sendEmailByTemplate( $recipientEmail, $templateType, $decoratedKeyValuePairs ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_GenericEmailNotificationSender( new MM_WPFS_PhonyTransactionData( $recipientEmail ) );
        $sender->setTemplateType( $templateType );
        $sender->setDecoratedKeyValuePairs( $decoratedKeyValuePairs );
        $sender->sendEmail();
    }

    /**
     * @return bool
     */
    public static function isDemoMode() {
        return MM_WPFS_Utils::isDemoMode();
    }
}
