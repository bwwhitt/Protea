<?php
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

class MM_WPFS_Admin_CreateFormModel implements MM_WPFS_Binder {
    use MM_WPFS_Model;

    protected $name;
    protected $displayName;
    protected $type;
    protected $layout;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_CreateFormValidator();
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName() {
        return $this->displayName;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLayout() {
        return $this->layout;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->displayName  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CreateFormViewConstants::FIELD_FORM_DISPLAY_NAME );
        $this->name         = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CreateFormViewConstants::FIELD_FORM_NAME );
        $this->type         = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CreateFormViewConstants::FIELD_FORM_TYPE );
        $this->layout       = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CreateFormViewConstants::FIELD_FORM_LAYOUT );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'displayName'  => $this->displayName,
            'name'         => $this->name,
            'type'         => $this->type,
            'layout'       => $this->layout
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }


    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_StripeAccountModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $testSecretKey;
    protected $testPublishableKey;
    protected $liveSecretKey;
    protected $livePublishableKey;
    protected $apiMode;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_StripeAccountValidator();
    }

    /**
     * @return string
     */
    public function getTestSecretKey() {
        return $this->testSecretKey;
    }

    /**
     * @return string
     */
    public function getTestPublishableKey() {
        return $this->testPublishableKey;
    }

    /**
     * @return string
     */
    public function getLiveSecretKey() {
        return $this->liveSecretKey;
    }

    /**
     * @return string
     */
    public function getLivePublishableKey() {
        return $this->livePublishableKey;
    }

    /**
     * @return string
     */
    public function getApiMode() {
        return $this->apiMode;
    }


    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->testSecretKey        = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_ConfigureStripeAccountViewConstants::FIELD_API_TEST_SECRET_KEY );
        $this->testPublishableKey   = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_ConfigureStripeAccountViewConstants::FIELD_API_TEST_PUBLISHABLE_KEY );
        $this->liveSecretKey        = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_ConfigureStripeAccountViewConstants::FIELD_API_LIVE_SECRET_KEY );
        $this->livePublishableKey   = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_ConfigureStripeAccountViewConstants::FIELD_API_LIVE_PUBLISHABLE_KEY );
        $this->apiMode              = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_ConfigureStripeAccountViewConstants::FIELD_API_MODE, MM_WPFS::STRIPE_API_MODE_TEST );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'testSecretKey'         => $this->testSecretKey,
            'testPublishableKey'    => $this->testPublishableKey,
            'liveSecretKey'         => $this->liveSecretKey,
            'livePublishableKey'    => $this->livePublishableKey,
            'apiMode'               => $this->apiMode
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_MyAccountModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $cancelSubscriptions;
    protected $whenCancelSubscriptions;
    protected $updowngradeSubscriptions;
    protected $showInvoices;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_MyAccountValidator();
    }

    /**
     * @return mixed
     */
    public function getCancelSubscriptions() {
        return $this->cancelSubscriptions;
    }

    /**
     * @return mixed
     */
    public function getWhenCancelSubscriptions() {
        return $this->whenCancelSubscriptions;
    }

    /**
     * @return mixed
     */
    public function getUpdowngradeSubscriptions() {
        return $this->updowngradeSubscriptions;
    }

    /**
     * @return mixed
     */
    public function getShowInvoices() {
        return $this->showInvoices;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->cancelSubscriptions      = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CustomerPortalViewConstants::FIELD_MY_ACCOUNT_CANCEL_SUBSCRIPTIONS, 0 );
        $this->whenCancelSubscriptions  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CustomerPortalViewConstants::FIELD_MY_ACCOUNT_WHEN_CANCEL_SUBSCRIPTONS, MM_WPFS::CANCEL_SUBSCRIPTION_IMMEDIATELY );
        $this->updowngradeSubscriptions = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CustomerPortalViewConstants::FIELD_MY_ACCOUNT_UPDOWNGRADE_SUBSCRIPTIONS, 0 );
        $this->showInvoices             = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CustomerPortalViewConstants::FIELD_MY_ACCOUNT_SHOW_INVOICES, 0 );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'letCustomersCancelSubscriptions'       => $this->cancelSubscriptions,
            'whenToCancelSubscriptions'             => $this->whenCancelSubscriptions,
            'letCustomersUpdowngradeSubscriptions'  => $this->updowngradeSubscriptions,
            'showInvoicesToCustomers'               => $this->showInvoices
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_SecurityModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $secureInlineForms;
    protected $secureCheckoutForms;
    protected $secureCustomerPortal;
    protected $reCaptchaSecretKey;
    protected $reCaptchaSiteKey;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_SecurityValidator();
    }

    /**
     * @return mixed
     */
    public function getSecureInlineForms() {
        return $this->secureInlineForms;
    }

    /**
     * @return mixed
     */
    public function getSecureCheckoutForms() {
        return $this->secureCheckoutForms;
    }

    /**
     * @return mixed
     */
    public function getSecureCustomerPortal() {
        return $this->secureCustomerPortal;
    }

    /**
     * @return mixed
     */
    public function getReCaptchaSecretKey() {
        return $this->reCaptchaSecretKey;
    }

    /**
     * @return mixed
     */
    public function getReCaptchaSiteKey() {
        return $this->reCaptchaSiteKey;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->secureInlineForms    = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SecurityViewConstants::FIELD_SECURITY_SECURE_INLINE_FORMS, 0 );
        $this->secureCheckoutForms  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SecurityViewConstants::FIELD_SECURITY_SECURE_CHECKOUT_FORMS, 0 );
        $this->secureCustomerPortal = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SecurityViewConstants::FIELD_SECURITY_SECURE_CUSTOMER_PORTAL, 0 );
        $this->reCaptchaSecretKey   = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SecurityViewConstants::FIELD_SECURITY_RECAPTCHA_SECRET_KEY );
        $this->reCaptchaSiteKey     = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SecurityViewConstants::FIELD_SECURITY_RECAPTCHA_SITE_KEY );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'secureInlineForms'     => $this->secureInlineForms,
            'secureCheckoutForms'   => $this->secureCheckoutForms,
            'secureCustomerPortal'  => $this->secureCustomerPortal,
            'reCaptchaSecretKey'    => $this->reCaptchaSecretKey,
            'reCaptchaSiteKey'      => $this->reCaptchaSiteKey,
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_EmailOptionsModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $fromAddress;
    protected $fromAddressCustom;
    protected $sendCopyToAdmin;
    protected $sendCopyToList;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_EmailOptionsValidator();
    }

    /**
     * @return mixed
     */
    public function getFromAddress() {
        return $this->fromAddress;
    }

    /**
     * @return mixed
     */
    public function getFromAddressCustom() {
        return $this->fromAddressCustom;
    }

    /**
     * @return mixed
     */
    public function getSendCopyToAdmin() {
        return $this->sendCopyToAdmin;
    }

    /**
     * @return mixed
     */
    public function getSendCopyToList() {
        return $this->sendCopyToList;
    }


    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->fromAddress          = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_EmailOptionsViewConstants::FIELD_EMAIL_OPTIONS_FROM_ADDRESS );
        $this->fromAddressCustom    = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_EmailOptionsViewConstants::FIELD_EMAIL_OPTIONS_FROM_ADDRESS_CUSTOM );
        $this->sendCopyToAdmin      = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_EmailOptionsViewConstants::FIELD_EMAIL_OPTIONS_SEND_COPY_TO_ADMIN );
        $this->sendCopyToList       = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_EmailOptionsViewConstants::FIELD_EMAIL_OPTIONS_SEND_COPY_TO_LIST_HIDDEN );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'fromAddress'           => $this->fromAddress,
            'fromAddressCustom'     => $this->fromAddressCustom,
            'sendCopyToAdmin'       => $this->sendCopyToAdmin,
            'sendCopyToList'        => $this->getSendCopyToList()
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_EmailTemplatesModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $emailTemplates;

    public function __construct() {
    }

    /**
     * @return mixed
     */
    public function getEmailTemplates() {
        return $this->emailTemplates;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->emailTemplates = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_EmailTemplatesViewConstants::FIELD_EMAIL_TEMPLATES_HIDDEN );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'emailTemplates' => $this->emailTemplates,
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_FormsOptionsModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $fillInEmail;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_FormsOptionsValidator();
    }

    /**
     * @return mixed
     */
    public function getFillInEmail() {
        return $this->fillInEmail;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->fillInEmail = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormsOptionsViewConstants::FIELD_FORMS_OPTIONS_FILL_IN_EMAIL, 0 );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'fillInEmail' => $this->fillInEmail
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_FormsAppearanceModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $customCss;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_FormsAppearanceValidator();
    }

    /**
     * @return mixed
     */
    public function getCustomCss() {
        return $this->customCss;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->customCss = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormsAppearanceViewConstants::FIELD_FORMS_APPEARANCE_CUSTOM_CSS_HIDDEN, '' );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'customCss' => $this->customCss
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

class MM_WPFS_Admin_WordpressDashboardModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    protected $decimalSeparator;
    protected $useSymbolNotCode;
    protected $currencySymbolAtFirstPosition;
    protected $putSpaceBetweenSymbolAndAmount;

    public function __construct() {
        $this->__validator = new MM_WPFS_Admin_WordpressDashboardValidator();
    }

    /**
     * @return mixed
     */
    public function getDecimalSeparator() {
        return $this->decimalSeparator;
    }

    /**
     * @return mixed
     */
    public function getUseSymbolNotCode() {
        return $this->useSymbolNotCode;
    }

    /**
     * @return mixed
     */
    public function getCurrencySymbolAtFirstPosition() {
        return $this->currencySymbolAtFirstPosition;
    }

    /**
     * @return mixed
     */
    public function getPutSpaceBetweenSymbolAndAmount() {
        return $this->putSpaceBetweenSymbolAndAmount;
    }

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->decimalSeparator                 = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_WordpressDashboardViewConstants::FIELD_WP_DASHBOARD_DECIMAL_SEPARATOR, MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT );
        $this->useSymbolNotCode                 = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_WordpressDashboardViewConstants::FIELD_WP_DASHBOARD_SYMBOL_NOT_CODE, 1 );
        $this->currencySymbolAtFirstPosition    = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_WordpressDashboardViewConstants::FIELD_WP_DASHBOARD_SYMBOL_AT_FIRST_POSITION, 1 );
        $this->putSpaceBetweenSymbolAndAmount   = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_WordpressDashboardViewConstants::FIELD_WP_DASHBOARD_SPACE_BETWEEN_SYMBOL_AND_AMOUNT, 0 );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        return $bindingResult;
    }

    public function getData() {
        $data = array(
            'decimalSeparator'                  => $this->decimalSeparator,
            'useSymbolNotCode'                  => $this->useSymbolNotCode,
            'currencySymbolAtFirstPosition'     => $this->currencySymbolAtFirstPosition,
            'putSpaceBetweenSymbolAndAmount'    => $this->putSpaceBetweenSymbolAndAmount
        );

        return $data;
    }

    public function getPostData() {
        return array();
    }

    public function afterBind() {
        // noop
    }
}

interface MM_WPFS_Admin_InlineForm {
}

interface MM_WPFS_Admin_CheckoutForm {
}

abstract class MM_WPFS_Admin_FormModel implements MM_WPFS_Binder {

    use MM_WPFS_Model;

    const PARAM_CUSTOM_INPUTS = 'customInputs';
    const PARAM_FORM_DEFAULT_BILLING_COUNTRY = 'form_default_billing_country';

    protected $id;
    protected $name;
    protected $displayName;
    protected $buttonLabel;
    protected $redirectType;
    protected $redirectPageOrPostId;
    protected $redirectURl;
    protected $localeDecimalSeparator;
    protected $localeUseSymbolNotCode;
    protected $localeCurrencySymbolAtFirstPosition;
    protected $localePutSpaceBetweenSymbolAndAmount;
    protected $showTermsOfUse;
    protected $termsOfUseLabel;
    protected $termsOfUseErrorMessage;
    protected $customFields;
    protected $makeCustomFieldsRequired;
    protected $emailTemplatesHidden;
    protected $emailTemplates;

    protected $formHash;

    public function bind() {
        return $this->bindByArray( $_POST );
    }

    public function bindByArray( $postData ) {
        $bindingResult = new MM_WPFS_BindingResult();

        $this->id          = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_ID );
        $this->name        = $this->getSanitizedArrayParam( $postData, MM_WPFS_FormViewConstants::FIELD_FORM_NAME );
        $this->displayName = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_DISPLAY_NAME );

        $this->buttonLabel         = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_BUTTON_LABEL,
            /* translators: Default payment button text on one-time payment forms */
            __( 'Pay', 'wp-full-stripe' ) );

        $this->redirectType         = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_REDIRECT_TYPE, MM_WPFS::REDIRECT_TYPE_SHOW_CONFIRMATION_MESSAGE );
        $this->redirectPageOrPostId = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_REDIRECT_PAGE_POST_ID );
        $this->redirectURl          = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_REDIRECT_CUSTOM_URL );

        $this->customFields             = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_CUSTOM_FIELDS );
        $this->makeCustomFieldsRequired = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_MAKE_CUSTOM_FIELDS_REQUIRED, 0 );

        $this->showTermsOfUse         = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_SHOW_TERMS_OF_SERVICE, 0 );
        $this->termsOfUseLabel        = $this->getArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_TERMS_OF_SERVICE_LABEL );
        $this->termsOfUseErrorMessage = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_TERMS_OF_SERVICE_ERROR );

        $this->localeDecimalSeparator               = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_LOCALE_DECIMAL_SEPARATOR, MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA );
        $this->localeUseSymbolNotCode               = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_LOCALE_SYMBOL_NOT_CODE, 1 );
        $this->localeCurrencySymbolAtFirstPosition  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_LOCALE_SYMBOL_AT_FIRST_POSITION, 1 );
        $this->localePutSpaceBetweenSymbolAndAmount = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_LOCALE_SPACE_BETWEEN_SYMBOL_AND_AMOUNT, 0 );

        $this->emailTemplatesHidden = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_EMAIL_TEMPLATES );

        return $bindingResult;
    }

    public function getData() {

        $data = array(
            'name'                                  => $this->name,
            'displayName'                           => $this->displayName,
            'buttonTitle'                           => $this->buttonLabel,
            'showCustomInput'                       => '1',
            'customInputRequired'                   => $this->makeCustomFieldsRequired,
            'customInputs'                          => $this->customFields,
            'redirectOnSuccess'                     => $this->redirectType !== MM_WPFS::REDIRECT_TYPE_SHOW_CONFIRMATION_MESSAGE ? '1' : '0',
            'redirectPostID'                        => $this->redirectPageOrPostId,
            'redirectUrl'                           => $this->redirectURl,
            'redirectToPageOrPost'                  => $this->redirectType === MM_WPFS::REDIRECT_TYPE_TO_PAGE_OR_POST ? '1' : '0',
            'showTermsOfUse'                        => $this->showTermsOfUse,
            'termsOfUseLabel'                       => $this->termsOfUseLabel,
            'termsOfUseNotCheckedErrorMessage'      => $this->termsOfUseErrorMessage,
            'decimalSeparator'                      => $this->localeDecimalSeparator,
            'showCurrencySymbolInsteadOfCode'       => $this->localeUseSymbolNotCode,
            'showCurrencySignAtFirstPosition'       => $this->localeCurrencySymbolAtFirstPosition,
            'putWhitespaceBetweenCurrencyAndAmount' => $this->localePutSpaceBetweenSymbolAndAmount,
            'emailTemplates'                        => $this->emailTemplates,
            // Legacy fields
            'showDetailedSuccessPage'               => 1,
            'sendEmailReceipt'                      => 0,
        );

        return $data;
    }

    public function getPostData() {
        // tnagy unsupported operation
        return array();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName() {
        return $this->displayName;
    }

    /**
     * @return mixed
     */
    public function getButtonLabel() {
        return $this->buttonLabel;
    }

    /**
     * @return mixed
     */
    public function getRedirectType() {
        return $this->redirectType;
    }

    /**
     * @return mixed
     */
    public function getRedirectPageOrPostId() {
        return $this->redirectPageOrPostId;
    }

    /**
     * @return mixed
     */
    public function getRedirectURl() {
        return $this->redirectURl;
    }

    /**
     * @return mixed
     */
    public function getLocaleDecimalSeparator() {
        return $this->localeDecimalSeparator;
    }

    /**
     * @return mixed
     */
    public function getLocaleUseSymbolNotCode() {
        return $this->localeUseSymbolNotCode;
    }

    /**
     * @return mixed
     */
    public function getLocaleCurrencySymbolAtFirstPosition() {
        return $this->localeCurrencySymbolAtFirstPosition;
    }

    /**
     * @return mixed
     */
    public function getLocalePutSpaceBetweenSymbolAndAmount() {
        return $this->localePutSpaceBetweenSymbolAndAmount;
    }

    /**
     * @return mixed
     */
    public function getShowTermsOfUse() {
        return $this->showTermsOfUse;
    }

    /**
     * @return mixed
     */
    public function getTermsOfUseLabel()
    {
        return $this->termsOfUseLabel;
    }

    /**
     * @return mixed
     */
    public function getTermsOfUseErrorMessage() {
        return $this->termsOfUseErrorMessage;
    }

    /**
     * @return mixed
     */
    public function getCustomFields() {
        return $this->customFields;
    }

    /**
     * @return mixed
     */
    public function getMakeCustomFieldsRequired() {
        return $this->makeCustomFieldsRequired;
    }

    /**
     * @return mixed
     */
    public function getEmailTemplatesHidden() {
        return $this->emailTemplatesHidden;
    }

    /**
     * @param mixed $emailTemplates
     */
    public function setEmailTemplates( $emailTemplates ) {
        $this->emailTemplates = $emailTemplates;
    }

    /**
     * @return mixed
     */
    public function getFormHash() {
        return $this->formHash;
    }

    /**
     * @param mixed $formHash
     */
    public function setFormHash($formHash) {
        $this->formHash = $formHash;
    }
}

trait MM_WPFS_Admin_InlineFormModel {
    protected $cardInputFieldLanguage;
    protected $collectBillingAddress;
    protected $defaultBillingCountry;
    protected $collectShippingAddress;

    protected function bindInlineParams( $dataArray ) {
        $this->cardInputFieldLanguage = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_InlineFormViewConstants::FIELD_FORM_CARD_FIELD_LANGUAGE, MM_WPFS::PREFERRED_LANGUAGE_AUTO );

        $this->collectBillingAddress  = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_COLLECT_BILLING_ADDRESS, 0 );
        $this->defaultBillingCountry  = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_DEFAULT_BILLING_COUNTRY, MM_WPFS::DEFAULT_BILLING_COUNTRY_INITIAL_VALUE );
        $this->collectShippingAddress = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_COLLECT_SHIPPING_ADDRESS, 0 );
        if ( $this->collectShippingAddress == 1 ) {
            $this->collectBillingAddress = 1;
        }
    }

    protected function getInlineDataArray() {

        $data = array(
            'preferredLanguage'     => $this->cardInputFieldLanguage,
            'showAddress'           => $this->collectBillingAddress,
            'defaultBillingCountry' => $this->defaultBillingCountry,
            'showShippingAddress'   => $this->collectShippingAddress,
        );

        return $data;
    }

    /**
     * @return mixed
     */
    public function getCardInputFieldLanguage() {
        return $this->cardInputFieldLanguage;
    }

    /**
     * @return mixed
     */
    public function getCollectBillingAddress() {
        return $this->collectBillingAddress;
    }

    /**
     * @return mixed
     */
    public function getDefaultBillingCountry() {
        return $this->defaultBillingCountry;
    }

    /**
     * @return mixed
     */
    public function getCollectShippingAddress() {
        return $this->collectShippingAddress;
    }
}

trait MM_WPFS_Admin_CheckoutFormModel {
    protected $checkoutFormLanguage;
    protected $collectBillingAddress;
    protected $defaultBillingCountry;
    protected $collectShippingAddress;
    protected $openButtonLabel;

    protected function bindCheckoutParams( $dataArray ) {
        $this->openButtonLabel = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_CheckoutFormViewConstants::FIELD_FORM_OPEN_BUTTON_LABEL,
            /* translators: Default payment button text on one-time payment forms */
            __( 'Pay', 'wp-full-stripe' ) );

        $this->checkoutFormLanguage = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_CheckoutFormViewConstants::FIELD_FORM_CHECKOUT_LANGUAGE, MM_WPFS::PREFERRED_LANGUAGE_AUTO );

        $this->collectBillingAddress  = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_COLLECT_BILLING_ADDRESS, 0 );
        $this->defaultBillingCountry  = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_DEFAULT_BILLING_COUNTRY, MM_WPFS::DEFAULT_BILLING_COUNTRY_INITIAL_VALUE );
        $this->collectShippingAddress = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_FormViewConstants::FIELD_FORM_COLLECT_SHIPPING_ADDRESS, 0 );
        if ( $this->collectShippingAddress == 1 ) {
            $this->collectBillingAddress = 1;
        }
    }

    protected function getCheckoutDataArray() {

        $data = array(
            'openButtonTitle'       => $this->openButtonLabel,
            'preferredLanguage'     => $this->checkoutFormLanguage,
            'showBillingAddress'    => $this->collectBillingAddress,
            'defaultBillingCountry' => $this->defaultBillingCountry,
            'showShippingAddress'   => $this->collectShippingAddress,
            // Legacy fields
            'image'                 => '',
        );

        return $data;
    }

    /**
     * @return mixed
     */
    public function getCheckoutFormLanguage() {
        return $this->checkoutFormLanguage;
    }

    /**
     * @return mixed
     */
    public function getCollectBillingAddress() {
        return $this->collectBillingAddress;
    }

    /**
     * @return mixed
     */
    public function getDefaultBillingCountry() {
        return $this->defaultBillingCountry;
    }

    /**
     * @return mixed
     */
    public function getCollectShippingAddress() {
        return $this->collectShippingAddress;
    }

    /**
     * @return mixed
     */
    public function getOpenButtonLabel() {
        return $this->openButtonLabel;
    }
}

abstract class MM_WPFS_Admin_SaveCardFormModel extends MM_WPFS_Admin_FormModel {
    protected $transactionDescription;

    public function __construct() {

    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->transactionDescription = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SaveCardFormViewConstants::FIELD_FORM_TRANSACTION_DESCRIPTION );

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $data = array(
            'stripeDescription'         => $this->transactionDescription,
            // The fields below are required because save card forms and payment forms are in the same database table
            'currency'                  => MM_WPFS::CURRENCY_USD,
            'amount'                    => 0,
            'customAmount'              => MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE,
            'listOfAmounts'             => null,
            'allowListOfAmountsCustom'  => 0,
            'showButtonAmount'          => 0,
            'amountSelectorStyle'       => MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN,
        );

        $data = array_merge( $data, $parentData );

        return $data;
    }

    /**
     * @return mixed
     */
    public function getTransactionDescription() {
        return $this->transactionDescription;
    }
}

trait MM_WPFS_Admin_TaxRateTools {
    protected $taxRateType;
    protected $collectCustomerTaxId;
    protected $taxRates;

    /**
     * @param $taxRateIds
     */
    protected function prepareTaxRates( $taxRateIds )  {
        $taxRates = array();
        $taxRatesLookup = array();

        $stripeTaxRates = $this->stripe->getTaxRates();
        foreach ($stripeTaxRates as $stripeTaxRate) {
            if ( array_search( $stripeTaxRate->id, $taxRateIds) !== false ) {
                $taxRatesLookup[$stripeTaxRate->id] = array(
                    'displayName'       => $stripeTaxRate->display_name,
                    'inclusive'         => $stripeTaxRate->inclusive,
                    'country'           => $stripeTaxRate->country,
                    'state'             => $stripeTaxRate->state,
                    'percentage'        => $stripeTaxRate->percentage,
                    'jurisdiction'      => $stripeTaxRate->jurisdiction,
                    'taxRateId'         => $stripeTaxRate->id
                );
            }
        }

        foreach ($taxRateIds as $priceId) {
            if (array_key_exists($priceId, $taxRatesLookup)) {
                array_push($taxRates, $taxRatesLookup[$priceId]);
            }
        }

        return $taxRates;
    }

    /**
     * @return mixed
     */
    public function getTaxRateType() {
        return $this->taxRateType;
    }

    /**
     * @return mixed
     */
    public function getCollectCustomerTaxId() {
        return $this->collectCustomerTaxId;
    }

    /**
     * @return mixed
     */
    public function getTaxRates() {
        return $this->taxRates;
    }
}

abstract class MM_WPFS_Admin_PaymentFormModel extends MM_WPFS_Admin_FormModel {
    use MM_WPFS_Admin_TaxRateTools;

    protected $transactionDescription;
    protected $currency;
    protected $paymentType;
    protected $chargeType;
    protected $allowCustomPaymentAmount;
    protected $productSelectorStyle;
    protected $onetimeProducts;
    protected $showCouponField;
    protected $generateInvoice;

    /** @var MM_WPFS_Stripe */
    protected $stripe;

    public function __construct() {
        $this->stripe = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
    }

    /**
     * @param $onetimeProducts
     */
    protected function prepareOnetimeProducts( $priceIds )  {
        $products = array();
        $productsLookup = array();

        $stripePrices = $this->stripe->getOnetimePrices();
        foreach ($stripePrices as $stripePrice) {
            if (array_search($stripePrice->id, $priceIds) !== false) {
                $productsLookup[$stripePrice->id] = array(
                    'stripePriceId' => $stripePrice->id,
                    'name' => $stripePrice->product->name,
                    'price' => $stripePrice->unit_amount,
                    'currency' => $stripePrice->currency,
                );
            }
        }

        foreach ($priceIds as $priceId) {
            if (array_key_exists($priceId, $productsLookup)) {
                array_push($products, $productsLookup[$priceId]);
            }
        }

        return $products;
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->transactionDescription = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_TRANSACTION_DESCRIPTION );

        $this->currency     = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_CURRENCY );
        $this->chargeType   = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_CHARGE_TYPE, MM_WPFS::CHARGE_TYPE_IMMEDIATE );
        $this->paymentType  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_PAYMENT_TYPE, MM_WPFS::PAYMENT_TYPE_SPECIFIED_AMOUNT );
        $this->allowCustomPaymentAmount = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_ALLOW_CUSTOM_PAYMENT_AMOUNT, 0 );

        $this->generateInvoice  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_GENERATE_INVOICE, 0 );

        $onetimeProducts = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_ONETIME_PRODUCTS );
        $this->onetimeProducts = $this->prepareOnetimeProducts( $onetimeProducts );

        $this->taxRateType  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_TAX_RATE_TYPE, MM_WPFS::FIELD_VALUE_TAX_RATE_NO_TAX);
        $this->collectCustomerTaxId  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_COLLECT_CUSTOMER_TAX_ID, 0 );

        $taxRates = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_TAX_RATES );
        $this->taxRates = $this->prepareTaxRates( $taxRates );

        $this->productSelectorStyle = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_PRODUCT_SELECTOR_STYLE, MM_WPFS::AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS );

        $this->showCouponField = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_SHOW_COUPON, 0 );

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $data = array(
            'stripeDescription'         => $this->transactionDescription,
            'currency'                  => $this->currency,
            'customAmount'              => $this->paymentType,
            'chargeType'                => $this->chargeType,
            'generateInvoice'           => $this->generateInvoice,
            'allowListOfAmountsCustom'  => $this->allowCustomPaymentAmount,
            'amountSelectorStyle'       => $this->productSelectorStyle,
            'decoratedProducts'         => json_encode( $this->onetimeProducts ),
            'vatRateType'               => $this->taxRateType,
            'collectCustomerTaxId'      => $this->collectCustomerTaxId,
            'vatRates'                  => json_encode( $this->taxRates ),
            'showCouponInput'           => $this->showCouponField,
            // The fields below are here for compatibility
            'amount'                    => 0,
            'showButtonAmount'          => 0,
        );

        $data = array_merge( $data, $parentData );

        return $data;
    }

    /**
     * @return mixed
     */
    public function getTransactionDescription() {
        return $this->transactionDescription;
    }

    /**
     * @return mixed
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getPaymentType() {
        return $this->paymentType;
    }

    /**
     * @return mixed
     */
    public function getChargeType() {
        return $this->chargeType;
    }

    /**
     * @return mixed
     */
    public function getAllowCustomPaymentAmount() {
        return $this->allowCustomPaymentAmount;
    }

    /**
     * @return mixed
     */
    public function getProductSelectorStyle() {
        return $this->productSelectorStyle;
    }

    /**
     * @return mixed
     */
    public function getOnetimeProducts() {
        return $this->onetimeProducts;
    }

    /**
     * @return mixed
     */
    public function getShowCouponField() {
        return $this->showCouponField;
    }

    /**
     * @return mixed
     */
    public function getGenerateInvoice() {
        return $this->generateInvoice;
    }
}

abstract class MM_WPFS_Admin_DonationFormModel extends MM_WPFS_Admin_FormModel {
    protected $transactionDescription;
    protected $currency;
    protected $donationAmounts;
    protected $donationFrequencyOnetime;
    protected $donationFrequencyDaily;
    protected $donationFrequencyWeekly;
    protected $donationFrequencyMonthly;
    protected $donationFrequencyAnnual;
    protected $allowCustomDonationAmount;

    public function __construct() {
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->transactionDescription = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_TRANSACTION_DESCRIPTION );

        $this->currency = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_CURRENCY );

        $this->donationAmounts           = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_DONATION_AMOUNTS );
        $this->allowCustomDonationAmount = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_ALLOW_CUSTOM_DONATION_AMOUNT, 0 );

        $this->donationFrequencyOnetime = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_DONATION_FREQUENCY_ONETIME, 0 );
        $this->donationFrequencyDaily   = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_DONATION_FREQUENCY_DAILY, 0 );
        $this->donationFrequencyWeekly  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_DONATION_FREQUENCY_WEEKLY, 0 );
        $this->donationFrequencyMonthly = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_DONATION_FREQUENCY_MONTHLY, 0 );
        $this->donationFrequencyAnnual  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_DonationFormViewConstants::FIELD_FORM_DONATION_FREQUENCY_ANNUAL, 0 );

        return $bindingResult;
    }

    public function getData() {

        $parentData = parent::getData();

        $data = array(
           'stripeDescription'              => $this->transactionDescription,
            'currency'                      => $this->currency,
            'donationAmounts'               => json_encode( $this->donationAmounts ),
            'allowCustomDonationAmount'     => $this->allowCustomDonationAmount,
            'allowOneTimeDonation'          => $this->donationFrequencyOnetime,
            'allowDailyRecurring'           => $this->donationFrequencyDaily,
            'allowWeeklyRecurring'          => $this->donationFrequencyWeekly,
            'allowMonthlyRecurring'         => $this->donationFrequencyMonthly,
            'allowAnnualRecurring'          => $this->donationFrequencyAnnual
        );

        $data = array_merge( $data, $parentData );

        return $data;
    }

    /**
     * @return mixed
     */
    public function getTransactionDescription() {
        return $this->transactionDescription;
    }

    /**
     * @return mixed
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getDonationAmounts() {
        return $this->donationAmounts;
    }

    /**
     * @return mixed
     */
    public function getDonationFrequencyOnetime() {
        return $this->donationFrequencyOnetime;
    }

    /**
     * @return mixed
     */
    public function getDonationFrequencyDaily() {
        return $this->donationFrequencyDaily;
    }

    /**
     * @return mixed
     */
    public function getDonationFrequencyWeekly() {
        return $this->donationFrequencyWeekly;
    }

    /**
     * @return mixed
     */
    public function getDonationFrequencyMonthly() {
        return $this->donationFrequencyMonthly;
    }

    /**
     * @return mixed
     */
    public function getDonationFrequencyAnnual() {
        return $this->donationFrequencyAnnual;
    }

    /**
     * @return mixed
     */
    public function getAllowCustomDonationAmount() {
        return $this->allowCustomDonationAmount;
    }


}

abstract class MM_WPFS_Admin_SubscriptionFormModel extends MM_WPFS_Admin_FormModel {
    use MM_WPFS_Admin_TaxRateTools;

    protected $allowSubscriptionQuantity;
    protected $subscriptionMinimumQuantity;
    protected $subscriptionMaximumQuantity;
    protected $productSelectorStyle;
    protected $recurringProducts;
    protected $showCouponField;

    /** @var MM_WPFS_Stripe */
    protected $stripe;

    public function __construct() {
        $this->stripe = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
    }

    /**
     * @param $products array
     * @return array
     */
    protected function extractPriceIdsFromProducts($products ) {
        $priceIds = array();

        foreach($products as $product ) {
            array_push( $priceIds, $product->stripePriceId );
        }

        return $priceIds;
    }

    /**
     * @param $products array
     *
     * @return array
     */
    protected function createProductsLookup($products ) {
        $productsLookup = array();

        foreach( $products as $product ) {
            $productsLookup[ $product->stripePriceId ] = $product;
        }

        return $productsLookup;
    }

    /**
     * @param $form \StdClass
     * @return array
     */
    protected function prepareRecurringProducts( $recurringProducts ) {
        $priceIds = $this->extractPriceIdsFromProducts( $recurringProducts );
        $recurringProductsLookup = $this->createProductsLookup( $recurringProducts );

        $savedProducts = array();
        $savedProductsLookup = array();

        $stripePrices = $this->stripe->getRecurringPrices();
        foreach ($stripePrices as $stripePrice) {
            if (array_search($stripePrice->id, $priceIds) !== false) {
                $mergedProduct = $recurringProductsLookup[ $stripePrice->id ];

                $mergedProduct->name  = $stripePrice->product->name;
                $mergedProduct->price = $stripePrice->unit_amount;
                $mergedProduct->currency = $stripePrice->currency;
                $mergedProduct->interval = $stripePrice->recurring['interval'];
                $mergedProduct->intervalCount = $stripePrice->recurring['interval_count'];

                $savedProductsLookup[$stripePrice->id] = $mergedProduct;
            }
        }

        foreach ($priceIds as $priceId) {
            if (array_key_exists($priceId, $savedProductsLookup)) {
                array_push($savedProducts, $savedProductsLookup[$priceId]);
            }
        }

        return $savedProducts;
    }

    public function bindByArray( $postData ) {

        $bindingResult = parent::bindByArray( $postData );

        $this->allowSubscriptionQuantity  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SubscriptionFormView::FIELD_FORM_ALLOW_SUBSCRIPTION_QUANTITY, 0 );
        $this->subscriptionMaximumQuantity  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SubscriptionFormView::FIELD_FORM_SUBSCRIPTION_MAXIMUM_QUANTITY, 0 );

        $this->productSelectorStyle  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SubscriptionFormView::FIELD_FORM_PLAN_SELECTOR_STYLE );

        $recurringProducts = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_SubscriptionFormView::FIELD_FORM_RECURRING_PRODUCTS );
        $this->recurringProducts = $this->prepareRecurringProducts( $recurringProducts );

        $this->taxRateType  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_TAX_RATE_TYPE, MM_WPFS::FIELD_VALUE_TAX_RATE_NO_TAX);
        $this->collectCustomerTaxId  = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_COLLECT_CUSTOMER_TAX_ID, 0 );

        $taxRates = $this->getJSONDecodedArrayParam( $postData, MM_WPFS_Admin_PaymentFormViewConstants::FIELD_FORM_TAX_RATES );
        $this->taxRates = $this->prepareTaxRates( $taxRates );

        $this->showCouponField = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_SubscriptionFormView::FIELD_FORM_SHOW_COUPON, 0 );

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $data = array(
            'decoratedPlans'                 => json_encode( $this->recurringProducts ),
            'vatRateType'                    => $this->taxRateType,
            'collectCustomerTaxId'           => $this->collectCustomerTaxId,
            'vatRates'                       => json_encode( $this->taxRates ),
            'planSelectorStyle'              => $this->productSelectorStyle,
            'allowMultipleSubscriptions'     => $this->allowSubscriptionQuantity,
            'maximumQuantityOfSubscriptions' => $this->subscriptionMaximumQuantity,
            'showCouponInput'                => $this->showCouponField,
        );

        $data = array_merge( $data, $parentData );

        return $data;
    }

    /**
     * @return mixed
     */
    public function getAllowSubscriptionQuantity() {
        return $this->allowSubscriptionQuantity;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionMinimumQuantity() {
        return $this->subscriptionMinimumQuantity;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionMaximumQuantity() {
        return $this->subscriptionMaximumQuantity;
    }

    /**
     * @return mixed
     */
    public function getProductSelectorStyle() {
        return $this->productSelectorStyle;
    }

    /**
     * @return mixed
     */
    public function getRecurringProducts() {
        return $this->recurringProducts;
    }

    /**
     * @return mixed
     */
    public function getShowCouponField() {
        return $this->showCouponField;
    }
}

class MM_WPFS_Admin_InlineSaveCardFormModel extends MM_WPFS_Admin_SaveCardFormModel implements MM_WPFS_Admin_InlineForm {
    protected $title;

    use MM_WPFS_Admin_InlineFormModel;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_INLINE_SAVE_CARD_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_InlineSaveCardFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindInlineParams( $postData );
        // Legacy fields
        $this->title = '';

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $inlineData = $this->getInlineDataArray();

        $data = array(
            'formTitle'             => $this->title,
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }
}

class MM_WPFS_Admin_CheckoutSaveCardFormModel extends MM_WPFS_Admin_SaveCardFormModel implements MM_WPFS_Admin_CheckoutForm {
    use MM_WPFS_Admin_CheckoutFormModel;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_CHECKOUT_SAVE_CARD_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_CheckoutSaveCardFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindCheckoutParams( $postData );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $inlineData = $this->getCheckoutDataArray();

        $data = array(
            // Legacy fields
            'showRememberMe'        => 0,
            'disableStyling'        => 0,
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }
}

class MM_WPFS_Admin_InlinePaymentFormModel extends MM_WPFS_Admin_PaymentFormModel implements MM_WPFS_Admin_InlineForm {
    protected $title;

    use MM_WPFS_Admin_InlineFormModel;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_INLINE_PAYMENT_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_InlinePaymentFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindInlineParams( $postData );
        // Legacy fields
        $this->title = '';

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $inlineData = $this->getInlineDataArray();

        $data = array(
            'formTitle'             => $this->title,
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }
}

class MM_WPFS_Admin_CheckoutPaymentFormModel extends MM_WPFS_Admin_PaymentFormModel implements MM_WPFS_Admin_CheckoutForm {
    use MM_WPFS_Admin_CheckoutFormModel;

    protected $checkoutProductName;
    protected $checkoutProductDescription;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_CHECKOUT_PAYMENT_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_CheckoutPaymentFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindCheckoutParams( $postData );

        $this->checkoutProductName = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CheckoutPaymentFormViewConstants::FIELD_FORM_CHECKOUT_PRODUCT_NAME );
        $this->checkoutProductDescription = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CheckoutPaymentFormViewConstants::FIELD_FORM_CHECKOUT_PRODUCT_DESCRIPTION );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $checkoutData = $this->getCheckoutDataArray();

        $data = array(
            'productDesc'   => $this->checkoutProductName,
            'companyName'   => $this->checkoutProductDescription,
            // Legacy fields
            'showRememberMe'        => 0,
            'disableStyling'        => 0,
            'useBitcoin'            => 0,
            'useAlipay'             => 0
        );

        $data = array_merge( $data, $checkoutData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

    /**
     * @return mixed
     */
    public function getCheckoutProductName() {
        return $this->checkoutProductName;
    }

    /**
     * @return mixed
     */
    public function getCheckoutProductDescription() {
        return $this->checkoutProductDescription;
    }
}

class MM_WPFS_Admin_InlineDonationFormModel extends MM_WPFS_Admin_DonationFormModel implements MM_WPFS_Admin_InlineForm {
    use MM_WPFS_Admin_InlineFormModel;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_INLINE_DONATION_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_InlineDonationFormValidator();
    }

    public function load( $dataArray ) {
        // tnagy unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindInlineParams( $postData );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $inlineData = $this->getInlineDataArray();

        $data = array(
            // No fields specific to inline donation forms
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }
}

class MM_WPFS_Admin_CheckoutDonationFormModel extends MM_WPFS_Admin_DonationFormModel implements MM_WPFS_Admin_CheckoutForm {
    use MM_WPFS_Admin_CheckoutFormModel;

    protected $checkoutProductName;
    protected $checkoutProductDescription;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_CHECKOUT_DONATION_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_CheckoutDonationFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindCheckoutParams( $postData );

        $this->checkoutProductName = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CheckoutDonationFormViewConstants::FIELD_FORM_CHECKOUT_PRODUCT_NAME );
        $this->checkoutProductDescription = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CheckoutDonationFormViewConstants::FIELD_FORM_CHECKOUT_PRODUCT_DESCRIPTION );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $checkoutData = $this->getCheckoutDataArray();

        $data = array(
            'productDesc'   => $this->checkoutProductName,
            'companyName'   => $this->checkoutProductDescription,
        );

        $data = array_merge( $data, $checkoutData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

    /**
     * @return mixed
     */
    public function getCheckoutProductName() {
        return $this->checkoutProductName;
    }

    /**
     * @return mixed
     */
    public function getCheckoutProductDescription() {
        return $this->checkoutProductDescription;
    }
}

class MM_WPFS_Admin_InlineSubscriptionFormModel extends MM_WPFS_Admin_SubscriptionFormModel implements MM_WPFS_Admin_InlineForm {
    protected $title;

    use MM_WPFS_Admin_InlineFormModel;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_INLINE_SUBSCRIPTION_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_InlineSubscriptionFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindInlineParams( $postData );
        // Legacy fields
        $this->title = '';

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $inlineData = $this->getInlineDataArray();

        $data = array(
            'formTitle'             => $this->title,
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }
}

class MM_WPFS_Admin_CheckoutSubscriptionFormModel extends MM_WPFS_Admin_SubscriptionFormModel implements MM_WPFS_Admin_CheckoutForm {
    use MM_WPFS_Admin_CheckoutFormModel;

    protected $simpleButtonLayout;

    public function __construct() {
        $this->formHash = MM_WPFS_AdminViewConstants::FORM_HASH_ADMIN_CHECKOUT_SUBSCRIPTION_FORM;
        parent::__construct();
        $this->__validator = new MM_WPFS_Admin_CheckoutSubscriptionFormValidator();
    }

    public function load( $dataArray ) {
        // unsupported operation
    }

    public function bindByArray( $postData ) {
        $bindingResult = parent::bindByArray( $postData );

        $this->bindCheckoutParams( $postData );

        $this->simpleButtonLayout = $this->getSanitizedArrayParam( $postData, MM_WPFS_Admin_CheckoutSubscriptionFormViewConstants::FIELD_FORM_SIMPLE_BUTTON_LAYOUT, 0 );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getData() {
        $parentData = parent::getData();

        $checkoutData = $this->getCheckoutDataArray();

        $data = array(
            'simpleButtonLayout'    => $this->simpleButtonLayout,
            // Legacy fields
            'showRememberMe'        => 0,
            'disableStyling'        => 0,
            'vatRateType'           => MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT
        );

        $data = array_merge( $data, $checkoutData, $parentData );

        return $data;
    }

    public function afterBind() {

    }
}
