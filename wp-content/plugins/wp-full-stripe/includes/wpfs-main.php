<?php

/*
WP Full Stripe
https://paymentsplugin.com
Complete Stripe payments integration for Wordpress
Mammothology
6.0.10
https://paymentsplugin.com
*/

class MM_WPFS {

	const VERSION = '6.0.10';
	const REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS = 'wpfs_rendered_forms';

	const HANDLE_WP_FULL_STRIPE_JS = 'wp-full-stripe-js';

	const SHORTCODE_FULLSTRIPE_FORM = 'fullstripe_form';
	const SHORTCODE_FULLSTRIPE_THANKYOU = 'fullstripe_thankyou';
	const SHORTCODE_FULLSTRIPE_THANKYOU_SUCCESS = 'fullstripe_thankyou_success';
	const SHORTCODE_FULLSTRIPE_THANKYOU_DEFAULT = 'fullstripe_thankyou_default';
	const HANDLE_WP_FULL_STRIPE_UTILS_JS = 'wp-full-stripe-utils-js';
	const HANDLE_SPRINTF_JS = 'sprintf-js';
	const HANDLE_STRIPE_JS_V_3 = 'stripe-js-v3';
	const HANDLE_STYLE_WPFS_VARIABLES = 'wpfs-variables-css';
	const HANDLE_STYLE_WPFS_FORMS = 'wpfs-forms-css';
	const HANDLE_GOOGLE_RECAPTCHA_V_2 = 'google-recaptcha-v2';
	const URL_RECAPTCHA_API_SITEVERIFY = 'https://www.google.com/recaptcha/api/siteverify';
	const SOURCE_GOOGLE_RECAPTCHA_V2_API_JS = 'https://www.google.com/recaptcha/api.js';

	// Generic form types
	const FORM_TYPE_PAYMENT = 'payment';
    const FORM_TYPE_SUBSCRIPTION = 'subscription';
    const FORM_TYPE_DONATION = 'donation';
    const FORM_TYPE_SAVE_CARD = 'save_card';

	const FORM_TYPE_CHECKOUT = 'checkout';

	const FORM_TYPE_INLINE_PAYMENT = 'inline_payment';
	const FORM_TYPE_CHECKOUT_PAYMENT = 'checkout_payment';
	const FORM_TYPE_INLINE_SUBSCRIPTION = 'inline_subscription';
    const FORM_TYPE_CHECKOUT_SUBSCRIPTION = 'checkout_subscription';
	const FORM_TYPE_INLINE_SAVE_CARD = 'inline_save_card';
    const FORM_TYPE_CHECKOUT_SAVE_CARD = 'checkout_save_card';
    const FORM_TYPE_INLINE_DONATION = 'inline_donation';
    const FORM_TYPE_CHECKOUT_DONATION = 'checkout_donation';

    // legacy form types, used only for shortcodes
    const FORM_TYPE_POPUP_PAYMENT = 'popup_payment';
    const FORM_TYPE_POPUP_SUBSCRIPTION = 'popup_subscription';
    const FORM_TYPE_POPUP_SAVE_CARD = 'popup_save_card';
    const FORM_TYPE_POPUP_DONATION = 'popup_donation';

    const FORM_TYPE_ADMIN_CREATE_FORM = 'createForm';
    const FORM_TYPE_ADMIN_CONFIGURE_STRIPE_ACCOUNT = 'configureStripeAccount';
    const FORM_TYPE_ADMIN_CONFIGURE_CUSTOMER_PORTAL = 'configureMyAccount';
    const FORM_TYPE_ADMIN_CONFIGURE_SECURITY = 'configureSecurity';
    const FORM_TYPE_ADMIN_CONFIGURE_EMAIL_OPTIONS = 'configureEmailOptions';
    const FORM_TYPE_ADMIN_CONFIGURE_EMAIL_TEMPLATES = 'configureEmailTemplates';
    const FORM_TYPE_ADMIN_CONFIGURE_FORMS_OPTIONS = 'configureFormsOptions';
    const FORM_TYPE_ADMIN_CONFIGURE_FORMS_APPEARANCE = 'configureFormsAppearance';
    const FORM_TYPE_ADMIN_CONFIGURE_WP_DASHBOARD = 'configureWpDashboard';
    const FORM_TYPE_ADMIN_INLINE_SAVE_CARD_FORM = 'inlineSaveCardForm';
    const FORM_TYPE_ADMIN_CHECKOUT_SAVE_CARD_FORM = 'checkoutSaveCardForm';
    const FORM_TYPE_ADMIN_INLINE_DONATION_FORM = 'inlineDonationForm';
    const FORM_TYPE_ADMIN_CHECKOUT_DONATION_FORM = 'checkoutDonationForm';
    const FORM_TYPE_ADMIN_ADD_CUSTOM_FIELD = 'addCustomField';
    const FORM_TYPE_ADMIN_ADD_SUGGESTED_DONATION_AMOUNT = 'addSuggestedDonationAmount';
    const FORM_TYPE_ADMIN_INLINE_PAYMENT_FORM = 'inlinePaymentForm';
    const FORM_TYPE_ADMIN_CHECKOUT_PAYMENT_FORM = 'checkoutPaymentForm';
    const FORM_TYPE_ADMIN_INLINE_SUBSCRIPTION_FORM = 'inlineSubscriptionForm';
    const FORM_TYPE_ADMIN_CHECKOUT_SUBSCRIPTION_FORM = 'checkoutSubscriptionForm';
    const FORM_TYPE_ADMIN_ADD_PLAN_PROPERTIES = 'addPlanProperties';
    const FORM_TYPE_ADMIN_EDIT_PRODUCT_PROPERTIES = 'editProductProperties';

    const FORM_LAYOUT_INLINE = 'inline';
    const FORM_LAYOUT_CHECKOUT = 'checkout';

    const STRIPE_API_MODE_TEST = 'test';
    const STRIPE_API_MODE_LIVE = 'live';

    const REDIRECT_TYPE_SHOW_CONFIRMATION_MESSAGE = 'showConfirmationMessage';
    const REDIRECT_TYPE_TO_PAGE_OR_POST = 'redirectToPageOrPost';
    const REDIRECT_TYPE_TO_CUSTOM_URL = 'redirecToCustomUrl';

    const VAT_RATE_TYPE_NO_VAT = 'no_vat';
	const VAT_RATE_TYPE_FIXED_VAT = 'fixed_vat';
	const VAT_RATE_TYPE_CUSTOM_VAT = 'custom_vat';

	const NO_VAT_PERCENT = 0.0;

	const DEFAULT_BILLING_COUNTRY_INITIAL_VALUE = 'US';

	const PREFERRED_LANGUAGE_AUTO = 'auto';

	const DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT = 10;

	const PAYMENT_TYPE_LIST_OF_AMOUNTS = 'list_of_amounts';
	const PAYMENT_TYPE_CUSTOM_AMOUNT = 'custom_amount';
	const PAYMENT_TYPE_SPECIFIED_AMOUNT = 'specified_amount';
	const PAYMENT_TYPE_CARD_CAPTURE = 'card_capture';

	const CURRENCY_USD = 'usd';

	const OPTION_API_TEST_SECRET_KEY = 'secretKey_test';
    const OPTION_API_TEST_PUBLISHABLE_KEY = 'publishKey_test';
    const OPTION_API_LIVE_SECRET_KEY = 'secretKey_live';
    const OPTION_API_LIVE_PUBLISHABLE_KEY = 'publishKey_live';
    const OPTION_API_MODE = 'apiMode';
    const OPTION_FILL_IN_EMAIL_FOR_LOGGED_IN_USERS = 'lock_email_field_for_logged_in_users';
	const OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA = 'secure_inline_forms_with_google_recaptcha';
	const OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA = 'secure_checkout_forms_with_google_recaptcha';
	const OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA = 'secure_subscription_update_with_google_recaptcha';
	const OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY = 'google_recaptcha_site_key';
	const OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY = 'google_recaptcha_secret_key';
	const OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS = 'my_account_subscribers_cancel_subscriptions';
    const OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_UPDOWNGRADE_SUBSCRIPTIONS = 'my_account_subscribers_updowngrade_subscriptions';
    const OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS = 'my_account_when_cancel_subscriptions';
    const OPTION_CUSTOMER_PORTAL_SHOW_INVOICES_SECTION = 'my_account_show_invoices_section';
    const OPTION_CUSTOMER_PORTAL_SHOW_ALL_INVOICES = 'my_account_show_all_invoices';
	const OPTION_DECIMAL_SEPARATOR_SYMBOL = 'decimal_separator_symbol';
	const OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE = 'show_currency_symbol_instead_of_code';
	const OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION = 'show_currency_sign_first';
	const OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT = 'put_whitespace_between_currency_and_amount';
    const OPTION_LAST_WEBHOOK_EVENT_TEST = 'last_webhook_event_test';
    const OPTION_LAST_WEBHOOK_EVENT_LIVE = 'last_webhook_event_live';
    const OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS = 'email_receipt_sender_address';
    const OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES = 'email_notification_bcc_addresses';
    const OPTION_EMAIL_TEMPLATES = 'email_receipts';
    const OPTION_RECEIPT_EMAIL_TYPE = 'receiptEmailType';
    const OPTION_FORM_CUSTOM_CSS = 'form_css';

    const OPTION_VALUE_RECEIPT_EMAIL_PLUGIN = 'plugin';

	const CANCEL_SUBSCRIPTION_IMMEDIATELY = 'immediately';
    const CANCEL_SUBSCRIPTION_AT_PERIOD_END = 'atPeriodEnd';

	const DECIMAL_SEPARATOR_SYMBOL_DOT = 'dot';
	const DECIMAL_SEPARATOR_SYMBOL_COMMA = 'comma';

	const CHARGE_TYPE_IMMEDIATE = 'immediate';
	const CHARGE_TYPE_AUTHORIZE_AND_CAPTURE = 'authorize_and_capture';

	const PAYMENT_METHOD_CARD = 'card';

	const STRIPE_CHARGE_STATUS_SUCCEEDED = 'succeeded';
	const STRIPE_CHARGE_STATUS_PENDING = 'pending';
	const STRIPE_CHARGE_STATUS_FAILED = 'failed';

	const PAYMENT_STATUS_UNKNOWN = 'unknown';
	const PAYMENT_STATUS_FAILED = 'failed';
	const PAYMENT_STATUS_REFUNDED = 'refunded';
	const PAYMENT_STATUS_EXPIRED = 'expired';
	const PAYMENT_STATUS_PAID = 'paid';
	const PAYMENT_STATUS_AUTHORIZED = 'authorized';
	const PAYMENT_STATUS_PENDING = 'pending';
	const PAYMENT_STATUS_RELEASED = 'released';

	const REFUND_STATUS_SUCCEEDED = 'succeeded';
	const REFUND_STATUS_FAILED = 'failed';
	const REFUND_STATUS_PENDING = 'pending';
	const REFUND_STATUS_CANCELED = 'canceled';

	const SUBSCRIPTION_STATUS_ENDED = 'ended';
	const SUBSCRIPTION_STATUS_CANCELLED = 'cancelled';

	const AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS = 'radio-buttons';
	const AMOUNT_SELECTOR_STYLE_DROPDOWN = 'dropdown';
	const AMOUNT_SELECTOR_STYLE_BUTTON_GROUP = 'button-group';

	const PLAN_SELECTOR_STYLE_DROPDOWN = 'dropdown';
	const PLAN_SELECTOR_STYLE_RADIO_BUTTONS = 'radio-buttons';

	const JS_VARIABLE_WPFS_FORM_OPTIONS = 'wpfsFormSettings';
	const JS_VARIABLE_AJAX_URL = 'ajaxUrl';
	const JS_VARIABLE_STRIPE_KEY = 'stripeKey';
	const JS_VARIABLE_GOOGLE_RECAPTCHA_SITE_KEY = 'googleReCaptchaSiteKey';
	const JS_VARIABLE_L10N = 'l10n';
	const JS_VARIABLE_FORM_FIELDS = 'formFields';

	const ACTION_NAME_BEFORE_SAVE_CARD = 'fullstripe_before_card_capture';
	const ACTION_NAME_AFTER_SAVE_CARD = 'fullstripe_after_card_capture';
	const ACTION_NAME_BEFORE_CHECKOUT_SAVE_CARD = 'fullstripe_before_checkout_card_capture';
	const ACTION_NAME_AFTER_CHECKOUT_SAVE_CARD = 'fullstripe_after_checkout_card_capture';

	const ACTION_NAME_BEFORE_PAYMENT_CHARGE = 'fullstripe_before_payment_charge';
	const ACTION_NAME_AFTER_PAYMENT_CHARGE = 'fullstripe_after_payment_charge';
	const ACTION_NAME_BEFORE_CHECKOUT_PAYMENT_CHARGE = 'fullstripe_before_checkout_payment_charge';
	const ACTION_NAME_AFTER_CHECKOUT_PAYMENT_CHARGE = 'fullstripe_after_checkout_payment_charge';

    const ACTION_NAME_BEFORE_DONATION_CHARGE = 'fullstripe_before_donation_charge';
    const ACTION_NAME_AFTER_DONATION_CHARGE = 'fullstripe_after_donation_charge';
    const ACTION_NAME_BEFORE_CHECKOUT_DONATION_CHARGE = 'fullstripe_before_checkout_donation_charge';
    const ACTION_NAME_AFTER_CHECKOUT_DONATION_CHARGE = 'fullstripe_after_checkout_donation_charge';

    const ACTION_NAME_BEFORE_SUBSCRIPTION_CHARGE = 'fullstripe_before_subscription_charge';
	const ACTION_NAME_AFTER_SUBSCRIPTION_CHARGE = 'fullstripe_after_subscription_charge';
	const ACTION_NAME_BEFORE_CHECKOUT_SUBSCRIPTION_CHARGE = 'fullstripe_before_checkout_subscription_charge';
	const ACTION_NAME_AFTER_CHECKOUT_SUBSCRIPTION_CHARGE = 'fullstripe_after_checkout_subscription_charge';

	const ACTION_NAME_BEFORE_SUBSCRIPTION_CANCELLATION = 'fullstripe_before_subscription_cancellation';
	const ACTION_NAME_AFTER_SUBSCRIPTION_CANCELLATION = 'fullstripe_after_subscription_cancellation';

    const ACTION_NAME_BEFORE_SUBSCRIPTION_UPDATE = 'fullstripe_before_subscription_update';
    const ACTION_NAME_AFTER_SUBSCRIPTION_UPDATE = 'fullstripe_after_subscription_update';

    const ACTION_NAME_BEFORE_SUBSCRIPTION_ACTIVATION = 'fullstripe_before_subscription_activation';
    const ACTION_NAME_AFTER_SUBSCRIPTION_ACTIVATION = 'fullstripe_after_subscription_activation';

    const FILTER_NAME_GET_VAT_PERCENT = 'fullstripe_get_vat_percent';
	const FILTER_NAME_SELECT_SUBSCRIPTION_PLAN = 'fullstripe_select_subscription_plan';
	const FILTER_NAME_SET_CUSTOM_AMOUNT = 'fullstripe_set_custom_amount';
	const FILTER_NAME_ADD_TRANSACTION_METADATA = 'fullstripe_add_transaction_metadata';
	const FILTER_NAME_MODIFY_EMAIL_MESSAGE = 'fullstripe_modify_email_message';
	const FILTER_NAME_MODIFY_EMAIL_SUBJECT = 'fullstripe_modify_email_subject';
	const FILTER_NAME_GET_UPGRADE_DOWNGRADE_PLANS = 'fullstripe_get_upgrade_downgrade_plans';

	const STRIPE_OBJECT_ID_PREFIX_PAYMENT_INTENT = 'pi_';
	const STRIPE_OBJECT_ID_PREFIX_CHARGE = 'ch_';
	const PAYMENT_OBJECT_TYPE_UNKNOWN = 'Unknown';
	const PAYMENT_OBJECT_TYPE_STRIPE_PAYMENT_INTENT = '\StripeWPFS\PaymentIntent';
	const PAYMENT_OBJECT_TYPE_STRIPE_CHARGE = '\StripeWPFS\Charge';

	const SUBSCRIBER_STATUS_CANCELLED = 'cancelled';
	const SUBSCRIBER_STATUS_RUNNING = 'running';
	const SUBSCRIBER_STATUS_ENDED = 'ended';
	const SUBSCRIBER_STATUS_INCOMPLETE = 'incomplete';

    const DONATION_STATUS_UNKNOWN = 'unknown';
    const DONATION_STATUS_PAID = 'paid';
    const DONATION_STATUS_RUNNING = 'running';
    const DONATION_STATUS_REFUNDED = 'refunded';

    const HTTP_PARAM_NAME_PLAN = 'wpfsPlan';
	const HTTP_PARAM_NAME_AMOUNT = 'wpfsAmount';

    const DONATION_PLAN_ID_PREFIX = "wpfsDonationPlan";

    const EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT = 'paymentMade';
    const EMAIL_TEMPLATE_ID_PAYMENT_RECEIPT_STRIPE = 'paymentMadeStripe';
    const EMAIL_TEMPLATE_ID_CARD_SAVED = 'cardCaptured';
    const EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT = 'subscriptionStarted';
    const EMAIL_TEMPLATE_ID_SUBSCRIPTION_RECEIPT_STRIPE = 'subscriptionStartedStripe';
    const EMAIL_TEMPLATE_ID_SUBSCRIPTION_ENDED = 'subscriptionFinished';
    const EMAIL_TEMPLATE_ID_DONATION_RECEIPT = 'donationMade';
    const EMAIL_TEMPLATE_ID_DONATION_RECEIPT_STRIPE = 'donationMadeStripe';
    const EMAIL_TEMPLATE_ID_CUSTOMER_PORTAL_SECURITY_CODE = 'cardUpdateConfirmationRequest';

    const COUNTRY_CODE_UNITED_STATES = 'US';
    const FIELD_VALUE_TAX_RATE_NO_TAX = 'taxRateNoTax';
    const FIELD_VALUE_TAX_RATE_FIXED = 'taxRateFixed';
    const FIELD_VALUE_TAX_RATE_DYNAMIC = 'taxRateDynamic';

    public static $instance;

	private $debugLog = false;

	/** @var MM_WPFS_Customer */
	private $customer = null;
	/** @var MM_WPFS_Admin */
	private $admin = null;
	/** @var MM_WPFS_Database */
	private $database = null;
	/** @var MM_WPFS_Stripe */
	private $stripe = null;
	/** @var MM_WPFS_Admin_Menu */
	private $adminMenu = null;
	/** @var MM_WPFS_TransactionDataService */
	private $transactionDataService = null;
	/** @var MM_WPFS_CustomerPortalService */
	private $cardUpdateService = null;
	/** @var MM_WPFS_CheckoutSubmissionService */
	private $checkoutSubmissionService = null;
	/**
	 * @var bool Choose to load scripts and styles the WordPress way. We should move this field to a wp_option later.
	 */
	private $loadScriptsAndStylesWithActionHook = false;
    /**
     * @var bool Turn this off if you don't want to load the form CSS styles
     */
	private $includeDefaultStyles = true;

	public function __construct() {

		$this->includes();
		$this->setup();
		$this->hooks();

	}

	function includes() {

		include 'wpfs-localization.php';
		include 'wp/class-wp-list-table.php';
        include 'wpfs-tables.php';
        include 'wpfs-languages.php';
		include 'wpfs-admin.php';
		include 'wpfs-admin-menu.php';
		include 'wpfs-form-models.php';
        include 'wpfs-admin-models.php';
		include 'wpfs-assets.php';
        include 'wpfs-pricing.php';
		include 'wpfs-customer-portal-service.php';
		include 'wpfs-checkout-charge-handler.php';
		include 'wpfs-checkout-submission-service.php';
		include 'wpfs-countries.php';
        include 'wpfs-states.php';
		include 'wpfs-currencies.php';
		include 'wpfs-customer.php';
		include 'wpfs-database.php';
		include 'wpfs-logger-service.php';
		include 'wpfs-mailer.php';
		include 'wpfs-news-feed-url.php';
		include 'wpfs-patcher.php';
		include 'wpfs-payments.php';
		include 'wpfs-form-views.php';
		include 'wpfs-help.php';
        include 'wpfs-admin-views.php';
		include 'wpfs-transaction-data-service.php';
		include 'wpfs-form-validators.php';
        include 'wpfs-admin-validators.php';
		include 'wpfs-web-hook-events.php';
        include 'wpfs-api.php';

		do_action( 'fullstripe_includes_action' );
	}

	function setup() {

		//set option defaults
		$options = get_option( 'fullstripe_options' );
		if ( ! $options || $options['fullstripe_version'] != self::VERSION ) {
			$this->set_option_defaults( $options );
			// tnagy reload saved options
			$options = get_option( 'fullstripe_options' );
		}
		$this->update_option_defaults( $options );

		MM_WPFS_LicenseManager::getInstance()->activateLicenseIfNeeded();

		//setup subclasses to handle everything
		$this->admin                     = new MM_WPFS_Admin();
		$this->adminMenu                 = new MM_WPFS_Admin_Menu();
		$this->customer                  = new MM_WPFS_Customer();
		$this->database                  = new MM_WPFS_Database();
		$this->stripe                    = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
		$this->transactionDataService    = new MM_WPFS_TransactionDataService();
		$this->cardUpdateService         = new MM_WPFS_CustomerPortalService();
		$this->checkoutSubmissionService = new MM_WPFS_CheckoutSubmissionService();

		do_action( 'fullstripe_setup_action' );

	}

	function set_option_defaults( $options ) {
		if ( ! $options ) {

			$emailReceipts = MM_WPFS_Mailer::getDefaultEmailTemplates();

			/** @noinspection PhpUndefinedClassInspection */
			$default_options = array(
				'secretKey_test'                                                  => 'YOUR_TEST_SECRET_KEY',
				'publishKey_test'                                                 => 'YOUR_TEST_PUBLISHABLE_KEY',
				'secretKey_live'                                                  => 'YOUR_LIVE_SECRET_KEY',
				'publishKey_live'                                                 => 'YOUR_LIVE_PUBLISHABLE_KEY',
				'apiMode'                                                         => 'test',
				MM_WPFS::OPTION_FORM_CUSTOM_CSS                                   => "",
				'receiptEmailType'                                                => MM_WPFS::OPTION_VALUE_RECEIPT_EMAIL_PLUGIN,
				'email_receipts'                                                  => json_encode( $emailReceipts ),
                MM_WPFS::OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS                 => get_bloginfo( 'admin_email' ),
                MM_WPFS::OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES                  => json_encode( array() ),
				'admin_payment_receipt'                                           => '0',
				'lock_email_field_for_logged_in_users'                            => '1',
				'fullstripe_version'                                              => self::VERSION,
				'webhook_token'                                                   => $this->createWebhookToken(),
				'custom_input_field_max_count'                                    => MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT,
				MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA        => '0',
				MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA      => '0',
				MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA => '0',
				MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY                        => 'YOUR_GOOGLE_RECAPTCHA_SITE_KEY',
				MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY                      => 'YOUR_GOOGLE_RECAPTCHA_SECRET_KEY',
                MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS   => '1',
                MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_UPDOWNGRADE_SUBSCRIPTIONS   => '0',
                MM_WPFS::OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS              => MM_WPFS::CANCEL_SUBSCRIPTION_IMMEDIATELY,
				MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_INVOICES_SECTION                  => '1',
				MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_ALL_INVOICES                      => '0',
				MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL                          => MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT,
				MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE              => '1',
				MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION              => '1',
				MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT        => '0',
				MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_TEST                           => null,
                MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_LIVE                           => null
			);

			$edd_options   = MM_WPFS_LicenseManager::getInstance()->getLicenseOptionDefaults();
			$final_options = array_merge( $default_options, $edd_options );

			update_option( 'fullstripe_options', $final_options );
		} else {

			// different version

			$options['fullstripe_version'] = self::VERSION;
			if ( ! array_key_exists( 'secretKey_test', $options ) ) {
				$options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_test', $options ) ) {
				$options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'secretKey_live', $options ) ) {
				$options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_live', $options ) ) {
				$options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'apiMode', $options ) ) {
				$options['apiMode'] = 'test';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_FORM_CUSTOM_CSS, $options ) ) {
				$options[MM_WPFS::OPTION_FORM_CUSTOM_CSS] = "";
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_RECEIPT_EMAIL_TYPE, $options ) ) {
				$options[MM_WPFS::OPTION_RECEIPT_EMAIL_TYPE] = MM_WPFS::OPTION_VALUE_RECEIPT_EMAIL_PLUGIN;
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_EMAIL_TEMPLATES, $options ) ) {
                $emailReceipts             = MM_WPFS_Mailer::getDefaultEmailTemplates();
                $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] = json_encode( $emailReceipts );
            } else {
                $emailReceipts = json_decode( $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] );
                MM_WPFS_Mailer::updateMissingEmailTemplatesWithDefaults($emailReceipts);
                $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] = json_encode( $emailReceipts );
            }
			if ( ! array_key_exists( MM_WPFS::OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS, $options ) ) {
				$options[ MM_WPFS::OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS ] = get_bloginfo( 'admin_email' );
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES, $options ) ) {
                $options[ MM_WPFS::OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES ] = json_encode( array() );
            }
			if ( ! array_key_exists( 'admin_payment_receipt', $options ) ) {
				$options['admin_payment_receipt'] = '0';
			}
			if ( ! array_key_exists( 'lock_email_field_for_logged_in_users', $options ) ) {
				$options['lock_email_field_for_logged_in_users'] = '1';
			}
			if ( ! array_key_exists( 'webhook_token', $options ) ) {
				$options['webhook_token'] = $this->createWebhookToken();
			}
			if ( ! array_key_exists( 'custom_input_field_max_count', $options ) ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			} elseif ( $options['custom_input_field_max_count'] != MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SITE_KEY';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SECRET_KEY';
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS, $options ) ) {
                $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] = '1';
            }
            if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_UPDOWNGRADE_SUBSCRIPTIONS, $options ) ) {
                $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_UPDOWNGRADE_SUBSCRIPTIONS ] = '1';
            }
            if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS, $options ) ) {
                $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS ] = MM_WPFS::CANCEL_SUBSCRIPTION_IMMEDIATELY;
            }
			if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_INVOICES_SECTION, $options ) ) {
				$options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_INVOICES_SECTION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_ALL_INVOICES, $options ) ) {
				$options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_ALL_INVOICES ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL, $options ) ) {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT, $options ) ) {
				$options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] = '0';
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_TEST, $options ) ) {
                $options[ MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_TEST ] = null;
            }
            if ( ! array_key_exists( MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_LIVE, $options ) ) {
                $options[ MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_LIVE ] = null;
            }

			MM_WPFS_LicenseManager::getInstance()->setLicenseOptionDefaultsIfEmpty( $options );

			update_option( 'fullstripe_options', $options );
		}

		// also, if version changed then the DB might be out of date
		MM_WPFS::setup_db( false );
	}

	/**
	 * Generates a unique random token for authenticating webhook callbacks.
	 *
	 * @return string
	 */
	private function createWebhookToken() {
		$siteURL           = get_site_url();
		$randomToken       = hash( 'md5', rand() );
		$generatedPassword = substr( hash( 'sha512', rand() ), 0, 6 );

		return hash( 'md5', $siteURL . '|' . $randomToken . '|' . $generatedPassword );
	}

	public static function setup_db( $network_wide ) {
		if ( $network_wide ) {
			MM_WPFS_Utils::log( "setup_db() - Activating network-wide" );
			if ( function_exists( 'get_sites' ) && function_exists( 'get_current_network_id' ) ) {
				$site_ids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
			} else {
				$site_ids = MM_WPFS_Database::get_site_ids();
			}

			foreach ( $site_ids as $site_id ) {
				switch_to_blog( $site_id );
				self::setup_db_single_site();
				restore_current_blog();
			}
		} else {
			MM_WPFS_Utils::log( "setup_db() - Activating for single site" );
			self::setup_db_single_site();
		}
	}

	public static function setup_db_single_site() {
		MM_WPFS_Database::fullstripe_setup_db();
		MM_WPFS_Patcher::apply_patches();
	}

    function update_option_defaults( $options ) {
		if ( $options ) {
			if ( ! array_key_exists( 'secretKey_test', $options ) ) {
				$options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_test', $options ) ) {
				$options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'secretKey_live', $options ) ) {
				$options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_live', $options ) ) {
				$options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'apiMode', $options ) ) {
				$options['apiMode'] = 'test';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_FORM_CUSTOM_CSS, $options ) ) {
				$options[MM_WPFS::OPTION_FORM_CUSTOM_CSS] = "";
			}
			if ( ! array_key_exists( 'receiptEmailType', $options ) ) {
				$options['receiptEmailType'] = MM_WPFS::OPTION_VALUE_RECEIPT_EMAIL_PLUGIN;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_EMAIL_TEMPLATES, $options ) ) {
				$emailReceipts             = MM_WPFS_Mailer::getDefaultEmailTemplates();
				$options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] = json_encode( $emailReceipts );
			} else {
				$emailReceipts = json_decode( $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] );
                MM_WPFS_Mailer::updateMissingEmailTemplatesWithDefaults($emailReceipts);
                $options[ MM_WPFS::OPTION_EMAIL_TEMPLATES ] = json_encode( $emailReceipts );
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS, $options ) ) {
				$options[ MM_WPFS::OPTION_EMAIL_NOTIFICATION_SENDER_ADDRESS ] = get_bloginfo( 'admin_email' );
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES, $options ) ) {
                $options[ MM_WPFS::OPTION_EMAIL_NOTIFICATION_BCC_ADDRESSES ] = json_encode( array() );
            }
			if ( ! array_key_exists( 'admin_payment_receipt', $options ) ) {
				$options['admin_payment_receipt'] = 'no';
			} else {
				if ( $options['admin_payment_receipt'] == '0' ) {
					$options['admin_payment_receipt'] = 'no';
				}
				if ( $options['admin_payment_receipt'] == '1' ) {
					$options['admin_payment_receipt'] = 'website_admin';
				}
			}
			if ( ! array_key_exists( 'lock_email_field_for_logged_in_users', $options ) ) {
				$options['lock_email_field_for_logged_in_users'] = '1';
			}
			if ( ! array_key_exists( 'webhook_token', $options ) ) {
				$options['webhook_token'] = $this->createWebhookToken();
			}
			if ( ! array_key_exists( 'custom_input_field_max_count', $options ) ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			} elseif ( $options['custom_input_field_max_count'] != MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SITE_KEY';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SECRET_KEY';
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS, $options ) ) {
                $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] = '1';
            }
            if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_UPDOWNGRADE_SUBSCRIPTIONS, $options ) ) {
                $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_LET_SUBSCRIBERS_UPDOWNGRADE_SUBSCRIPTIONS ] = '0';
            }
            if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS, $options ) ) {
                $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS ] = MM_WPFS::CANCEL_SUBSCRIPTION_IMMEDIATELY;
            }
			if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_INVOICES_SECTION, $options ) ) {
				$options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_INVOICES_SECTION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_ALL_INVOICES, $options ) ) {
				$options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_SHOW_ALL_INVOICES ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL, $options ) ) {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT, $options ) ) {
				$options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] = '0';
			}
            if ( ! array_key_exists( MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_LIVE, $options ) ) {
                $options[ MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_LIVE ] = null;
            }
            if ( ! array_key_exists( MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_TEST, $options ) ) {
                $options[ MM_WPFS::OPTION_LAST_WEBHOOK_EVENT_TEST ] = null;
            }

			MM_WPFS_LicenseManager::getInstance()->setLicenseOptionDefaultsIfEmpty( $options );

			update_option( 'fullstripe_options', $options );
		}
	}

    /**
     * @param $liveMode
     *
     * @return string
     */
	public static function getStripeAuthenticationTokenByMode( $liveMode ) {
        $options = get_option( 'fullstripe_options' );

        $token = $liveMode ? $options['secretKey_live'] : $options['secretKey_test'];

        return $token;
    }

    /**
     * @return string
     */
    public static function getStripeAuthenticationToken() {
	    return self::getStripeAuthenticationTokenByMode( self::isStripeAPIInLiveMode() );
    }

    /**
     * @return bool
     */
    public static function isStripeAPIInLiveMode() {
        $options = get_option( 'fullstripe_options' );

        return $options['apiMode'] === MM_WPFS::STRIPE_API_MODE_LIVE;
    }

    /**
     * @return mixed
     */
    public static function getStripeTestAuthenticationToken() {
        $options = get_option( 'fullstripe_options' );

        return $options['secretKey_test'];
    }

    /**
     * @return mixed
     */
    public static function getStripeLiveAuthenticationToken() {
        $options = get_option( 'fullstripe_options' );

        return $options['secretKey_live'];
    }

	function hooks() {

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

		add_action( 'fullstripe_update_email_template_defaults', array( $this, 'updateEmailTemplateDefaults' ), 10, 0 );
		add_action( 'wp_head', array( $this, 'fullstripe_wp_head' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'fullstripe_enqueue_scripts_and_styles' ) );

        add_shortcode( self::SHORTCODE_FULLSTRIPE_FORM, array( $this, 'fullstripe_form' ) );
        add_shortcode( self::SHORTCODE_FULLSTRIPE_THANKYOU, array( $this, 'fullstripe_thankyou' ) );
        add_shortcode( self::SHORTCODE_FULLSTRIPE_THANKYOU_SUCCESS, array( $this, 'fullstripe_thankyou_success' ) );
        add_shortcode( self::SHORTCODE_FULLSTRIPE_THANKYOU_DEFAULT, array( $this, 'fullstripe_thankyou_default' ) );

        do_action( 'fullstripe_main_hooks_action' );
	}

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new MM_WPFS();
		}

		return self::$instance;
	}

	public function updateEmailTemplateDefaults() {
        MM_WPFS_Mailer::updateDefaultEmailTemplatesInOptions();
    }

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function esc_html_id_attr( $value ) {
		return preg_replace( '/[^a-z0-9\-_:\.]|^[^a-z]+/i', '', $value );
	}

	public static function get_credit_card_image_for( $currency ) {
		$creditCardImage = 'creditcards.png';

		if ( $currency === MM_WPFS::CURRENCY_USD ) {
			$creditCardImage = 'creditcards-us.png';
		}

		return $creditCardImage;
	}

    public static function getCustomFieldMaxCount() {
		$options = get_option( 'fullstripe_options' );
		if ( is_array( $options ) && array_key_exists( 'custom_input_field_max_count', $options ) ) {
			$customInputFieldMaxCount = $options['custom_input_field_max_count'];
			if ( is_numeric( $customInputFieldMaxCount ) ) {
				return $customInputFieldMaxCount;
			}
		}

		return self::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
	}

	public function plugin_action_links( $links, $file ) {
		static $currentPlugin;

		if ( ! $currentPlugin ) {
			$currentPlugin = plugin_basename( 'wp-full-stripe/wp-full-stripe.php' );
		}

		if ( $file == $currentPlugin ) {
			$settingsLabel =
				/* translators: Link label displayed on the Plugins page in WP admin */
				__( 'Settings', 'wp-full-stripe-admin' );
			$settingsLink  = '<a href="' . menu_page_url( MM_WPFS_Admin_Menu::SLUG_SETTINGS, false ) . '">' . esc_html( $settingsLabel ) . '</a>';
			array_unshift( $links, $settingsLink );
		}

		return $links;
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_payment_form( $attributes ) {

		$curentAttributes = array(
			'type' => self::FORM_TYPE_INLINE_PAYMENT
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$curentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $curentAttributes );

		return apply_filters( 'fullstripe_payment_form_output', $content );
	}

    /**
     * @param $type string
     * @return string
     */
	protected function normalizeShortCodeFormType( $type ) {
	    $result = $type;

	    switch ( $type ) {
            case MM_WPFS::FORM_TYPE_PAYMENT:
                $result = MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
                break;

            case MM_WPFS::FORM_TYPE_SUBSCRIPTION:
                $result = MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
                break;

            case MM_WPFS::FORM_TYPE_POPUP_PAYMENT:
                $result = MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT;
                break;

            case MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION:
                $result = MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION;
                break;

            case MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD:
                $result = MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD;
                break;

            case MM_WPFS::FORM_TYPE_POPUP_DONATION:
                $result = MM_WPFS::FORM_TYPE_CHECKOUT_DONATION;
                break;
        }

	    return $result;
    }

	/**
	 * Generalized function to handle the new shortcode format
	 *
	 * @param $atts
	 *
	 * @return mixed|void
	 */
	function fullstripe_form( $atts ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'fullstripe_form(): CALLED' );
		}

		$form_type = self::FORM_TYPE_INLINE_PAYMENT;
		$form_name = 'default';
		if ( array_key_exists( 'type', $atts ) ) {
			$form_type = $atts['type'];
		}
		if ( array_key_exists( 'name', $atts ) ) {
			$form_name = $atts['name'];
		}
        $form_type = $this->normalizeShortCodeFormType( $form_type );
		$form = $this->getFormByTypeAndName( $form_type, $form_name );

		ob_start();
		if ( ! is_null( $form ) ) {
			$options           = get_option( 'fullstripe_options' );
			$lock_email        = $options['lock_email_field_for_logged_in_users'];
			$email_address     = '';
			$is_user_logged_in = is_user_logged_in();
			if ( '1' == $lock_email && $is_user_logged_in ) {
				$current_user  = wp_get_current_user();
				$email_address = $current_user->user_email;
			}

			$view = null;
			if ( self::FORM_TYPE_INLINE_PAYMENT === $form_type ) {
				$view = new MM_WPFS_InlinePaymentFormView( $form );
				$view->setCurrentEmailAddress( $email_address );
			} elseif ( self::FORM_TYPE_INLINE_SUBSCRIPTION === $form_type ) {
				$stripeRecurringPrices = $this->stripe->getRecurringPrices();
				$view         = new MM_WPFS_InlineSubscriptionFormView( $form, $stripeRecurringPrices );
				$view->setCurrentEmailAddress( $email_address );
			} elseif ( self::FORM_TYPE_INLINE_SAVE_CARD === $form_type ) {
				$view = new MM_WPFS_InlineSaveCardFormView( $form );
				$view->setCurrentEmailAddress( $email_address );
            } elseif ( self::FORM_TYPE_INLINE_DONATION === $form_type ) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $view = new MM_WPFS_InlineDonationFormView( $form );
			} elseif ( self::FORM_TYPE_CHECKOUT_PAYMENT === $form_type ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$view = new MM_WPFS_CheckoutPaymentFormView( $form );
			} elseif ( self::FORM_TYPE_CHECKOUT_SUBSCRIPTION === $form_type ) {
                $stripeRecurringPrices = $this->stripe->getRecurringPrices();
				/** @noinspection PhpUnusedLocalVariableInspection */
				$view = new MM_WPFS_CheckoutSubscriptionFormView( $form, $stripeRecurringPrices );
			} elseif ( self::FORM_TYPE_CHECKOUT_SAVE_CARD === $form_type ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$view = new MM_WPFS_CheckoutSaveCardFormView( $form );
            } elseif ( self::FORM_TYPE_CHECKOUT_DONATION === $form_type ) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $view = new MM_WPFS_CheckoutDonationFormView( $form );
            }

			$selectedPlanId = null;
			if ( $view instanceof MM_WPFS_SubscriptionFormView ) {
				$isSimpleButtonSubscription = $view instanceof MM_WPFS_CheckoutSubscriptionFormView && 1 == $form->simpleButtonLayout;
				if ( ! $isSimpleButtonSubscription ) {
					$selectedPlanParamValue = isset( $_GET[ self::HTTP_PARAM_NAME_PLAN ] ) ? sanitize_text_field( $_GET[ self::HTTP_PARAM_NAME_PLAN ] ) : null;
					// $selectedPlanId is used in the view included below
					$selectedPlanId = apply_filters( self::FILTER_NAME_SELECT_SUBSCRIPTION_PLAN, null, $view->getFormName(), $view->getSelectedStripePlanIds(), $selectedPlanParamValue );
				}
			}

			if ( $view instanceof MM_WPFS_PaymentFormView &&
			     MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $form->customAmount
			) {
				$customAmountParamValue = isset( $_GET[ self::HTTP_PARAM_NAME_AMOUNT ] ) ? sanitize_text_field( $_GET[ self::HTTP_PARAM_NAME_AMOUNT ] ) : null;

				if ( ! empty( $customAmountParamValue ) ) {
					$customAmount = apply_filters( self::FILTER_NAME_SET_CUSTOM_AMOUNT, 0, $view->getFormName(), $customAmountParamValue );

					if ( $customAmount !== 0 ) {
						$customAmountAttributes                                          = $view->customAmount()->attributes( false );
						$customAmountAttributes[ MM_WPFS_FormViewConstants::ATTR_VALUE ] = MM_WPFS_Currencies::formatByForm( $form, $form->currency, $customAmount, false, false );
						$view->customAmount()->setAttributes( $customAmountAttributes );
					}
				}
			}

			if ( false === $this->loadScriptsAndStylesWithActionHook ) {
				$renderedForms = self::getRenderedForms()->renderLater( $form_type );
				if ( $renderedForms->getTotal() == 1 ) {
					$this->fullstripe_load_css();
					$this->fullstripe_load_js();
					$this->fullstripe_set_common_js_variables();
				}
			}

			$popupFormSubmit = null;
			if ( isset( $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ] ) ) {
				$submitHash = $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ];
				/** @noinspection PhpUnusedLocalVariableInspection */
				$popupFormSubmit = $this->checkoutSubmissionService->retrieveSubmitEntry( $submitHash );
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'fullstripe_form(): popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
				}

				if ( isset( $popupFormSubmit ) && $popupFormSubmit->formHash === $view->getFormHash() ) {
					if (
						MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_CREATED === $popupFormSubmit->status
						|| MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_PENDING === $popupFormSubmit->status
						|| MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_COMPLETE === $popupFormSubmit->status
					) {
						// tnagy we do not render messages for created/complete submissions
						$popupFormSubmit = null;
					} else {
						// tnagy we set the form submission to complete, the last message will be shown when the shortcode renders
						$this->checkoutSubmissionService->updateSubmitEntryWithComplete( $popupFormSubmit );
					}
				}
			}

			/** @noinspection PhpIncludeInspection */
			include MM_WPFS_Assets::templates( 'forms/wpfs-form.php' );
		} else {
			include MM_WPFS_Assets::templates( 'forms/wpfs-form-not-found.php' );
		}

		$content = ob_get_clean();

		return apply_filters( 'fullstripe_form_output', $content );
	}

	/**
	 * Returns a form from database identified by type and name.
	 *
	 * @param $formType
	 * @param $formName
	 *
	 * @return mixed|null
	 */
	function getFormByTypeAndName( $formType, $formName ) {
		$form = null;

		if ( self::FORM_TYPE_INLINE_PAYMENT === $formType ) {
			$form = $this->database->getInlinePaymentFormByName( $formName );
		} elseif ( self::FORM_TYPE_INLINE_SUBSCRIPTION === $formType ) {
			$form = $this->database->getInlineSubscriptionFormByName( $formName );
		} elseif ( self::FORM_TYPE_INLINE_SAVE_CARD === $formType ) {
			$form = $this->database->getInlinePaymentFormByName( $formName );
        } elseif ( self::FORM_TYPE_INLINE_DONATION === $formType ) {
            $form = $this->database->getInlineDonationFormByName( $formName );
		} elseif ( self::FORM_TYPE_CHECKOUT_PAYMENT === $formType ) {
			$form = $this->database->getCheckoutPaymentFormByName( $formName );
		} elseif ( self::FORM_TYPE_CHECKOUT_SUBSCRIPTION === $formType ) {
			$form = $this->database->getCheckoutSubscriptionFormByName( $formName );
		} elseif ( self::FORM_TYPE_CHECKOUT_SAVE_CARD === $formType ) {
			$form = $this->database->getCheckoutPaymentFormByName( $formName );
        } elseif ( self::FORM_TYPE_CHECKOUT_DONATION === $formType ) {
            $form = $this->database->getCheckoutDonationFormByName( $formName );
        }

		return $form;
	}

    /**
	 * @return WPFS_RenderedFormData
	 */
	public static function getRenderedForms() {
		if ( ! array_key_exists( self::REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS, $_REQUEST ) ) {
			$_REQUEST[ self::REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS ] = new WPFS_RenderedFormData();
		}

		return $_REQUEST[ self::REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS ];
	}

	/**
	 * Register and enqueue WPFS styles
	 */
	public function fullstripe_load_css() {
		if ( $this->includeDefaultStyles ) {

			wp_register_style( self::HANDLE_STYLE_WPFS_VARIABLES, MM_WPFS_Assets::css( 'wpfs-variables.css' ), null, MM_WPFS::VERSION );
			wp_register_style( self::HANDLE_STYLE_WPFS_FORMS, MM_WPFS_Assets::css( 'wpfs-forms.css' ), array( self::HANDLE_STYLE_WPFS_VARIABLES ), MM_WPFS::VERSION );

			wp_enqueue_style( self::HANDLE_STYLE_WPFS_FORMS );
		}

		do_action( 'fullstripe_load_css_action' );
	}

	/**
	 * Register and enqueue WPFS scripts
	 */
	public function fullstripe_load_js() {
		$source = add_query_arg(
			array(
				'render' => 'explicit'
			),
			self::SOURCE_GOOGLE_RECAPTCHA_V2_API_JS
		);
		wp_register_script( self::HANDLE_GOOGLE_RECAPTCHA_V_2, $source, null, MM_WPFS::VERSION, true /* in footer */ );
		wp_register_script( self::HANDLE_SPRINTF_JS, MM_WPFS_Assets::scripts( 'sprintf.min.js' ), null, MM_WPFS::VERSION );
		wp_register_script( self::HANDLE_STRIPE_JS_V_3, 'https://js.stripe.com/v3/', array( 'jquery' ) );
		wp_register_script( self::HANDLE_WP_FULL_STRIPE_UTILS_JS, MM_WPFS_Assets::scripts( 'wpfs-utils.js' ), null, MM_WPFS::VERSION );

		wp_enqueue_script( self::HANDLE_SPRINTF_JS );
		wp_enqueue_script( self::HANDLE_STRIPE_JS_V_3 );
		wp_enqueue_script( self::HANDLE_WP_FULL_STRIPE_UTILS_JS );
		if (
			MM_WPFS_Utils::getSecureInlineFormsWithGoogleRecaptcha()
			|| MM_WPFS_Utils::getSecureCheckoutFormsWithGoogleRecaptcha()
		) {
			$dependencies = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-selectmenu',
				'jquery-ui-autocomplete',
				'jquery-ui-tooltip',
				'jquery-ui-spinner',
				self::HANDLE_SPRINTF_JS,
				self::HANDLE_WP_FULL_STRIPE_UTILS_JS,
				self::HANDLE_STRIPE_JS_V_3,
				self::HANDLE_GOOGLE_RECAPTCHA_V_2
			);
		} else {
			$dependencies = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-selectmenu',
				'jquery-ui-autocomplete',
				'jquery-ui-tooltip',
				'jquery-ui-spinner',
				self::HANDLE_SPRINTF_JS,
				self::HANDLE_WP_FULL_STRIPE_UTILS_JS,
				self::HANDLE_STRIPE_JS_V_3
			);
		}
		wp_enqueue_script( self::HANDLE_WP_FULL_STRIPE_JS, MM_WPFS_Assets::scripts( 'wpfs.js' ), $dependencies, MM_WPFS::VERSION );

		do_action( 'fullstripe_load_js_action' );
	}

	function fullstripe_set_common_js_variables() {
        $options = get_option( 'fullstripe_options' );

        $wpfsFormOptions = array(
            self::JS_VARIABLE_AJAX_URL                      => admin_url( 'admin-ajax.php' ),
            self::JS_VARIABLE_GOOGLE_RECAPTCHA_SITE_KEY     => MM_WPFS_Utils::getGoogleRecaptchaSiteKey(),
            self::JS_VARIABLE_FORM_FIELDS                   => array(
                'inlinePayment'         => MM_WPFS_InlinePaymentFormView::getFields(),
                'inlineSaveCard'        => MM_WPFS_InlineSaveCardFormView::getFields(),
                'inlineSubscription'    => MM_WPFS_InlineSubscriptionFormView::getFields(),
                'inlineDonation'        => MM_WPFS_InlineDonationFormView::getFields(),
                'checkoutPayment'       => MM_WPFS_CheckoutPaymentFormView::getFields(),
                'checkoutSaveCard'      => MM_WPFS_CheckoutSaveCardFormView::getFields(),
                'checkoutSubscription'  => MM_WPFS_CheckoutSubscriptionFormView::getFields(),
                'checkoutDonation'      => MM_WPFS_CheckoutDonationFormView::getFields(),
            ),
            self::JS_VARIABLE_L10N                          => array(
                'validation_errors'                      => array(
                    'internal_error'                         =>
                    /* translators: Banner message of internal error when no error message is returned by the application */
                        __( 'An internal error occurred.', 'wp-full-stripe' ),
                    'internal_error_title'                   =>
                    /* translators: Banner title of internal error */
                        __( 'Internal Error', 'wp-full-stripe' ),
                    'mandatory_field_is_empty'               =>
                    /* translators: Error message for required fields when empty.
                     * p1: custom input field label
                     */
                        __( "Please enter a value for '%s'", 'wp-full-stripe' ),
                    'custom_payment_amount_value_is_invalid' =>
                    /* translators: Field validation error message when payment amount is empty or invalid */
                        __( 'Payment amount is invalid', 'wp-full-stripe' ),
                    'invalid_payment_amount'                 =>
                    /* translators: Banner message when the payment amount cannot be determined (the form has been tampered with) */
                        __( 'Cannot determine payment amount', 'wp-full-stripe' ),
                    'invalid_payment_amount_title'           =>
                    /* translators: Banner title when the payment amount cannot be determined (the form has been tampered with) */
                        __( 'Invalid payment amount', 'wp-full-stripe' )
                ),
                'stripe_errors'                          => array(
                    MM_WPFS_Stripe::INVALID_NUMBER_ERROR               => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INVALID_NUMBER_ERROR ),
                    MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_MONTH     => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_MONTH ),
                    MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_YEAR      => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_YEAR ),
                    MM_WPFS_Stripe::INVALID_EXPIRY_MONTH_ERROR         => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INVALID_EXPIRY_MONTH_ERROR ),
                    MM_WPFS_Stripe::INVALID_EXPIRY_YEAR_ERROR          => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INVALID_EXPIRY_YEAR_ERROR ),
                    MM_WPFS_Stripe::INVALID_CVC_ERROR                  => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INVALID_CVC_ERROR ),
                    MM_WPFS_Stripe::INCORRECT_NUMBER_ERROR             => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INCORRECT_NUMBER_ERROR ),
                    MM_WPFS_Stripe::EXPIRED_CARD_ERROR                 => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::EXPIRED_CARD_ERROR ),
                    MM_WPFS_Stripe::INCORRECT_CVC_ERROR                => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INCORRECT_CVC_ERROR ),
                    MM_WPFS_Stripe::INCORRECT_ZIP_ERROR                => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::INCORRECT_ZIP_ERROR ),
                    MM_WPFS_Stripe::CARD_DECLINED_ERROR                => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::CARD_DECLINED_ERROR ),
                    MM_WPFS_Stripe::MISSING_ERROR                      => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::MISSING_ERROR ),
                    MM_WPFS_Stripe::PROCESSING_ERROR                   => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::PROCESSING_ERROR ),
                    MM_WPFS_Stripe::MISSING_PAYMENT_INFORMATION        => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::MISSING_PAYMENT_INFORMATION ),
                    MM_WPFS_Stripe::COULD_NOT_FIND_PAYMENT_INFORMATION => $this->stripe->resolveErrorMessageByCode( MM_WPFS_Stripe::COULD_NOT_FIND_PAYMENT_INFORMATION )
                ),
                'subscription_charge_interval_templates' => array(
                    'daily'            => __( 'Subscription will be charged every day.', 'wp-full-stripe' ),
                    'weekly'           => __( 'Subscription will be charged every week.', 'wp-full-stripe' ),
                    'monthly'          => __( 'Subscription will be charged every month.', 'wp-full-stripe' ),
                    'yearly'           => __( 'Subscription will be charged every year.', 'wp-full-stripe' ),
                    'y_days'           => __( 'Subscription will be charged every %d days.', 'wp-full-stripe' ),
                    'y_weeks'          => __( 'Subscription will be charged every %d weeks.', 'wp-full-stripe' ),
                    'y_months'         => __( 'Subscription will be charged every %d months.', 'wp-full-stripe' ),
                    'y_years'          => __( 'Subscription will be charged every %d years.', 'wp-full-stripe' ),
                    'x_times_daily'    => __( 'Subscription will be charged every day, for %d occasions.', 'wp-full-stripe' ),
                    'x_times_weekly'   => __( 'Subscription will be charged every week, for %d occasions.', 'wp-full-stripe' ),
                    'x_times_monthly'  => __( 'Subscription will be charged every month, for %d occasions.', 'wp-full-stripe' ),
                    'x_times_yearly'   => __( 'Subscription will be charged every year, for %d occasions.', 'wp-full-stripe' ),
                    'x_times_y_days'   => __( 'Subscription will be charged every %1$d days, for %2$d occasions.', 'wp-full-stripe' ),
                    'x_times_y_weeks'  => __( 'Subscription will be charged every %1$d weeks, for %2$d occasions.', 'wp-full-stripe' ),
                    'x_times_y_months' => __( 'Subscription will be charged every %1$d months, for %2$d occasions.', 'wp-full-stripe' ),
                    'x_times_y_years'  => __( 'Subscription will be charged every %1$d years, for %2$d occasions.', 'wp-full-stripe' ),
                ),
	            'products' => array(
	            	'default_product_name' => __( 'My product', 'wp-full-stripe'),
                    'other_amount_label'   => __('Other amount', 'wp-full-stripe'),
	            )
            )
        );
        if ( $options['apiMode'] === 'test' ) {
            $wpfsFormOptions[ self::JS_VARIABLE_STRIPE_KEY ] = $options['publishKey_test'];
        } else {
            $wpfsFormOptions[ self::JS_VARIABLE_STRIPE_KEY ] = $options['publishKey_live'];
        }

        wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_WPFS_FORM_OPTIONS, $wpfsFormOptions );
	}

	/**
	 * @param $subscriptionId
	 * @param $planId
	 * @param $chargeMaxCount
	 *
	 * @return bool|int
	 * @throws Exception
	 */
	public function updateSubscriptionPlanAndCounters( $subscriptionId, $planId, $chargeMaxCount ) {
		return $this->database->updateSubscriptionPlanAndCounters( $subscriptionId, $planId, $chargeMaxCount );
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_subscription_form( $attributes ) {

		$currentAttributes = array(
			'type' => self::FORM_TYPE_INLINE_SUBSCRIPTION
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$currentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $currentAttributes );

		return apply_filters( 'fullstripe_subscription_form_output', $content );
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_checkout_form( $attributes ) {

		$currentAttributes = array(
			'type' => self::FORM_TYPE_CHECKOUT_PAYMENT
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$currentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $currentAttributes );

		return apply_filters( 'fullstripe_checkout_form_output', $content );
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_checkout_subscription_form( $attributes ) {

		$currentAttributes = array(
			'type' => self::FORM_TYPE_CHECKOUT_SUBSCRIPTION
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$currentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $currentAttributes );

		return apply_filters( 'fullstripe_checkout_subscription_form_output', $content );
	}

	function fullstripe_thankyou( $attributes, $content = null ) {
		$transactionDataKey = isset( $_REQUEST[ MM_WPFS_TransactionDataService::REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY ] ) ? $_REQUEST[ MM_WPFS_TransactionDataService::REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY ] : null;
		$transactionData    = $this->transactionDataService->retrieve( $transactionDataKey );

		if ( $transactionData !== false ) {
			$_REQUEST['transaction_data'] = $transactionData;
		}

		return do_shortcode( $content );
	}

	function fullstripe_thankyou_default( $attributes, $content = null ) {
		if ( isset( $_REQUEST['transaction_data'] ) ) {
			return '';
		} else {
			return do_shortcode( $content );
		}
	}

	function fullstripe_thankyou_success( $attributes, $content = null ) {
		if ( isset( $_REQUEST['transaction_data'] ) ) {
			$transactionData = $_REQUEST['transaction_data'];
		} else {
			$transactionData = null;
		}

		if ( ! is_null( $transactionData ) && $transactionData instanceof MM_WPFS_FormTransactionData ) {

		    /* @var $procesor MM_WPFS_ThankYouPostProcessor */
		    $processor = MM_WPFS_ThankYouPostProcessorFactory::create( $this->database, $transactionData );
            return do_shortcode( $processor->process( $content ));

		} else {
			return '';
		}
	}

	function fullstripe_wp_head() {
		//output the custom css
		$options = get_option( 'fullstripe_options' );
		echo '<style type="text/css" media="screen">' . $options[MM_WPFS::OPTION_FORM_CUSTOM_CSS] . '</style>';
	}

	/**
	 * Register and enqueue styles and scripts to load for this addon
	 */
	public function fullstripe_enqueue_scripts_and_styles() {
		if ( $this->loadScriptsAndStylesWithActionHook ) {
			global $wp;
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_enqueue_scripts_and_styles(): CALLED, wp=' . print_r( $wp, true ) );
			}
			if ( ! is_null( $wp ) && isset( $wp->request ) ) {
				$pageByPath = get_page_by_path( $wp->request );
				if ( ! is_null( $pageByPath ) && isset( $pageByPath->post_content ) ) {
					if (
						has_shortcode( $pageByPath->post_content, self::SHORTCODE_FULLSTRIPE_FORM )
					) {
						$this->fullstripe_load_css();
						$this->fullstripe_load_js();
						$this->fullstripe_set_common_js_variables();
					}
				}
			}
		}
	}

    /**
	 * @return MM_WPFS_Admin_Menu
	 */
	public function getAdminMenu() {
		return $this->adminMenu;
	}

    /**
	 * @return MM_WPFS_Admin
	 */
	public function getAdmin() {
		return $this->admin;
	}

	public function get_form_validation_data() {
		return new WPFS_FormValidationData();
	}

    /**
     * todo: This function is used by WPFSM.
     * Let's move it to a dedicated API endpoint
     *
     * @param $plan_id
     *
     * @return mixed|void|null
     */
	public function get_plan( $plan_id ) {
		return $this->stripe != null ? apply_filters( 'fullstripe_subscription_plan_filter', $this->stripe->retrievePlan( $plan_id ) ) : null;
	}

}

class WPFS_FormValidationData {

	const NAME_LENGTH = 100;
	const FORM_TITLE_LENGTH = 100;
	const BUTTON_TITLE_LENGTH = 100;
	const REDIRECT_URL_LENGTH = 1024;
	const COMPANY_NAME_LENGTH = 100;
	const PRODUCT_DESCRIPTION_LENGTH = 100;
	const OPEN_BUTTON_TITLE_LENGTH = 100;
	const PAYMENT_AMOUNT_LENGTH = 8;
	const PAYMENT_AMOUNT_DESCRIPTION_LENGTH = 128;
	const IMAGE_LENGTH = 500;

}

class WPFS_PlanValidationData {
	const STATEMENT_DESCRIPTOR_LENGTH = 22;
}

class WPFS_RenderedFormData {

	private $inlinePayments = 0;
	private $inlineSubscriptions = 0;
	private $checkoutPayments = 0;
	private $checkoutSubscriptions = 0;
	private $inlineDonations = 0;
    private $checkoutDonations = 0;

	public function renderLater($type ) {
		if ( MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION === $type ) {
			$this->checkoutSubscriptions += 1;
        } elseif ( MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION === $type ) {
            $this->inlineSubscriptions += 1;
        } elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT === $type ) {
            $this->checkoutPayments += 1;
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_PAYMENT === $type ) {
			$this->inlinePayments += 1;
        } elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD === $type ) {
            $this->checkoutPayments += 1;
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD === $type ) {
			$this->inlinePayments += 1;
		} elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_DONATION === $type ) {
            $this->checkoutDonations += 1;
        } elseif ( MM_WPFS::FORM_TYPE_INLINE_DONATION === $type ) {
            $this->inlineDonations += 1;
        }

		return $this;
	}

	/**
	 * @return int
	 */
	public function getInlinePayments() {
		return $this->inlinePayments;
	}

	/**
	 * @return int
	 */
	public function getInlineSubscriptions() {
		return $this->inlineSubscriptions;
	}

	/**
	 * @return int
	 */
	public function getCheckoutPayments() {
		return $this->checkoutPayments;
	}

	/**
	 * @return int
	 */
	public function getCheckoutSubscriptions() {
		return $this->checkoutSubscriptions;
	}

    /**
     * @return int
     */
    public function getInlineDonations() {
        return $this->inlineDonations;
    }

    /**
     * @return int
     */
    public function getCheckoutDonations() {
        return $this->checkoutDonations;
    }

    /**
	 * @return int
	 */
	public function getTotal() {
		return $this->inlinePayments + $this->inlineSubscriptions + $this->checkoutPayments + $this->checkoutSubscriptions + $this->inlineDonations + $this->checkoutDonations;
	}

}

class MM_WPFS_Utils {

	const ADDITIONAL_DATA_KEY_ACTION_NAME = 'action_name';
	const ADDITIONAL_DATA_KEY_CUSTOMER = 'customer';
	const ADDITIONAL_DATA_KEY_MACROS = 'macros';
	const ADDITIONAL_DATA_KEY_MACRO_VALUES = 'macroValues';
	const WPFS_LOG_MESSAGE_PREFIX = "WPFS: ";
	const STRIPE_METADATA_KEY_MAX_LENGTH = 40;
	const STRIPE_METADATA_VALUE_MAX_LENGTH = 500;
	const STRIPE_METADATA_KEY_MAX_COUNT = 20;
	const ELEMENT_PART_SEPARATOR = '--';
	const SHORTCODE_PATTERN = '[fullstripe_form name="%s" type="%s"]';

	const ESCAPE_TYPE_NONE = 'none';
	const ESCAPE_TYPE_HTML = 'esc_html';
	const ESCAPE_TYPE_ATTR = 'esc_attr';
	const WPFS_ENCRYPT_METHOD_AES_256_CBC = 'AES-256-CBC';

	public static function extractFirstTierPricingFromPlan( $plan ) {
        return $plan->tiers[0]['unit_amount'];
    }

	public static function formatPlanAmountForPlanList( $plan ) {
        $amountStr = '';

	    if ( $plan->billing_scheme == 'tiered' ) {
            $formattedAmount = MM_WPFS_Currencies::formatAndEscape( $plan->currency, MM_WPFS_Utils::extractFirstTierPricingFromPlan( $plan ) );
            $amountStr = sprintf(__( "Starting at %s", 'wp-full-stripe-admin' ), $formattedAmount );
        } else {
            $amountStr = MM_WPFS_Currencies::formatAndEscape( $plan->currency, $plan->amount );
        }

        return $amountStr;
    }

	/**
	 * @return bool
	 */
	public static function generateCSSFormID( $form_hash ) {
		return MM_WPFS_FormView::ATTR_ID_VALUE_PREFIX . $form_hash;
	}


	/**
	 * @return bool
	 */
	public static function isDemoMode() {
		return defined( 'WP_FULL_STRIPE_DEMO_MODE' );
	}


	/** todo: this can be simplified, no mapping is needed anymore, the type can be injected directly
	 * @param $form
	 *
	 * @return bool|string
	 */
	public static function createShortCodeString( $form ) {
		$formType = MM_WPFS_Utils::getFormType( $form );

		if ( MM_WPFS::FORM_TYPE_INLINE_PAYMENT === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_PAYMENT );
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD );
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION );
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_DONATION === $formType ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_DONATION );
        } elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT );
		} elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD );
		} elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION );
		} elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_DONATION === $formType ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_DONATION );
        }

		return false;
	}

    /**
     * @param $form
     *
     * @return bool|string
     */
    public static function createShortCodeByTypeAndLayout( $form ) {
        if ( MM_WPFS::FORM_TYPE_PAYMENT === $form->type &&
             MM_WPFS::FORM_LAYOUT_INLINE === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_PAYMENT );
        } elseif (  MM_WPFS::FORM_TYPE_SAVE_CARD === $form->type &&
                    MM_WPFS::FORM_LAYOUT_INLINE === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD );
        } elseif (  MM_WPFS::FORM_TYPE_SUBSCRIPTION === $form->type &&
                    MM_WPFS::FORM_LAYOUT_INLINE === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION );
        } elseif (  MM_WPFS::FORM_TYPE_DONATION === $form->type &&
                    MM_WPFS::FORM_LAYOUT_INLINE === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_DONATION );
        } elseif (  MM_WPFS::FORM_TYPE_PAYMENT === $form->type &&
                    MM_WPFS::FORM_LAYOUT_CHECKOUT === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT );
        } elseif (  MM_WPFS::FORM_TYPE_SAVE_CARD === $form->type &&
                    MM_WPFS::FORM_LAYOUT_CHECKOUT === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD );
        } elseif (  MM_WPFS::FORM_TYPE_SUBSCRIPTION === $form->type &&
                    MM_WPFS::FORM_LAYOUT_CHECKOUT === $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION );
        } elseif (  MM_WPFS::FORM_TYPE_DONATION === $form->type &&
                    MM_WPFS::FORM_LAYOUT_CHECKOUT == $form->layout ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_CHECKOUT_DONATION );
        }

        return false;
    }

    /**
	 * @param $form
	 *
	 * @return null|string
	 */
	public static function getFormType( $form ) {
		if ( is_null( $form ) ) {
			return null;
		}
		if ( isset( $form->paymentFormID ) ) {
			if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $form->customAmount ) {
				return MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD;
			} else {
				return MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
			}
		}
		if ( isset( $form->subscriptionFormID ) ) {
			return MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
		}
		if ( isset( $form->checkoutFormID ) ) {
			if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $form->customAmount ) {
				return MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD;
			} else {
				return MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT;
			}
		}
		if ( isset( $form->checkoutSubscriptionFormID ) ) {
			return MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION;
		}

        if ( isset( $form->donationFormID ) ) {
            return MM_WPFS::FORM_TYPE_INLINE_DONATION;
        }
        if ( isset( $form->checkoutDonationFormID ) ) {
            return MM_WPFS::FORM_TYPE_CHECKOUT_DONATION;
        }

        return null;
	}

	public static function isInlinePaymentFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
    }

    public static function isCheckoutPaymentFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_POPUP_PAYMENT || $type === MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT;
    }

    public static function isInlineSaveCardFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD;
    }

    public static function isCheckoutSaveCardFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD || $type === MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD;
    }

    public static function isInlineSubscriptionFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
    }

    public static function isCheckoutSubscriptionFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION || $type === MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION;
    }

    public static function isInlineDonationFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_INLINE_DONATION;
    }

    public static function isCheckoutDonationFormType( $type ) {
        return $type === MM_WPFS::FORM_TYPE_POPUP_DONATION || $type === MM_WPFS::FORM_TYPE_CHECKOUT_DONATION;
    }

    /**
     * @param $slug
     * @param $id
     * @param $type
     *
     * @return string
     */
    public static function createFormEditUrl( $slug, $id, $type ) {
        $editUrl = add_query_arg(
            array(
                'page' => $slug,
                'form' => $id,
                'type' => $type
            ),
            admin_url( "admin.php" )
        );

        return $editUrl;
    }

    /**
     * @param $id string
     * @param $type string
     * @param $layout string
     *
     * @return string
     */
    public static function getFormEditUrl( $id, $type, $layout ) {
        $editUrl = "#";

        if ( $type   === MM_WPFS::FORM_TYPE_PAYMENT &&
            $layout === MM_WPFS::FORM_LAYOUT_INLINE ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_INLINE_PAYMENT );
        } elseif ( $type   === MM_WPFS::FORM_TYPE_PAYMENT &&
            $layout === MM_WPFS::FORM_LAYOUT_CHECKOUT ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT );
        } elseif (  $type   === MM_WPFS::FORM_TYPE_SUBSCRIPTION &&
            $layout === MM_WPFS::FORM_LAYOUT_INLINE ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION );
        } elseif (  $type   === MM_WPFS::FORM_TYPE_SUBSCRIPTION &&
            $layout === MM_WPFS::FORM_LAYOUT_CHECKOUT ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION );
        } elseif (  $type   === MM_WPFS::FORM_TYPE_DONATION &&
            $layout === MM_WPFS::FORM_LAYOUT_INLINE ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_INLINE_DONATION );
        } elseif (  $type   === MM_WPFS::FORM_TYPE_DONATION &&
            $layout === MM_WPFS::FORM_LAYOUT_CHECKOUT ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_CHECKOUT_DONATION );
        } elseif (  $type   === MM_WPFS::FORM_TYPE_SAVE_CARD &&
            $layout === MM_WPFS::FORM_LAYOUT_INLINE ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD );
        } elseif (  $type   === MM_WPFS::FORM_TYPE_SAVE_CARD &&
            $layout === MM_WPFS::FORM_LAYOUT_CHECKOUT ) {
            $editUrl = self::createFormEditUrl( MM_WPFS_Admin_Menu::SLUG_EDIT_FORM, $id, MM_WPFS::FORM_TYPE_CHECKOUT_SAVE_CARD );
        }

        return $editUrl;
    }


    public static function generateFormElementId($element_id, $form_hash, $index = null ) {
		if ( is_null( $element_id ) ) {
			return null;
		}

		$generated_id = $element_id . MM_WPFS_Utils::ELEMENT_PART_SEPARATOR . $form_hash;
		if ( ! is_null( $index ) ) {
			$generated_id .= MM_WPFS_Utils::ELEMENT_PART_SEPARATOR . $index;
		}

		return esc_attr( $generated_id );
	}

	public static function generateFormHash($form_type, $form_id, $form_name ) {
		$data = $form_type . '|' . $form_id . '|' . $form_name;

		return substr( base64_encode( hash( 'sha256', $data ) ), 0, 7 );
	}

	public static function sanitize_text( $value ) {
		return self::stripslashes_deep( sanitize_text_field( $value ) );
	}

	public static function stripslashes_deep( $value ) {
		$value = is_array( $value ) ?
			array_map( 'MM_WPFS_Utils::stripslashes_deep', $value ) :
			stripslashes( $value );

		return $value;
	}

	public static function addHttpPrefix($url ) {
		if ( ! isset( $url ) ) {
			return null;
		}
		if ( substr( $url, 0, 7 ) != 'http://' && substr( $url, 0, 8 ) != 'https://' ) {
			return 'http://' . $url;
		}

		return sanitize_text_field( $url );
	}

	/**
	 * @param $googleReCAPTCHAResponse
	 *
	 * @return array|bool|mixed|object|WP_Error
	 */
	public static function verifyReCAPTCHA( $googleReCAPTCHAResponse ) {
		$googleReCAPTCHASecretKey = MM_WPFS_Utils::get_google_recaptcha_secret_key();

		if ( ! is_null( $googleReCAPTCHASecretKey ) && ! is_null( $googleReCAPTCHAResponse ) ) {
			$inputArray = array(
				'secret'   => $googleReCAPTCHASecretKey,
				'response' => $googleReCAPTCHAResponse,
				'remoteip' => $_SERVER['REMOTE_ADDR']
			);
			$request    = wp_remote_post(
				MM_WPFS::URL_RECAPTCHA_API_SITEVERIFY,
				array(
					'timeout'   => 10,
					'sslverify' => true,
					'body'      => $inputArray
				)
			);
			if ( ! is_wp_error( $request ) ) {
				$request = json_decode( wp_remote_retrieve_body( $request ) );

				return $request;
			} else {
				return false;
			}
		}

		return false;
	}

	public static function get_google_recaptcha_secret_key() {
		$googleReCAPTCHASecretKey = null;
		$options                  = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
			$googleReCAPTCHASecretKey = $options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ];
		}

		return $googleReCAPTCHASecretKey;
	}

	/**
	 * @param $line1
	 * @param $line2
	 * @param $city
	 * @param $state
	 * @param $countryName
	 * @param $countryCode
	 * @param $zip
	 *
	 * @return array
	 */
	public static function prepareAddressData($line1, $line2, $city, $state, $countryName, $countryCode, $zip ) {
		$addressData = array(
			'line1'        => is_null( $line1 ) ? '' : $line1,
			'line2'        => is_null( $line2 ) ? '' : $line2,
			'city'         => is_null( $city ) ? '' : $city,
			'state'        => is_null( $state ) ? '' : $state,
			'country'      => is_null( $countryName ) ? '' : $countryName,
			'country_code' => is_null( $countryCode ) ? '' : $countryCode,
			'zip'          => is_null( $zip ) ? '' : $zip
		);

		return $addressData;
	}

	/**
	 * This function creates a Stripe shipping address hash
	 *
	 * @param $shipping_name
	 * @param $shipping_phone
	 * @param $address_array array previously created with prepare_address_data()
	 *
	 * @return array
	 */
	public static function prepareStripeShippingHashFromArray($shipping_name, $shipping_phone, $address_array ) {
		return self::prepare_stripe_shipping_hash(
			$shipping_name,
			$shipping_phone,
			$address_array['line1'],
			$address_array['line2'],
			$address_array['city'],
			$address_array['state'],
			$address_array['country_code'],
			$address_array['zip']
		);
	}

	/**
	 * This function creates a Stripe shipping address hash
	 *
	 * @param $shipping_name string Customer name
	 * @param $shipping_phone string Customer phone (including extension)
	 * @param $line1 string Address line 1 (Street address/PO Box/Company name)
	 * @param $line2 string Address line 2 (Apartment/Suite/Unit/Building)
	 * @param $city string City/District/Suburb/Town/Village
	 * @param $state string State/County/Province/Region
	 * @param $country_code string 2-letter country code
	 * @param $postal_code string ZIP or postal code
	 *
	 * @return array
	 */
	public static function prepare_stripe_shipping_hash( $shipping_name, $shipping_phone, $line1, $line2, $city, $state, $country_code, $postal_code ) {
		$shipping_hash = array();

		//-- The 'name' property is required. It must contain a non-empty value or be null
		$shipping_hash['name'] = ! empty( $shipping_name ) ? $shipping_name : null;

		if ( ! empty( $shipping_phone ) ) {
			$shipping_hash['phone'] = $shipping_phone;
		}
		$address_hash             = self::prepare_stripe_address_hash( $line1, $line2, $city, $state, $country_code, $postal_code );
		$shipping_hash['address'] = $address_hash;

		return $shipping_hash;
	}

	/**
	 * This function creates a Stripe address hash
	 *
	 * @param $line1 string Address line 1 (Street address/PO Box/Company name)
	 * @param $line2 string Address line 2 (Apartment/Suite/Unit/Building)
	 * @param $city string City/District/Suburb/Town/Village
	 * @param $state string State/County/Province/Region
	 * @param $country_code string 2-letter country code
	 * @param $postal_code string ZIP or postal code
	 *
	 * @return array
	 */
	public static function prepare_stripe_address_hash( $line1, $line2, $city, $state, $country_code, $postal_code ) {
		$address_hash = array();

		//-- The 'line1' property is required
		if ( empty( $line1 ) ) {
			throw new InvalidArgumentException( __FUNCTION__ . '(): address line1 is required.' );
		} else {
			$address_hash['line1'] = $line1;
		}
		if ( ! empty( $line2 ) ) {
			$address_hash['line2'] = $line2;
		}
		if ( ! empty( $city ) ) {
			$address_hash['city'] = $city;
		}
		if ( ! empty( $state ) ) {
			$address_hash['state'] = $state;
		}
		if ( ! empty( $country_code ) ) {
			$address_hash['country'] = $country_code;
		}
		if ( ! empty( $postal_code ) ) {
			$address_hash['postal_code'] = $postal_code;
		}

		return $address_hash;
	}

	/**
	 * This function creates a Stripe address hash from an array created previously created with prepare_address_data()
	 *
	 * @param array $address_array
	 *
	 * @return array
	 */
	public static function prepare_stripe_address_hash_from_array( $address_array ) {
		return self::prepare_stripe_address_hash(
			$address_array['line1'],
			$address_array['line2'],
			$address_array['city'],
			$address_array['state'],
			$address_array['country_code'],
			$address_array['zip']
		);
	}

	/**
	 * @param $value
	 * @param $escapeType
	 *
	 * @return string|void
	 */
	public static function escape( $value, $escapeType ) {
		if ( is_null( $value ) ) {
			return $value;
		}
		if ( self::ESCAPE_TYPE_HTML === $escapeType ) {
			return esc_html( $value );
		} elseif ( self::ESCAPE_TYPE_ATTR === $escapeType ) {
			return esc_attr( $value );
		} else {
			return $value;
		}
	}

	/**
	 * @param $netValue
	 * @param $taxPercent
	 *
	 * @return mixed
	 */
	public static function calculateGrossFromNet( $netValue, $taxPercent ) {
		if ( ! is_numeric( $netValue ) ) {
			throw new InvalidArgumentException( sprintf( 'Parameter %s=%s is not numeric.', 'netValue', $netValue ) );
		}
		if ( ! is_numeric( $taxPercent ) ) {
			throw new InvalidArgumentException( sprintf( 'Parameter %s=%s is not numeric.', 'taxPercent', $taxPercent ) );
		}

		if ( $taxPercent == 0.0 ) {
			$grossValue = $netValue;
			$taxValue   = 0;
		} else {
			$grossValue = round( $netValue * ( 1.0 + round( $taxPercent, 4 ) / 100.0 ) );
			$taxValue   = $grossValue - $netValue;
		}

		$result = array(
			'net'        => $netValue,
			'taxPercent' => $taxPercent,
			'taxValue'   => $taxValue,
			'gross'      => $grossValue
		);

		return $result;
	}

	/**
	 * @param $customInputLabels
	 * @param $customInputValues
	 *
	 * @return array
	 */
	public static function prepare_custom_input_data( $customInputLabels, $customInputValues ) {
		$customInputs = array();
		if ( ! is_null( $customInputLabels ) && ! is_null( $customInputValues ) ) {
			foreach ( $customInputLabels as $i => $label ) {
				$customInputs[ $label ] = $customInputValues[ $i ];
			}
		}

		return $customInputs;
	}

    /**
     * @param $stripePlans
     *
     * @return array
     */
	public static function getStripePlanLookup($stripePlans ) {
	    $planIds = array();

	    foreach ( $stripePlans as $stripePlan) {
	        $planIds[ $stripePlan->id ] = $stripePlan;
        }

	    return $planIds;
    }

	/**
	 * @param $stripePlans
	 * @param $formPlans
	 *
	 * @return array
	 */
	public static function getSortedFormPlans( $stripePlans, $formPlans ) {
		$plans = array();
		$formPlanProperties = !is_null( $formPlans ) ? json_decode( $formPlans ) : array();
		$stripePlanLookup = self::getStripePlanLookup( $stripePlans );

        foreach ( $formPlanProperties as $formPlanProperty) {
            if ( array_key_exists( $formPlanProperty->stripePriceId, $stripePlanLookup ) ) {
                $plan = new \StdClass;
                $plan->properties = $formPlanProperty;
                $plan->stripePlan = $stripePlanLookup[ $formPlanProperty->stripePriceId ];

                array_push( $plans, $plan );
            }
        }

		return $plans;
	}

	/**
	 * @deprecated
	 *
	 * @param $currency
	 * @param $amount
	 *
	 * @return null|string|void
	 */
	public static function format_amount( $currency, $amount ) {
		$formattedAmount = null;
		$currencyArray   = MM_WPFS_Currencies::getCurrencyFor( $currency );
		if ( is_array( $currencyArray ) ) {
			if ( $currencyArray['zeroDecimalSupport'] == true ) {
				$pattern   = '%d';
				$theAmount = is_numeric( $amount ) ? $amount : 0;
			} else {
				$pattern   = '%0.2f';
				$theAmount = is_numeric( $amount ) ? ( $amount / 100.0 ) : 0;
			}

			$formattedAmount = esc_attr( sprintf( $pattern, $theAmount ) );
		}

		return $formattedAmount;
	}

    /**
	 * Parse amount as smallest common currency unit with the given currency if the amount is a number.
	 *
	 * @param $currency
	 * @param $amount
	 *
	 * @return int|string the parsed value if the amount is a valid number, the amount itself otherwise
	 */
	public static function parse_amount( $currency, $amount ) {
		if ( ! is_numeric( $amount ) ) {
			return $amount;
		}
		$currencyArray = MM_WPFS_Currencies::getCurrencyFor( $currency );
		if ( is_array( $currencyArray ) ) {
			if ( $currencyArray['zeroDecimalSupport'] == true ) {
				$theAmount = $amount;
			} else {
				$theAmount = $amount * 100.0;
			}

			return $theAmount;
		}

		return $amount;
	}

	/**
	 * @deprecated
	 * Insert the inputs into the metadata array
	 *
	 * @param $metadata
	 * @param $customInputs
	 * @param $customInputValues
	 *
	 * @return mixed
	 */
	public static function add_custom_inputs( $metadata, $customInputs, $customInputValues ) {

		// MM_WPFS_Utils::log( 'add_custom_inputs(): CALLED, params: metadata=' . print_r( $metadata, true ) . ', customInputs=' . print_r( $customInputs, true ) . ', customInputValues=' . print_r( $customInputValues, true ) );

		if ( $customInputs == null ) {
			$customInputValueString = is_array( $customInputValues ) ? implode( ",", $customInputValues ) : printf( $customInputValues );
			if ( ! empty( $customInputValueString ) ) {
				$metadata['custom_inputs'] = $customInputValueString;
			}
		} else {
			$customInputLabels = MM_WPFS_Utils::decodeCustomFieldLabels( $customInputs );
			foreach ( $customInputLabels as $i => $label ) {
				$key = $label;
				if ( array_key_exists( $key, $metadata ) ) {
					$key = $label . $i;
				}
				if ( ! empty( $customInputValues[ $i ] ) ) {
					$metadata[ $key ] = $customInputValues[ $i ];
				}
			}
		}

		return $metadata;
	}

	/**
	 * @deprecated
	 *
	 * @param $encodedCustomInputs
	 *
	 * @return array
	 */
	public static function decodeCustomFieldLabels($encodedCustomInputs ) {
		$customInputLabels = array();
		if ( ! is_null( $encodedCustomInputs ) && !empty( $encodedCustomInputs ) ) {
			$customInputLabels = explode( '{{', $encodedCustomInputs );
		}

		return $customInputLabels;
	}

	/**
	 * @deprecated
	 *
	 * @param $customerEmail
	 * @param $customerName
	 * @param $formName
	 * @param $billingName
	 * @param $billingAddressLine1
	 * @param $billingAddressLine2
	 * @param $billingAddressZip
	 * @param $billingAddressCity
	 * @param $billingAddressState
	 * @param $billingAddressCountry
	 * @param $billingAddressCountryCode
	 * @param $shippingName
	 * @param $shippingAddressLine1
	 * @param $shippingAddressLine2
	 * @param $shippingAddressZip
	 * @param $shippingAddressCity
	 * @param $shippingAddressState
	 * @param $shippingAddressCountry
	 * @param $shippingAddressCountryCode
	 *
	 * @return array
	 */
	public static function create_metadata( $customerEmail, $customerName, $formName, $billingName = null, $billingAddressLine1 = null, $billingAddressLine2 = null, $billingAddressZip = null, $billingAddressCity = null, $billingAddressState = null, $billingAddressCountry = null, $billingAddressCountryCode = null, $shippingName = null, $shippingAddressLine1 = null, $shippingAddressLine2 = null, $shippingAddressZip = null, $shippingAddressCity = null, $shippingAddressState = null, $shippingAddressCountry = null, $shippingAddressCountryCode = null ) {
		$metadata = array();

		if ( ! empty( $customerEmail ) ) {
			$metadata['customer_email'] = $customerEmail;
		}
		if ( ! empty( $customerName ) ) {
			$metadata['customer_name'] = $customerName;
		}
		if ( ! empty( $formName ) ) {
			$metadata['form_name'] = $formName;
		}

		if ( ! empty( $billingName ) ) {
			$metadata['billing_name'] = $billingName;
		}
		if ( ! empty( $billingAddressLine1 ) || ! empty( $billingAddressZip ) || ! empty( $billingAddressCity ) || ! empty( $billingAddressCountry ) ) {
			$metadata['billing_address'] = implode( '|', array(
				$billingAddressLine1,
				$billingAddressLine2,
				$billingAddressZip,
				$billingAddressCity,
				$billingAddressState,
				$billingAddressCountry,
				$billingAddressCountryCode
			) );
		}
		if ( ! empty( $shippingName ) ) {
			$metadata['shipping_name'] = $shippingName;
		}
		if ( ! empty( $shippingAddressLine1 ) || ! empty( $shippingAddressZip ) || ! empty( $shippingAddressCity ) || ! empty( $shippingAddressCountry ) ) {
			$metadata['shipping_address'] = implode( '|', array(
				$shippingAddressLine1,
				$shippingAddressLine2,
				$shippingAddressZip,
				$shippingAddressCity,
				$shippingAddressState,
				$shippingAddressCountry,
				$shippingAddressCountryCode
			) );
		}

		return $metadata;
	}

	/**
	 * @deprecated
	 *
	 * @param $showCustomInput
	 * @param $customInputLabels
	 * @param $customInputTitle
	 * @param $customInputValues
	 * @param $customInputRequired
	 *
	 * @return ValidationResult
	 */
	public static function validate_custom_input_values( $showCustomInput, $customInputLabels, $customInputTitle, $customInputValues, $customInputRequired ) {
		$result = new ValidationResult();

		if ( $showCustomInput == 0 ) {
			return $result;
		}

		if ( $customInputRequired == 1 ) {
			if ( $customInputLabels == null ) {
				if ( is_null( $customInputValues ) || ( trim( $customInputValues ) == false ) ) {
					$result->setValid( false );
					$result->setMessage( sprintf( __( "Please enter a value for '%s'", 'wp-full-stripe' ), MM_WPFS_Localization::translateLabel( $customInputTitle ) ) );
				}
			} else {
				$customInputLabelArray = MM_WPFS_Utils::decodeCustomFieldLabels( $customInputLabels );
				foreach ( $customInputLabelArray as $i => $label ) {
					if ( $result->isValid() && ( is_null( $customInputValues[ $i ] ) || ( trim( $customInputValues[ $i ] ) == false ) ) ) {
						$result->setValid( false );
						$result->setMessage( sprintf( __( "Please enter a value for '%s'", 'wp-full-stripe' ), MM_WPFS_Localization::translateLabel( $label ) ) );
					}
				}
			}
		}

		if ( $result->isValid() ) {
			if ( $customInputLabels == null ) {
				if ( is_string( $customInputValues ) && strlen( $customInputValues ) > MM_WPFS_Utils::STRIPE_METADATA_VALUE_MAX_LENGTH ) {
					$result->setValid( false );
					$result->setMessage( sprintf(
					/* translators: Validation error message for a field whose value is too long */
						__( "The value for '%s' is too long.", 'wp-full-stripe' ), MM_WPFS_Localization::translateLabel( $customInputTitle ) ) );
				}
			} else {
				$customInputLabelArray = MM_WPFS_Utils::decodeCustomFieldLabels( $customInputLabels );
				foreach ( $customInputLabelArray as $i => $label ) {
					if ( $result->isValid() && ( is_string( $customInputValues[ $i ] ) && strlen( $customInputValues[ $i ] ) > MM_WPFS_Utils::STRIPE_METADATA_VALUE_MAX_LENGTH ) ) {
						$result->setValid( false );
						$result->setMessage( sprintf(
						/* translators: Validation error message for a field whose value is too long */
							__( "The value for '%s' is too long.", 'wp-full-stripe' ), MM_WPFS_Localization::translateLabel( $label ) ) );
					}
				}
			}
		}

		return $result;
	}

	/**
	 * @deprecated
	 *
	 * @param \StripeWPFS\StripeObject $stripeCustomer
	 *
	 * @return string Stripe Customer's name or null
	 */
	public static function retrieve_customer_name( $stripeCustomer ) {
		$customerName = null;
		if ( isset( $stripeCustomer ) && isset( $stripeCustomer->metadata ) && isset( $stripeCustomer->metadata->customer_name ) ) {
			$customerName = $stripeCustomer->metadata->customer_name;
		}
		if ( is_null( $customerName ) ) {
			if ( isset( $stripeCustomer->subscriptions ) ) {
				foreach ( $stripeCustomer->subscriptions as $subscription ) {
					if ( is_null( $customerName ) ) {
						if ( isset( $subscription->metadata ) && isset( $subscription->metadata->customer_name ) ) {
							$customerName = $subscription->metadata->customer_name;
						}
					}
				}
			}
		}

		return $customerName;
	}

	/**
	 * @param MM_WPFS_Database $databaseService
	 * @param MM_WPFS_Stripe $stripeService
	 * @param string $stripeCustomerEmail
	 *
	 * @param bool $returnStripeCustomerObject
	 *
	 * @return \StripeWPFS\Customer
	 */
	public static function findExistingStripeCustomerByEmail($databaseService, $stripeService, $stripeCustomerEmail, $returnStripeCustomerObject = false ) {

		$options  = get_option( 'fullstripe_options' );
		$liveMode = $options['apiMode'] === 'live';

		$customers = $databaseService->getExistingStripeCustomersByEmail( $stripeCustomerEmail, $liveMode );

		$result = null;
		foreach ( $customers as $customer ) {
			$stripeCustomer = null;
			try {
				$stripeCustomer = $stripeService->retrieveCustomer( $customer['stripeCustomerID'] );
			} catch ( Exception $e ) {
				MM_WPFS_Utils::logException( $e );
			}

			if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
				if ( $returnStripeCustomerObject ) {
					$result = $stripeCustomer;
				} else {
					$result = $customer;
				}
				break;
			}
		}

		return $result;
	}

	public static function logException( Exception $e, $object = null ) {
		if ( isset( $e ) ) {
			if ( is_null( $object ) ) {
				$message = sprintf( 'Message=%s, Stack=%s ', $e->getMessage(), $e->getTraceAsString() );
			} else {
				$message = sprintf( 'Class=%s, Message=%s, Stack=%s ', get_class( $object ), $e->getMessage(), $e->getTraceAsString() );
			}
			MM_WPFS_Utils::log( $message );
		}
	}

	public static function log( $message ) {
		error_log( self::WPFS_LOG_MESSAGE_PREFIX . $message );
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 * @param MM_WPFS_OneTimePaymentTransactionData $transactionData
	 *
	 * @return mixed
	 */
	public static function prepareStripeChargeDescription( $paymentFormModel, $transactionData ) {
		$stripeChargeDescription = '';
		if ( isset( $paymentFormModel->getForm()->stripeDescription ) && ! empty( $paymentFormModel->getForm()->stripeDescription ) ) {
			$formStripeDescription   = MM_WPFS_Localization::translateLabel( $paymentFormModel->getForm()->stripeDescription );

            $replacer = new MM_WPFS_OneTimePaymentMacroReplacer( $paymentFormModel->getForm(), $transactionData );
            $stripeChargeDescription = $replacer->replaceMacrosWithHtmlEscape( $formStripeDescription );
		}

		return $stripeChargeDescription;
	}

    /**
     * @param MM_WPFS_Public_DonationFormModel $donationFormModel
     * @param MM_WPFS_DonationTransactionData $transactionData
     *
     * @return mixed
     */
    public static function prepareStripeDonationDescription( $donationFormModel, $transactionData ) {
        $stripeChargeDescription = '';
        if ( isset( $donationFormModel->getForm()->stripeDescription ) && ! empty( $donationFormModel->getForm()->stripeDescription ) ) {
            $formStripeDescription   = MM_WPFS_Localization::translateLabel( $donationFormModel->getForm()->stripeDescription );

            $replacer = new MM_WPFS_DonationMacroReplacer( $donationFormModel->getForm(), $transactionData );
            $stripeChargeDescription = $replacer->replaceMacrosWithHtmlEscape( $formStripeDescription );
        }

        return $stripeChargeDescription;
    }

    /**
     * @param MM_WPFS_Public_PaymentFormModel $saveCardFormModel
     * @param MM_WPFS_SaveCardTransactionData $transactionData
     *
     * @return mixed
     */
    public static function prepareStripeCardSavedDescription( $saveCardFormModel, $transactionData ) {
        $stripeCustomerDescription = '';
        if ( isset( $saveCardFormModel->getForm()->stripeDescription ) && ! empty( $saveCardFormModel->getForm()->stripeDescription ) ) {
            $formStripeDescription     = MM_WPFS_Localization::translateLabel( $saveCardFormModel->getForm()->stripeDescription );

            $replacer = new MM_WPFS_SaveCardMacroReplacer( $saveCardFormModel->getForm(), $transactionData );
            $stripeCustomerDescription = $replacer->replaceMacrosWithHtmlEscape( $formStripeDescription );
        }

        return $stripeCustomerDescription;
    }

    /**
	 * @param $content
	 * @param $custom_input_values
	 * @param string $escapeTypes
	 *
	 * @return mixed
	 */
	public static function replace_custom_fields( $content, $custom_input_values, $escapeTypes = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		$custom_field_macros       = self::get_custom_field_macros();
		$custom_field_macro_values = self::get_custom_field_macro_values( count( $custom_field_macros ), $custom_input_values, $escapeTypes );
		$content                   = str_replace(
			$custom_field_macros,
			$custom_field_macro_values,
			$content
		);

		return $content;
	}

	/**
	 * @return array
	 */
	public static function get_custom_field_macros() {
		$customInputFieldMaxCount = MM_WPFS::getCustomFieldMaxCount();

		$customFieldMacros = array();

		for ( $i = 1; $i <= $customInputFieldMaxCount; $i ++ ) {
			array_push( $customFieldMacros, "%CUSTOMFIELD$i%" );
		}

		return $customFieldMacros;
	}

	/**
	 * @param $customFieldCount
	 * @param $customInputValues
	 * @param string $escapeType
	 *
	 * @return array
	 */
	public static function get_custom_field_macro_values( $customFieldCount, $customInputValues, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		$macroValues = array();
		if ( isset( $customInputValues ) && is_array( $customInputValues ) ) {
			$customInputValueCount = count( $customInputValues );
			for ( $index = 0; $index < $customFieldCount; $index ++ ) {
				if ( $index < $customInputValueCount ) {
					$value = $customInputValues[ $index ];
				} else {
					$value = '';
				}
				array_push( $macroValues, self::escape( $value, $escapeType ) );
			}
		}

		return $macroValues;
	}

	/**
	 * @param MM_WPFS_Database $databaseService
	 * @param MM_WPFS_Stripe $stripeService
	 * @param string $stripeCustomerEmail
	 *
	 * @return \StripeWPFS\Customer
	 */
	public static function findExistingStripeCustomerAnywhereByEmail($databaseService, $stripeService, $stripeCustomerEmail ) {

		$options  = get_option( 'fullstripe_options' );
		$liveMode = $options['apiMode'] === 'live';

		$customers = $databaseService->getExistingStripeCustomersByEmail( $stripeCustomerEmail, $liveMode );

		$result = null;
		foreach ( $customers as $customer ) {
			$stripeCustomer = null;
			try {
				$stripeCustomer = $stripeService->retrieveCustomer( $customer['stripeCustomerID'] );
			} catch ( Exception $e ) {
				MM_WPFS_Utils::logException( $e );
			}

			if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
				$result = $stripeCustomer;
				break;
			}
		}

		if ( is_null( $result ) ) {
			$stripeCustomers = $stripeService->getCustomersByEmail( $stripeCustomerEmail );

			foreach ( $stripeCustomers as $stripeCustomer ) {
				if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
					$result = $stripeCustomer;
					break;
				}
			}
		}

		return $result;
	}

	public static function getGoogleRecaptchaSiteKey() {
		$googleReCAPTCHASiteKey = null;
		$options                = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
			$googleReCAPTCHASiteKey = $options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ];
		}

		return $googleReCAPTCHASiteKey;
	}

	public static function getSecureInlineFormsWithGoogleRecaptcha() {
		$options = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
			return $options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] == '1';
		}

		return false;
	}

	public static function getSecureCheckoutFormsWithGoogleRecaptcha() {
		$options = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
			return $options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] == '1';
		}

		return false;
	}

	public static function getSecureCustomerPortalWithGoogleRecaptcha() {
		$options = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
			return $options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] == '1';
		}

		return false;
	}

	public static function getCancelSubscriptionsAtPeriodEnd() {
        $options = get_option( 'fullstripe_options' );

        return ( $options[ MM_WPFS::OPTION_CUSTOMER_PORTAL_WHEN_CANCEL_SUBSCRIPTIONS] === MM_WPFS::CANCEL_SUBSCRIPTION_AT_PERIOD_END );
    }

	public static function getDefaultPaymentStripeDescription() {
		return
			/* translators: Default transaction description for one-time payments */
			__( 'Payment on form %FORM_NAME%', 'wp-full-stripe' );
	}

	public static function getDefaultSaveCardDescription() {
		return
			/* translators: Default transaction description for saved cards */
			__( 'Card saved on form %FORM_NAME%', 'wp-full-stripe' );
	}

    public static function getDefaultDonationDescription() {
        return
            /* translators: Default transaction description for donations */
            __( 'Donation on form %FORM_NAME%', 'wp-full-stripe' );
    }

    /**
	 * @deprecated
	 *
	 * @param $data
	 *
	 * @return array|string
	 */
	public static function html_escape_value( $data ) {
		if ( ! is_array( $data ) ) {
			return htmlspecialchars( $data, ENT_QUOTES, 'UTF-8', false );
		}

		$escapedData = array();

		foreach ( $data as $value ) {
			array_push( $escapedData, self::html_escape_value( $value ) );
		}

		return $escapedData;
	}

	public static function getDefaultTermsOfUseLabel() {
		$defaultTermsOfUseURL = home_url( '/terms-of-use' );

		return sprintf(
		/* translators: Default label for the Terms of Use checkbox */
			__( "I accept the <a href='%s' target='_blank'>Terms of Use</a>" ), $defaultTermsOfUseURL );
	}

	public static function getDefaultTermsOfUseNotCheckedErrorMessage() {
		return
			/* translators: Field validation error message when the Terms of use checkbox is not checked */
			__( 'Please accept the Terms of Use', 'wp-full-stripe' );
	}

	public static function getDefaultCouponInvalidErrorMessage() {
		return
			/* translators: Banner message of expired coupon */
			__( 'This coupon has expired.', 'wp-full-stripe' );
	}

	public static function getDefaultInvalidCouponCurrencyErrorMessage() {
		return
			/* translators: Banner message of expired coupon */
			__( 'This coupon has an invalid currency.', 'wp-full-stripe' );
	}

	public static function getDefaultPaymentButtonTitle() {
	    return
            /* translators: Default payment button label on inline one-time payment forms */
            __( 'Make payment', 'wp-full-stripe' );
    }

    public static function getDefaultSaveCardButtonTitle() {
        return
            /* translators: Default payment button label on inline save card forms */
            __( 'Save card', 'wp-full-stripe' );
    }

    public static function getDefaultPaymentOpenButtonTitle() {
        return
            /* translators: Default payment button label on checkout one-time payment forms */
            __( 'Make payment', 'wp-full-stripe' );
    }

    public static function getDefaultSubscriptionButtonTitle() {
        return
            /* translators: Default subscription button label on inline subscription forms */
            __( 'Subscribe', 'wp-full-stripe' );
    }

    public static function getDefaultSubscriptionOpenButtonTitle() {
        return
            /* translators: Default subscription button label on checkout subscription forms */
            __( 'Subscribe', 'wp-full-stripe' );
    }

    public static function getDefaultDonationButtonTitle() {
        return
            /* translators: Default donation button label on inline donation forms */
            __( 'Donate', 'wp-full-stripe' );
    }

    public static function getDefaultDonationOpenButtonTitle() {
        return
            /* translators: Default donation button label on inline donation forms */
            __( 'Donate', 'wp-full-stripe' );
    }

    public static function getDefaultProductDescription() {
        /* translators: Placeholder product name for newly created one-time payment forms */
        return __('My Product', 'wp-full-stripe');
    }

    public static function getDefaultDonationProductDescription() {
        /* translators: Placeholder product name for newly created donation forms */
        return __('My Donation', 'wp-full-stripe');
    }

    public static function getPaymentStatuses() {
		return array(
			MM_WPFS::PAYMENT_STATUS_FAILED,
			MM_WPFS::PAYMENT_STATUS_RELEASED,
			MM_WPFS::PAYMENT_STATUS_REFUNDED,
			MM_WPFS::PAYMENT_STATUS_EXPIRED,
			MM_WPFS::PAYMENT_STATUS_PAID,
			MM_WPFS::PAYMENT_STATUS_AUTHORIZED,
			MM_WPFS::PAYMENT_STATUS_PENDING
		);
	}

    public static function getSubscriptionStatuses() {
        return array(
            MM_WPFS::SUBSCRIBER_STATUS_INCOMPLETE,
            MM_WPFS::SUBSCRIBER_STATUS_RUNNING,
            MM_WPFS::SUBSCRIBER_STATUS_ENDED,
            MM_WPFS::SUBSCRIBER_STATUS_CANCELLED
        );
    }

    /**
	 * @param $payment
	 *
	 * @return string
	 */
	public static function getPaymentStatus($payment ) {
		if ( is_null( $payment ) ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_FAILED === $payment->last_charge_status ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_FAILED;
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_PENDING === $payment->last_charge_status ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_PENDING;
		} elseif ( 1 == $payment->expired ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_EXPIRED;
		} elseif ( 1 == $payment->refunded ) {
			if ( 1 == $payment->captured ) {
				$payment_status = MM_WPFS::PAYMENT_STATUS_REFUNDED;
			} else {
				$payment_status = MM_WPFS::PAYMENT_STATUS_RELEASED;
			}
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $payment->last_charge_status && 1 == $payment->paid && 1 == $payment->captured ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_PAID;
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $payment->last_charge_status && 1 == $payment->paid && 0 == $payment->captured ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_AUTHORIZED;
		} else {
			$payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
		}

		return $payment_status;
	}

    /**
     * @param $donation
     *
     * @return string
     */
    public static function getDonationPaymentStatus($donation ) {
        if ( is_null( $donation ) ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_FAILED === $donation->lastChargeStatus ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_FAILED;
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_PENDING === $donation->lastChargeStatus ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_PENDING;
        } elseif ( 1 == $donation->expired ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_EXPIRED;
        } elseif ( 1 == $donation->refunded ) {
            if ( 1 == $donation->captured ) {
                $payment_status = MM_WPFS::PAYMENT_STATUS_REFUNDED;
            } else {
                $payment_status = MM_WPFS::PAYMENT_STATUS_RELEASED;
            }
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $donation->lastChargeStatus && 1 == $donation->paid && 1 == $donation->captured ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_PAID;
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $donation->lastChargeStatus && 1 == $donation->paid && 0 == $donation->captured ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_AUTHORIZED;
        } else {
            $payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
        }

        return $payment_status;
    }

    /**
     * @param $donation
     *
     * @return string
     */
    public static function getDonationStatus( $donation ) : string {
        $status         = MM_WPFS::DONATION_STATUS_UNKNOWN;
        $oneTimeStatus  = MM_WPFS_Utils::getDonationPaymentStatus( $donation );

        if ( MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME === $donation->donationFrequency ) {
            if ( MM_WPFS::PAYMENT_STATUS_PAID === $oneTimeStatus ) {
                $status = MM_WPFS::DONATION_STATUS_PAID;
            } else if ( MM_WPFS::PAYMENT_STATUS_REFUNDED === $oneTimeStatus  ) {
                $status = MM_WPFS::DONATION_STATUS_REFUNDED;
            }
        } else {
            if ( $donation->subscriptionStatus === \StripeWPFS\Subscription::STATUS_ACTIVE ) {
                $status = MM_WPFS::DONATION_STATUS_RUNNING;
            } else if ( $donation->subscriptionStatus === MM_WPFS::SUBSCRIBER_STATUS_CANCELLED &&
                MM_WPFS::PAYMENT_STATUS_PAID === $oneTimeStatus ) {
                $status = MM_WPFS::DONATION_STATUS_PAID;
            } else if ( $donation->subscriptionStatus === MM_WPFS::SUBSCRIBER_STATUS_CANCELLED &&
                MM_WPFS::PAYMENT_STATUS_REFUNDED === $oneTimeStatus ) {
                $status = MM_WPFS::DONATION_STATUS_REFUNDED;
            }
        }

        return $status;
    }


    public static function get_cancellation_count_for_plan( $plan ) {
		$cancellation_count = 0;
		if ( isset( $plan ) && isset( $plan->metadata ) ) {
			if ( isset( $plan->metadata->cancellation_count ) ) {
				if ( is_numeric( $plan->metadata->cancellation_count ) ) {
					$cancellation_count = intval( $plan->metadata->cancellation_count );
				}
			}
		}

		return $cancellation_count;
	}

	/**
	 * @param $form
	 *
	 * @return null|string
	 */
	public static function getFormId( $form ) {
		if ( is_null( $form ) ) {
			return null;
		}
		if ( isset( $form->paymentFormID ) ) {
			return $form->paymentFormID;
		}
		if ( isset( $form->subscriptionFormID ) ) {
			return $form->subscriptionFormID;
		}
		if ( isset( $form->checkoutFormID ) ) {
			return $form->checkoutFormID;
		}
		if ( isset( $form->checkoutSubscriptionFormID ) ) {
			return $form->checkoutSubscriptionFormID;
		}
        if ( isset( $form->donationFormID ) ) {
            return $form->donationFormID;
        }
        if ( isset( $form->checkoutDonationFormID ) ) {
            return $form->checkoutDonationFormID;
        }

		return null;
	}

	/**
	 * @param $payment
	 *
	 * @return string
	 */
	public static function getPaymentObjectType($payment ) {
		if ( isset( $payment ) && isset( $payment->eventID ) ) {
			if ( strlen( $payment->eventID ) > 3 ) {
				if ( MM_WPFS::STRIPE_OBJECT_ID_PREFIX_PAYMENT_INTENT === substr( $payment->eventID, 0, 3 ) ) {
					return MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_PAYMENT_INTENT;
				} elseif ( MM_WPFS::STRIPE_OBJECT_ID_PREFIX_CHARGE === substr( $payment->eventID, 0, 3 ) ) {
					return MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_CHARGE;
				}
			}
		}

		return MM_WPFS::PAYMENT_OBJECT_TYPE_UNKNOWN;
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return bool
	 */
	public static function hasToCapturePaymentIntentByFormModel($paymentFormModel ) {
		if ( MM_WPFS::CHARGE_TYPE_IMMEDIATE === $paymentFormModel->getForm()->chargeType ) {
			$capture = true;
		} elseif ( MM_WPFS::CHARGE_TYPE_AUTHORIZE_AND_CAPTURE === $paymentFormModel->getForm()->chargeType ) {
			$capture = false;
		} else {
			$capture = true;
		}

		return $capture;
	}

	/**
	 * @param MM_WPFS_Public_FormModel $formModelObject
	 *
	 * @return mixed|string|void
	 */
	public static function generateFormNonce( $formModelObject ) {
		$nonceObject            = new stdClass();
		$nonceObject->created   = time();
		$nonceObject->formHash  = $formModelObject->getFormHash();
		$nonceObject->fieldHash = md5( json_encode( $formModelObject ) );
		$nonceObject->salt      = wp_generate_password( 16, false );

		return json_encode( $nonceObject );
	}

	public static function decodeFormNonce( $text ) {
		$decodedObject = json_decode( $text );

		if ( null === $decodedObject || false === $decodedObject || JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		return $decodedObject;
	}

	public static function encrypt( $message ) {
		$nonce = \Sodium\randombytes_buf( \Sodium\CRYPTO_SECRETBOX_NONCEBYTES );

		$encodedMessage = base64_encode(
			$nonce . \Sodium\crypto_secretbox(
				$message,
				$nonce,
				self::getEncryptionKey()
			)
		);

		return $encodedMessage;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private static function getEncryptionKey() {
		$desiredKeyLength = 32;
		if ( strlen( NONCE_KEY ) == $desiredKeyLength ) {
			return NONCE_KEY;
		} elseif ( strlen( NONCE_KEY ) > $desiredKeyLength ) {
			return substr( NONCE_KEY, 0, 32 );
		} else {
			throw new Exception( 'WordPress Constant NONCE_KEY is too short' );
		}
	}

	public static function decrypt( $secretMessage ) {
		$decodedMessage   = base64_decode( $secretMessage );
		$nonce            = mb_substr( $decodedMessage, 0, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );
		$encryptedMessage = mb_substr( $decodedMessage, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );
		$decryptedMessage = \Sodium\crypto_secretbox_open( $encryptedMessage, $nonce, self::getEncryptionKey() );

		return $decryptedMessage;
	}

    /**
     * This function is the exact copy of wp_timezone_string() of Wordpress.
     * We had to copy it here because it's available only since v5.3.0 .
     *
     * @return string
     */
    public static function getWordpressTimezone() {
        $timezone_string = get_option( 'timezone_string' );

        if ( $timezone_string ) {
            return $timezone_string;
        }

        $offset  = (float) get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - $hours );

        $sign      = ( $offset < 0 ) ? '-' : '+';
        $abs_hour  = abs( $hours );
        $abs_mins  = abs( $minutes * 60 );
        $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

        return $tz_offset;
    }


    public static function calculateTrialEndFromNow($trialDays ) {
        $currentTimestamp     = time();
        $oneDayInSeconds      = 24 * 60 * 60;

        return $currentTimestamp + $trialDays * $oneDayInSeconds;
    }

    public static function calculateBillingCycleAnchorFromNow( $billingCycleAnchorDay ) {
        return self::calculateBillingCycleAnchorFromTimestamp( $billingCycleAnchorDay, time() );
    }

    public static function calculateBillingCycleAnchorFromTimestamp( $billingCycleAnchorDay, $startingTimestamp ) {
        $oneDayInSeconds      = 24 * 60 * 60;

        // We save the default timezone because we'll restore it after our calculations
        $defaultTz            = date_default_timezone_get();
        $userTz               = self::getWordpressTimezone();

        date_default_timezone_set( $userTz );
        $currentDayOfMonth    = date("d", $startingTimestamp);
        $numDaysInMonth       = date("t", $startingTimestamp);
        date_default_timezone_set( $defaultTz );

        $billingAnchorTimestamp = null;
        if ( $billingCycleAnchorDay >= $currentDayOfMonth ) {
            $billingAnchorTimestamp = $startingTimestamp + ( $billingCycleAnchorDay - $currentDayOfMonth ) * $oneDayInSeconds;
        } else {
            $billingAnchorTimestamp = $startingTimestamp + ( $numDaysInMonth - $currentDayOfMonth + $billingCycleAnchorDay ) * $oneDayInSeconds;
        }


        return $billingAnchorTimestamp;
    }

    public static function generateDonationAmountsLabel( $donationForm ) {
        $donationAmounts      = json_decode( $donationForm->donationAmounts );
        $donationAmountsLabel = '';

        if ( json_last_error() == JSON_ERROR_NONE ) {
            for ( $idx = 0; $idx < count( $donationAmounts ); $idx++ ) {
                $donationAmount = (int)$donationAmounts[ $idx ];
                $donationAmountsLabel .= MM_WPFS_Currencies::formatAndEscapeByAdmin( $donationForm->currency, $donationAmount, true, true );

                if ( $idx != count( $donationAmounts ) - 1 ) {
                    $donationAmountsLabel .= ", ";
                }
            }
        }
        if ( $donationForm->allowCustomDonationAmount == 1 ) {
            $donationAmountsLabel .= ", custom";
        }

        return $donationAmountsLabel;
    }

    public static function decodeJsonArray( $arr ) {
	    $res = json_decode( $arr );
        if ( json_last_error() != JSON_ERROR_NONE ) {
            // todo: Log the json decode error
            $res = array();
        }

        return $res;
    }

    /**
	 * @param $bindingResult MM_WPFS_BindingResult
	 *
	 * @return array
	 */
    public static function generateReturnValueFromBindings( $bindingResult ) {
        return array(
            'success' => false,
            'bindingResult' => array(
                'fieldErrors' => array(
                    'title' =>
                    /* translators: Banner title of a hidden field's validation error */
                        __('Field validation error', 'wp-full-stripe'),
                    'errors' => $bindingResult->getFieldErrors()
                ),
                'globalErrors' => array(
                    'title' =>
                    /* translators: Banner title of a validation error which is not field specific */
                        __('Form error', 'wp-full-stripe'),
                    'errors' => $bindingResult->getGlobalErrors()
                )
            )
        );
    }

    public static function determineCustomerName($cardHolderName, $businessName, $billingName) {
        $result = null;

        if (!empty($billingName)) {
            $result = $billingName;
        }
        if (is_null($result) && !empty($businessName)) {
            $result = $businessName;
        }
        if (is_null($result)) {
            $result = $cardHolderName;
        }

        return $result;
    }

}

/**
 * @deprecated
 * Class ValidationResult
 */
class ValidationResult {
	/** @var bool */
	protected $valid = true;
	/** @var  string */
	protected $message;

	/**
	 * @return boolean
	 */
	public function isValid() {
		return $this->valid;
	}

	/**
	 * @param boolean $valid
	 */
	public function setValid( $valid ) {
		$this->valid = $valid;
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

}

trait MM_WPFS_MacroReplacerTools {
    /** @var array */
    protected $rawKeyValuePairs;
    /** @var array */
    protected $decoratedKeyValuePairs;
    /** @var MM_WPFS_TransactionData */
    protected $transactionData;

    public function getRawKeyValuePairs() {
        return $this->rawKeyValuePairs;
    }

    public function getDecoratedKeyValuePairs() {
        return $this->decoratedKeyValuePairs;
    }

    protected function getDecoratedMacroKeys() {
        return array_keys( $this->decoratedKeyValuePairs );
    }

    protected function getDecoratedMacroValues() {
        return array_values( $this->decoratedKeyValuePairs );
    }

    protected function replaceMacrosWithEscape($template, $escapeType ) {
        $keys 	= $this->getDecoratedMacroKeys();
        $values = $this->getDecoratedMacroValues();

	    $escapedValues = array();
        foreach ( $values as $value ) {
            array_push( $escapedValues, MM_WPFS_Utils::escape( $value, $escapeType ));
        }

        $template = str_replace( $keys, $escapedValues, $template );

        return $template;
    }

    public function replaceMacrosWithHtmlEscape( $template ) {
        return $this->replaceMacrosWithEscape( $template, MM_WPFS_Utils::ESCAPE_TYPE_HTML );
    }

    public function replaceMacrosWithAttributeEscape( $template ) {
        return $this->replaceMacrosWithEscape( $template, MM_WPFS_Utils::ESCAPE_TYPE_ATTR );
    }

    public function replaceMacros( $template ) {
        return $this->replaceMacrosWithEscape( $template, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
    }

    abstract protected function initRawKeyValuePairs();
    abstract protected function initDecoratedKeyValuePairs();
    abstract public static function getMacroKeys();
}

class MM_WPFS_GenericMacroReplacer {
    use MM_WPFS_MacroReplacerTools;

    function __construct( $transactionData ) {
        $this->transactionData  = $transactionData;

        $this->initRawKeyValuePairs();
        $this->initDecoratedKeyValuePairs();
    }

    protected function initRawKeyValuePairs() {
        $this->rawKeyValuePairs = $this->transactionData;
    }

    protected function initDecoratedKeyValuePairs() {
        $this->decoratedKeyValuePairs = $this->transactionData;
    }

    public static function getMacroKeys() {
        return [];
    }
}

class MM_WPFS_MyAccountLoginMacroReplacer {
    use MM_WPFS_MacroReplacerTools;

    function __construct( $transactionData ) {
        $this->transactionData  = $transactionData;

        $this->initRawKeyValuePairs();
        $this->initDecoratedKeyValuePairs();
    }

    public static function getMacroKeys() {
        return array(
            '%NAME%',
            '%CUSTOMERNAME%',
            '%CUSTOMER_EMAIL%',
            '%CARD_UPDATE_SECURITY_CODE%',
            '%CARD_UPDATE_SESSION_HASH%',
            '%DATE%'
        );
    }

    protected function getKeyValuePairs() {
        $siteTitle      = get_bloginfo( 'name' );
        $dateFormat     = get_option( 'date_format' );

        $keyValuePairs = array(
            '%NAME%'		                => $siteTitle,
            '%CUSTOMERNAME%'		        => $this->transactionData->getCustomerName(),
            '%CUSTOMER_EMAIL%'		        => $this->transactionData->getCustomerEmail(),
            '%CARD_UPDATE_SECURITY_CODE%'   => $this->transactionData->getSecurityCode(),
            '%CARD_UPDATE_SESSION_HASH%'    => $this->transactionData->getSessionHash(),
            '%DATE%'                        => date( $dateFormat )
        );

        return $keyValuePairs;
    }
    
    protected function initRawKeyValuePairs() {
        $this->rawKeyValuePairs = $this->getKeyValuePairs();
    }

    protected function initDecoratedKeyValuePairs() {
        $this->decoratedKeyValuePairs = $this->getKeyValuePairs();
    }
}

abstract class MM_WPFS_FormMacroReplacer {
    use MM_WPFS_MacroReplacerTools;

    /** @var stdClass */
    protected $form;

    function __construct( $form, $transactionData ) {
        $this->form             = $form;
        $this->transactionData  = $transactionData;

        $this->rawKeyValuePairs = array();
        $this->decoratedKeyValuePairs = array();

        $this->initRawKeyValuePairs();
        $this->initDecoratedKeyValuePairs();
    }

    public static function getMacroKeys() {
        return array(
            '%NAME%',
            '%CUSTOMERNAME%',
            '%CUSTOMER_EMAIL%',
            '%BILLING_NAME%',
            '%ADDRESS1%',
            '%ADDRESS2%',
            '%CITY%',
            '%STATE%',
            '%COUNTRY%',
            '%COUNTRY_CODE%',
            '%ZIP%',
            '%SHIPPING_NAME%',
            '%SHIPPING_ADDRESS1%',
            '%SHIPPING_ADDRESS2%',
            '%SHIPPING_CITY%',
            '%SHIPPING_STATE%',
            '%SHIPPING_COUNTRY%',
            '%SHIPPING_COUNTRY_CODE%',
            '%SHIPPING_ZIP%',
            '%DATE%',
            '%FORM_NAME%',
            '%STRIPE_CUSTOMER_ID%',
            '%TRANSACTION_ID%',
            '%CUSTOMFIELD1%'
        );
    }

    private function getKeyValuePairs() {
        $siteTitle      = get_bloginfo( 'name' );
        $dateFormat     = get_option( 'date_format' );
        $billingAddress = $this->transactionData->getBillingAddress();
        $shippingAddress = $this->transactionData->getShippingAddress();

        $keyValuePairs = array(
            '%NAME%'		            => $siteTitle,
            '%CUSTOMERNAME%'		    => $this->transactionData->getCustomerName(),
            '%CUSTOMER_EMAIL%'		    => $this->transactionData->getCustomerEmail(),
            '%BILLING_NAME%'            => $this->transactionData->getBillingName(),
            '%ADDRESS1%'                => is_null( $billingAddress ) ? null : $billingAddress['line1'],
            '%ADDRESS2%'                => is_null( $billingAddress ) ? null : $billingAddress['line2'],
            '%CITY%'                    => is_null( $billingAddress ) ? null : $billingAddress['city'],
            '%STATE%'                   => is_null( $billingAddress ) ? null : $billingAddress['state'],
            '%COUNTRY%'                 => is_null( $billingAddress ) ? null : $billingAddress['country'],
            '%COUNTRY_CODE%'            => is_null( $billingAddress ) ? null : $billingAddress['country_code'],
            '%ZIP%'                     => is_null( $billingAddress ) ? null : $billingAddress['zip'],
            '%SHIPPING_NAME%'           => $this->transactionData->getShippingName(),
            '%SHIPPING_ADDRESS1%'       => is_null( $shippingAddress ) ? null : $shippingAddress['line1'],
            '%SHIPPING_ADDRESS2%'       => is_null( $shippingAddress ) ? null : $shippingAddress['line2'],
            '%SHIPPING_CITY%'           => is_null( $shippingAddress ) ? null : $shippingAddress['city'],
            '%SHIPPING_STATE%'          => is_null( $shippingAddress ) ? null : $shippingAddress['state'],
            '%SHIPPING_COUNTRY%'        => is_null( $shippingAddress ) ? null : $shippingAddress['country'],
            '%SHIPPING_COUNTRY_CODE%'   => is_null( $shippingAddress ) ? null : $shippingAddress['country_code'],
            '%SHIPPING_ZIP%'            => is_null( $shippingAddress ) ? null : $shippingAddress['zip'],
            '%DATE%'                    => date( $dateFormat ),
            '%FORM_NAME%'               => $this->transactionData->getFormName(),
            '%STRIPE_CUSTOMER_ID%'      => $this->transactionData->getStripeCustomerId(),
            '%TRANSACTION_ID%'          => $this->transactionData->getTransactionId()
        );

        $customInputKeyValuePairs = $this->getCustomInputKeyValuePairs();

        return array_merge( $keyValuePairs, $customInputKeyValuePairs );
    }

    protected function initRawKeyValuePairs() {
        $this->rawKeyValuePairs = array_merge( $this->rawKeyValuePairs, $this->getKeyValuePairs() );
    }

    protected function initDecoratedKeyValuePairs() {
        $this->decoratedKeyValuePairs = array_merge( $this->decoratedKeyValuePairs, $this->getKeyValuePairs() );
    }

    private function getCustomInputKeyValuePairs() {
        $keyValuePairs      = array();
        $customInputValues  = $this->transactionData->getCustomInputValues();

        $customInputFieldMaxCount = MM_WPFS::getCustomFieldMaxCount();
        $customInputValueCount    = 0;
        if ( isset( $customInputValues ) && is_array( $customInputValues ) ) {
            $customInputValueCount = count($customInputValues);
        }

        for ( $idx = 0; $idx < $customInputFieldMaxCount; $idx++ ) {
            $key = "%CUSTOMFIELD" . ( $idx+1 ) . "%";

            if ( $idx < $customInputValueCount ) {
                $value = $customInputValues[ $idx ];
            } else {
                $value = '';
            }
            $customInputElement = array( $key => $value  );

            $keyValuePairs = array_merge( $keyValuePairs, $customInputElement );
        }

        return $keyValuePairs;
    }
}

class MM_WPFS_SaveCardMacroReplacer extends MM_WPFS_FormMacroReplacer {
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }
}

abstract class MM_WPFS_ProductMacroReplacer extends MM_WPFS_FormMacroReplacer {
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }

    public static function getMacroKeys() {
        return array_merge( parent::getMacroKeys(), array(
            '%PRODUCT_NAME%'
        ));
    }

    private function getKeyValuePairs() {
        $keyValuePairs = array(
            '%PRODUCT_NAME%'    => $this->transactionData->getProductName()
        );

        return $keyValuePairs;
    }

    protected function initRawKeyValuePairs() {
        parent::initRawKeyValuePairs();

        $this->rawKeyValuePairs = array_merge( $this->rawKeyValuePairs, $this->getKeyValuePairs() );
    }

    protected function initDecoratedKeyValuePairs() {
        parent::initDecoratedKeyValuePairs();

        $this->decoratedKeyValuePairs = array_merge( $this->decoratedKeyValuePairs, $this->getKeyValuePairs() );
    }
}

abstract class MM_WPFS_OneTimeInvolvedMacroReplacer extends MM_WPFS_ProductMacroReplacer {
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }

    public static function getMacroKeys() {
        return array_merge( parent::getMacroKeys(), array(
            '%AMOUNT%'
        ));
    }

    protected function initRawKeyValuePairs() {
        parent::initRawKeyValuePairs();

        $keyValuePairs = array(
            '%AMOUNT%' => $this->transactionData->getAmount(),
        );

        $this->rawKeyValuePairs = array_merge( $this->rawKeyValuePairs, $keyValuePairs );
    }

    protected function initDecoratedKeyValuePairs() {
        parent::initDecoratedKeyValuePairs();

        $keyValuePairs = array(
            '%AMOUNT%'		    => MM_WPFS_Currencies::formatAndEscapeByForm(
            	$this->form,
                $this->transactionData->getCurrency(),
                $this->transactionData->getAmount()
            ),
        );

        $this->decoratedKeyValuePairs = array_merge( $this->decoratedKeyValuePairs, $keyValuePairs );
    }
}


class MM_WPFS_OneTimePaymentMacroReplacer extends MM_WPFS_OneTimeInvolvedMacroReplacer {
    /**
     * @param $form array
     * @param $transactionData MM_WPFS_OneTimePaymentTransactionData
     */
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }

    public static function getMacroKeys() {
        return array_merge( parent::getMacroKeys(), array(
            '%COUPON_CODE%',
            '%INVOICE_URL%',
            '%INVOICE_NUMBER%',
            '%CUSTOMER_TAX_ID%',
            '%PRODUCT_AMOUNT_GROSS%',
            '%PRODUCT_AMOUNT_TAX%',
            '%PRODUCT_AMOUNT_NET%',
            '%PRODUCT_AMOUNT_DISCOUNT%'
        ));
    }

    protected function initRawKeyValuePairs() {
        parent::initRawKeyValuePairs();

        $keyValuePairs = array(
            '%COUPON_CODE%'             => $this->transactionData->getCouponCode(),
            '%INVOICE_URL%'             => $this->transactionData->getInvoiceUrl(),
            '%INVOICE_NUMBER%'          => $this->transactionData->getInvoiceNumber(),
            '%CUSTOMER_TAX_ID%'         => $this->transactionData->getCustomerTaxId(),
            '%PRODUCT_AMOUNT_GROSS%'    => $this->transactionData->getProductAmountGross(),
            '%PRODUCT_AMOUNT_TAX%'      => $this->transactionData->getProductAmountTax(),
            '%PRODUCT_AMOUNT_NET%'      => $this->transactionData->getProductAmountNet(),
            '%PRODUCT_AMOUNT_DISCOUNT%' => $this->transactionData->getProductAmountDiscount(),
        );

        $this->rawKeyValuePairs = array_merge( $this->rawKeyValuePairs, $keyValuePairs );
    }

    protected function initDecoratedKeyValuePairs() {
        parent::initDecoratedKeyValuePairs();

        $keyValuePairs = array(
            '%COUPON_CODE%'     => $this->transactionData->getCouponCode(),
            '%INVOICE_URL%'     => $this->transactionData->getInvoiceUrl(),
            '%INVOICE_NUMBER%'  => $this->transactionData->getInvoiceNumber(),
            '%CUSTOMER_TAX_ID%' => $this->transactionData->getCustomerTaxId(),
            '%PRODUCT_AMOUNT_GROSS%'  => MM_WPFS_Currencies::formatAndEscapeByForm(
                $this->form,
                $this->transactionData->getCurrency(),
                $this->transactionData->getProductAmountGross()
            ),
            '%PRODUCT_AMOUNT_TAX%'  => MM_WPFS_Currencies::formatAndEscapeByForm(
                $this->form,
                $this->transactionData->getCurrency(),
                $this->transactionData->getProductAmountTax()
            ),
            '%PRODUCT_AMOUNT_NET%'  => MM_WPFS_Currencies::formatAndEscapeByForm(
                $this->form,
                $this->transactionData->getCurrency(),
                $this->transactionData->getProductAmountNet()
            ),
            '%PRODUCT_AMOUNT_DISCOUNT%'  => MM_WPFS_Currencies::formatAndEscapeByForm(
                $this->form,
                $this->transactionData->getCurrency(),
                $this->transactionData->getProductAmountDiscount()
            ),
        );

        $this->decoratedKeyValuePairs = array_merge( $this->decoratedKeyValuePairs, $keyValuePairs );
    }
}

class MM_WPFS_DonationMacroReplacer extends MM_WPFS_OneTimeInvolvedMacroReplacer {
    /**
     * @param $form array
     * @param $transactionData MM_WPFS_DonationTransactionData
     */
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }

    public static function getMacroKeys() {
        return array_merge( parent::getMacroKeys(), array(
            '%DONATION_FREQUENCY%',
        ));
    }

    protected function initRawKeyValuePairs() {
        parent::initRawKeyValuePairs();

        $keyValuePairs = array(
            '%DONATION_FREQUENCY%'    => MM_WPFS_Localization::getDonationFrequencyLabel( $this->transactionData->getDonationFrequency() ),
        );

        $this->rawKeyValuePairs = array_merge( $this->rawKeyValuePairs, $keyValuePairs );
    }

    protected function initDecoratedKeyValuePairs() {
        parent::initDecoratedKeyValuePairs();

        $keyValuePairs = array(
            '%DONATION_FREQUENCY%' => MM_WPFS_Localization::getDonationFrequencyLabel( $this->transactionData->getDonationFrequency() ),
        );

        $this->decoratedKeyValuePairs = array_merge( $this->decoratedKeyValuePairs, $keyValuePairs );
    }
}

class MM_WPFS_SubscriptionMacroReplacer extends MM_WPFS_ProductMacroReplacer {
    /**
     * @param $form array
     * @param $transactionData MM_WPFS_SubscriptionTransactionData
     */
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }

    public static function getMacroKeys() {
        return array_merge( parent::getMacroKeys(), array(
            '%SETUP_FEE%',
            '%SETUP_FEE_NET%',
            '%SETUP_FEE_GROSS%',
            '%SETUP_FEE_VAT%',
            '%SETUP_FEE_VAT_RATE%',
            '%SETUP_FEE_TOTAL%',
            '%SETUP_FEE_NET_TOTAL%',
            '%SETUP_FEE_GROSS_TOTAL%',
            '%SETUP_FEE_VAT_TOTAL%',
            '%PLAN_NAME%',
            '%PLAN_AMOUNT%',
            '%PLAN_AMOUNT_NET%',
            '%PLAN_AMOUNT_GROSS%',
            '%PLAN_AMOUNT_VAT%',
            '%PLAN_AMOUNT_VAT_RATE%',
            '%PLAN_QUANTITY%',
            '%PLAN_AMOUNT_TOTAL%',
            '%PLAN_AMOUNT_NET_TOTAL%',
            '%PLAN_AMOUNT_GROSS_TOTAL%',
            '%PLAN_AMOUNT_VAT_TOTAL%',
            '%INVOICE_URL%',
            '%INVOICE_NUMBER%',
            '%RECEIPT_URL%',
            '%AMOUNT%',
            '%COUPON_CODE%',
            '%CUSTOMER_TAX_ID%'
        ));
    }

    protected function initRawKeyValuePairs() {
        parent::initRawKeyValuePairs();

        $keyValuePairs = array(
            '%SETUP_FEE%'               => $this->transactionData->getSetupFeeGrossAmount(),
            '%SETUP_FEE_NET%'           => $this->transactionData->getSetupFeeNetAmount(),
            '%SETUP_FEE_GROSS%'         => $this->transactionData->getSetupFeeGrossAmount(),
            '%SETUP_FEE_VAT%'           => $this->transactionData->getSetupFeeTaxAmount(),
            '%SETUP_FEE_TOTAL%'         => $this->transactionData->getSetupFeeGrossAmountTotal(),
            '%SETUP_FEE_NET_TOTAL%'     => $this->transactionData->getSetupFeeNetAmountTotal(),
            '%SETUP_FEE_GROSS_TOTAL%'   => $this->transactionData->getSetupFeeGrossAmountTotal(),
            '%SETUP_FEE_VAT_TOTAL%'     => $this->transactionData->getSetupFeeTaxAmountTotal(),
            '%PLAN_NAME%'               => $this->transactionData->getPlanName(),
            '%PLAN_AMOUNT%'             => $this->transactionData->getPlanGrossAmount(),
            '%PLAN_AMOUNT_NET%'         => $this->transactionData->getPlanNetAmount(),
            '%PLAN_AMOUNT_GROSS%'       => $this->transactionData->getPlanGrossAmount(),
            '%PLAN_AMOUNT_VAT%'         => $this->transactionData->getPlanTaxAmount(),
            '%PLAN_QUANTITY%'           => $this->transactionData->getPlanQuantity(),
            '%PLAN_AMOUNT_TOTAL%'       => $this->transactionData->getPlanGrossAmountTotal(),
            '%PLAN_AMOUNT_NET_TOTAL%'   => $this->transactionData->getPlanNetAmountTotal(),
            '%PLAN_AMOUNT_GROSS_TOTAL%' => $this->transactionData->getPlanGrossAmountTotal(),
            '%PLAN_AMOUNT_VAT_TOTAL%'   => $this->transactionData->getPlanTaxAmountTotal(),
            '%INVOICE_URL%'             => $this->transactionData->getInvoiceUrl(),
            '%INVOICE_NUMBER%'          => $this->transactionData->getInvoiceNumber(),
            '%RECEIPT_URL%'             => $this->transactionData->getReceiptUrl(),
            '%AMOUNT%'                  => $this->transactionData->getPlanGrossAmountAndGrossSetupFeeTotal(),
            '%COUPON_CODE%'             => $this->transactionData->getCouponCode(),
            '%CUSTOMER_TAX_ID%'         => $this->transactionData->getCustomerTaxId(),
        );

        $this->rawKeyValuePairs = array_merge( $this->rawKeyValuePairs, $keyValuePairs );
    }

    protected function initDecoratedKeyValuePairs() {
        parent::initDecoratedKeyValuePairs();

        $stripePlanCurrency = $this->transactionData->getPlanCurrency();

        $keyValuePairs = array(
            '%SETUP_FEE%'               => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeGrossAmount() ),
            '%SETUP_FEE_NET%'           => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeNetAmount() ),
            '%SETUP_FEE_GROSS%'         => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeGrossAmount() ),
            '%SETUP_FEE_VAT%'           => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeTaxAmount() ),
            '%SETUP_FEE_TOTAL%'         => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeGrossAmountTotal() ),
            '%SETUP_FEE_NET_TOTAL%'     => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeNetAmountTotal() ),
            '%SETUP_FEE_GROSS_TOTAL%'   => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeGrossAmountTotal() ),
            '%SETUP_FEE_VAT_TOTAL%'     => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getSetupFeeTaxAmountTotal() ),
            '%PLAN_NAME%'               => $this->transactionData->getPlanName(),
            '%PLAN_AMOUNT%'             => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanGrossAmount() ),
            '%PLAN_AMOUNT_NET%'         => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanNetAmount() ),
            '%PLAN_AMOUNT_GROSS%'       => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanGrossAmount() ),
            '%PLAN_AMOUNT_VAT%'         => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanTaxAmount() ),
            '%PLAN_QUANTITY%'           => $this->transactionData->getPlanQuantity(),
            '%PLAN_AMOUNT_TOTAL%'       => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanGrossAmountTotal() ),
            '%PLAN_AMOUNT_NET_TOTAL%'   => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanNetAmountTotal() ),
            '%PLAN_AMOUNT_GROSS_TOTAL%' => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanGrossAmountTotal() ),
            '%PLAN_AMOUNT_VAT_TOTAL%'   => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanTaxAmountTotal() ),
            '%INVOICE_URL%'             => $this->transactionData->getInvoiceUrl(),
            '%INVOICE_NUMBER%'          => $this->transactionData->getInvoiceNumber(),
            '%RECEIPT_URL%'             => $this->transactionData->getReceiptUrl(),
            '%AMOUNT%'                  => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $stripePlanCurrency, $this->transactionData->getPlanGrossAmountAndGrossSetupFeeTotal() ),
            '%COUPON_CODE%'             => $this->transactionData->getCouponCode(),
            '%CUSTOMER_TAX_ID%'         => $this->transactionData->getCustomerTaxId(),
        );

        $this->decoratedKeyValuePairs = array_merge( $this->decoratedKeyValuePairs, $keyValuePairs );
    }
}

class MM_WPFS_ThankYouPostProcessorFactory {
    public static function create( $database, $transactionData) {
        $processor = null;

        if ($transactionData instanceof MM_WPFS_SubscriptionTransactionData) {
            $processor = new MM_WPFS_SubscriptionThankYouPostProcessor( $database, $transactionData);
        } else if ($transactionData instanceof MM_WPFS_DonationTransactionData) {
            $processor = new MM_WPFS_DonationThankYouPostProcessor( $database, $transactionData);
        } else if ($transactionData instanceof MM_WPFS_OneTimePaymentTransactionData) {
            $processor = new MM_WPFS_OneTimePaymentThankYouPostProcessor( $database, $transactionData);
        } else if ($transactionData instanceof MM_WPFS_SaveCardTransactionData) {
            $processor = new MM_WPFS_SaveCardThankYouPostProcessor( $database, $transactionData);
        } else {
            throw new Exception("Unknown thank you postprocessor class: " . get_class($transactionData));
        }

        return $processor;
    }
}

abstract class MM_WPFS_ThankYouPostProcessor {
    /* @var $database MM_WPFS_Database */
    protected $database;
    /* @var $form array */
    protected $form;
    /* @var $transactionData MM_WPFS_FormTransactionData */
    protected $transactionData;
    /* @var $replacer MM_WPFS_FormMacroReplacer */
    protected $replacer;

    public function __construct($database, $transactionData) {
        $this->database         = $database;
        $this->transactionData = $transactionData;
    }

    /**
     * Returns a form from database identified by a name.
     *
     * @param $formName
     *
     * @return mixed|null
     */
    function getFormByName( $formName ) {
        $form = null;

        if ( is_null( $form ) ) {
            $form = $this->database->getInlinePaymentFormByName( $formName );
        }
        if ( is_null( $form ) ) {
            $form = $this->database->getInlineSubscriptionFormByName( $formName );
        }
        if ( is_null( $form ) ) {
            $form = $this->database->getCheckoutPaymentFormByName( $formName );
        }
        if ( is_null( $form ) ) {
            $form = $this->database->getCheckoutSubscriptionFormByName( $formName );
        }
        if ( is_null( $form ) ) {
            $form = $this->database->getInlineDonationFormByName( $formName );
        }
        if ( is_null( $form ) ) {
            $form = $this->database->getCheckoutDonationFormByName( $formName );
        }

        return $form;
    }

    public function process( $content) {
        $params = array(
            'formType'                => $this->getFormType(),
            'rawPlaceholders'         => $this->replacer->getRawKeyValuePairs(),
            'decoratedPlaceholders'   => $this->replacer->getDecoratedKeyValuePairs()
        );

        $content = apply_filters( 'fullstripe_thank_you_output', $content, $params );
        $result = $this->replacer->replaceMacrosWithHtmlEscape( $content );

        return $result;
    }

    abstract protected function getFormType();
}

class MM_WPFS_SubscriptionThankYouPostProcessor extends MM_WPFS_ThankYouPostProcessor {
    public function __construct( $database, $transactionData ) {
        parent::__construct( $database, $transactionData );

        $this->form = $this->getFormByName( $this->transactionData->getFormName() );
        $this->replacer = new MM_WPFS_SubscriptionMacroReplacer( $this->form, $transactionData );
    }

    protected function getFormType() {
        return MM_WPFS::FORM_TYPE_SUBSCRIPTION;
    }
}

class MM_WPFS_DonationThankYouPostProcessor extends MM_WPFS_ThankYouPostProcessor {
    public function __construct( $database, $transactionData ) {
        parent::__construct( $database, $transactionData );

        $this->form = $this->getFormByName( $this->transactionData->getFormName() );
        $this->replacer = new MM_WPFS_DonationMacroReplacer( $this->form, $transactionData );
    }

    protected function getFormType() {
        return MM_WPFS::FORM_TYPE_DONATION;
    }
}

class MM_WPFS_OneTimePaymentThankYouPostProcessor extends MM_WPFS_ThankYouPostProcessor {
    public function __construct( $database, $transactionData ) {
        parent::__construct( $database, $transactionData );

        $this->form = $this->getFormByName( $this->transactionData->getFormName() );
        $this->replacer = new MM_WPFS_OneTimePaymentMacroReplacer( $this->form, $transactionData );
    }

    protected function getFormType() {
        return MM_WPFS::FORM_TYPE_PAYMENT;
    }
}

class MM_WPFS_SaveCardThankYouPostProcessor extends MM_WPFS_ThankYouPostProcessor {
    public function __construct( $database, $transactionData ) {
        parent::__construct( $database, $transactionData );

        $this->form = $this->getFormByName( $this->transactionData->getFormName() );
        $this->replacer = new MM_WPFS_SaveCardMacroReplacer( $this->form, $transactionData );
    }

    protected function getFormType() {
        return MM_WPFS::FORM_TYPE_SAVE_CARD;
    }
}


/**
 * Class WPFS_UserFriendlyException
 *
 * This exception can be thrown in action and event hooks, and it's content (title, message)
 * will be displayed as a global message above the form which has invoked it.
 */
class WPFS_UserFriendlyException extends Exception {
    protected $_title;

    /**
     * WPFS_UserFriendlyException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct( $message, $code, $previous );
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->_title = $title;
    }
}


MM_WPFS::getInstance();
