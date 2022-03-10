jQuery.noConflict();
(function ($) {

    "use strict";

    $(function () {

            const FORM_TYPE_INLINE_PAYMENT = 'inline_payment';
            const FORM_TYPE_CHECKOUT_PAYMENT = 'checkout_payment';
            const FORM_TYPE_INLINE_SUBSCRIPTION = 'inline_subscription';
            const FORM_TYPE_CHECKOUT_SUBSCRIPTION = 'checkout_subscription';
            const FORM_TYPE_INLINE_SAVE_CARD = 'inline_save_card';
            const FORM_TYPE_CHECKOUT_SAVE_CARD = 'checkout_save_card';
            const FORM_TYPE_INLINE_DONATION = 'inline_donation';
            const FORM_TYPE_CHECKOUT_DONATION = 'checkout_donation';

            const PAYMENT_TYPE_LIST_OF_AMOUNTS = 'list_of_amounts';
            const PAYMENT_TYPE_CUSTOM_AMOUNT = 'custom_amount';
            const PAYMENT_TYPE_SPECIFIED_AMOUNT = 'specified_amount';
            const PAYMENT_TYPE_CARD_CAPTURE = 'card_capture';
            const AMOUNT_OTHER = 'other';

            const FIELD_DESCRIPTOR_MACRO_FIELD_ID = '{fieldId}';
            const MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT = '{{amount}}';
            const ERROR_MESSAGE_FIELD_CLASS = 'wpfs-form-error-message';

            const AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS = 'radio-buttons';
            const AMOUNT_SELECTOR_STYLE_DROPDOWN = 'dropdown';
            const AMOUNT_SELECTOR_STYLE_BUTTON_GROUP = 'button-group';

            const COUNTRY_CODE_UNITED_STATES = 'US';

            const PAYMENT_DETAIL_ROW_SETUP_FEE  = 'setupFee';
            const PAYMENT_DETAIL_ROW_PRODUCT    = 'product';
            const PAYMENT_DETAIL_ROW_DISCOUNT   = 'discount';
            const PAYMENT_DETAIL_ROW_TAX        = 'tax';
            const PAYMENT_DETAIL_ROW_TAX_0      = 'tax-0';
            const PAYMENT_DETAIL_ROW_TAX_1      = 'tax-1';
            const PAYMENT_DETAIL_ROW_TOTAL      = 'total';

            const SERVER_ACTION_CALCULATE_PRICING = 'wpfs-calculate-pricing';

            const TAX_RATE_TYPE_NO_TAX = 'taxRateNoTax';
            const TAX_RATE_TYPE_FIXED = 'taxRateFixed';
            const TAX_RATE_TYPE_DYNAMIC = 'taxRateDynamic';

            var debugLog = false;

            //noinspection JSUnresolvedVariable
            var stripe = Stripe(wpfsFormSettings.stripeKey);

            var reCAPTCHAWidgetIds = [];
            var googleReCAPTCHA = null;

            function createCurrencyFormatter($form) {
                if ($form) {
                    var decimalSeparator = $form.data('wpfs-decimal-separator');
                    var showCurrencySymbolInsteadOfCode = $form.data('wpfs-show-currency-symbol-instead-of-code');
                    var showCurrencySignAtFirstPosition = $form.data('wpfs-show-currency-sign-at-first-position');
                    var putWhitespaceBetweenCurrencyAndAmount = $form.data('wpfs-put-whitespace-between-currency-and-amount');

                    if (debugLog) {
                        logInfo('createCurrencyFormatter', 'form=' + $form.data('wpfs-form-id'));
                        logInfo('createCurrencyFormatter', 'decimalSeparator=' + JSON.stringify(decimalSeparator));
                        logInfo('createCurrencyFormatter', 'showCurrencySymbolInsteadOfCode=' + JSON.stringify(showCurrencySymbolInsteadOfCode));
                        logInfo('createCurrencyFormatter', 'showCurrencySignAtFirstPosition=' + JSON.stringify(showCurrencySignAtFirstPosition));
                        logInfo('createCurrencyFormatter', 'putWhitespaceBetweenCurrencyAndAmount=' + JSON.stringify(putWhitespaceBetweenCurrencyAndAmount));
                    }
                    return WPFSCurrencyFormatter(
                        decimalSeparator,
                        showCurrencySymbolInsteadOfCode,
                        showCurrencySignAtFirstPosition,
                        putWhitespaceBetweenCurrencyAndAmount
                    );
                }
                logWarn('createCurrencyFormatter', '$form is null');
                return null;
            }

            /**
             *
             * @param currency
             * @param amount
             * @param coupon
             * @returns {{amountCurrency: *, amount: *, couponCurrency: (OPTIONS.currency|{importance, type, default}|*|Array), discount: number, discountType: *, total: number, error: *}}
             */
            function applyCoupon(currency, amount, coupon) {
                // console.log('applyCoupon(): CALLED, currency=' + currency + ', amount=' + amount + ', coupon=' + JSON.stringify(coupon));
                var discount = 0;
                var discountType = null;
                var error = null;
                if (coupon != null) {
                    //noinspection JSUnresolvedVariable
                    if (coupon.hasOwnProperty('percent_off') && coupon.percent_off != null) {
                        discountType = 'percent_off';
                        //noinspection JSUnresolvedVariable
                        var percentOff = parseInt(coupon.percent_off) / 100;
                        discount = Math.round(amount * percentOff);
                    } else {
                        //noinspection JSUnresolvedVariable
                        if (coupon.hasOwnProperty('amount_off') && coupon.amount_off != null) {
                            discountType = 'amount_off';
                            if (coupon.hasOwnProperty('currency')) {
                                if (coupon.currency == currency) {
                                    //noinspection JSUnresolvedVariable
                                    discount = parseInt(coupon.amount_off);
                                } else {
                                    error = 'currency mismatch';
                                }
                            } else {
                                error = 'currency mismatch';
                            }
                        } else {
                            error = 'invalid coupon';
                        }
                    }
                }
                var amountAsInteger = parseInt(amount);
                var total = amountAsInteger - discount;

                var result = {
                    amountCurrency: currency,
                    amount: amountAsInteger,
                    couponCurrency: (coupon != null && coupon.hasOwnProperty('currency') ? coupon.currency : null),
                    discount: discount,
                    discountType: discountType,
                    discountPercentOff: (coupon != null && coupon.hasOwnProperty('percent_off') ? coupon.percent_off : null),
                    total: total,
                    error: error
                };

                // console.log('applyCoupon(): result=' + JSON.stringify(result));

                return result;
            }

            function resetCaptcha($form) {
                if ($form) {
                    var formHash = $form.data('wpfs-form-hash');
                    if (formHash) {
                        if (googleReCAPTCHA != null) {
                            if (reCAPTCHAWidgetIds[formHash] !== "undefined") {
                                googleReCAPTCHA.reset(reCAPTCHAWidgetIds[formHash]);
                            }
                        }
                    }
                }
            }

            function getParentForm(element) {
                return $(element).parents('form:first');
            }

            function isInViewport($anElement) {
                var $window = $(window);

                //noinspection JSValidateTypes
                var viewPortTop = $window.scrollTop();
                var viewPortBottom = viewPortTop + $window.height();

                var elementTop = $anElement.offset().top;
                var elementBottom = elementTop + $anElement.outerHeight();

                if (debugLog) {
                    console.log('isInViewport(): elementBottom=' + elementBottom + ', viewPortBottom=' + viewPortBottom + ', elementTop=' + elementTop + ', viewPortTop=' + viewPortTop);
                }

                return ((elementBottom <= viewPortBottom) && (elementTop >= viewPortTop));
            }

            function getGlobalMessageContainerTitle($messageContainer, title) {
                var $messageContainerTitle = $('.wpfs-form-message-title', $messageContainer);
                if (0 == $messageContainerTitle.length) {
                    $('<div>', {class: 'wpfs-form-message-title'}).prependTo($messageContainer);
                    $messageContainerTitle = $('.wpfs-form-message-title', $messageContainer);
                }
                $messageContainerTitle.html(title);
                return $messageContainerTitle;
            }

            function getGlobalMessageContainer($form, message) {
                var $messageContainer = $('.wpfs-form-message', $form);
                if (0 == $messageContainer.length) {
                    $('<div>', {class: 'wpfs-form-message'}).prependTo($form);
                    $messageContainer = $('.wpfs-form-message', $form);
                }
                $messageContainer.html(message);
                return $messageContainer;
            }

            function scrollToElement($anElement, fade) {
                if ($anElement && $anElement.offset() && $anElement.offset().top) {
                    if (!isInViewport($anElement)) {
                        $('html, body').animate({
                            scrollTop: $anElement.offset().top - 100
                        }, 1000);
                    }
                }
                if ($anElement && fade) {
                    $anElement.fadeIn(500).fadeOut(500).fadeIn(500);
                }
            }

            function clearGlobalMessage($form) {
                var $messageContainer = getGlobalMessageContainer($form, '');
                $messageContainer.remove();
            }

            function __showGlobalMessage($form, messageTitle, message) {
                var $globalMessageContainer = getGlobalMessageContainer($form, message);
                getGlobalMessageContainerTitle($globalMessageContainer, messageTitle);
                return $globalMessageContainer;
            }

            function showSuccessGlobalMessage($form, messageTitle, message) {
                var $globalMessageContainer = __showGlobalMessage($form, messageTitle, message);
                $globalMessageContainer.addClass('wpfs-form-message--correct');
                scrollToElement($globalMessageContainer, false);
            }

            function showErrorGlobalMessage($form, messageTitle, message) {
                var $globalMessageContainer = __showGlobalMessage($form, messageTitle, message);
                $globalMessageContainer.addClass('wpfs-form-message--incorrect');
                scrollToElement($globalMessageContainer, false);
            }

            function clearFieldErrors($form) {
                $('.wpfs-form-error-message', $form).remove();
                $('.wpfs-form-control', $form).removeClass('wpfs-form-control--error');
                $('.wpfs-input-group', $form).removeClass('wpfs-input-group--error');
                $('.wpfs-form-control--error', $form).removeClass('wpfs-form-control--error');
                $('.wpfs-form-check-input--error', $form).removeClass('wpfs-form-check-input--error');
            }

            function clearFieldError($form, fieldName, fieldId) {
                var formType = $form.data('wpfs-form-type');
                var fieldDescriptor = getFieldDescriptor(formType, fieldName);
                if (debugLog) {
                    logInfo('clearFieldError', 'fieldName=' + fieldName + ', fieldId=' + fieldId);
                    logInfo('clearFieldError', JSON.stringify(fieldDescriptor));
                }
                if (fieldDescriptor != null) {

                    // tnagy read field descriptor
                    var fieldType = fieldDescriptor.type;
                    var fieldClass = fieldDescriptor.class;
                    var fieldSelector = fieldDescriptor.selector;
                    var fieldErrorClass = fieldDescriptor.errorClass;
                    var fieldErrorSelector = fieldDescriptor.errorSelector;

                    // tnagy initialize field
                    var theFieldSelector;
                    if (fieldId != null) {
                        theFieldSelector = '#' + fieldId;
                    } else {
                        theFieldSelector = fieldSelector;
                    }
                    var $field = $(theFieldSelector, $form);

                    // tnagy remove error class, remove error message
                    var errorMessageFieldSelector = '.' + ERROR_MESSAGE_FIELD_CLASS;
                    if ('input' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('input-group' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(fieldErrorSelector).siblings(errorMessageFieldSelector).remove();
                    } else if ('input-custom' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('select-menu' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest('.' + fieldClass).removeClass(fieldErrorClass);
                            $(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('checkbox' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('card' === fieldType) {
                        if (fieldErrorSelector != null) {
                            var $cardContainer = $('.' + fieldClass, $form);
                            $cardContainer.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    }
                } else {
                    logInfo('showFieldError', 'FieldDescription not found!');
                }
            }

            function getFieldDescriptor(formType, fieldName) {
                var fieldDescriptor = null;
                if (FORM_TYPE_INLINE_PAYMENT === formType && wpfsFormSettings.formFields.hasOwnProperty('inlinePayment')) {
                    var inlinePaymentFormFields = wpfsFormSettings.formFields.inlinePayment;
                    if (inlinePaymentFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlinePaymentFormFields[fieldName];
                    }
                } else if (FORM_TYPE_INLINE_SUBSCRIPTION === formType && wpfsFormSettings.formFields.hasOwnProperty('inlineSubscription')) {
                    var inlineSubscriptionFormFields = wpfsFormSettings.formFields.inlineSubscription;
                    if (inlineSubscriptionFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlineSubscriptionFormFields[fieldName];
                    }
                } else if (FORM_TYPE_CHECKOUT_PAYMENT === formType && wpfsFormSettings.formFields.hasOwnProperty('checkoutPayment')) {
                    var popupPaymentFormFields = wpfsFormSettings.formFields.checkoutPayment;
                    if (popupPaymentFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupPaymentFormFields[fieldName];
                    }
                } else if (FORM_TYPE_CHECKOUT_SUBSCRIPTION === formType && wpfsFormSettings.formFields.hasOwnProperty('checkoutSubscription')) {
                    var popupSubscriptionFormFields = wpfsFormSettings.formFields.checkoutSubscription;
                    if (popupSubscriptionFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupSubscriptionFormFields[fieldName];
                    }
                } else if (FORM_TYPE_INLINE_SAVE_CARD === formType && wpfsFormSettings.formFields.hasOwnProperty('inlineSaveCard')) {
                    var inlineCardCaptureFormFields = wpfsFormSettings.formFields.inlineSaveCard;
                    if (inlineCardCaptureFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlineCardCaptureFormFields[fieldName];
                    }
                } else if (FORM_TYPE_CHECKOUT_SAVE_CARD === formType && wpfsFormSettings.formFields.hasOwnProperty('checkoutSaveCard')) {
                    var popupCardCaptureFormFields = wpfsFormSettings.formFields.checkoutSaveCard;
                    if (popupCardCaptureFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupCardCaptureFormFields[fieldName];
                    }
                } else if (FORM_TYPE_INLINE_DONATION === formType && wpfsFormSettings.formFields.hasOwnProperty('inlineDonation')) {
                    var inlineDonationFormFields = wpfsFormSettings.formFields.inlineDonation;
                    if (inlineDonationFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlineDonationFormFields[fieldName];
                    }
                } else if (FORM_TYPE_CHECKOUT_DONATION === formType && wpfsFormSettings.formFields.hasOwnProperty('checkoutDonation')) {
                    var popupDonationFormFields = wpfsFormSettings.formFields.checkoutDonation;
                    if (popupDonationFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupDonationFormFields[fieldName];
                    }
                }
                return fieldDescriptor;
            }

            function showFormError($form, fieldName, fieldId, errorTitle, errorMessage, scrollTo) {
                var formType = $form.data('wpfs-form-type');
                var fieldDescriptor = getFieldDescriptor(formType, fieldName);
                if (fieldDescriptor != null) {
                    var fieldIsHidden = fieldDescriptor.hidden;
                    if (true == fieldIsHidden) {
                        showErrorGlobalMessage($form, errorTitle, errorMessage);
                    } else {
                        showFieldError($form, fieldName, fieldId, errorMessage, scrollTo);
                    }
                    if (fieldName.startsWith('wpfs-billing')) {
                        $('.wpfs-billing-address-switch', $form).click();
                    }
                    if (fieldName.startsWith('wpfs-shipping')) {
                        $('.wpfs-shipping-address-switch', $form).click();
                    }
                }
            }

            function showFieldError($form, fieldName, fieldId, fieldErrorMessage, scrollTo) {
                var formType = $form.data('wpfs-form-type');
                var fieldDescriptor = getFieldDescriptor(formType, fieldName);
                if (debugLog) {
                    logInfo('showFieldError', 'fieldName=' + fieldName + ', fieldId=' + fieldId + ', fieldErrorMessage=' + fieldErrorMessage);
                    logInfo('showFieldError', JSON.stringify(fieldDescriptor));
                    logInfo('showFieldError', fieldErrorMessage);
                }
                if (fieldDescriptor != null) {

                    // tnagy read field descriptor
                    var fieldType = fieldDescriptor.type;
                    var fieldClass = fieldDescriptor.class;
                    var fieldSelector = fieldDescriptor.selector;
                    var fieldErrorClass = fieldDescriptor.errorClass;
                    var fieldErrorSelector = fieldDescriptor.errorSelector;

                    // tnagy initialize field
                    var theFieldSelector;
                    if (fieldId != null) {
                        theFieldSelector = '#' + fieldId;
                    } else {
                        theFieldSelector = fieldSelector;
                    }
                    var $field = $(theFieldSelector, $form);

                    // tnagy create error message
                    var $fieldError = $('<div>', {
                        class: ERROR_MESSAGE_FIELD_CLASS,
                        'data-wpfs-field-error-for': fieldId
                    }).html(fieldErrorMessage);

                    // tnagy add error class, insert error message
                    if ('input' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.insertAfter($field);
                    } else if ('input-group' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.insertAfter($field.closest(fieldErrorSelector));
                    } else if ('input-custom' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.insertAfter($field);
                    } else if ('dropdown' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest('.' + fieldClass).addClass(fieldErrorClass);
                            $(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.appendTo($field.parent());
                    } else if ('checkbox' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.appendTo($field.parent());
                    } else if ('card' === fieldType) {
                        if (fieldErrorSelector != null) {
                            var $cardContainer = $('.' + fieldClass, $form);
                            $cardContainer.closest(fieldErrorSelector).addClass(fieldErrorClass);
                            $fieldError.insertAfter($cardContainer);
                        }
                    } else if ('captcha' === fieldType) {
                        if (fieldErrorSelector != null) {
                            var $captchaContainer = $('.' + fieldClass, $form);
                            $captchaContainer.closest(fieldErrorSelector).addClass(fieldErrorClass);
                            $fieldError.insertAfter($captchaContainer);
                        }
                    }

                    if (typeof scrollTo != "undefined") {
                        if (scrollTo) {
                            scrollToElement($field, false);
                        }
                    }
                } else {
                    logInfo('showFieldError', 'FieldDescription not found!');
                }
            }

            function processValidationErrors($form, data) {
                var formId = $form.data('wpfs-form-id');
                var formHash = $form.data('wpfs-form-hash');
                var hasErrors = false;
                if (data && data.bindingResult) {
                    if (data.bindingResult.fieldErrors && data.bindingResult.fieldErrors.errors) {
                        var fieldErrors = data.bindingResult.fieldErrors.errors;
                        for (var index in fieldErrors) {
                            var fieldError = fieldErrors[index];
                            var fieldId = fieldError.id;
                            var fieldName = fieldError.name;
                            var fieldErrorMessage = fieldError.message;
                            showFormError($form, fieldName, fieldId, data.bindingResult.fieldErrors.title, fieldErrorMessage);
                            if (!hasErrors) {
                                hasErrors = true;
                            }
                        }
                        var firstErrorFieldId = $('.wpfs-form-error-message', $form).first().data('wpfs-field-error-for');
                        if (firstErrorFieldId) {
                            scrollToElement($('#' + firstErrorFieldId, $form), false);
                        }
                    }
                    if (data.bindingResult.globalErrors) {
                        var globalErrorMessages = '';
                        for (var i = 0; i < data.bindingResult.globalErrors.errors.length; i++) {
                            globalErrorMessages += data.bindingResult.globalErrors.errors[i] + '<br/>';
                        }
                        if ('' !== globalErrorMessages) {
                            showErrorGlobalMessage($form, data.bindingResult.globalErrors.title, globalErrorMessages);
                            if (!hasErrors) {
                                hasErrors = true;
                            }
                        }
                    }
                } else {
                    showErrorGlobalMessage($form, data.messageTitle, data.message);
                    logResponseException('WPFS form=' + formId, data);
                    hasErrors = true;
                }
                if (hasErrors) {
                    resetCaptcha($form);
                }
            }

            function clearAndShowAndEnableAndFocusCustomAmount($form) {
                $('div[data-wpfs-amount-row=custom-amount]', $form).show();
                $('input[name=wpfs-custom-amount-unique]', $form).val('').prop('disabled', false).focus();
            }

            function clearAndHideAndDisableCustomAmount($form) {
                $('div[data-wpfs-amount-row="custom-amount"]', $form).hide();
                $('input[name="wpfs-custom-amount-unique"]', $form).val('').prop('disabled', true);
            }

            function updateButtonCaption($form, buttonTitle, currencyCode, currencySymbol, amount, zeroDecimalSupport) {
                if (debugLog) {
                    console.log('updateButtonCaption(): params=' + JSON.stringify([currencyCode, currencySymbol, amount, zeroDecimalSupport]));
                    var formId = $form.data('wpfs-form-id');
                    console.log('updateButtonCaption(): formId=' + formId);
                }

                var formatter = createCurrencyFormatter($form);
                var captionPattern;
                var caption;
                var amountMacroRegExp = new RegExp(MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT, "g");
                if (currencySymbol == null || amount == null) {
                    caption = buttonTitle;
                    if (caption.indexOf(MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT) !== -1) {
                        caption = caption.replace(amountMacroRegExp, "").trim();
                    }
                } else {
                    var amountPart = formatter.format(amount, currencyCode, currencySymbol, zeroDecimalSupport);
                    if (buttonTitle.indexOf(MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT) !== -1) {
                        captionPattern = buttonTitle.replace(amountMacroRegExp, "%s").trim();
                        var captionPatternParams = [];

                        captionPatternParams.push(amountPart);
                        caption = vsprintf(captionPattern, captionPatternParams);
                    } else {
                        caption = buttonTitle;
                    }

                }
                $form.find('button[type=submit]').html(caption);
            }

            function disableFormButtons($form) {
                $form.find('button').prop('disabled', true);
            }

            function enableFormButtons($form) {
                $form.find('button').prop('disabled', false);
            }

            function disableFormInputsSelectsButtons($form) {
                if (debugLog) {
                    console.log('disableFormInputsSelectsButtons(): CALLED');
                }
                $form.find('input:not(.wpfs-custom-amount), select:not([data-wpfs-select]), button').prop('disabled', true);
            }

            function enableFormInputsSelectsButtons($form) {
                if (debugLog) {
                    console.log('enableFormInputsSelectsButtons(): CALLED');
                }
                $form.find('input, select:not([data-wpfs-select]), button').prop('disabled', false);
            }

            function resetTextFields($form) {
                $form.find('input[type=text], input[type=password], input[type=email]').val('');
            }

            function resetStepperFields($form) {
                $form.find('input[data-toggle="stepper"]').each(function () {
                    var defaultValue = 1;
                    if (typeof $(this).data('default-value') !== 'undefined') {
                        defaultValue = $(this).data('default-value');
                    }
                    $(this).val(defaultValue);
                });
            }

            function resetSelectFields($form) {
                $form.find('select').prop('selectedIndex', 0).change();
                $form.find('[data-toggle="selectmenu"]').wpfsSelectmenu('refresh');
            }

            function resetCheckboxesAndRadioButtons($form) {
                $form.find('input[type=radio], input[type=checkbox]').prop('checked', false);
            }

            function resetCardField(card) {
                if (card != null) {
                    card.clear();
                }
            }

            function resetCouponField($form, formId) {
                var coupon = WPFS.getCoupon(formId);
                if (coupon != null) {
                    WPFS.removeCoupon(formId);
                    hideRedeemedCouponRow($form);
                    showCouponToRedeemRow($form);
                }
            }

            function resetBillingAndShippingAddressSelector($form) {
                $('.wpfs-same-billing-and-shipping-address', $form).prop('checked', true).change();
            }

            function selectFirstCustomAmount($form) {
                $('.wpfs-custom-amount', $form).first().click();
            }

            function selectFirstPlanFromDropdown($form) {
                var $planSelectElements = $form.find('select.wpfs-subscription-plan-select');
                $planSelectElements.each(function () {
                    if ($('option:selected', $(this)).length) {
                        $(this).val($('option:selected', $(this)).val());
                    } else {
                        $(this).val($('option:first', $(this)).val());
                    }
                });
            }

            function selectFirstPlanFromButtonGroup($form) {
                if ($('input:radio[name="wpfs-plan"]:not(:disabled):checked', $form).length === 0) {
                    $('input:radio[name="wpfs-plan"]:not(:disabled):first', $form).prop('checked', true);
                }
            }

            function selectFirstDonationFrequency($form) {
                if ($('input:radio[name="wpfs-donation-frequency"]:not(:disabled):checked', $form).length === 0) {
                    $('input:radio[name="wpfs-donation-frequency"]:not(:disabled):first', $form).prop('checked', true);
                }
            }

            function isSubscriptionForm($form) {
                var formType = $form.data('wpfs-form-type');
                return FORM_TYPE_INLINE_SUBSCRIPTION === formType || FORM_TYPE_CHECKOUT_SUBSCRIPTION === formType;
            }

            function isPaymentForm($form) {
                var formType = $form.data('wpfs-form-type');
                return FORM_TYPE_INLINE_PAYMENT === formType || FORM_TYPE_CHECKOUT_PAYMENT === formType;
            }

            function selectFirstSubscriptionPlan($form) {
                selectFirstPlanFromDropdown($form);
                selectFirstPlanFromButtonGroup($form);

                handlePlanChange($form);
            }

            function resetFormFields($form, card) {
                var formId = $form.data('wpfs-form-id');
                var formHash = $form.data('wpfs-form-hash');

                resetTextFields($form);
                resetStepperFields($form);
                resetSelectFields($form)
                resetCheckboxesAndRadioButtons($form);
                resetCardField(card);
                resetCouponField($form, formId);
                resetCaptcha($form);
                resetBillingAndShippingAddressSelector($form);
                removeHiddenFormFields($form);

                selectFirstCustomAmount($form);
                selectFirstSubscriptionPlan($form);
                selectFirstDonationFrequency($form)
            }

            function removeCustomAmountIndexInput($form) {
                $form.find('input[name="wpfs-custom-amount-index"]').remove();
            }

            function removeHiddenFormFields($form) {
                removePaymentMethodIdInput($form);
                removePaymentIntentIdInput($form);
                removeSetupIntentIdInput($form);
                removeSubscriptionIdInput($form);
                removeCustomAmountIndexInput($form);
                removeWPFSNonceInput($form);
            }

            function showLoadingAnimation($form) {
                $form.find('button[type=submit]').addClass('wpfs-btn-primary--loader');
            }

            function hideLoadingAnimation($form) {
                $form.find('button[type=submit]').removeClass('wpfs-btn-primary--loader');
            }

            function clearPaymentDetails($form) {
                var rowTypes = [
                    PAYMENT_DETAIL_ROW_SETUP_FEE,
                    PAYMENT_DETAIL_ROW_PRODUCT,
                    PAYMENT_DETAIL_ROW_DISCOUNT,
                    PAYMENT_DETAIL_ROW_TAX_0,
                    PAYMENT_DETAIL_ROW_TAX_1,
                    PAYMENT_DETAIL_ROW_TOTAL,
                ];

                rowTypes.forEach( (rowType) => {
                    $('tr[data-wpfs-summary-row="' + rowType  +'"]', $form).hide();
                });
                $('.wpfs-summary-description', $form).hide();
            }

            function addPaymentMethodIdInput($form, result) {
                if (typeof (result) !== 'undefined' && result.hasOwnProperty('paymentMethod') && result.paymentMethod.hasOwnProperty('id')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-stripe-payment-method-id',
                        value: result.paymentMethod.id
                    }).appendTo($form);
                }
            }

            function addPaymentIntentIdInput($form, result) {
                if (typeof (result) !== 'undefined' && result.hasOwnProperty('paymentIntent') && result.paymentIntent.hasOwnProperty('id')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-stripe-payment-intent-id',
                        value: result.paymentIntent.id
                    }).appendTo($form);
                }
            }

            function addSetupIntentIdInput($form, result) {
                if (typeof (result) !== 'undefined' && result.hasOwnProperty('setupIntent') && result.setupIntent.hasOwnProperty('id')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-stripe-setup-intent-id',
                        value: result.setupIntent.id
                    }).appendTo($form);
                }
            }

            function addWPFSNonceInput($form, data) {
                if (typeof (data) !== 'undefined' && data.hasOwnProperty('nonce')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-nonce',
                        value: data.nonce
                    }).appendTo($form);
                }
            }

            function removePaymentMethodIdInput($form) {
                $('input[name="wpfs-stripe-payment-method-id"]', $form).remove();
            }

            function removeWPFSNonceInput($form) {
                $('input[name="wpfs-nonce"]', $form).remove();
            }

            function removePaymentIntentIdInput($form) {
                $('input[name="wpfs-stripe-payment-intent-id"]', $form).remove();
            }

            function removeSetupIntentIdInput($form) {
                $('input[name="wpfs-stripe-setup-intent-id"]', $form).remove();
            }

            function removeSubscriptionIdInput($form) {
                $('input[name="wpfs-stripe-subscription-id"]', $form).remove();
            }

            function findListOfAmountsElement($form) {
                var $element = null;
                var amountSelectorStyle = $form.data('wpfs-amount-selector-style');
                if (AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === amountSelectorStyle) {
                    $element = $form.find('input[name="wpfs-custom-amount"]');
                } else if (AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === amountSelectorStyle) {
                    $element = $form.find('input[name="wpfs-custom-amount"]');
                } else if (AMOUNT_SELECTOR_STYLE_DROPDOWN === amountSelectorStyle) {
                    $element = $form.find('select[name="wpfs-custom-amount"]');
                }
                return $element;
            }

            function findCustomAmountElement($form) {
                return $form.find('input[name="wpfs-custom-amount-unique"]');
            }

            function findSelectedAmountFromListOfAmounts($form) {
                var amount = null;
                var amountSelectorStyle = $form.data('wpfs-amount-selector-style');
                if (AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === amountSelectorStyle) {
                    amount = $form.find('input[name="wpfs-custom-amount"]:checked');
                } else if (AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === amountSelectorStyle) {
                    amount = $form.find('input[name="wpfs-custom-amount"]:checked');
                } else if (AMOUNT_SELECTOR_STYLE_DROPDOWN === amountSelectorStyle) {
                    amount = $form.find('select[name="wpfs-custom-amount"] option:selected');
                }

                // This is for finding the custom hidden element if there are no preset amounts
                if (amount == null || amount.length === 0) {
                    amount = $form.find('input[name="wpfs-custom-amount"]');
                }

                return amount;
            }

            function addCustomAmountIndexInput($form) {
                var $selectedAmount = findSelectedAmountFromListOfAmounts($form);
                if ($selectedAmount !== null && $selectedAmount.length > 0) {
                    var amountIndex = $selectedAmount.data('wpfs-amount-index');
                    if (typeof (amountIndex) !== 'undefined') {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'wpfs-custom-amount-index',
                            value: encodeURIComponent(amountIndex)
                        }).appendTo($form);
                    }
                }
            }

            function addCurrentURL($form) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'wpfs-referrer',
                    value: encodeURI(window.location.href)
                }).appendTo($form);
            }

            function setPageParametersField($form) {
                var params = getQueryStringIntoArray();
                var paramsJson = JSON.stringify(params);
                $('input[name="wpfs-form-get-parameters"]', $form).val(encodeURIComponent(paramsJson));
            }

            function submitPaymentData($form, card) {

                if (debugLog) {
                    logInfo('submitPaymentData', 'CALLED');
                }

                clearGlobalMessage($form);
                clearFieldErrors($form);

                $.ajax({
                    type: "POST",
                    url: wpfsFormSettings.ajaxUrl,
                    data: $form.serialize(),
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        if (debugLog) {
                            logInfo('submitPaymentData', 'SUCCESS response=' + JSON.stringify(data));
                        }

                        if (data.nonce) {
                            removeWPFSNonceInput($form);
                            addWPFSNonceInput($form, data);
                        }

                        if (data.success) {

                            if (data.redirect) {
                                window.location = data.redirectURL;
                            } else {
                                // tnagy reset form fields
                                resetFormFields($form, card);

                                // tnagy show success message
                                showSuccessGlobalMessage($form, data.messageTitle, data.message);

                                // tnagy enable submit button
                                enableFormButtons($form);
                            }

                        } else if (data.requiresAction) {
                            handleStripeIntentAction($form, card, data);
                        } else {
                            processValidationErrors($form, data);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('submitPaymentData', jqXHR, textStatus, errorThrown);
                        showErrorGlobalMessage($form, wpfsFormSettings.l10n.validation_errors.internal_error_title, wpfsFormSettings.l10n.validation_errors.internal_error);
                    },
                    complete: function () {
                        enableFormButtons($form);
                        hideLoadingAnimation($form);
                    }
                });

            }

            function handleStripeIntentAction($form, card, data) {
                function handlePaymentIntentAction(result) {
                    if (debugLog) {
                        logInfo('handlePaymentIntentAction', 'result=' + JSON.stringify(result));
                    }
                    if (result.error) {
                        logWarn('handleStripeIntentAction', result.error.message);
                        showErrorGlobalMessage($form, wpfsFormSettings.l10n.stripe_errors.internal_error_title, result.error.message);
                    } else {
                        removePaymentIntentIdInput($form);
                        addPaymentIntentIdInput($form, result);
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        submitPaymentData($form, card);
                    }
                }

                function handleSetupIntentAction(result) {
                    if (debugLog) {
                        logInfo('handleSetupIntentAction', 'result=' + JSON.stringify(result));
                    }
                    if (result.error) {
                        logWarn('handleSetupIntentAction', result.error.message);
                        showErrorGlobalMessage($form, wpfsFormSettings.l10n.stripe_errors.internal_error_title, result.error.message);
                    } else {
                        removeSetupIntentIdInput($form);
                        addSetupIntentIdInput($form, result);
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        submitPaymentData($form, card);
                    }
                }

                function handlePaymentSetupIntentAction(result) {
                    if (debugLog) {
                        logInfo('handlePaymentSetupIntentAction', 'result=' + JSON.stringify(result));
                    }
                    if (result.error) {
                        logWarn('handlePaymentSetupIntentAction', result.error.message);
                        showErrorGlobalMessage($form, wpfsFormSettings.l10n.stripe_errors.internal_error_title, result.error.message);
                    } else {
                        removePaymentIntentIdInput($form);
                        removeSetupIntentIdInput($form);
                        addPaymentIntentIdInput($form, result);
                        addSetupIntentIdInput($form, result);
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        submitPaymentData($form, card);
                    }
                }

                if (debugLog) {
                    logInfo('handleStripeIntentAction', 'CALLED, params: data=' + JSON.stringify(data));
                }
                if (FORM_TYPE_INLINE_PAYMENT === data.formType) {
                    if ( data.isManualConfirmation ) {
                        stripe.handleCardAction(data.paymentIntentClientSecret).then( handlePaymentIntentAction );
                    } else {
                        stripe.confirmCardPayment(data.paymentIntentClientSecret).then( handlePaymentIntentAction );
                    }
                }
                if (FORM_TYPE_INLINE_SUBSCRIPTION === data.formType) {
                    if (data.hasOwnProperty('paymentIntentClientSecret') && data.paymentIntentClientSecret != null) {
                        stripe.handleCardPayment(data.paymentIntentClientSecret).then( handlePaymentSetupIntentAction );
                    } else if (data.hasOwnProperty('setupIntentClientSecret') && data.setupIntentClientSecret != null) {
                        stripe.handleCardSetup(data.setupIntentClientSecret).then( handlePaymentSetupIntentAction );
                    }
                }
                if (FORM_TYPE_INLINE_DONATION === data.formType) {
                    stripe.handleCardAction(data.paymentIntentClientSecret).then( handlePaymentIntentAction);
                }
                if (FORM_TYPE_INLINE_SAVE_CARD === data.formType) {
                    stripe.handleCardSetup(data.setupIntentClientSecret).then( handleSetupIntentAction );
                }
            }

            function handleCustomAmountChange($customAmountSelectElement, $form) {
                var amountType = $form.data('wpfs-amount-type');
                var amountValue = $customAmountSelectElement.val();

                if (PAYMENT_TYPE_LIST_OF_AMOUNTS === amountType ) {
                    if (AMOUNT_OTHER === amountValue) {
                        clearAndShowAndEnableAndFocusCustomAmount($form);
                    } else {
                        clearAndHideAndDisableCustomAmount($form);
                    }
                }
            }

            function parseCustomAmountUnique($form) {
                var result = null;
                var formatter = createCurrencyFormatter($form);
                var $element = $('.wpfs-custom-amount--unique', $form);
                var customAmountValue = $element.val();

                if ( customAmountValue !== '' ) {
                    var zeroDecimalSupport = $element.data('wpfs-zero-decimal-support') === true;

                    result = parseCurrencyAmount(formatter.parse(customAmountValue), zeroDecimalSupport, true);
                }

                return result;
            }

            function gatherFormDataForTaxCalculation( $form ) {
                var data = {};

                if ( $('select[name="wpfs-tax-country"] option:selected', $form).length > 0 ) {
                    data.country = $('select[name="wpfs-tax-country"] option:selected', $form).val();

                    if ( data.country === COUNTRY_CODE_UNITED_STATES &&
                         $('select[name="wpfs-tax-state"] option:selected', $form).length > 0) {
                        data.state = $('select[name="wpfs-tax-state"] option:selected', $form).val()
                    } else {
                        data.state = null;
                    }
                } else if ( $('select[name="wpfs-billing-address-country"] option:selected', $form).length > 0 ) {
                    data.country = $('select[name="wpfs-billing-address-country"] option:selected', $form).val();

                    if ( data.country === COUNTRY_CODE_UNITED_STATES &&
                         $('select[name="wpfs-billing-address-state-select"] option:selected', $form).length > 0) {
                        data.state = $('select[name="wpfs-billing-address-state-select"] option:selected', $form).val()
                    } else {
                        data.state = null;
                    }
                } else {
                    data.country = null;
                    data.state = null;
                }

                if ( $('input[name="wpfs-plan-quantity"]', $form).length > 0 ) {
                    data.quantity = $('input[name="wpfs-plan-quantity"]', $form).spinner('value');
                } else {
                    data.quantity = 1;
                }

                if ( $('input[name="wpfs-buying-as-business"]', $form).prop('checked')) {
                    data.taxId = $('input[name="wpfs-tax-id"]', $form).val();
                } else {
                    data.taxId = null;
                }

                data.formId = extractFormNameFromNode( $form );
                data.formType = extractFormTypeFromNode( $form );

                var coupon = WPFS.getCoupon( data.formId );
                if ( coupon !== null ) {
                    data.coupon = coupon.name;
                } else {
                    data.coupon = null;
                }

                data.customAmount = null;
                if ( $('.wpfs-custom-amount--unique', $form).length > 0 ) {
                    var customAmount = parseCustomAmountUnique($form);

                    if ( !isNaN( customAmount ) ) {
                        data.customAmount = customAmount;
                    }
                }

                return data;
            }

            function refreshShippingStateField( $form, country ) {
                if ( country === COUNTRY_CODE_UNITED_STATES ) {
                    $( 'input[name="wpfs-shipping-address-state"]', $form ).hide();
                    $( '.wpfs-form-select.wpfs-shipping-address-state-select' ).show();
                } else {
                    $( 'input[name="wpfs-shipping-address-state"]', $form ).show();
                    $( '.wpfs-form-select.wpfs-shipping-address-state-select' ).hide();
                }
            }

            function handleShippingAddressCountryChange($shippingAddressSelectElement, $form) {
                if (debugLog) {
                    console.log('handleShippingAddressCountryChange(): CALLED');
                }

                var selectedCountry = $('option:selected', $shippingAddressSelectElement).val();
                refreshShippingStateField( $form, selectedCountry );
            }

            function refreshBillingStateField( $form, country ) {
                if ( country === COUNTRY_CODE_UNITED_STATES ) {
                    $( 'input[name="wpfs-billing-address-state"]', $form ).hide();
                    $( '.wpfs-form-select.wpfs-billing-address-state-select' ).show();
                } else {
                    $( 'input[name="wpfs-billing-address-state"]', $form ).show();
                    $( '.wpfs-form-select.wpfs-billing-address-state-select' ).hide();
                }
            }

            function handleBillingAddressCountryChange($billingAddressSelectElement, $form) {
                if (debugLog) {
                    console.log('handleBillingAddressCountryChange(): CALLED');
                }

                var selectedCountry = $('option:selected', $billingAddressSelectElement).val();
                refreshBillingStateField( $form, selectedCountry );

                if ( isDynamicTaxesForm($form) ) {
                    refreshPricingFromServer( $form );
                }
            }

            function handleBillingAddressStateChange($billingAddressStateSelectElement, $form) {
                if ( isDynamicTaxesForm($form) ) {
                    refreshPricingFromServer( $form );
                }
            }

            function findPlanElementByPlanId($form, planId) {
                var $planElement;
                if ($('.wpfs-subscription-plan-select', $form).length > 0) {
                    $planElement = $('select[name="wpfs-plan"] option[data-wpfs-value="' + planId + '"]', $form);
                } else if ($('.wpfs-subscription-plan-radio', $form).length > 0) {
                    $planElement = $('input[name="wpfs-plan"][data-wpfs-value="' + planId + '"]', $form);
                } else if ($('input[name="wpfs-plan"]', $form).length > 0) {
                    $planElement = $('input[name="wpfs-plan"][data-wpfs-value="' + planId + '"]', $form);
                } else {
                    logError('handlePlanChange', 'WARNING: Subscription Plan Selector UI element not found!');
                }
                return $planElement;
            }

            function handleAmountChange($form) {
                if (debugLog) {
                    console.log('handleAmountChange(): CALLED');
                }
                if (PAYMENT_TYPE_LIST_OF_AMOUNTS === $form.data('wpfs-amount-type')) {
                    handlePaymentListOfAmountsChange($form);
                } else if (PAYMENT_TYPE_CUSTOM_AMOUNT === $form.data('wpfs-amount-type')) {
                    handlePaymentCustomAmountChange($form);
                } else if (PAYMENT_TYPE_SPECIFIED_AMOUNT === $form.data('wpfs-amount-type')) {
                    handlePaymentHiddenChange($form);
                } else {
                    logWarn('handlePaymentChange()', 'Payment Amount UI element not found!');
                }
            }

            function handlePaymentListOfAmountsChange($form) {
                if (debugLog) {
                    console.log('handlePaymentListOfAmountsChange(): CALLED');
                }

                var $selectedAmountElement;

                if ($('.wpfs-custom-amount-select', $form).length > 0) {
                    $selectedAmountElement = $('.wpfs-custom-amount-select option:selected', $form);
                } else if ($('input.wpfs-custom-amount', $form).length > 0) {
                    $selectedAmountElement = $('input.wpfs-custom-amount:checked', $form);
                }
                if (AMOUNT_OTHER === $selectedAmountElement.val()) {
                    $selectedAmountElement = $('input.wpfs-custom-amount--unique', $form);
                }

                handlePaymentAmountChange($form, $selectedAmountElement);
            }

            function handlePaymentCustomAmountChange($form) {
                var $selectedAmountElement = $('.wpfs-custom-amount--unique', $form);
                handlePaymentAmountChange($form, $selectedAmountElement);
            }

            function handlePaymentHiddenChange($form) {
                var $selectedAmountElement = $('.wpfs-custom-amount', $form);
                handlePaymentAmountChange($form, $selectedAmountElement);
            }

            function getTotalAmountForPrice( paymentDetailsConfig ) {
                var total = 0;

                if ( paymentDetailsConfig.hasOwnProperty('total') ) {
                    total = paymentDetailsConfig.total;
                } else {
                    var paymentDetails = WPFS.getPaymentDetailsForPrice( paymentDetailsConfig.formId, paymentDetailsConfig.priceId );

                    if ( paymentDetails !== null ) {
                        paymentDetails.forEach((item) => {
                            total += item.amount;
                        });
                    }
                }

                var amountLabel = formatCurrencyAmount( total, paymentDetailsConfig.zeroDecimalSupport );
                return parseCurrencyAmount( amountLabel, paymentDetailsConfig.zeroDecimalSupport );
            }

            function showPaymentButtonCaption( $form, paymentDetailsConfig ) {
                var total = getTotalAmountForPrice( paymentDetailsConfig );

                if (total === null || isNaN(total)) {
                    updateButtonCaption($form, paymentDetailsConfig.buttonTitle, null, null, null, null);
                } else {
                    updateButtonCaption($form, paymentDetailsConfig.buttonTitle, paymentDetailsConfig.currency, paymentDetailsConfig.currencySymbol, paymentDetailsConfig.zeroDecimalSupport ? total : total.toFixed(2), paymentDetailsConfig.zeroDecimalSupport);
                }
            }

            function renderSubscriptionSummary( interval, intervalCount, cancellationCount ) {
                var subscriptionDetailsLabel = null;

                if (cancellationCount > 0) {
                    if (intervalCount > 1) {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_y_days, [intervalCount, cancellationCount]);
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_y_weeks, [intervalCount, cancellationCount]);
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_y_months, [intervalCount, cancellationCount]);
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_y_years, [intervalCount, cancellationCount]);
                        }
                    } else {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_daily, [cancellationCount]);
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_weekly, [cancellationCount]);
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_monthly, [cancellationCount]);
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.x_times_yearly, [cancellationCount]);
                        }
                    }
                } else {
                    if (intervalCount > 1) {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.y_days, [intervalCount]);
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.y_weeks, [intervalCount]);
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.y_months, [intervalCount]);
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsFormSettings.l10n.subscription_charge_interval_templates.y_years, [intervalCount]);
                        }
                    } else {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = wpfsFormSettings.l10n.subscription_charge_interval_templates.daily;
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = wpfsFormSettings.l10n.subscription_charge_interval_templates.weekly;
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = wpfsFormSettings.l10n.subscription_charge_interval_templates.monthly;
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = wpfsFormSettings.l10n.subscription_charge_interval_templates.yearly;
                        }
                    }
                }

                return subscriptionDetailsLabel;
            }

            function showPaymentDetails( $form, paymentDetailsConfig ) {
                var lineItems = WPFS.getPaymentDetailsForPrice( paymentDetailsConfig.formId, paymentDetailsConfig.priceId );

                renderPaymentDetails( $form, paymentDetailsConfig, lineItems, null );
            }

            function showSubscriptionDetails( $form, paymentDetailsConfig ) {
                var lineItems = WPFS.getPaymentDetailsForPrice( paymentDetailsConfig.formId, paymentDetailsConfig.priceId );
                var subscriptionDetailsLabel = renderSubscriptionSummary( paymentDetailsConfig.interval,paymentDetailsConfig.intervalCount, paymentDetailsConfig.cancellationCount );

                renderPaymentDetails( $form, paymentDetailsConfig, lineItems, subscriptionDetailsLabel );
            }

           function renderPaymentDetails( $form, paymentDetailsConfig, lineItems, summaryLabel ) {
                clearPaymentDetails( $form );

                if ( lineItems !== null ) {
                    var formatter = createCurrencyFormatter($form);

                    var total = 0;
                    var taxIdx = 0;
                    lineItems.forEach((lineItem) => {
                        var amount = formatter.format(
                            formatCurrencyAmount( lineItem.amount, paymentDetailsConfig.zeroDecimalSupport ),
                            paymentDetailsConfig.currency.toUpperCase(),
                            paymentDetailsConfig.currencySymbol,
                            paymentDetailsConfig.zeroDecimalSupport
                        );

                        if ( lineItem.type === PAYMENT_DETAIL_ROW_PRODUCT ||
                             lineItem.type === PAYMENT_DETAIL_ROW_SETUP_FEE ||
                             lineItem.type === PAYMENT_DETAIL_ROW_DISCOUNT
                        ) {
                            $('td[data-wpfs-summary-row-label="' + lineItem.type + '"]', $form).text(lineItem.displayName);
                            $('td[data-wpfs-summary-row-value="' + lineItem.type + '"]', $form).text(amount);
                            $('tr[data-wpfs-summary-row="' + lineItem.type + '"]', $form).show();
                        } else if ( lineItem.type === PAYMENT_DETAIL_ROW_TAX ) {
                            var taxLabel = lineItem.displayName + ' (' + lineItem.percentage + '%)';

                            $('td[data-wpfs-summary-row-label="tax-' + taxIdx + '"]', $form).text(taxLabel);
                            $('td[data-wpfs-summary-row-value="tax-' + taxIdx + '"]', $form).text(amount);
                            $('tr[data-wpfs-summary-row="tax-' + taxIdx + '"]', $form).show();

                            taxIdx++;
                        }

                        total += lineItem.amount;
                    });
                    var totalLabel = formatter.format(
                        formatCurrencyAmount( total, paymentDetailsConfig.zeroDecimalSupport ),
                        paymentDetailsConfig.currency.toUpperCase(),
                        paymentDetailsConfig.currencySymbol,
                        paymentDetailsConfig.zeroDecimalSupport
                    );
                    $('td[data-wpfs-summary-row-value="' + PAYMENT_DETAIL_ROW_TOTAL + '"]', $form).text(totalLabel);
                    $('tr[data-wpfs-summary-row="' + PAYMENT_DETAIL_ROW_TOTAL + '"]', $form).show();
                }

                if ( summaryLabel !== null ) {
                    $('.wpfs-summary-description', $form).text(summaryLabel);
                    $('.wpfs-summary-description', $form).show();
                }
            }

            function handleDonationPaymentAmountChange($form, $selectedProductElement) {
                var buttonCaptionConfig = {
                    formId:             extractFormNameFromNode($form),
                    currency:           $selectedProductElement.data('wpfs-currency'),
                    currencySymbol:     $selectedProductElement.data('wpfs-currency-symbol'),
                    zeroDecimalSupport: $selectedProductElement.data('wpfs-zero-decimal-support') === true,
                    buttonTitle:        $selectedProductElement.data('wpfs-button-title'),
                };
                if ( $selectedProductElement.hasClass('wpfs-custom-amount--unique')) {
                    buttonCaptionConfig.total = parseCurrencyAmount(
                        $selectedProductElement.val(),
                        buttonCaptionConfig.zeroDecimalSupport,
                        true
                    );
                } else {
                    buttonCaptionConfig.total = $selectedProductElement.data('wpfs-amount-in-smallest-common-currency');
                }

                showPaymentButtonCaption( $form, buttonCaptionConfig );
            }

            function handlePriceAwarePaymentAmountChange($form, $selectedProductElement) {
                var buttonCaptionConfig = {
                    priceId:            $selectedProductElement.data('wpfs-amount-price-id'),
                    formId:             extractFormNameFromNode($form),
                    currency:           $selectedProductElement.data('wpfs-currency'),
                    currencySymbol:     $selectedProductElement.data('wpfs-currency-symbol'),
                    zeroDecimalSupport: $selectedProductElement.data('wpfs-zero-decimal-support') === true,
                    buttonTitle:        $selectedProductElement.data('wpfs-button-title'),
                };
                showPaymentButtonCaption( $form, buttonCaptionConfig );

                var paymentDetailsConfig = {
                    priceId:            $selectedProductElement.data('wpfs-amount-price-id'),
                    formId:             extractFormNameFromNode($form),
                    currency:           $selectedProductElement.data('wpfs-currency'),
                    currencySymbol:     $selectedProductElement.data('wpfs-currency-symbol'),
                    zeroDecimalSupport: $selectedProductElement.data('wpfs-zero-decimal-support') === true,
                };
                showPaymentDetails( $form, paymentDetailsConfig );

            }

            function isDonationForm($form) {
                var formType = extractFormTypeFromNode( $form );

                return (formType === FORM_TYPE_INLINE_DONATION || formType === FORM_TYPE_CHECKOUT_DONATION);
            }

            function isPossiblePricingRecalculationForm($form) {
                var formType = extractFormTypeFromNode( $form );

                return ( formType === FORM_TYPE_INLINE_PAYMENT || formType === FORM_TYPE_CHECKOUT_PAYMENT ||
                         formType === FORM_TYPE_INLINE_SUBSCRIPTION || formType === FORM_TYPE_CHECKOUT_SUBSCRIPTION );
            }

            function isNoCouponAppliedForm($form) {
                var coupon = WPFS.getCoupon( extractFormNameFromNode($form) );

                return coupon === null;
            }

            function isNoTaxForm($form) {
                var result = true;

                if ( isPossiblePricingRecalculationForm($form) ) {
                    if ( extractTaxRateTypeFromNode($form) !== TAX_RATE_TYPE_NO_TAX ) {
                        result = false;
                    }
                }

                return result;
            }

            function isDynamicTaxesForm($form) {
                var result = false;

                if ( isPossiblePricingRecalculationForm($form) ) {
                    if ( extractTaxRateTypeFromNode($form) === TAX_RATE_TYPE_DYNAMIC ) {
                        result = true;
                    }
                }

                return result;
            }

            function handlePaymentAmountChange($form, $selectedProductElement) {
                if ( isDonationForm($form) ) {
                    handleDonationPaymentAmountChange($form, $selectedProductElement)
                } else {
                    handlePriceAwarePaymentAmountChange($form, $selectedProductElement)
                }
            }

            function handlePlanChange($form) {
                if (debugLog) {
                    console.log('handlePlanChange(): CALLED');
                }
                if ($('.wpfs-subscription-plan-select', $form).length > 0) {
                    handlePlanSelectChange($form);
                } else if ($('.wpfs-subscription-plan-radio', $form).length > 0) {
                    handlePlanRadioButtonChange($form);
                } else if ($('.wpfs-subscription-plan-hidden', $form).length > 0) {
                    handlePlanHiddenChange($form);
                } else {
                    logWarn('handlePlanChange()', 'Subscription Plan Selector UI element not found!');
                }
            }

            function handlePlanSelectChange($form) {
                if (debugLog) {
                    console.log('handlePlanSelectChange(): CALLED');
                }
                var $subscriptionPlanSelectElement = $('.wpfs-subscription-plan-select', $form);
                var selectedPlanId = $subscriptionPlanSelectElement.val();
                var $selectedPlanElement = $('option[value="' + selectedPlanId + '"]', $form);
                handleSubscriptionPlanChange($form, $selectedPlanElement);
            }

            function handlePlanRadioButtonChange($form) {
                if (debugLog) {
                    console.log('handlePlanRadioButtonChange(): CALLED');
                }
                var $selectedPlanElement = $('.wpfs-subscription-plan-radio:checked', $form);
                if (debugLog) {
                    console.log('handlePlanRadioButtonChange(): selectedPlanElement=' + JSON.stringify($selectedPlanElement));
                }
                handleSubscriptionPlanChange($form, $selectedPlanElement);
            }

            function handlePlanHiddenChange($form) {
                if (debugLog) {
                    console.log('handlePlanHiddenChange(): CALLED');
                }
                var $selectedPlanElement = $('.wpfs-subscription-plan-hidden', $form);
                if (debugLog) {
                    console.log('handlePlanHiddenChange(): selectedPlanElement=' + JSON.stringify($selectedPlanElement));
                }
                handleSubscriptionPlanChange($form, $selectedPlanElement);
            }

            function handleSubscriptionPlanChange($form, $selectedPlanElement) {
                if (debugLog) {
                    console.log('handleSubscriptionPlanChange(): CALLED');
                }

                var paymentDetailsConfig = {
                    priceId:            $selectedPlanElement.data('wpfs-value'),
                    formId:             extractFormNameFromNode($form),
                    currency:           $selectedPlanElement.data('wpfs-currency'),
                    currencySymbol:     $selectedPlanElement.data('wpfs-currency-symbol'),
                    zeroDecimalSupport: $selectedPlanElement.data('wpfs-zero-decimal-support') === true,
                    interval:           $selectedPlanElement.attr('data-wpfs-interval'),
                    intervalCount:      parseInt($selectedPlanElement.attr('data-wpfs-interval-count')),
                    cancellationCount:  parseInt($selectedPlanElement.attr('data-wpfs-cancellation-count')),
                };
                showSubscriptionDetails( $form, paymentDetailsConfig );
            }

            function showCouponToRedeemRow($form) {
                $('.wpfs-coupon-to-redeem-row', $form).show();
            }

            function hideCouponToRedeemRow($form) {
                $('.wpfs-coupon-to-redeem-row', $form).hide();
            }

            function showRedeemedCouponRow($form, couponName) {
                var redeemedCouponLabelPattern = $('.wpfs-coupon-redeemed-label', $form).data('wpfs-coupon-redeemed-label');
                var redeemedCouponLabel = vsprintf(redeemedCouponLabelPattern, [couponName]);
                if (debugLog) {
                    console.log('showRedeemedCouponRow(): redeemCouponLabelPattern=' + redeemedCouponLabelPattern + ', couponName=' + couponName + ', redeemCouponLabel=' + redeemedCouponLabel);
                }
                $('.wpfs-coupon-redeemed-label', $form).html(redeemedCouponLabel);
                $('.wpfs-coupon-redeemed-row', $form).show();
            }

            function hideRedeemedCouponRow($form) {
                $('.wpfs-coupon-redeemed-label', $form).html('');
                $('.wpfs-coupon-redeemed-row', $form).hide();
            }

            function showRedeemLoadingAnimation($form) {
                var $redeemLink = $('.wpfs-coupon-redeem-link', $form);
                $redeemLink.blur();
                if ($redeemLink.hasClass('wpfs-input-group-link--loader')) {
                    return;
                }
                $redeemLink.addClass('wpfs-input-group-link--loader');
            }

            function hideRedeemLoadingAnimation($form) {
                var $redeemLink = $('.wpfs-coupon-redeem-link', $form);
                $redeemLink.blur();
                $redeemLink.removeClass('wpfs-input-group-link--loader');
            }

            function findPaymentAmountData($form) {
                var result = {};
                var currency;
                var amount;
                var amountType = $form.data('wpfs-amount-type');
                var amountSelectorStyle = $form.data('wpfs-amount-selector-style');
                var zeroDecimalSupport;
                var returnSmallestCommonCurrencyUnit = true;
                var customAmountValue;
                var formatter;
                if (debugLog) {
                    console.log('findPaymentAmountData(): ' + 'amountType=' + amountType + ', ' + 'amountSelectorStyle=' + amountSelectorStyle);
                }
                if (PAYMENT_TYPE_SPECIFIED_AMOUNT === amountType) {
                    result.paymentType = PAYMENT_TYPE_SPECIFIED_AMOUNT;
                    result.customAmount = false;
                    currency = $form.data('wpfs-currency');
                    amount = parseInt($form.data('wpfs-amount'));
                } else if (PAYMENT_TYPE_LIST_OF_AMOUNTS === amountType) {
                    result.paymentType = PAYMENT_TYPE_LIST_OF_AMOUNTS;
                    var $listOfAmounts = findListOfAmountsElement($form);
                    currency = $listOfAmounts.data('wpfs-currency');
                    zeroDecimalSupport = true === $listOfAmounts.data('wpfs-zero-decimal-support');
                    var allowCustomAmountValue = 1 == $form.data('wpfs-allow-list-of-amounts-custom');
                    var $selectedAmount = findSelectedAmountFromListOfAmounts($form);
                    if ($selectedAmount && $selectedAmount.length > 0) {
                        amount = $selectedAmount.val();
                    }
                    if (debugLog) {
                        console.log('findPaymentAmountData(): ' + 'zeroDecimalSupport=' + zeroDecimalSupport + ', ' + 'allowCustomAmountValue=' + allowCustomAmountValue + ', ' + 'amount=' + amount);
                    }
                    if (allowCustomAmountValue && AMOUNT_OTHER == amount) {
                        result.customAmount = true;
                        customAmountValue = $('input[name="wpfs-custom-amount-unique"]', $form).val();
                        formatter = createCurrencyFormatter($form);
                        if (formatter.validForParse(customAmountValue)) {
                            clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                            amount = parseCurrencyAmount(formatter.parse(customAmountValue), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                        } else {
                            amount = null;
                            clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                            showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsFormSettings.l10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                        }
                        if (debugLog) {
                            console.log('findPaymentAmountData(): ' + 'custom amount=' + amount);
                        }
                    } else {
                        result.customAmount = false;
                        amount = parseCurrencyAmount($selectedAmount.val(), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                    }
                } else if (PAYMENT_TYPE_CUSTOM_AMOUNT == amountType) {
                    result.customAmount = true;
                    var customAmountElement = findCustomAmountElement($form);
                    currency = customAmountElement.data('wpfs-currency');
                    zeroDecimalSupport = true === customAmountElement.data('wpfs-zero-decimal-support');
                    customAmountValue = customAmountElement.val();
                    formatter = createCurrencyFormatter($form);
                    if (formatter.validForParse(customAmountValue)) {
                        clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                        amount = parseCurrencyAmount(formatter.parse(customAmountValue), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                    } else {
                        amount = null;
                        clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                        showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsFormSettings.l10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                    }
                } else if (PAYMENT_TYPE_CARD_CAPTURE === amountType) {
                    result.paymentType = PAYMENT_TYPE_CARD_CAPTURE;
                    result.customAmount = false;
                    currency = $form.data('wpfs-currency');
                    amount = parseInt($form.data('wpfs-amount'));
                }
                if (amount === null || isNaN(amount) || amount < 0) {
                    result.valid = false;
                } else {
                    result.valid = true;
                    result.currency = currency;
                    result.amount = amount;
                }

                if (debugLog) {
                    console.log('findPaymentAmountData(): ' + 'result=' + JSON.stringify(result));
                }

                return result;
            }

            function findPlanAmountData($form) {
                var result = {};
                var planAmount, setupFee, currency, currencySymbol, vatPercent, vatAmount, discountAmount;
                var $selectedPlan;
                if (1 == $form.data('wpfs-simple-button-layout')) {
                    $selectedPlan = $('input[name="wpfs-plan"]', $form);
                } else {
                    if ($('.wpfs-subscription-plan-select', $form).length > 0) {
                        $selectedPlan = $('select[name="wpfs-plan"] option:selected', $form);
                    } else if ($('.wpfs-subscription-plan-radio', $form).length > 0) {
                        $selectedPlan = $('.wpfs-subscription-plan-radio:checked', $form);
                    } else if ($('.wpfs-subscription-plan-hidden', $form).length > 0) {
                        $selectedPlan = $('.wpfs-subscription-plan-hidden', $form);
                    } else {
                        logError('findPlanAmountData', 'WARNING: Subscription Plan Selector UI element not found!');
                    }
                }
                if ($selectedPlan && $selectedPlan.length == 1) {
                    planAmount = $selectedPlan.data('wpfs-plan-amount-in-smallest-common-currency');
                    setupFee = $selectedPlan.data('wpfs-plan-setup-fee-in-smallest-common-currency');
                    currency = $selectedPlan.data('wpfs-currency');
                    currencySymbol = $selectedPlan.data('wpfs-currency-symbol');
                    vatPercent = $selectedPlan.data('wpfs-vat-percent');
                }
                if (planAmount === null || isNaN(planAmount) || planAmount < 0) {
                    result.valid = false;
                } else {
                    var formId = $form.data('wpfs-form-id');
                    var coupon = WPFS.getCoupon(formId);
                    var couponResult = applyCoupon(currency, planAmount + setupFee, coupon);
                    discountAmount = couponResult.discount;
                    var subTotal = planAmount + setupFee - discountAmount;
                    vatAmount = calculateVATAmount(subTotal, vatPercent);
                    result.valid = true;
                    result.amount = subTotal + vatAmount;
                    result.currency = currency;
                    result.currencySymbol = currencySymbol;
                }
                return result;
            }

            var WPFS = {};
            WPFS.couponMap = {};
            WPFS.paymentDetailsMap = {};
            /**
             * @param formId
             * @param coupon
             */
            WPFS.setCoupon = function (formId, coupon) {
                WPFS.couponMap[formId] = coupon;
            };
            /**
             * @param formId
             * @returns {*}
             */
            WPFS.getCoupon = function (formId) {
                if (WPFS.couponMap.hasOwnProperty(formId)) {
                    return WPFS.couponMap[formId];
                }
                return null;
            };
            /**
             * @param formId
             */
            WPFS.removeCoupon = function (formId) {
                if (WPFS.couponMap.hasOwnProperty(formId)) {
                    delete WPFS.couponMap[formId];
                }
            };

            /**
             * @param formId
             * @param paymentDetails
             */
            WPFS.setPaymentDetails = function (formId, paymentDetails) {
                WPFS.paymentDetailsMap[formId] = paymentDetails;
            };
            /**
             * @param formId
             * @returns {*}
             */
            WPFS.getPaymentDetails = function (formId) {
                if (WPFS.paymentDetailsMap.hasOwnProperty(formId)) {
                    return WPFS.paymentDetailsMap[formId];
                }
                return null;
            };
            /**
             * @param formId
             * @param priceId
             * @returns {*}
             */
            WPFS.getPaymentDetailsForPrice = function (formId, priceId) {
                var priceMap = WPFS.getPaymentDetails(formId);

                if ( priceMap !== null ) {
                    if (priceMap.hasOwnProperty(priceId)) {
                        return priceMap[priceId];
                    }
                    return null;
                }
                return null;
            };
            /**
             * @param formId
             * @param priceId
             * @param priceData
             */
            WPFS.setPaymentDetailsForPrice = function (formId, priceId, priceData) {
                var priceMap = WPFS.getPaymentDetails(formId);
                if ( priceMap === null ) {
                    priceMap = {};
                }

                priceMap[priceId] = priceData;
                WPFS.setPaymentDetails( formId, priceMap );
            };
            /**
             * @param formId
             */
            WPFS.removePaymentDetails = function (formId) {
                if (WPFS.paymentDetailsMap.hasOwnProperty(formId)) {
                    delete WPFS.paymentDetailsMap[formId];
                }
            };

            WPFS.initCustomAmount = function () {
                $('input.wpfs-custom-amount').change(function (e) {
                    var $form = getParentForm(this);
                    handleCustomAmountChange($(this), $form);
                    handleAmountChange($form);
                });
                $('.wpfs-custom-amount--unique').blur(function () {
                    var $form = getParentForm(this);

                    if ( isPossiblePricingRecalculationForm($form) ) {
                        if ( isNoTaxForm($form) && isNoCouponAppliedForm($form) ) {
                            refreshOtherPricingLocally( $form );
                        } else {
                            refreshPricingFromServer( $form );
                        }
                    } else {
                        refreshPaymentDetails( $form );
                    }
                });
            };

            WPFS.initStripeJSCard = function () {
                var $cards = $('form[data-wpfs-form-type] [data-toggle="card"]');

                if ($cards.length === 0) {
                    return;
                }

                $cards.each(function (index) {

                    // tnagy get references for form, formId
                    var $form = getParentForm(this);
                    var formId = $form.data('wpfs-form-id');
                    var elementsLocale = $form.data('wpfs-preferred-language');

                    // tnagy create Stripe Elements\Card
                    var elements = stripe.elements({
                        locale: elementsLocale
                    });
                    var cardElement = elements.create('card', {
                        hidePostalCode: true,
                        classes: {
                            base: 'wpfs-form-card',
                            empty: 'wpfs-form-control--empty',
                            focus: 'wpfs-form-control--focus',
                            complete: 'wpfs-form-control--complete',
                            invalid: 'wpfs-form-control--error'
                        },
                        style: {
                            base: {
                                color: '#2F2F37',
                                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Oxygen-Sans", Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                                fontSmoothing: 'antialiased',
                                fontSize: '15px',
                                '::placeholder': {
                                    color: '#7F8393'
                                }
                            },
                            invalid: {
                                color: '#2F2F37',
                                iconColor: '#CC3434'
                            }
                        }
                    });
                    cardElement.mount('div.wpfs-form-control[data-wpfs-form-id="' + formId + '"]');

                    // tnagy handle form submission
                    $form.submit(function (event) {

                        event.preventDefault();

                        /*
                         tnagy disable submit button and show loading animation,
                         clear message panel, reset token and amount index
                         */
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        clearFieldErrors($form);
                        clearGlobalMessage($form);
                        removeHiddenFormFields($form);

                        // Add get parameters to the form
                        setPageParametersField($form);

                        // tnagy add amount index
                        addCustomAmountIndexInput($form);

                        //tnagy validate fields
                        var errorMessage = null;

                        // tnagy validate terms of use if necessary
                        var showTermsOfUse = $form.data('wpfs-show-terms-of-use');
                        if (1 == showTermsOfUse) {
                            var termsOfUseAccepted = $form.find('input[name=wpfs-terms-of-use-accepted]').prop('checked');
                            if (termsOfUseAccepted == false) {
                                errorMessage = $form.data('wpfs-terms-of-use-not-checked-error-message');
                                showFieldError($form, 'wpfs-terms-of-use-accepted', null, errorMessage);
                                scrollToElement($('input[name=wpfs-terms-of-use-accepted]', $form), false);
                                enableFormButtons($form);
                                hideLoadingAnimation($form);
                                return false;
                            }
                        }

                        var paymentMethodData = {};
                        var cardHolderNameField = $('input[name="wpfs-card-holder-name"]', $form);
                        if (cardHolderNameField.length > 0) {
                            var cardHolderName = cardHolderNameField.val();
                            if (cardHolderName != null && cardHolderName != '') {
                                paymentMethodData.billing_details = {
                                    name: cardHolderName
                                };
                            }
                        }
                        var billingAddressCountryField = $('input[name="wpfs-billing-address-country"]', $form);
                        if (billingAddressCountryField.length > 0) {
                            var billingAddressCountry = billingAddressCountryField.val();
                            if (billingAddressCountry != null && billingAddressCountry != '') {
                                paymentMethodData.billing_details.address = {
                                    country: billingAddressCountry
                                };
                            }
                        }

                        if (debugLog) {
                            console.log('form.submit(): ' + 'Creating PaymentMethod...');
                        }
                        stripe.createPaymentMethod(
                            'card',
                            cardElement,
                            paymentMethodData
                        ).then(function (createPaymentMethodResult) {
                            if (debugLog) {
                                console.log('form.submit(): ' + 'PaymentMethod creation result=' + JSON.stringify(createPaymentMethodResult));
                            }
                            clearFieldErrors($form);
                            if (createPaymentMethodResult.error) {
                                enableFormButtons($form);
                                hideLoadingAnimation($form);
                                showFieldError($form, 'cardnumber', null, createPaymentMethodResult.error.message);
                                scrollToElement($('.wpfs-form-card', $form), false);
                            } else {
                                addPaymentMethodIdInput($form, createPaymentMethodResult);
                                submitPaymentData($form, cardElement);
                            }
                        });

                        return false;
                    });
                });
            };

            WPFS.initCoupon = function () {
                const COUPON_FIELD_NAME = 'wpfs-coupon';

                function getCouponNode( $form ) {
                    return $('input[name="' + COUPON_FIELD_NAME + '"]', $form);
                }

                function emptyCouponField( $form ) {
                    var formId = extractFormNameFromNode( $form );
                    WPFS.removeCoupon(formId);

                    var $coupon = getCouponNode( $form);
                    if ($coupon.length > 0) {
                        $coupon.val('');
                    }
                }

                $('.wpfs-coupon-remove-link').click(function (event) {
                    event.preventDefault();
                    var $form = getParentForm(this);

                    emptyCouponField( $form );

                    hideRedeemedCouponRow($form);
                    showCouponToRedeemRow($form);

                    refreshPricingFromServer( $form );
                });
                $('.wpfs-coupon-redeem-link').click(function (event) {
                    event.preventDefault();
                    var $form = getParentForm(this);
                    var $coupon = getCouponNode( $form);

                    var taxData = gatherFormDataForTaxCalculation( $form );
                    taxData.coupon = $coupon.val();

                    disableFormButtons($form);
                    if ($coupon.length > 0) {
                        clearFieldErrors($form);
                        $coupon.prop('disabled', true);
                        showRedeemLoadingAnimation($form);

                        $.ajax({
                            type: 'POST',
                            url: wpfsFormSettings.ajaxUrl,
                            data: {
                                action: 'wp_full_stripe_check_coupon',
                                code: $coupon.val(),
                                taxData: taxData
                            },
                            cache: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.valid) {
                                    var formId = extractFormNameFromNode( $form );
                                    WPFS.setCoupon(formId, data.coupon);

                                    hideCouponToRedeemRow($form);
                                    showRedeemedCouponRow($form, data.coupon.name);

                                    setPaymentDetailsData( $form, data.productPricing );
                                    refreshPaymentDetails( $form );
                                } else {
                                    var couponFieldId = $coupon.attr('id');
                                    showFieldError($form, COUPON_FIELD_NAME, couponFieldId, data.msg);
                                    scrollToElement($coupon, false);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                logError('coupon', jqXHR, textStatus, errorThrown);
                                showErrorGlobalMessage($form, wpfsFormSettings.l10n.stripe_errors.internal_error);
                            },
                            complete: function () {
                                $coupon.prop('disabled', false);
                                hideRedeemLoadingAnimation($form);
                                enableFormButtons($form);
                            }
                        });
                    }
                });
            };

            WPFS.initInputGroup = function () {
                var inputGroupPrependClass = '.wpfs-input-group-prepend';
                $(document).on('click', inputGroupPrependClass, function (e) {
                    var $target = $(e.target);
                    if ($target.hasClass('wpfs-input-group-link')) {
                        return;
                    }

                    if ($target.parents('.wpfs-input-group-link').length > 0) {
                        return;
                    }

                    $(this).next().focus();
                });

                $(document).on('mouseenter', inputGroupPrependClass, function () {
                    $(this).next().mouseenter();
                });

                $(document).on('mouseleave', inputGroupPrependClass, function () {
                    $(this).next().mouseleave();
                });

                var inputGroupAppendClass = '.wpfs-input-group-append';
                $(document).on('click', inputGroupAppendClass, function (e) {
                    var $target = $(e.target);
                    if ($target.hasClass('wpfs-input-group-link')) {
                        return;
                    }

                    if ($target.parents('.wpfs-input-group-link').length > 0) {
                        return;
                    }

                    $(this).prev().focus();
                });

                $(document).on('mouseenter', inputGroupAppendClass, function () {
                    $(this).prev().mouseenter();
                });

                $(document).on('mouseleave', inputGroupAppendClass, function () {
                    $(this).prev().mouseleave();
                });
            };

            WPFS.initSelectmenu = function () {
                $.widget('custom.wpfsSelectmenu', $.ui.selectmenu, {
                    _renderItem: function (ul, item) {
                        var $li = $('<li>');
                        var wrapper = $('<div>', {
                            class: 'menu-item-wrapper ui-menu-item-wrapper',
                            text: item.label
                        });

                        if (item.disabled) {
                            $li.addClass('ui-state-disabled');
                        }

                        return $li.append(wrapper).appendTo(ul);
                    }
                });

                var $selectmenus = $('[data-toggle="selectmenu"]');
                $selectmenus.each(function () {
                    if (typeof $(this).select2 === "function") {
                        try {
                            $(this).select2('destroy');
                        } catch (err) {
                        }
                    }

                    var $selectmenu = $(this).wpfsSelectmenu({
                        classes: {
                            'ui-selectmenu-button': 'wpfs-form-control wpfs-selectmenu-button',
                            'ui-selectmenu-menu': 'wpfs-ui wpfs-selectmenu-menu'
                        },
                        icons: {
                            button: "wpfs-icon-arrow"
                        },
                        create: function () {
                            if (debugLog) {
                                console.log('selectmenu.create(): CALLED');
                            }
                            var $this = $(this);
                            var $selectMenuButton = $this.next();
                            $selectMenuButton.addClass($this.attr('class'));
                            if ($this.find('option:selected:disabled').length > 0) {
                                $selectMenuButton.addClass('ui-state-placeholder');
                            }
                        },
                        open: function () {
                            if (debugLog) {
                                console.log('selectmenu.open(): CALLED');
                            }
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;
                            $button.removeClass('ui-selectmenu-button-closed');
                            $button.addClass('ui-selectmenu-button-open');
                            var selectedClass = 'ui-state-selected';
                            var selectedIndex = $this.find('option').index($this.find('option:selected'));
                            $('.ui-selectmenu-open .ui-menu-item-wrapper').removeClass(selectedClass);
                            var $menuItem = $('.ui-selectmenu-open .ui-menu-item').eq(selectedIndex);
                            if (!$menuItem.hasClass('ui-state-disabled')) {
                                $menuItem.find('.ui-menu-item-wrapper').addClass(selectedClass);
                            }
                        },
                        close: function () {
                            if (debugLog) {
                                console.log('selectmenu.close(): CALLED')
                            }
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;
                            $button.removeClass('ui-selectmenu-button-open');
                            $button.addClass('ui-selectmenu-button-closed');
                        },
                        change: function (event, ui) {
                            if (debugLog) {
                                console.log('selectmenu.change(): CALLED');
                            }
                            var $selectElement = $(event.target);
                            var $form = getParentForm(event.target);
                            if ($selectElement.length > 0) {
                                var selectElementType = $selectElement.data('wpfs-select');
                                if ('wpfs-billing-address-country-select' === selectElementType) {
                                    var $billingAddressCountrySelectElement = $('.wpfs-billing-address-country-select', $form);
                                    handleBillingAddressCountryChange($billingAddressCountrySelectElement, $form);
                                } else if ('wpfs-billing-address-state-select' === selectElementType) {
                                    var $billingAddressStateSelectElement = $('.wpfs-billing-address-state-select', $form);
                                    handleBillingAddressStateChange($billingAddressStateSelectElement, $form);
                                } else if ('wpfs-shipping-address-country-select' === selectElementType) {
                                    var $shippingAddressCountrySelectElement = $('.wpfs-shipping-address-country-select', $form);
                                    handleShippingAddressCountryChange($shippingAddressCountrySelectElement, $form);
                                } else if ('wpfs-custom-amount-select' === selectElementType) {
                                    handleAmountChange($form);
                                    var amountType = $form.data('wpfs-amount-type');
                                    if ('list_of_amounts' === amountType) {
                                        var $customAmountSelectElement = $('.wpfs-custom-amount-select', $form);
                                        handleCustomAmountChange($customAmountSelectElement, $form);
                                    }
                                } else if ('wpfs-subscription-plan-select' === selectElementType) {
                                    handlePlanChange($form);
                                } else if ('wpfs-tax-country-select' === selectElementType ) {
                                    handleTaxCountryChange($form);
                                } else if ('wpfs-tax-state-select' === selectElementType ) {
                                    handleTaxStateChange($form);
                                }
                            }
                            $(this).next().removeClass('ui-state-placeholder');
                        }
                    });

                    var $selectmenuParent = $selectmenu.parent();
                    $selectmenuParent.find('.ui-selectmenu-button')
                        .addClass('wpfs-form-control')
                        .addClass('wpfs-selectmenu-button')
                        .addClass('ui-button');

                    $selectmenu.data('custom-wpfsSelectmenu').menuWrap
                        .addClass('wpfs-ui')
                        .addClass('wpfs-selectmenu-menu');

                });
            };

            WPFS.initAddressSwitcher = function () {
                $('.wpfs-same-billing-and-shipping-address').change(function () {
                    if (debugLog) {
                        logInfo('.wpfs-same-billing-and-shipping-address CHANGE', 'CALLED');
                    }
                    var addressSwitcherId = $(this).data('wpfs-address-switcher-id');
                    var billingAddressSwitchId = $(this).data('wpfs-billing-address-switch-id');
                    if ($(this).prop('checked')) {
                        $('#' + addressSwitcherId).hide();
                        $('#' + billingAddressSwitchId).click();
                    } else {
                        $('#' + addressSwitcherId).show();
                        $('#' + billingAddressSwitchId).click();
                    }
                    $('.wpfs-billing-address-switch').change();
                    $('.wpfs-shipping-address-switch').change();
                });
                $('.wpfs-billing-address-switch').change(function () {
                    if (debugLog) {
                        logInfo('.wpfs-billing-address-switch CHANGE', 'CALLED');
                    }
                    var billingAddressPanelId = $(this).data('wpfs-billing-address-panel-id');
                    var shippingAddressPanelId = $(this).data('wpfs-shipping-address-panel-id');
                    if ($(this).prop('checked')) {
                        $('#' + billingAddressPanelId).show();
                        $('#' + shippingAddressPanelId).hide();
                    }
                });
                $('.wpfs-shipping-address-switch').change(function () {
                    var billingAddressPanelId = $(this).data('wpfs-billing-address-panel-id');
                    var shippingAddressPanelId = $(this).data('wpfs-shipping-address-panel-id');
                    if ($(this).prop('checked')) {
                        $('#' + billingAddressPanelId).hide();
                        $('#' + shippingAddressPanelId).show();
                    }
                });
            };

            WPFS.initTooltip = function () {
                $.widget.bridge('wpfstooltip', $.ui.tooltip);

                $('[data-toggle="tooltip"]').wpfstooltip({
                    items: '[data-toggle="tooltip"]',
                    content: function () {
                        var contentId = $(this).data('tooltip-content');
                        return $('[data-tooltip-id="' + contentId + '"]').html();
                    },
                    position: {
                        my: 'left top+5',
                        at: 'left bottom+5',
                        using: function (position, feedback) {
                            var $this = $(this);
                            $this.css(position);
                            $this
                                .addClass(feedback.vertical)
                                .addClass(feedback.horizontal);
                        }
                    },
                    classes: {
                        'ui-tooltip': 'wpfs-ui wpfs-tooltip'
                    },
                    tooltipClass: 'wpfs-ui wpfs-tooltip'
                }).on({
                    "click": function (event) {
                        event.preventDefault();
                        $(this).tooltip("open");
                    }
                });
            };

            WPFS.initStepper = function () {
                var $stepper = $('[data-toggle="stepper"]');
                $stepper.each(function () {
                    var $this = $(this);
                    var defaultValue = $this.data('defaultValue') || 1;

                    if ($this.val() === '') {
                        $this.val(defaultValue);
                    }

                    $this.spinner({
                        min: $this.data('min') || 1,
                        max: $this.data('max') || 9999,
                        icons: {
                            down: 'wpfs-icon-decrease',
                            up: 'wpfs-icon-increase'
                        },
                        change: function (e, ui) {
                            var $this = $(this);
                            if ($this.spinner('isValid')) {
                                defaultValue = $this.val();
                            } else {
                                $this.val(defaultValue);
                            }
                        },
                        spin: function (e, ui) {
                            var $this = $(this);
                            var uiSpinner = $this.data('uiSpinner');
                            var min = uiSpinner.options.min;
                            var max = uiSpinner.options.max;
                            var $container = $this.parent();
                            var disabledClassName = 'ui-state-disabled';
                            var up = $container.find('.ui-spinner-up');
                            var down = $container.find('.ui-spinner-down');

                            up.removeClass(disabledClassName);
                            down.removeClass(disabledClassName);

                            if (ui.value === max) {
                                up.addClass(disabledClassName);
                            }

                            if (ui.value === min) {
                                down.addClass(disabledClassName);
                            }
                        }
                    })
                        .parent()
                        .find('.ui-icon').text('');
                });
            };

            WPFS.initDatepicker = function () {
                $('[data-toggle="datepicker"]').each(function () {
                    var $this = $(this);
                    var dateFormat = $this.data('dateFormat') || 'dd/mm/yyyy';
                    var defaultValue = $this.data('defaultValue') || '';

                    if ($this.val() === '') {
                        $this.val(defaultValue);
                    }

                    $this
                        .attr('placeholder', dateFormat)
                        .inputmask({
                            alias: dateFormat,
                            showMaskOnHover: false,
                            showMaskOnFocus: false
                        })
                        .datepicker({
                            prevText: '',
                            nextText: '',
                            hideIfNoPrevNext: true,
                            firstDay: 1,
                            dateFormat: dateFormat.replace(/yy/g, 'y'),
                            showOtherMonths: true,
                            selectOtherMonths: true,
                            onChangeMonthYear: function (year, month, inst) {
                                if (inst.dpDiv.hasClass('bottom')) {
                                    setTimeout(function () {
                                        inst.dpDiv.css('top', inst.input.offset().top - inst.dpDiv.outerHeight());
                                    });
                                }
                            },
                            beforeShow: function (el, inst) {
                                var $el = $(el);
                                inst.dpDiv.addClass('wpfs-ui wpfs-datepicker-div');
                                setTimeout(function () {
                                    if ($el.offset().top > inst.dpDiv.offset().top) {
                                        inst.dpDiv.removeClass('top');
                                        inst.dpDiv.addClass('bottom');
                                    } else {
                                        inst.dpDiv.removeClass('bottom');
                                        inst.dpDiv.addClass('top');
                                    }
                                });
                            }
                        })
                        .on('blur', function () {
                            var $this = $(this);
                            setTimeout(function () {
                                var date = $this.val();
                                var isValid = Inputmask.isValid(date, {
                                    alias: dateFormat
                                });
                                if (isValid) {
                                    defaultValue = date;
                                } else {
                                    $this.val(defaultValue);
                                }
                            }, 50);
                        });
                });
            };

            WPFS.initCombobox = function () {
                $.widget('custom.combobox', {
                    _selectOptions: [],
                    _lastValidValue: null,
                    _create: function () {
                        this.wrapper = $('<div>')
                            .addClass('wpfs-input-group wpfs-combobox')
                            .addClass(this.element.attr('class'))
                            .insertAfter(this.element);

                        this._selectOptions = this.element.children('option').map(function () {
                            var $this = $(this);
                            var text = $this.text();
                            var value = $this.val();
                            if (value && value !== '') {
                                return {
                                    label: text,
                                    value: text,
                                    option: this
                                };
                            }
                        });

                        this.element.hide();
                        this._createAutocomplete();
                        this._createShowAllButton();
                    },
                    _createAutocomplete: function () {
                        var selected = this.element.children(':selected');
                        var value = selected.val() ? selected.text() : '';

                        this.input = $('<input>')
                            .attr('placeholder', this.element.data('placeholder'))
                            .appendTo(this.wrapper)
                            .val(value)
                            .addClass('wpfs-input-group-form-control')
                            .autocomplete({
                                delay: 0,
                                minLength: 0,
                                source: $.proxy(this, '_source'),
                                position: {
                                    my: 'left-1px top+2px',
                                    at: 'left-1px bottom+2px',
                                    using: function (position, feedback) {
                                        var $this = $(this);
                                        $this.css(position);
                                        $this.width(feedback.target.width + 46);
                                    }
                                },
                                classes: {
                                    'ui-autocomplete': 'wpfs-ui wpfs-combobox-menu'
                                },
                                open: function () {
                                    $(this).parent().addClass('wpfs-combobox--open');
                                },
                                close: function () {
                                    var $this = $(this);
                                    $this.parent().removeClass('wpfs-combobox--open');
                                    $this.blur();
                                },
                                search: function (e, ui) {
                                    // Fix autocomplete combobox memory leak
                                    $(this).data('uiAutocomplete').menu.bindings = $();
                                }
                            })
                            .on('focus', function () {
                                $(this).data('uiAutocomplete').search('');
                            })
                            .on('keydown', function (e) {
                                if (e.keyCode === 13) {
                                    $(this).blur();
                                }
                            });

                        this.input.data('uiAutocomplete')._renderItem = this._renderItem;

                        this._on(this.input, {
                            autocompleteselect: function (e, ui) {
                                if (!ui.item.noResultsItem) {
                                    ui.item.option.selected = true;
                                    this._trigger('select', e, {
                                        item: ui.item.option
                                    });
                                }
                                this._trigger('blur');
                            },
                            autocompletechange: '_validateValue'
                        });
                    },
                    _createShowAllButton: function () {
                        var input = this.input;
                        var wasOpen = false;
                        var html = '<div class="wpfs-input-group-append"><span class="wpfs-input-group-icon"><span class="wpfs-icon-arrow"></span></span></div>';

                        $(html)
                            .appendTo(this.wrapper)
                            .on('mousedown', function () {
                                wasOpen = input.autocomplete('widget').is(':visible');
                            })
                            .on('click', function (e) {
                                e.stopPropagation();
                                if (wasOpen) {
                                    input.autocomplete('close');
                                } else {
                                    input.trigger('focus');
                                }
                            });
                    },
                    _source: function (request, response) {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
                        var results = this._selectOptions.map(function (i, option) {
                            if (option.value && (!request.term || matcher.test(option.label))) {
                                return option;
                            }
                        });

                        if (results.length > 0) {
                            response(results);
                        } else {
                            response([{
                                label: this.element.data('noResultsMessage'),
                                value: request.term,
                                noResultsItem: true
                            }]);
                        }
                    },
                    _validateValue: function (e, ui) {
                        // Selected an item, nothing to do
                        if (ui.item) {
                            return;
                        }

                        // Search for a match (case-insensitive)
                        var value = this.input.val();
                        var valueLowerCase = value.toLowerCase();
                        var valid = false;
                        var selectedText = null;
                        this.element.children('option').each(function () {
                            var text = $(this).text();
                            if (text.toLowerCase() === valueLowerCase) {
                                selectedText = text;
                                this.selected = valid = true;
                                return false;
                            }
                        });

                        if (valid) {
                            // Fix valid value
                            this.input.val(selectedText);
                            this._lastValidValue = selectedText;
                        } else if (!valid && this._lastValidValue !== null) {
                            // Set last valid value
                            this.input.val(this._lastValidValue);
                        } else {
                            // Remove invalid value
                            this.input.val('');
                            this.element.val('');
                            this.input.autocomplete('instance').term = '';
                        }
                    },
                    _renderItem: function (ul, item) {
                        var t = '';
                        var idx = item.label.toLowerCase().indexOf(this.term.toLowerCase());
                        var sameLabelAndTerm = item.label.toLowerCase() === this.term.toLowerCase();

                        if (idx !== -1 && !sameLabelAndTerm && this.term !== '') {
                            var termLength = this.term.length;
                            t += item.label.substring(0, idx);
                            t += '<strong>' + item.label.substr(idx, termLength) + '</strong>';
                            t += item.label.substr(idx + termLength);
                        } else {
                            t = item.label;
                        }

                        var className = '';
                        var $li = $('<li></li>');
                        var $div = $('<div class="ui-menu-item-wrapper">' + t + '</div>');
                        if (!item.noResultsItem) {
                            $li.data('item.autocomplete', item);
                            if (sameLabelAndTerm) {
                                $div.addClass('ui-state-selected');
                            }
                        } else {
                            $li.addClass('ui-state-disabled');
                        }

                        ul
                            .addClass('wpfs-ui')
                            .addClass('wpfs-combobox-menu');

                        return $li
                            .append($div)
                            .appendTo(ul);
                    },
                    _destroy: function () {
                        this.wrapper.remove();
                        this.element.show();
                    }
                });

                $('[data-toggle="combobox"]').combobox();
            };

            WPFS.initCheckoutForms = function () {

                if (debugLog) {
                    logInfo('initCheckoutForms', 'CALLED');
                }

                var popupFormsSelector = 'form[data-wpfs-form-type="' + FORM_TYPE_CHECKOUT_PAYMENT + '"], form[data-wpfs-form-type="' + FORM_TYPE_CHECKOUT_SUBSCRIPTION + '"], form[data-wpfs-form-type="' + FORM_TYPE_CHECKOUT_SAVE_CARD + '"], form[data-wpfs-form-type="' + FORM_TYPE_CHECKOUT_DONATION + '"]';
                var $popupForms = $(popupFormsSelector);
                $popupForms.each(function (index, popupForm) {
                    var $form = $(popupForm);

                    if (debugLog) {
                        logInfo('initCheckoutForms', 'form=' + $form.data('wpfs-form-id'));
                    }

                    $form.submit(function (event) {

                        if (debugLog) {
                            logInfo('checkoutFormSubmit', 'CALLED, formId=' + $form.data('wpfs-form-id'));
                        }

                        event.preventDefault();

                        /*
                         tnagy disable submit button and show loading animation,
                         clear message panel, reset token and amount index
                         */
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        clearFieldErrors($form);
                        clearGlobalMessage($form);
                        removeCustomAmountIndexInput($form);

                        // Add get parameters to the form
                        setPageParametersField($form);

                        // tnagy add amount index
                        addCustomAmountIndexInput($form);

                        // tnagy validate form
                        var hasErrors = false;
                        var $firstInvalidField = null;
                        var fieldErrorMessage = null;

                        // tnagy validate custom fields
                        var customInputRequired = $form.data('wpfs-custom-input-required');
                        if (1 == customInputRequired) {
                            var customInputFieldsWithMissingValue = [];
                            var customInputValues = $('input[name="wpfs-custom-input"]', $form);
                            if (customInputValues.length == 0) {
                                customInputValues = $('input[name="wpfs-custom-input[]"]', $form);
                            }
                            if (customInputValues) {
                                customInputValues.each(function () {
                                    if ($(this).val().length == 0) {
                                        customInputFieldsWithMissingValue.push(this);
                                    }
                                });
                            }
                            if (customInputFieldsWithMissingValue.length > 0) {
                                for (var index in customInputFieldsWithMissingValue) {
                                    var $customInputField = $(customInputFieldsWithMissingValue[index]);
                                    if ($customInputField.length > 0) {
                                        if ($firstInvalidField == null) {
                                            $firstInvalidField = $customInputField;
                                        }
                                        var id = $customInputField.attr('id');
                                        var name = $customInputField.attr('name');
                                        if (name) {
                                            name = name.replace(/\[]/g, '');
                                        }
                                        var label = $customInputField.data('wpfs-custom-input-label');
                                        fieldErrorMessage = vsprintf(wpfsFormSettings.l10n.validation_errors.mandatory_field_is_empty, [label]);
                                        showFieldError($form, name, id, fieldErrorMessage, false);
                                    }
                                }
                                hasErrors = true;
                            }
                        }

                        // tnagy validate terms of use if necessary
                        var showTermsOfUse = $form.data('wpfs-show-terms-of-use');
                        if (1 == showTermsOfUse) {
                            var termsOfUseAccepted = $form.find('input[name=wpfs-terms-of-use-accepted]').prop('checked');
                            if (termsOfUseAccepted == false) {
                                if ($firstInvalidField == null) {
                                    $firstInvalidField = $('input[name=wpfs-terms-of-use-accepted]', $form);
                                }
                                fieldErrorMessage = $form.data('wpfs-terms-of-use-not-checked-error-message');
                                showFieldError($form, 'wpfs-terms-of-use-accepted', null, fieldErrorMessage, false);
                                hasErrors = true;
                            }
                        }

                        // tnagy prevent submit on validation errors
                        if (hasErrors) {
                            if ($firstInvalidField && $firstInvalidField.length > 0) {
                                scrollToElement($firstInvalidField, false);
                            }
                            enableFormButtons($form);
                            hideLoadingAnimation($form);
                            return false;
                        }

                        // tnagy continue with valid data
                        var formType = $form.data('wpfs-form-type');
                        var amountData;
                        if (FORM_TYPE_CHECKOUT_PAYMENT === formType || FORM_TYPE_CHECKOUT_SAVE_CARD === formType || FORM_TYPE_CHECKOUT_DONATION === formType) {
                            amountData = findPaymentAmountData($form);
                        } else if (FORM_TYPE_CHECKOUT_SUBSCRIPTION === formType) {
                            amountData = findPlanAmountData($form);
                        }

                        if (debugLog) {
                            logInfo('initCheckoutForms', 'amountData=' + JSON.stringify(amountData));
                        }

                        if (false == amountData.valid) {
                            if (amountData.customAmount) {
                                showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsFormSettings.l10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                            } else {
                                showErrorGlobalMessage($form, wpfsFormSettings.l10n.validation_errors.invalid_payment_amount_title, wpfsFormSettings.l10n.validation_errors.invalid_payment_amount);
                            }
                            enableFormButtons($form);
                            hideLoadingAnimation($form);
                            return false;
                        }

                        addCurrentURL($form);

                        $.ajax({
                            type: "POST",
                            url: wpfsFormSettings.ajaxUrl,
                            data: $form.serialize(),
                            cache: false,
                            dataType: "json",
                            success: function (data) {

                                if (debugLog) {
                                    logInfo('initCheckoutForms', 'SUCCESS response=' + JSON.stringify(data));
                                }

                                if (data.success) {
                                    if (data.checkoutSessionId) {
                                        stripe.redirectToCheckout({
                                            sessionId: data.checkoutSessionId
                                        }).then(function (result) {
                                            // If `redirectToCheckout` fails due to a browser or network
                                            // error, display the localized error message to your customer
                                            // using `result.error.message`.
                                            if (debugLog) {
                                                logInfo('initCheckoutForms', 'result=' + JSON.stringify(result));
                                            }

                                        });
                                    } else {
                                        // tnagy no checkoutSessionId supplied
                                        logWarn('initCheckoutForms', 'No CheckoutSession supplied!');
                                    }
                                } else {
                                    if (data && (data.messageTitle || data.message)) {
                                        showErrorGlobalMessage($form, data.messageTitle, data.message);
                                    }
                                    processValidationErrors($form, data);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                logError('submitPaymentData', jqXHR, textStatus, errorThrown);
                                showErrorGlobalMessage($form, wpfsFormSettings.l10n.stripe_errors.internal_error_title, wpfsFormSettings.l10n.stripe_errors.internal_error);
                            },
                            complete: function () {
                                enableFormButtons($form);
                                hideLoadingAnimation($form);
                            }
                        });

                        return false;

                    });
                });
            };

            WPFS.initReCaptcha = function () {
                window.addEventListener('load', function () {
                    var reCAPTCHAWidgetId;
                    var formHash;
                    var formCAPTCHAs = document.getElementsByClassName('wpfs-form-captcha');
                    //noinspection JSUnresolvedVariable
                    if (window.grecaptcha !== 'undefined' && formCAPTCHAs !== null && formCAPTCHAs.length > 0) {
                        //noinspection JSUnresolvedVariable
                        googleReCAPTCHA = window.grecaptcha;
                        Array.prototype.forEach.call(formCAPTCHAs, function (element) {
                            formHash = element.getAttribute('data-wpfs-form-hash');
                            //noinspection JSUnresolvedVariable
                            reCAPTCHAWidgetId = googleReCAPTCHA.render(element, {
                                'sitekey': wpfsFormSettings.googleReCaptchaSiteKey
                            });
                            reCAPTCHAWidgetIds[formHash] = reCAPTCHAWidgetId;
                        });
                    }
                }, true);
            };

            WPFS.initPlanRadioButton = function () {
                $('input.wpfs-subscription-plan-radio').change(function (e) {
                    var $form = getParentForm(this);
                    handlePlanChange($form);
                });
            };

            function handleBuyingAsBusinessChange( $form ) {
                if ( $('input[name="wpfs-buying-as-business"]', $form).prop('checked')) {
                    $('#wpfs-business-name-row', $form).show();
                    $('#wpfs-tax-id-row', $form).show();
                } else {
                    $('#wpfs-business-name-row', $form).hide();
                    $('#wpfs-tax-id-row', $form).hide();
                    $('input[name="wpfs-tax-id"]', $form).val('');

                    if ( isDynamicTaxesForm($form) ) {
                        refreshPricingFromServer($form);
                    }
                }
            }

            WPFS.initBuyingAsBusiness = function () {
                $('input[name="wpfs-buying-as-business"]').change(function (e) {
                    var $form = getParentForm(this);
                    handleBuyingAsBusinessChange($form);
                });

                $('input[name="wpfs-tax-id"]').change(function (e) {
                    var $form = getParentForm(this);

                    if ( isDynamicTaxesForm( $form ) ) {
                        refreshPricingFromServer($form);
                    }
                });
            };

            function handleTaxCountryChange( $form ) {
                var selectedCountry = $('select[name="wpfs-tax-country"] option:selected', $form).val();

                if (selectedCountry === COUNTRY_CODE_UNITED_STATES) {
                    $('#wpfs-tax-state-row', $form).show();
                } else {
                    $('#wpfs-tax-state-row', $form).hide();
                }

                if ( isDynamicTaxesForm( $form ) ) {
                    refreshPricingFromServer( $form );
                }
            }

            function handleTaxStateChange( $form ) {
                if ( isDynamicTaxesForm( $form ) ) {
                    refreshPricingFromServer( $form );
                }
            }

            function handleQuantityChange($form) {
                refreshPricingFromServer( $form );
            }

            function extractFormNameFromNode( $form ) {
                return $form.data('wpfs-form-id');
            }

            function extractFormTypeFromNode( $form ) {
                return $form.data('wpfs-form-type');
            }

            function extractTaxRateTypeFromNode( $form ) {
                return $form.data('wpfs-tax-rate-type');
            }

            function setPaymentDetailsData( $form, data ) {
                var formName = extractFormNameFromNode( $form );

                WPFS.setPaymentDetails( formName, data );
            }

            function refreshPaymentDetails( $form ) {
                var formType = extractFormTypeFromNode( $form );

                if ( formType === FORM_TYPE_INLINE_PAYMENT || formType === FORM_TYPE_CHECKOUT_PAYMENT ||
                     formType === FORM_TYPE_INLINE_DONATION || formType === FORM_TYPE_CHECKOUT_DONATION ) {
                    handleAmountChange( $form );
                } else if ( formType === FORM_TYPE_INLINE_SUBSCRIPTION || formType === FORM_TYPE_CHECKOUT_SUBSCRIPTION ) {
                    handlePlanChange( $form );
                }
            }

            function createCustomAmountPricingData( $form, customAmount ) {
                return [{
                    'type':         'product',
                    'id':           'customAmount',
                    'displayName':  wpfsFormSettings.l10n.products.other_amount_label,
                    'currency':     $('.wpfs-custom-amount--unique', $form).data('wpfs-currency'),
                    'amount':       customAmount
                }];
            }

            function refreshOtherPricingLocally( $form ) {
                var customAmount = parseCustomAmountUnique($form);

                if ( !isNaN(customAmount) ) {
                    WPFS.setPaymentDetailsForPrice(
                        extractFormNameFromNode( $form ),
                        'customAmount',
                        createCustomAmountPricingData( $form, customAmount )
                    );

                    refreshPaymentDetails( $form );
                }
            }

            function refreshPricingFromServer( $form ) {
                var data = gatherFormDataForTaxCalculation( $form );
                data.action = SERVER_ACTION_CALCULATE_PRICING;
                
                clearPaymentDetails($form);
                showLoadingAnimation($form);
                disableFormInputsSelectsButtons($form);

                $.ajax({
                    type: 'POST',
                    url: wpfsFormSettings.ajaxUrl,
                    data: data,
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.success ) {
                            setPaymentDetailsData( $form, data.productPricing );
                            refreshPaymentDetails( $form );
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('refreshPricingFromServer', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                        hideLoadingAnimation($form);
                        enableFormInputsSelectsButtons($form);
                    }
                });
            }

            WPFS.initTaxCountry = function () {
                $('select.wpfs-tax-country-select').on('selectmenuchange', function (e) {
                    var $form = getParentForm(this);
                    handleTaxCountryChange($form);
                });
            };

            function readProductPricing() {
                if ( typeof(wpfsProductPricing) !== 'undefined' ) {
                    for (var key in wpfsProductPricing) {
                        if (wpfsProductPricing.hasOwnProperty(key)) {
                            WPFS.setPaymentDetails(key, wpfsProductPricing[key]);
                        }
                    }
                }
            }

            WPFS.ready = function () {

                if (debugLog) {
                    logInfo('ready', 'CALLED');
                }

                readProductPricing();

                // tnagy trigger plan change to initialize payment summary
                var $planSelectElements = $('select.wpfs-subscription-plan-select');
                $planSelectElements.each(function () {
                    var $form = getParentForm(this);
                    if ($('option:selected', $(this)).length) {
                        $(this).val($('option:selected', $(this)).val());
                    } else {
                        $(this).val($('option:first', $(this)).val());
                    }
                    handlePlanChange($form);
                });
                var $planRadioElements = $('input.wpfs-subscription-plan-radio');
                $planRadioElements.each(function () {
                    var $form = getParentForm(this);
                    if ($('input:radio[name="wpfs-plan"]:not(:disabled):checked', $form).length === 0) {
                        $('input:radio[name="wpfs-plan"]:not(:disabled):first', $form).attr('checked', true);
                    }
                    handlePlanChange($form);
                });
                var $planHiddenElements = $('input.wpfs-subscription-plan-hidden');
                $planHiddenElements.each(function () {
                    var $form = getParentForm(this);
                    handlePlanChange($form);
                });

                $('input.wpfs-custom-amount').each(function (index) {
                    var $form = getParentForm(this);
                    $form.find('input.wpfs-custom-amount').first().click();
                });

                // tnagy trigger payment amount change to initialize payment summary
                var $paymentsFormWithListOfAmounts = $('form[data-wpfs-amount-type="list_of_amounts"]');
                $paymentsFormWithListOfAmounts.each(function () {
                    var $form = $(this);
                    handleAmountChange($form);
                });
                var $paymentFormsWithSpecifiedAmount = $('form[data-wpfs-amount-type="specified_amount"]');
                $paymentFormsWithSpecifiedAmount.each(function () {
                    var $form = $(this);
                    handleAmountChange($form);
                });
                var $paymentFormsWithCustomAmount = $('form[data-wpfs-amount-type="custom_amount"]');
                $paymentFormsWithCustomAmount.each(function () {
                    var $form = $(this);
                    handleAmountChange($form);
                });

                var $spinnerElements = $('input[name="wpfs-plan-quantity"]');
                $spinnerElements.each(function () {
                    var $form = getParentForm(this);
                    $('input[name="wpfs-plan-quantity"]', $form).on('spinchange', function (e, ui) {
                        handleQuantityChange($form);
                    });
                });
            };

            // tnagy initialize components
            WPFS.initInputGroup();
            WPFS.initSelectmenu();
            WPFS.initAddressSwitcher();
            WPFS.initTooltip();
            WPFS.initStepper();
            WPFS.initDatepicker();
            WPFS.initCombobox();
            WPFS.initCustomAmount();
            WPFS.initStripeJSCard();
            WPFS.initCoupon();
            WPFS.initCheckoutForms();
            WPFS.initReCaptcha();
            WPFS.initPlanRadioButton();
            WPFS.initBuyingAsBusiness();
            WPFS.initTaxCountry();

            WPFS.ready();

        }
    );

})(jQuery);
