<?php
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.02.26.
 * Time: 14:16
 */
class MM_WPFS_Mailer {

    public static function generateSenderStringFromNameAndEmail( $name, $email ) {
        return "${name} <${email}>";
    }

    /**
     * @param $form
     * @param $templateType string
     */
    private static function isEmailTemplateEnabled( $form, $templateType ) {
        $isEnabled = false;
        $templates = json_decode( $form->emailTemplates );
        if ( !is_null( $templates )) {
            $isEnabled = $templates->{$templateType}->enabled;
        }

        return $isEnabled;
    }

    /**
     * @param $form
     */
    public static function canSendSaveCardPluginReceipt($form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_CARD_SAVED );
    }

    /**
     * @param $form
     */
    public static function canSendDonationPluginReceipt( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT );
    }

    /**
     * @param $form
     */
    public static function canSendDonationStripeReceipt( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT_STRIPE );
    }

    /**
     * @param $form
     */
    public static function canSendSubscriptionPluginReceipt( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT );
    }

    /**
     * @param $form
     */
    public static function canSendSubscriptionStripeReceipt( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT_STRIPE );
    }

    /**
     * @param $form
     */
    public static function canSendSubscriptionEndedPluginNotification( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_ENDED );
    }

    /**
     * @param $form
     */
    public static function canSendPaymentPluginReceipt( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT );
    }

    /**
     * @param $form
     */
    public static function canSendPaymentStripeReceipt( $form ) {
        return self::isEmailTemplateEnabled( $form, MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT_STRIPE );
    }

    /**
     * @param $formType
     *
     * @return array
     */
    public static function getEmailTemplateDescriptors($formType ) {
        $res = array();

        switch ( $formType ) {
            case MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD:
            case MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD:
                $saveCardReceipt = new \StdClass();
                $saveCardReceipt->type              = MM_WPFS::EMAIL_TEMPLATE_ID_CARD_SAVED;
                $saveCardReceipt->typeLabel         =
                    /* translators: Name of the email template that is used to send an email when a card is saved */
                    __('Card saved', 'wp-full-stripe-admin');
                $saveCardReceipt->typeDescription   =
                    /* translators: Description of the 'Card saved' email template */
                    __('The plugin sends this email when a customer submits a save card form.', 'wp-full-stripe-admin');
                array_push( $res, $saveCardReceipt );

                break;

            case MM_WPFS::FORM_TYPE_INLINE_DONATION:
            case MM_WPFS::FORM_TYPE_CHECKOUT_DONATION:
                $donationReceipt = new \StdClass();
                $donationReceipt->type              = MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT;
                $donationReceipt->typeLabel         =
                    /* translators: Name of the email template that is used to send an email when a donation is made */
                    __('Donation receipt', 'wp-full-stripe-admin');
                $donationReceipt->typeDescription   =
                    /* translators: Description of the 'Donation receipt' email template */
                    __('The plugin sends this email when a donor makes a donation.', 'wp-full-stripe-admin');
                array_push( $res, $donationReceipt );

                $donationReceiptStripe = new \StdClass();
                $donationReceiptStripe->type              = MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT_STRIPE;
                $donationReceiptStripe->typeLabel         =
                    /* translators: Name of the email template that Stripe sends when a donation is made */
                    __('Donation receipt (Stripe)', 'wp-full-stripe-admin');
                $donationReceiptStripe->typeDescription   =
                    /* translators: Description of the 'Donation receipt (Stripe)' email template */
                    __('Stripe sends a payment receipt when a donor makes a donation.', 'wp-full-stripe-admin');
                array_push( $res, $donationReceiptStripe );

                break;

            case MM_WPFS::FORM_TYPE_INLINE_PAYMENT:
            case MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT:
                $paymentReceipt = new \StdClass();
                $paymentReceipt->type              = MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT;
                $paymentReceipt->typeLabel         =
                    /* translators: Name of the email template that is used to send an email when a payment is made */
                    __('Payment receipt', 'wp-full-stripe-admin');
                $paymentReceipt->typeDescription   =
                    /* translators: Description of the 'Payment receipt' email template */
                    __('The plugin sends this email when a customer makes a one-time payment.', 'wp-full-stripe-admin');
                array_push( $res, $paymentReceipt );

                $paymentReceiptStripe = new \StdClass();
                $paymentReceiptStripe->type              = MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT_STRIPE;
                $paymentReceiptStripe->typeLabel         =
                    /* translators: Name of the email template that Stripe sends when a payment is made */
                    __('Payment receipt (Stripe)', 'wp-full-stripe-admin');
                $paymentReceiptStripe->typeDescription   =
                    /* translators: Description of the 'Payment receipt (Stripe)' email template */
                    __('Stripe sends this payment receipt when a customer makes a payment.', 'wp-full-stripe-admin');
                array_push( $res, $paymentReceiptStripe );

                break;

            case MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION:
            case MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION:
                $subscriptionReceipt = new \StdClass();
                $subscriptionReceipt->type              = MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT;
                $subscriptionReceipt->typeLabel         =
                    /* translators: Name of the email template that is used to send an email when a subscription is started */
                    __('Subscription receipt', 'wp-full-stripe-admin');
                $subscriptionReceipt->typeDescription   =
                    /* translators: Description of the 'Subscription receipt' email template */
                    __('The plugin sends this email when a customer subscribes to a plan.', 'wp-full-stripe-admin');
                array_push( $res, $subscriptionReceipt );

                $subscriptionEndedReceipt = new \StdClass();
                $subscriptionEndedReceipt->type              = MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_ENDED;
                $subscriptionEndedReceipt->typeLabel         =
                    /* translators: Name of the email template that is used to send an email when the plugin ends a subscription automatically */
                    __('Subscription ended', 'wp-full-stripe-admin');
                $subscriptionEndedReceipt->typeDescription   =
                    /* translators: Description of the 'Subscription ended' email template */
                    __('The plugin sends this email when payment-in-installments plan is cancelled automatically.', 'wp-full-stripe-admin');
                array_push( $res, $subscriptionEndedReceipt );

                $subscriptionReceiptStripe = new \StdClass();
                $subscriptionReceiptStripe->type              = MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT_STRIPE;
                $subscriptionReceiptStripe->typeLabel         =
                    /* translators: Name of the email template that Stripe sends when a subscription is started */
                    __('Subscription receipt (Stripe)', 'wp-full-stripe-admin');
                $subscriptionReceiptStripe->typeDescription   =
                    /* translators: Description of the 'Subscription receipt (Stripe)' email template */
                    __('Stripe sends a payment receipt when a customer subscribes to a plan.', 'wp-full-stripe-admin');
                array_push( $res, $subscriptionReceiptStripe );

            break;

        }

        return $res;
    }

    public static function isTemplateEnabled( $templateType ) {
        $options         = get_option( 'fullstripe_options' );
        $sendPluginEmail = $options[ MM_WPFS::OPTION_RECEIPT_EMAIL_TYPE ] === MM_WPFS::OPTION_VALUE_RECEIPT_EMAIL_PLUGIN;

        switch ( $templateType ) {
            case MM_WPFS::EMAIL_TEMPLATE_ID_CARD_SAVED:
            case MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT:
            case MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT:
            case MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT:
            case MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_ENDED:
                $templateEnabled = $sendPluginEmail;
                break;

            case MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT_STRIPE:
            case MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT_STRIPE:
            case MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT_STRIPE:
                $templateEnabled = ! $sendPluginEmail;
                break;
        }

        return $templateEnabled;
    }

    /**
     * @param $formType
     * @param $sendEmail
     * @param false $returnObject
     *
     * @return array|false|string
     */
    public static function createDefaultEmailTemplates($formType, $sendEmail, $returnObject = false ) {
        $templateDescriptors = MM_WPFS_Mailer::getEmailTemplateDescriptors( $formType );
        $emailTemplates      = new \StdClass;

        foreach( $templateDescriptors as $currentDescriptor ) {
            $isTemplateEnabled = $sendEmail === true ? self::isTemplateEnabled( $currentDescriptor->type ) : false;

            $defaultLanguage = new \StdClass;
            $defaultLanguage->subject = '';
            $defaultLanguage->body = '';

            $content = new \StdClass;
            $content->default = $defaultLanguage;

            $emailTemplate = new \StdClass;
            $emailTemplate->enabled = $isTemplateEnabled;
            $emailTemplate->senderName = '';
            $emailTemplate->senderAddress = '';
            $emailTemplate->receiverAddresses = array();
            $emailTemplate->content = $content;

            $emailTemplates->{$currentDescriptor->type} = $emailTemplate;
        }

        if ( $returnObject ) {
            return $emailTemplates;
        } else {
            return json_encode( $emailTemplates );
        }
    }

    public static function extractEmailTemplates( $formType, $emailTemplatesJson ) {
        $emailTemplates = json_decode( $emailTemplatesJson );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $emailTemplates = MM_WPFS_Mailer::createDefaultEmailTemplates( $formType, false, true );
        }

        return $emailTemplates;
    }

    public static function updateMissingEmailTemplatesWithDefaults( &$emailTemplates ) {
        if (!property_exists($emailTemplates, MM_WPFS::EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT)) {
            $emailTemplates->paymentMade = MM_WPFS_Mailer::createDefaultPaymentReceiptTemplate();
        }
        if (!property_exists($emailTemplates, MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT)) {
            $emailTemplates->subscriptionStarted = MM_WPFS_Mailer::createDefaultSubscriptionReceiptTemplate();
        }
        if (!property_exists($emailTemplates, MM_WPFS::EMAIL_TEMPLATE_ID_SUBSCRIPTION_ENDED)) {
            $emailTemplates->subscriptionFinished = MM_WPFS_Mailer::createDefaultSubscriptionEndedTemplate();
        }
        if (!property_exists($emailTemplates, MM_WPFS::EMAIL_TEMPLATE_ID_DONATION_RECEIPT)) {
            $emailTemplates->donationMade = MM_WPFS_Mailer::createDefaultDonationReceiptTemplate();
        }
        if (!property_exists($emailTemplates, MM_WPFS::EMAIL_TEMPLATE_ID_CARD_SAVED)) {
            $emailTemplates->cardCaptured = MM_WPFS_Mailer::createDefaultCardSavedTemplate();
        }
        if (!property_exists($emailTemplates, MM_WPFS::EMAIL_TEMPLATE_ID_CUSTOMER_PORTAL_SECURITY_CODE)) {
            $emailTemplates->cardUpdateConfirmationRequest = MM_WPFS_Mailer::createDefaultCustomerPortalSecurityCodeTemplate();
        }

        $emailTemplates = apply_filters('fullstripe_email_template_defaults', $emailTemplates );
    }

    /**
     * Constructs an \StdClass with the default email receipt templates.
     *
     * @return \StdClass
     */
    public static function getDefaultEmailTemplates() {
        $emailTemplates = new \StdClass;
        self::updateMissingEmailTemplatesWithDefaults( $emailTemplates );

        return $emailTemplates;
    }

    /**
     *
     */
    public static function updateDefaultEmailTemplatesInOptions() {
        $options = get_option( 'fullstripe_options' );

        $emailReceipts = json_decode( $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] );
        MM_WPFS_Mailer::updateMissingEmailTemplatesWithDefaults($emailReceipts);
        $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] = json_encode( $emailReceipts );

        update_option( 'fullstripe_options', $options );
    }

    /**
     * @return stdClass
     */
    public static function createDefaultSubscriptionEndedTemplate() {
        $subscriptionFinished = new stdClass();
        $subscriptionFinished->subject = 'Subscription Ended';
        $subscriptionFinished->html = '<html><body><p>Hi,</p><p>Your subscription has ended.</p><p>Thanks</p><br/>%NAME%</body></html>';

        return $subscriptionFinished;
    }

    public static function createDefaultCustomerPortalSecurityCodeTemplate() {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $homeUrl = home_url();
        $cardUpdateConfirmationRequest = new stdClass();
        $cardUpdateConfirmationRequest->subject = 'Login code for managing your account';
        $cardUpdateConfirmationRequest->html = '<html>
<body>
<p>Dear %CUSTOMER_EMAIL%,</p>

<p>You are receiving this email because you requested access to the page where you can manage your subscription(s).</p>

<br/>
<table>
    <tr>
        <td><b>Subscription management page:</b></td>
        <td><a href="https://www.example.com/manage-subscription">https://www.example.com/manage-subscription</a></td>
    </tr>
    <tr>
        <td><b>Your security code:</b></td>
        <td>%CARD_UPDATE_SECURITY_CODE%</td>
    </tr>
</table>

<br/>
<p>
    Thanks,<br/>
    %NAME%
</p>
</body>
</html>';

        return $cardUpdateConfirmationRequest;
    }

    /**
	 * @return stdClass
	 */
    public static function createDefaultPaymentReceiptTemplate() {
        $paymentMade = new stdClass();
        $paymentMade->subject = 'Payment Receipt';
        $paymentMade->html = "<html><body><p>Hi,</p><p>Here's your receipt for your payment of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";

        return $paymentMade;
    }

    /**
     * @return stdClass
     */
    public static function createDefaultDonationReceiptTemplate() {
        $paymentMade = new stdClass();
        $paymentMade->subject = 'Donation Receipt';
        $paymentMade->html = "<html><body><p>Hi,</p><p>Here's your receipt for your donation of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";

        return $paymentMade;
    }

    /**
     * @return stdClass
     */
    public static function createDefaultSubscriptionReceiptTemplate() {
        $subscriptionStarted = new stdClass();
        $subscriptionStarted->subject = 'Subscription Receipt';
        $subscriptionStarted->html = "<html><body><p>Hi,</p><p>Here's your receipt for your subscription of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";

        return $subscriptionStarted;
    }

    /**
	 * @return stdClass
	 */
    public static function createDefaultCardSavedTemplate() {
        $cardCaptured = new stdClass();
        $cardCaptured->subject = 'Card Information Saved';
        $cardCaptured->html = '<html><body><p>Hi,</p><p>Your payment information has been saved.</p><p>Thanks</p><br/>%NAME%</body></html>';

        return $cardCaptured;
    }

    /**
     * @param $form
     * @param MM_WPFS_DonationTransactionData $transactionData
     */
    public function sendDonationEmailReceipt($form, $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_DonationReceiptSender( $transactionData, $form );
        $sender->sendEmail();
    }

    /**
     * @param $form
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     */
    public function sendOneTimePaymentReceipt($form, $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_OneTimePaymentReceiptSender( $transactionData, $form );
        $sender->sendEmail();
    }

    /**
     * @param $form
     * @param $transactionData MM_WPFS_SaveCardTransactionData
     */
	public function sendSaveCardNotification($form, $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_SaveCardNotificationSender( $transactionData, $form );
        $sender->sendEmail();
    }

	/**
	 * @param $form
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 */
	public function sendSubscriptionStartedEmailReceipt( $form, $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_SubscriptionEmailReceiptSender( $transactionData, $form );
        $sender->sendEmail();
	}

	/**
	 * @param $form
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 */
	public function sendSubscriptionFinishedEmailReceipt( $form, $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_SubscriptionEndedNotificationSender( $transactionData, $form );
        $sender->sendEmail();
	}

    /**
     * @param $transactionData MM_WPFS_MyAccountLoginTransactionData
     */
	public function sendMyAccountLoginRequest( $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $sender = new MM_WPFS_MyAccountLoginNotificationSender( $transactionData );
        $sender->sendEmail();
    }
}

abstract class MM_WPFS_MailerTask {
    const TEMPLATE_TYPE_PAYMENT_RECEIPT                     = "PaymentReceipt";
    const TEMPLATE_TYPE_DONATION_RECEIPT                    = "DonationReceipt";
    const TEMPLATE_TYPE_SUBSCRIPTION_RECEIPT                = "SubscriptionReceipt";
    const TEMPLATE_TYPE_SUBSCRIPTION_ENDED                  = "SubscriptionEnded";
    const TEMPLATE_TYPE_CARD_SAVED                          = "CardSaved";
    const TEMPLATE_TYPE_MANAGE_SUBSCRIPTIONS_SECURITY_CODE  = "ManageSubscriptionsSecurityCode";

    protected $template;
    protected $form;
    /**
     * @var $transactionData MM_WPFS_TransactionData
     */
    protected $transactionData;
    protected $formName;

    public function __construct( $transactionData, $form = null ) {
        $this->transactionData  = $transactionData;
        $this->form             = $form;
    }

    protected abstract function getSubjectAndMessage();
    protected abstract function getMacroReplacer();

    public final function sendEmail( ) {
        list( $subject, $message ) = $this->getSubjectAndMessage();

        /**
         * @var $replacer MM_WPFS_MacroReplacerTools
         */
        $replacer = $this->getMacroReplacer();

        $subjectParams = [
            'template'                  => $this->template,
            'formName'                  => $this->formName,
            'rawPlaceholders'           => $replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'     => $replacer->getDecoratedKeyValuePairs(),
        ];
        $subject = $replacer->replaceMacrosWithHtmlEscape(
            apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_SUBJECT, $subject, $subjectParams )
        );

        $messageParams = [
            'template'                  => $this->template,
            'formName'                  => $this->formName,
            'rawPlaceholders'           => $replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'     => $replacer->getDecoratedKeyValuePairs(),
        ];
        $message = $replacer->replaceMacrosWithHtmlEscape(
            apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_MESSAGE, $message, $messageParams )
        );

        $this->sendEmailViaWordpress( $this->transactionData->getCustomerEmail(), $subject, $message );

    }

    private function sendEmailViaWordpress($email, $subject, $message ) {
        $options = get_option( 'fullstripe_options' );

        $senderName = html_entity_decode( get_bloginfo( 'name' ) );
        $senderEmail = $options[ MM_WPFS::OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS ];

        $headers[] = "Content-type: text/html";
        $headers[] = 'From: ' . MM_WPFS_Mailer::generateSenderStringFromNameAndEmail( $senderName, $senderEmail );

        $bccEmails = json_decode( $options[ MM_WPFS::OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES ] );
        foreach ( $bccEmails as $bccEmail ) {
            $headers[] = 'Bcc: <' . $bccEmail . '>';
        }

        wp_mail( $email, $subject, $message, apply_filters( 'fullstripe_email_headers_filter', $headers ));
    }

    protected function getEmailReceipts(){
        $options       = get_option( 'fullstripe_options' );
        $emailReceipts = json_decode( $options['email_receipts'] );

        return $emailReceipts;
    }
}

class MM_WPFS_DonationReceiptSender extends MM_WPFS_MailerTask {
    /**
     * @var $transactionData MM_WPFS_DonationTransactionData
     */

    public function __construct( $transactionData, $form = null ) {
        parent::__construct( $transactionData, $form );

        $this->template = MM_WPFS_MailerTask::TEMPLATE_TYPE_DONATION_RECEIPT;
        $this->formName = $this->transactionData->getFormName();
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_DonationMacroReplacer( $this->form, $this->transactionData );
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->donationMade->subject,
            $emailReceipts->donationMade->html
        );
    }
}

class MM_WPFS_GenericEmailNotificationSender extends MM_WPFS_MailerTask {

    protected $decoratedKeyValuePairs;

    public function setTemplateType( $templateType ) {
        $this->template = $templateType;
    }

    public function setDecoratedKeyValuePairs( $keyValuePairs ) {
        $this->decoratedKeyValuePairs = $keyValuePairs;
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->{$this->template}->subject,
            $emailReceipts->{$this->template}->html
        );
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_GenericMacroReplacer( $this->decoratedKeyValuePairs );
    }
}

class MM_WPFS_MyAccountLoginNotificationSender extends MM_WPFS_MailerTask {
    /**
     * @var $transactionData MM_WPFS_MyAccountLoginTransactionData
     */

    public function __construct( $transactionData, $form = null ) {
        parent::__construct( $transactionData, $form );

        $this->template = MM_WPFS_MailerTask::TEMPLATE_TYPE_MANAGE_SUBSCRIPTIONS_SECURITY_CODE;
        $this->formName = null;
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_MyAccountLoginMacroReplacer( $this->transactionData );
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->cardUpdateConfirmationRequest->subject,
            $emailReceipts->cardUpdateConfirmationRequest->html
        );
    }
}

class MM_WPFS_OneTimePaymentReceiptSender extends MM_WPFS_MailerTask {
    /**
     * @var $transactionData MM_WPFS_OneTimePaymentTransactionData
     */

    public function __construct( $transactionData, $form = null ) {
        parent::__construct( $transactionData, $form );

        $this->template = MM_WPFS_MailerTask::TEMPLATE_TYPE_PAYMENT_RECEIPT;
        $this->formName = $this->transactionData->getFormName();
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_OneTimePaymentMacroReplacer( $this->form, $this->transactionData );
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->paymentMade->subject,
            $emailReceipts->paymentMade->html
        );
    }
}

class MM_WPFS_SaveCardNotificationSender extends MM_WPFS_MailerTask {
    /**
     * @var $transactionData MM_WPFS_SaveCardTransactionData
     */

    public function __construct( $transactionData, $form = null ) {
        parent::__construct( $transactionData, $form );

        $this->template = MM_WPFS_MailerTask::TEMPLATE_TYPE_CARD_SAVED;
        $this->formName = $this->transactionData->getFormName();
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_SaveCardMacroReplacer( $this->form, $this->transactionData );
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->cardCaptured->subject,
            $emailReceipts->cardCaptured->html
        );
    }
}

class MM_WPFS_SubscriptionEmailReceiptSender extends MM_WPFS_MailerTask {
    /**
     * @var $transactionData MM_WPFS_SubscriptionTransactionData
     */

    public function __construct( $transactionData, $form = null ) {
        parent::__construct( $transactionData, $form );

        $this->template = MM_WPFS_MailerTask::TEMPLATE_TYPE_SUBSCRIPTION_RECEIPT;
        $this->formName = $this->transactionData->getFormName();
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_SubscriptionMacroReplacer( $this->form, $this->transactionData );
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->subscriptionStarted->subject,
            $emailReceipts->subscriptionStarted->html
        );
    }
}

class MM_WPFS_SubscriptionEndedNotificationSender extends MM_WPFS_MailerTask {
    /**
     * @var $transactionData MM_WPFS_SubscriptionTransactionData
     */

    public function __construct( $transactionData, $form = null ) {
        parent::__construct( $transactionData, $form );

        $this->template = MM_WPFS_MailerTask::TEMPLATE_TYPE_SUBSCRIPTION_ENDED;
        $this->formName = $this->transactionData->getFormName();
    }

    protected function getMacroReplacer() {
        return new MM_WPFS_SubscriptionMacroReplacer( $this->form, $this->transactionData );
    }

    protected function getSubjectAndMessage() {
        $emailReceipts = $this->getEmailReceipts();

        return array(
            $emailReceipts->subscriptionFinished->subject,
            $emailReceipts->subscriptionFinished->html
        );
    }
}
