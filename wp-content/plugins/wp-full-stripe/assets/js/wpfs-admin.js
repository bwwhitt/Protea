/*
 Plugin Name: WP Full Stripe
 Plugin URI: https://paymentsplugin.com
 Description: Complete Stripe payments integration for Wordpress
 Author: Mammothology
 Version: 6.0.10
 Author URI: https://paymentsplugin.com
 */

jQuery.noConflict();
(function ($) {
    $(function () {

        const PAYMENT_TYPE_SPECIFIED_AMOUNT = 'specified_amount';
        const PAYMENT_TYPE_CARD_CAPTURE = 'card_capture';
        const CURRENCY_USD = "usd";

        const ERROR_MESSAGE_FIELD_CLASS = 'wpfs-form-error-message';
        const FIELD_DESCRIPTOR_MACRO_FIELD_ID = '{fieldId}';

        const COOKIE_NAME_TAB_ID = 'wpfsTabId';

        const TRANSACTION_TAB_PAYMENTS = 'payments';
        const TRANSACTION_TAB_SUBSCRIPTIONS = 'subscriptions';
        const TRANSACTION_TAB_DONATIONS = 'donations';
        const TRANSACTION_TAB_SAVED_CARDS = 'saved-cards';

        const FIELD_TYPE_INPUT = 'input';
        const FIELD_TYPE_INPUT_DECORATED = 'input-decorated';
        const FIELD_TYPE_INPUT_CHECK_ITEM = 'input-check-item';
        const FIELD_TYPE_INPUT_CUSTOM = 'input-custom';
        const FIELD_TYPE_INPUT_GROUP = 'input-group';
        const FIELD_TYPE_DROPDOWN = 'dropdown';
        const FIELD_TYPE_CHECKBOX = 'checkbox';
        const FIELD_TYPE_CHECKLIST = 'checklist';
        const FIELD_TYPE_PRODUCTS = 'products';
        const FIELD_TYPE_CARD = 'card';
        const FIELD_TYPE_CAPTCHA = 'captcha';
        const FIELD_TYPE_TAGS = 'tags';

        const FORM_FIELD_CUSTOM_FIELD_LABEL = 'wpfs-custom-field-label';
        const FORM_FIELD_SUGGESTED_DONATION_AMOUNT = 'wpfs-suggested-donation-amount';
        const FORM_FIELD_PLAN_SETUP_FEE = 'wpfs-plan-setup-fee';
        const FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS = 'wpfs-plan-trial-period-days';
        const FORM_FIELD_END_SUBSCRIPTION = 'wpfs-end-subscription';
        const FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT = 'wpfs-subscription-cancellation-count';
        const FORM_FIELD_BILLING_CYCLE = 'wpfs-billing-cycle';
        const FORM_FIELD_BILLING_CYCLE_DAY = 'wpfs-billing-cycle-day';
        const FORM_FIELD_PRORATE_UNTIL_BILLING_ANCHOR_DAY = 'wpfs-prorate-until-billing-anchor-day';

        const FIELD_VALUE_TAX_RATE_NO_TAX = 'taxRateNoTax';
        const FIELD_VALUE_TAX_RATE_FIXED = 'taxRateFixed';
        const FIELD_VALUE_TAX_RATE_DYNAMIC = 'taxRateDynamic';

        const PRICE_USAGE_TYPE_METERED = 'metered';
        const PRICE_USAGE_TYPE_LICENSED = 'licensed';
        const PRICE_BILLING_SCHEME_TIERED = 'tiered';
        const PRICE_BILLING_SCHEME_PER_UNIT = 'per_unit';
        const PRICE_MODE_STANDARD = 'standard';
        const PRICE_MODE_GRADUATED = 'graduated';
        const PRICE_MODE_VOLUME = 'volume';

        const FORM_LAYOUT_INLINE = 'inline';
        const FORM_LAYOUT_CHECKOUT = 'checkout';

        const WPFS_DECIMAL_SEPARATOR_DOT = 'dot';
        const WPFS_DECIMAL_SEPARATOR_COMMA = 'comma';

        const PROPERTY_NAME_BINDING_RESULT = 'bindingResult';

        var debugLog = false;

        function WPFS_UserFriendlyException(message) {
            this.message = message;
            // Use V8's native method if available, otherwise fallback
            if ("captureStackTrace" in Error)
                Error.captureStackTrace(this, InvalidArgumentException);
            else
                this.stack = (new Error()).stack;
        }

        WPFS_UserFriendlyException.prototype = Object.create(Error.prototype);
        WPFS_UserFriendlyException.prototype.name = "WPFS_UserFriendlyException";
        WPFS_UserFriendlyException.prototype.constructor = WPFS_UserFriendlyException;


        function logException(source, response) {
            if (window.console && response) {
                if (response.ex_msg) {
                    console.log('ERROR: source=' + source + ', message=' + response.ex_msg);
                }
                if (response.ex_stack) {
                    console.log('ERROR: source=' + source + ', stack=' + response.ex_stack);
                }
            }
        }

        function copyToClipboard(str) {
            var el = document.createElement('textarea');
            el.value = str;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            var selected =
                document.getSelection().rangeCount > 0
                    ? document.getSelection().getRangeAt(0)
                    : false;
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            if (selected) {
                document.getSelection().removeAllRanges();
                document.getSelection().addRange(selected);
            }
        }

        function createAdminCurrencyFormatter() {
            var decimalSeparator = wpfsAdminSettings.preferences.currencyDecimalSeparatorSymbol;
            var showCurrencySymbolInsteadOfCode = wpfsAdminSettings.preferences.currencyShowSymbolInsteadOfCode;
            var showCurrencySignAtFirstPosition = wpfsAdminSettings.preferences.currencyShowIdentifierOnLeft;
            var putWhitespaceBetweenCurrencyAndAmount = wpfsAdminSettings.preferences.currencyPutSpaceBetweenCurrencyAndAmount;
            // logInfo('createAdminCurrencyFormatter', 'decimalSeparator=' + JSON.stringify(decimalSeparator));
            // logInfo('createAdminCurrencyFormatter', 'showCurrencySymbolInsteadOfCode=' + JSON.stringify(showCurrencySymbolInsteadOfCode));
            // logInfo('createAdminCurrencyFormatter', 'showCurrencySignAtFirstPosition=' + JSON.stringify(showCurrencySignAtFirstPosition));
            // logInfo('createAdminCurrencyFormatter', 'putWhitespaceBetweenCurrencyAndAmount=' + JSON.stringify(putWhitespaceBetweenCurrencyAndAmount));

            return WPFSCurrencyFormatter(
                decimalSeparator,
                showCurrencySymbolInsteadOfCode,
                showCurrencySignAtFirstPosition,
                putWhitespaceBetweenCurrencyAndAmount
            );
        }

        function showError(message) {
            showMessage('error', 'updated', message);
        }

        function showUpdate(message) {
            showMessage('updated', 'error', message);
        }

        var WPFS = {};

        WPFS.KeyboardKeys = {
            isEnter: function(e) {
                return e.which === 13 || e.key === 'Enter' || e.code === 'Enter';
            },
            isSpace: function(e) {
                return e.which === 32 || e.key === ' ' || e.code === 'Space';
            },
            isBackspace: function(e) {
                return e.which === 8 || e.key === 'Backspace' || e.code === 'Backspace';
            },
            isEsc: function(e) {
                return e.which === 27 || e.key === 'Escape' || e.code === 'Escape';
            }
        };

        WPFS.InputGroup = {
            init: function() {
                var inputGroupPrependClass = '.wpfs-input-group-prepend';
                $(document).on('click', inputGroupPrependClass, function(e) {
                    var $target = $(e.target);
                    if ($target.hasClass('wpfs-input-group-link')) {
                        return;
                    }

                    if ($target.parents('.wpfs-input-group-link').length > 0) {
                        return;
                    }

                    $(this).next().focus();
                });

                $(document).on('mouseenter', inputGroupPrependClass, function() {
                    $(this).next().mouseenter();
                });

                $(document).on('mouseleave', inputGroupPrependClass, function() {
                    $(this).next().mouseleave();
                });

                var inputGroupAppendClass = '.wpfs-input-group-append';
                $(document).on('click', inputGroupAppendClass, function(e) {
                    var $target = $(e.target);
                    if ($target.hasClass('wpfs-input-group-link')) {
                        return;
                    }

                    if ($target.parents('.wpfs-input-group-link').length > 0) {
                        return;
                    }

                    $(this).prev().focus();
                });

                $(document).on('mouseenter', inputGroupAppendClass, function() {
                    $(this).prev().mouseenter();
                });

                $(document).on('mouseleave', inputGroupAppendClass, function() {
                    $(this).prev().mouseleave();
                });
            }
        };

        WPFS.Selectmenu = {
            init: function() {
                $.widget('custom.wpfsSelectmenu', $.ui.selectmenu, {
                    _renderItem: function(ul, item) {
                        var $li = $('<li>');
                        var wrapper = $('<div>', {
                            class: 'menu-item-wrapper ui-menu-item-wrapper',
                            text: item.label
                        });

                        if (item.disabled) {
                            $li.addClass('ui-state-disabled');
                        } else if (item.element[0].selected) {
                            wrapper.addClass('ui-state-selected');
                        }

                        return $li.append(wrapper).appendTo(ul);
                    }
                });

                var $selectmenus = $('.js-selectmenu');
                $selectmenus.each(function() {
                    if (typeof $(this).select2 === 'function') {
                        try {
                            $(this).select2('destroy');
                        } catch (err) {}
                    }

                    var $selectmenu = $(this).wpfsSelectmenu({
                        classes: {
                            'ui-selectmenu-button': 'wpfs-form-control wpfs-selectmenu-button',
                            'ui-selectmenu-menu': 'wpfs-ui wpfs-selectmenu-menu'
                        },
                        icons: {
                            button: 'wpfs-icon-chevron'
                        },
                        create: function() {
                            var $this = $(this);
                            var $selectMenuButton = $this.next();
                            $selectMenuButton.addClass($this.attr('class'));
                            if ($this.find('option:selected:disabled').length > 0) {
                                $selectMenuButton.addClass('ui-state-placeholder');
                            }

                            if ($(this).data('selectmenu-prefix')) {
                                $selectMenuButton.find('.ui-selectmenu-text').text($(this).data('selectmenu-prefix') + $selectMenuButton.text());
                            }
                        },
                        open: function() {
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;

                            $button.removeClass('ui-selectmenu-button-closed');
                            $button.addClass('ui-selectmenu-button-open');

                            var dialogZIndex = parseInt($('.wpfs-dialog').css('zIndex'), 10);
                            if (!isNaN(dialogZIndex)) {
                                $('.ui-selectmenu-open').css('zIndex', dialogZIndex + 1);
                            }
                        },
                        close: function() {
                            var $this = $(this);
                            var wpfsSelectmenu = $this.data('custom-wpfsSelectmenu');
                            var $button = wpfsSelectmenu.button;
                            $button.removeClass('ui-selectmenu-button-open');
                            $button.addClass('ui-selectmenu-button-closed');

                            setTimeout(function() {
                                var selectedClass = 'ui-state-selected';
                                var selectedIndex = $this.find('option').index($this.find('option:selected'));
                                wpfsSelectmenu.menu.find('.ui-menu-item-wrapper').removeClass(selectedClass);
                                var $menuItem = wpfsSelectmenu.menu.find('.ui-menu-item').eq(selectedIndex);
                                if (!$menuItem.hasClass('ui-state-disabled')) {
                                    $menuItem.find('.ui-menu-item-wrapper').addClass(selectedClass);
                                }
                            }, 100);
                        },
                        change: function() {
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;
                            if ($this.data('selectmenu-prefix')) {
                                $button.find('.ui-selectmenu-text').text($this.data('selectmenu-prefix') + $button.text());
                            }
                            $button.removeClass('ui-state-placeholder');
                            $this.trigger('selectmenuchange');
                        }
                    });

                    $selectmenu.on('selectmenuselect', function() {
                        var $button = $(this).data('custom-wpfsSelectmenu').button;
                        if ($(this).data('selectmenu-prefix')) {
                            $button.find('.ui-selectmenu-text').text($(this).data('selectmenu-prefix') + $button.text());
                        }
                    });

                    $selectmenu.parent().find('.ui-selectmenu-button')
                        .addClass('wpfs-form-control')
                        .addClass('wpfs-selectmenu-button')
                        .addClass('ui-button');

                    $selectmenu.data('custom-wpfsSelectmenu').menuWrap
                        .addClass('wpfs-ui')
                        .addClass('wpfs-selectmenu-menu');
                });
            }
        };

        WPFS.Combobox = {
            init: function() {
                $.widget('custom.combobox', {
                    _selectOptions: [],
                    _lastValidValue: null,
                    _create: function() {
                        this.wrapper = $('<div>')
                            .addClass('wpfs-input-group wpfs-combobox')
                            .addClass(this.element.attr('class'))
                            .insertAfter(this.element);

                        this._selectOptions = this.element.children('option').map(function() {
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

                        var selectedOption = this.element.find(':selected');
                        if (selectedOption.length !== 0) {
                            this._lastValidValue = selectedOption.val();
                        }

                        this.element.hide();
                        this._createAutocomplete();
                        this._createShowAllButton();
                    },
                    _createAutocomplete: function() {
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
                                    my: 'left-1px top+2.5px',
                                    at: 'left-1px bottom+2.5px',
                                    using: function(position, feedback) {
                                        var $this = $(this);
                                        $this.css(position);
                                        $this.width(feedback.target.width + 48);
                                    }
                                },
                                classes: {
                                    'ui-autocomplete': 'wpfs-ui wpfs-combobox-menu'
                                },
                                open: function() {
                                    $(this).parent().addClass('wpfs-combobox--open');
                                },
                                close: function() {
                                    var $this = $(this);
                                    $this.parent().removeClass('wpfs-combobox--open');
                                    $this.blur();
                                },
                                search: function(e, ui) {
                                    // Fix autocomplete combobox memory leak
                                    $(this).data('uiAutocomplete').menu.bindings = $();
                                }
                            })
                            .on('focus', function() {
                                $(this).data('uiAutocomplete').search('');
                            })
                            .on('keydown', function(e) {
                                if (WPFS.KeyboardKeys.isEnter(e)) {
                                    $(this).blur();
                                }
                            });

                        this.input.data('uiAutocomplete')._renderItem = this._renderItem;

                        this._on(this.input, {
                            autocompleteselect: function(e, ui) {
                                if (!ui.item.noResultsItem) {
                                    ui.item.option.selected = true;
                                    this._trigger('select', e, {
                                        item: ui.item.option
                                    });
                                    this.element.trigger('comboboxchange');
                                }
                                this._trigger('blur');
                            },
                            autocompletechange: function(e, ui) {
                                this._validateValue(e, ui);
                                this.element.trigger('comboboxchange');
                            }
                        });
                    },
                    _createShowAllButton: function() {
                        var input = this.input;
                        var wasOpen = false;
                        var html = '<div class="wpfs-input-group-append"><span class="wpfs-input-group-icon"><span class="wpfs-icon-chevron"></span></span></div>';

                        $(html)
                            .appendTo(this.wrapper)
                            .on('mousedown', function() {
                                wasOpen = input.autocomplete('widget').is(':visible');
                            })
                            .on('click', function(e) {
                                e.stopPropagation();
                                if (wasOpen) {
                                    input.autocomplete('close');
                                } else {
                                    input.trigger('focus');
                                }
                            });
                    },
                    _source: function(request, response) {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
                        var results = this._selectOptions.map(function(i, option) {
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
                    _validateValue: function(e, ui) {
                        // Selected an item, nothing to do
                        if (ui.item) {
                            return;
                        }

                        // Search for a match (case-insensitive)
                        var value = this.input.val();
                        var valueLowerCase = value.toLowerCase();
                        var valid = false;
                        var selectedText = null;
                        this.element.children('option').each(function() {
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
                    _renderItem: function(ul, item) {
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
                    _destroy: function() {
                        this.wrapper.remove();
                        this.element.show();
                    },
                    refresh: function() {
                        this._destroy();
                        this._create();
                    }
                });

                $('.js-combobox').combobox();
            }
        };

        WPFS.Tooltip = {
            init: function() {
                $('.js-tooltip').tooltip({
                    show: 300,
                    items: '.js-tooltip',
                    content: function() {
                        var contentId = $(this).data('tooltip-content');
                        return $('[data-tooltip-id="' + contentId + '"]').html();
                    },
                    position: {
                        my: 'left top+4',
                        at: 'left bottom+4',
                        using: function(position, feedback) {
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
                });
            }
        };

        WPFS.FlashMessage = {
            init: function() {
                $(document.body).on('click', '.js-hide-flash-message', function(e) {
                    e.preventDefault();
                    var $flashMessage = $(this).parents('.js-flash-message');
                    $flashMessage.removeClass('wpfs-floating-message--show');
                });
            },
            close: function(delay) {
                setTimeout(function() {
                    $('.js-flash-message').removeClass('wpfs-floating-message--show');
                }, delay || 3000);
            }
        };

        WPFS.InlineMessage = {
            init: function() {
                $(document).on('click', '.js-close-this-message', function(e) {
                    e.preventDefault();
                    $(this).parents('.js-inline-message').addClass('wpfs-inline-message--hide');
                });
            }
        };

        WPFS.FormSearch = {
            init: function() {
                function checkInput(el) {
                    if ($(el).val() !== '') {
                        $(el).addClass('wpfs-form-search__input--active');
                    } else {
                        $(el).removeClass('wpfs-form-search__input--active');
                    }
                }

                var $formSearchEls = $('.js-form-search');
                $formSearchEls.each(function() {
                    var $searchInput = $(this).find('input');
                    checkInput($searchInput);
                    $searchInput.on('change', function() {
                        checkInput(this);
                    });
                });
            }
        };

        WPFS.SidePane = {
            init: function() {
                $('.js-open-side-pane').on('click', function(e) {
                    e.preventDefault();
                    var tooltip = $(this).data('ui-tooltip');
                    if (tooltip) {
                        tooltip.close();
                    }
                    var sidePaneId = $(this).data('side-pane-id');
                    WPFS.SidePane.open(sidePaneId);
                });

                $('.js-close-side-pane').on('click', function(e) {
                    e.preventDefault();
                    WPFS.SidePane.close();
                });

                $('.js-side-pane').on('click', function(e) {
                    if (!$(e.target).closest('.wpfs-side-pane').length) {
                        e.preventDefault();
                        WPFS.SidePane.close();
                    }
                });
            },
            open: function(sidePaneId) {
                $('[data-side-pane-id="' + sidePaneId + '"]').addClass('wpfs-side-pane-overlay--show');
                $(document).on('keyup.wpfs-side-pane', function(e) {
                    if (WPFS.KeyboardKeys.isEsc(e) && $('.wpfs-dialog-open').length === 0) {
                        WPFS.SidePane.close();
                    }
                });
                $(document.body).addClass('wpfs-side-pane-open');
            },
            close: function() {
                $('.wpfs-side-pane-overlay--show').removeClass('wpfs-side-pane-overlay--show');
                $(document).off('keyup.wpfs-side-pane');
                $(document.body).removeClass('wpfs-side-pane-open');
            }
        };

        WPFS.Dialog = {
            open: function(selector, options) {
                options = options || {};

                $(document.body).append('<div class="wpfs-dialog-container"></div>');

                $(selector).dialog({
                    resizable: false,
                    draggable: false,
                    height: 'auto',
                    width: options.wide ? 640 : 480,
                    modal: true,
                    dialogClass: 'wpfs-dialog' + (options.wide ? ' wpfs-dialog--wide' : ''),
                    closeText: '',
                    appendTo: '.wpfs-dialog-container',
                    closeOnEscape: false,
                    open: function() {
                        $(document.body).addClass('wpfs-dialog-open');

                        $('.ui-widget-overlay').addClass('wpfs-dialog-overlay');

                        $(this).find('.js-close-this-dialog').on('click.wpfs-dialog', function(e) {
                            e.preventDefault();
                            $(selector).dialog('close');
                        });

                        $(document).on('keyup.wpfs-dialog', function(e) {
                            if (WPFS.KeyboardKeys.isEsc(e)) {
                                $(selector).dialog('close');
                            }
                        });
                    },
                    close: function() {
                        $(document).off('keyup.wpfs-dialog');
                        $(document.body).removeClass('wpfs-dialog-open');
                        $('.wpfs-dialog-container').remove();
                        $('.ui-widget-overlay').removeClass('wpfs-dialog-overlay');
                        $(this).find('.js-close-this-dialog').off('click.wpfs-dialog');
                    }
                });
            }
        };

        WPFS.HelpDropdown = {
            init: function() {
                $('.js-open-help-dropdown').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var $this = $(this);

                    $this.blur();

                    $('.js-help-dropdown').fadeIn(300);
                    var tooltip = $this.data('ui-tooltip');
                    if (tooltip) {
                        tooltip.disable();
                    }

                    $(document.body).on('click.wpfs-help-dropdown', function(e) {
                        if (!$(e.target).closest('.js-help-dropdown').length) {
                            if (tooltip) {
                                tooltip.enable();
                            }

                            $('.js-help-dropdown').fadeOut(300);

                            $(document.body).off('click.wpfs-help-dropdown');
                            $(document.body).off('keyup.wpfs-help-dropdown');
                        }
                    });

                    $(document).on('keyup.wpfs-help-dropdown', function(e) {
                        if (WPFS.KeyboardKeys.isEsc(e)) {
                            $(document.body).trigger('click');
                        }
                    });
                });
            }
        };

        WPFS.Controls = {
            init: function() {
                $('.js-reset-controls').on('click', function(e) {
                    e.preventDefault();
                    var $parent = $(this).parent();
                    var $input = $parent.find('input');

                    $input.val('');
                    $input.trigger('change');

                    $parent.find('select').each(function() {
                        $(this).prop('selectedIndex', 0);
                        $(this).wpfsSelectmenu('refresh').trigger('selectmenuselect');
                    });
                });
            }
        };

        WPFS.Carousel = {
            init: function() {
                $('.js-carousel').slick({
                    prevArrow: '<button type="button" class="slick-prev"><div class="wpfs-icon-arrow-left"></div></button>',
                    nextArrow: '<button type="button" class="slick-next"><div class="wpfs-icon-arrow-left"></div></button>',
                    dots: true
                });
            }
        };

        WPFS.ShortcodePopover = {
            init: function() {
                $('.js-open-shortcode-popover').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $('.js-open-shortcode-popover').not(this).each(function() {
                        var tooltip = $(this).data('ui-tooltip');
                        if (tooltip) {
                            tooltip.enable();
                        }
                    });

                    var $this = $(this);
                    var $shortcodePopover = $('.js-shortcode-popover');

                    $this.blur();

                    $shortcodePopover.find('input').val($this.data('shortcode-value'));

                    var $window = $(window);
                    var viewportHeight = $window.height() + $window.scrollTop();
                    var popoverTop = $this.offset().top - 32;
                    var popoverBottomTop = popoverTop + $shortcodePopover.outerHeight() + 60;

                    if (viewportHeight > popoverBottomTop) {
                        $shortcodePopover.css({
                            top: popoverTop + 28
                        });
                    } else {
                        $shortcodePopover.css({
                            top: popoverTop - $shortcodePopover.outerHeight() - 8
                        });
                    }

                    $shortcodePopover.fadeIn(300);
                    var tooltip = $this.data('ui-tooltip');
                    if (tooltip) {
                        tooltip.disable();
                    }

                    $(document.body).on('click.wpfs-shortcode-popover', function(e) {
                        if (!$(e.target).closest('.js-shortcode-popover').length) {
                            if (tooltip) {
                                tooltip.enable();
                            }

                            $shortcodePopover.fadeOut(300);

                            $(document.body).off('click.wpfs-shortcode-popover');
                            $(document.body).off('keyup.wpfs-shortcode-popover');
                        }
                    });

                    $(document).on('keyup.wpfs-shortcode-popover', function(e) {
                        if (WPFS.KeyboardKeys.isEsc(e)) {
                            $(document.body).trigger('click');
                        }
                    });
                });

                $('.js-close-shortcode-popover').on('click', function(e) {
                    e.preventDefault();
                    $(document.body).trigger('click');
                });

                $('.js-copy-shortcode').on('click', function(e) {
                    e.preventDefault();

                    var $shortcodePopover = $('.js-shortcode-popover');
                    var shortcode = $shortcodePopover.find('input').val();
                    copyToClipboard( shortcode );
                    displaySuccessMessageBanner(wpfsAdminL10n.shortcodeCopiedMessage);

                    $(document.body).trigger('click');
                });
            }
        };

        WPFS.CodeEditor = {
            editors: [],
            init: function() {
                $('.js-code-editor').each(function() {
                    this.value = this.value
                        .split('\n')
                        .map(function(row) {
                            return row.trim();
                        })
                        .join('\n');

                    var mode = null;
                    if (this.dataset['editorMode'] === 'html') {
                        mode = 'html';
                        this.value = html_beautify(this.value, {
                            indent_size: '2',
                            extra_liners: []
                        });
                    } else if (this.dataset['editorMode'] === 'css') {
                        mode = 'css';
                        this.value = css_beautify(this.value, {
                            indent_size: '2'
                        });
                    }

                    ace.config.set('basePath', wpfsAdminSettings.aceEditorPath);
                    var editor = ace.edit(this);
                    editor.setOptions({
                        minLines: 20,
                        maxLines: 20,
                        selectionStyle: 'text',
                        highlightActiveLine: false,
                        highlightSelectedWord: true
                    });
                    editor.setTheme('ace/theme/solarized_dark');
                    editor.session.setMode('ace/mode/' + mode);
                    editor.session.setTabSize(2);
                    editor.session.setUseWrapMode(true);
                    editor.getSession().setUseWorker(false);

                    WPFS.CodeEditor.editors.push( editor );
                });
            },
            getEditorValue: function( idx ) {
                return WPFS.CodeEditor.editors[idx].getValue();
            }
        };

        WPFS.ColorPicker = {
            init: function() {
                $('.js-color-picker').each(function() {
                    var $parent = $(this).parent();
                    var $input = $parent.find('input');
                    var pickr = Pickr.create({
                        el: '.js-color-picker',
                        theme: 'nano',
                        components: {
                            preview: true,
                            opacity: true,
                            hue: true
                        },
                        default: $input.val()
                    });

                    var typing = false;
                    $input.on('keyup', function() {
                        pickr.setColor($(this).val(), false);
                        typing = true;
                    });

                    $input.on('blur', function() {
                        typing = false;
                    });

                    pickr.on('change', function(color, instance) {
                        if (!typing) {
                            var hex = '#' + color.toHEXA().join('').toUpperCase();
                            $input.val(hex);
                            $parent.find('button').css('color', hex);
                        }
                    });
                });
            }
        };

        WPFS.Webhook = {
            init: function() {
                $('.js-webhook-info-toggler').on('click', function(e) {
                    e.preventDefault();
                    var btnOpenedClass = 'wpfs-webhook__info-toggler--open';
                    var webhookInfoClass = 'wpfs-webhook__inline-message--open';
                    var $this = $(this);
                    var $btnTextEl = $this.find('span:first');

                    if ($this.hasClass(btnOpenedClass)) {
                        $btnTextEl.text($this.data('closed-text'));
                        $this.removeClass(btnOpenedClass);
                        $this.next().removeClass(webhookInfoClass);
                    } else {
                        $btnTextEl.text($this.data('opened-text'));
                        $this.addClass(btnOpenedClass);
                        $this.next().addClass(webhookInfoClass);
                    }
                });
            }
        };

        WPFS.ToPascalCase = {
            init: function() {
                $('.js-to-pascal-case').on('blur', function() {
                    var $this = $(this);
                    var $input = $($this.data('to-pascal-case'));

                    if ( $this.val() !== '' &&
                         $input.val().trim() === '' ) {
                        if ($input.length > 0) {
                            $input.val(WPFS.ToPascalCase.get($this.val()));
                        }
                    }
                });
            },
            get: function(str) {
                return str.match(/[a-z]+/gi)
                    .map(function(word) {
                        return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
                    })
                    .join('');
            }
        };

        WPFS.TagsInput = {
            tagTemplate: '<div class="wpfs-tag wpfs-tag--removable">{{tag}}<button class="wpfs-btn wpfs-btn-icon wpfs-btn-icon--12 wpfs-tag__remove js-remove-tag"><span class="wpfs-icon-close"></span></button></div>',
            generateItem: function(element) {
                var template = WPFS.TagsInput.tagTemplate.replace('{{tag}}', element.val().trim());
                $(template).insertBefore(element);
                element.val('');
            },
            init: function() {
                var self = this;
                $('.js-tags-input input').on({
                    keydown: function(e) {
                        var $this = $(this);
                        if ((WPFS.KeyboardKeys.isEnter(e) || WPFS.KeyboardKeys.isSpace(e)) && $this.val().trim() !== '') {
                            e.preventDefault();
                            self.generateItem($this);
                        }

                        if (WPFS.KeyboardKeys.isBackspace(e) && $this.val() === '') {
                            var $parent = $this.parent();
                            $parent.find('.wpfs-tag:last').remove();
                        }
                    },
                    blur: function() {
                        var $this = $(this);
                        if ($this.val().trim() !== '') {
                            self.generateItem($this);
                        }
                    }
                });

                $(document).on('click', '.js-remove-tag', function(e) {
                    e.preventDefault();
                    var $this = $(this);
                    if ($this.hasClass('wpfs-tag')) {
                        $(this).remove();
                    } else {
                        $(this).parents('.wpfs-tag').remove();
                    }
                });
            }
        };

        WPFS.InsertToken = {
            init: function(source, clickSelector, targetSelector) {
                var dialogSelector = '#wpfs-insert-token-dialog';
                var tokenTargetSelector = targetSelector;
                var tokenAutocompleteSelector = '.js-token-autocomplete';

                $(clickSelector).off('click.insert-token');
                $(clickSelector).on('click.insert-token', function(e) {
                    e.preventDefault();
                    WPFS.InsertToken.init(source, clickSelector, targetSelector);
                    $(tokenAutocompleteSelector).val('');
                    WPFS.Dialog.open(dialogSelector);
                });

                $(tokenAutocompleteSelector).autocomplete({
                    minLength: 0,
                    source: function(request, response) {
                        function hasMatch(s) {
                            return s.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
                        }

                        if (request.term === '') {
                            response(source);
                            return;
                        }

                        var matches = [];
                        for (var i = 0, l = source.length; i < l; i++) {
                            var obj = source[i];
                            if (hasMatch(obj.value) || hasMatch(obj.desc)) {
                                matches.push(obj);
                            }
                        }

                        response(matches);
                    },
                    appendTo: '.js-insert-token-dialog',
                    select: function(event, ui) {
                        var $target = $(tokenTargetSelector);
                        if ($target.length > 0) {
                            var cursorPos = $target.data('selectionStart');
                            if (cursorPos !== undefined) {
                                var text = $target.val();
                                var textBefore = text.substring(0,  cursorPos);
                                var textAfter  = text.substring(cursorPos, text.length);
                                $target.val(textBefore + ui.item.value + textAfter);
                            } else {
                                $target.val($target.val() + ui.item.value);
                            }
                        }
                        $(event.target).val('');
                        $(dialogSelector).dialog('close');
                    }
                }).autocomplete('instance')._renderItem = function(ul, item) {
                    ul.addClass('wpfs-token-list');
                    return $('<li class="wpfs-token-list__item">')
                        .append('<div class="wpfs-token-list__value">' + item.label + '</div>')
                        .append('<div class="wpfs-token-list__desc">' + item.desc + '</div>')
                        .appendTo(ul);
                };
                $(tokenAutocompleteSelector).autocomplete('search', '');

                $(tokenAutocompleteSelector).off('keydown.insert-token');
                $(tokenAutocompleteSelector).on('keydown.insert-token', function(e) {
                    if (WPFS.KeyboardKeys.isEsc(e)) {
                        $(dialogSelector).dialog('close');
                    }
                });
            }
        };

        WPFS.Toggler = {
            init: function() {
                $('.wpfs-toggler input[type=checkbox]').on('change', function() {
                    var $parent = $(this).parent();
                    if ($(this).is(':checked')) {
                        $parent.addClass('wpfs-toggler--checked');
                    } else {
                        $parent.removeClass('wpfs-toggler--checked');
                    }
                });
                $('.wpfs-toggler input[type=checkbox]:checked').parent().addClass('wpfs-toggler--checked');
            }
        };

        WPFS.Sortable = {
            init: function() {
                $('.js-sortable').sortable();
                $('.js-sortable').disableSelection();
            }
        };

        WPFS.InlineAutocomplete = function(options) {
            this.source = options.source;
            this.$input = $(options.selector);

            if ($('.' + options.containerClass).length !== 0) {
                $('.' + options.containerClass).remove();
            }
            this.$autocomplete = $('<div class="' + options.containerClass + '"/>').insertAfter(this.$input);

            this.bindInput = function() {
                var self = this;
                this.$input.off('keyup.inline-autocomplete');
                this.$input.on('keyup.inline-autocomplete', function() {
                    var $this = $(this);
                    var value = $this.val();
                    if (value === '') {
                        self.renderItems(self.source);
                    } else {
                        var result = [];
                        self.source.forEach(function(item) {
                            if (item.label.toLowerCase().indexOf(value.toLowerCase()) !== -1 ||
                                item.price.toLowerCase().indexOf(value.toLowerCase()) !== -1 ) {
                                result.push(item);
                            }
                        });
                        self.renderItems(result);
                    }
                });
            };

            this.renderItems = function(items) {
                this.$autocomplete.empty();

                var itemTemplate = this.$input.parent().find('script[type="text/template"]').text();

                var self = this;
                items.forEach(function(item) {
                    var itemContent = itemTemplate;

                    for (const key in item) {
                        itemContent = itemContent.replace(new RegExp('{' + key + '}', 'g'), item[key]);
                    }

                    self.$autocomplete.append(itemContent);
                });
            };

            this.$input.val('');
            this.renderItems(options.source);
            this.bindInput();
        };

        WPFS.FileInput = {
            readURL: function(input, img) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        img.attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            },
            init: function() {
                var self = this;
                $('.js-file-input input[type="file"]').on('change', function() {
                    var $this = $(this);
                    var $container = $this.parents('.js-file-input');
                    var $previewImg = $container.find('.wpfs-form-file__preview img');
                    $previewImg.attr('src', '');
                    $container.removeClass('wpfs-form-file--filled');
                    if ($this.val() !== '') {
                        $container.addClass('wpfs-form-file--filled');
                        self.readURL(this, $previewImg);
                    }
                });
            }
        };

        WPFS.Datepicker = {
            init: function() {
                $('.js-datepicker').each(function() {
                    var $this = $(this);
                    var dateFormat = $this.data('dateFormat') || 'dd/mm/yyyy';
                    var defaultValue = $this.data('defaultValue') || '';

                    if ($this.val() === '') {
                        $this.val(defaultValue);
                    }

                    if ($this.next().hasClass('wpfs-form-search__btn')) {
                        $this.next().on('click', function(e) {
                            e.preventDefault();
                            $this.trigger('focus');
                        });
                    }

                    $this
                        .datepicker({
                            prevText: '',
                            nextText: '',
                            hideIfNoPrevNext: true,
                            firstDay: 1,
                            dateFormat: dateFormat.replace(/yy/g, 'y'),
                            showOtherMonths: true,
                            selectOtherMonths: true,
                            onChangeMonthYear: function(year, month, inst) {
                                if (inst.dpDiv.hasClass('bottom')) {
                                    setTimeout(function() {
                                        inst.dpDiv.css('top', inst.input.offset().top - inst.dpDiv.outerHeight());
                                    });
                                }
                            },
                            beforeShow: function(el, inst) {
                                var $el = $(el);
                                inst.dpDiv.addClass('wpfs-ui wpfs-datepicker-div');
                                setTimeout(function() {
                                    if ($el.offset().top > inst.dpDiv.offset().top) {
                                        inst.dpDiv.removeClass('top');
                                        inst.dpDiv.addClass('bottom');
                                    } else {
                                        inst.dpDiv.removeClass('bottom');
                                        inst.dpDiv.addClass('top');
                                    }
                                });
                            }
                        });
                });
            }
        };

        function createDeleteFormData( model ) {
            return {
                action: 'wpfs-delete-form',
                id:     model.get('id'),
                type:   model.get('type'),
                layout: model.get('layout')
            };
        }

        function createCloneFormData( model ) {
            return {
                action:               'wpfs-clone-form',
                id:                   model.get('id'),
                type:                 model.get('type'),
                layout:               model.get('layout'),
                newFormName:          model.get('newFormName'),
                newFormDisplayName:   model.get('newFormDisplayName'),
                editNewForm:          model.get('editNewForm')
            };
        }

        function displaySuccessMessageBanner( message ) {
            var successMessageModel = new WPFS.SuccessMessageModel({
                successMessage:    message
            });
            var successMessageView = new WPFS.SuccessMessageView({
                model: successMessageModel
            })

            $('#wpfs-success-message-container').empty().append( successMessageView.render().el );
            WPFS.FlashMessage.init();
            WPFS.FlashMessage.close();
        }

        function setTimeoutToRedirect( redirectUrl, timeout ) {
            setTimeout(function () {
                window.location = redirectUrl;
            }, timeout);
        }

        function displayErrorMessageInDialog( dialogSelector, errorMessage ) {
            var modalDialogErrorModel = new WPFS.SuccessMessageModel({
                errorMessage: errorMessage
            });
            var modalDialogErrorView = new WPFS.ModalDialogErrorView({
                model:  modalDialogErrorModel
            });
            $(dialogSelector).empty().append( modalDialogErrorView.render().el );

            $('.js-close-this-dialog').on( 'click', function (event) {
                $(dialogSelector).dialog('close');
            });
        }

        function makeAjaxCallFromDialog( requestData, dialogSelector ) {
            $.ajax({
                type: "POST",
                url: wpfsAdminSettings.ajaxUrl,
                data: requestData,
                cache: false,
                dataType: "json",
                success: function (responseData) {
                    if (responseData.success) {
                        $(dialogSelector).dialog('close');

                        displaySuccessMessageBanner(responseData.msg);
                        setTimeoutToRedirect( responseData.redirectURL, 1000 );
                    } else {
                        displayErrorMessageInDialog( dialogSelector, responseData.msg );

                        logException('wpfs-admin.makeAjaxCallFromDialog()', responseData);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    displayErrorMessageInDialog( dialogSelector, textStatus );
                    logError('wpfs-admin.makeAjaxCallFromDialog()', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    // no op
                }
            });
        }

        function makeAjaxCallFromDetailsDialog( requestData, dialogSelector ) {
            $.ajax({
                type: "POST",
                url: wpfsAdminSettings.ajaxUrl,
                data: requestData,
                cache: false,
                dataType: "json",
                success: function (responseData) {
                    if (responseData.success) {
                        $(dialogSelector).dialog('close');
                        WPFS.SidePane.close('details');

                        displaySuccessMessageBanner(responseData.msg);
                        setTimeoutToRedirect( responseData.redirectURL, 1000 );
                    } else {
                        displayErrorMessageInDialog( dialogSelector, responseData.msg );

                        logException('wpfs-admin.makeAjaxCallFromDialog()', responseData);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    displayErrorMessageInDialog( dialogSelector, textStatus );
                    logError('wpfs-admin.makeAjaxCallFromDialog()', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    // no op
                }
            });
        }

        function showButtonLoader() {
            $('.wpfs-button-loader').addClass('wpfs-btn-primary--loader').prop('disabled', true);
        }

        function hideButtonLoader() {
            $('.wpfs-button-loader').removeClass('wpfs-btn-primary--loader').prop('disabled', false);
        }


        function getFormType( $form ) {
            return $form.data('wpfs-form-type');
        }

        function getFieldDescriptor(formType, fieldName) {
            var fieldDescriptor = null;

            if (wpfsFormFields.hasOwnProperty(formType)) {
                var formFields = wpfsFormFields[formType]

                if (formFields.hasOwnProperty(fieldName )) {
                    fieldDescriptor = formFields[fieldName];
                }
            }

            return fieldDescriptor;
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

        function getGlobalMessageContainerTitle($messageContainer, title) {
            var $messageContainerTitle = $('.wpfs-form-message-title', $messageContainer);
            if (0 == $messageContainerTitle.length) {
                $('<div>', {class: 'wpfs-form-message-title'}).prependTo($messageContainer);
                $messageContainerTitle = $('.wpfs-form-message-title', $messageContainer);
            }
            $messageContainerTitle.html(title);
            return $messageContainerTitle;
        }

        function getGlobalMessageContainer($form, title, message) {
            var $messageContainer = $('.wpfs-form-message', $form);
            if (0 == $messageContainer.length) {
                $form.prepend(
                    '<div class="wpfs-form-message wpfs-form-message--mb">' +
                        '<div class="wpfs-form-message__inner">' +
                            '<div class="wpfs-form-message__title">' + title + '</div>' +
                            '<p>' + message + '</p>' +
                        '</div>' +
                    '</div>'
                );
                $messageContainer = $('.wpfs-form-message', $form);
            }

            return $messageContainer;
        }

        function clearGlobalMessage($form) {
            var $messageContainer = getGlobalMessageContainer($form, '', '');
            $messageContainer.remove();
        }

        function __showGlobalMessage($form, title, message) {
            return getGlobalMessageContainer($form, title, message);
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

        function showFieldError($form, field, fieldDescriptor, scrollTo) {
            if (debugLog) {
                logInfo('showFieldError.field: ', JSON.stringify(field));
                logInfo('showFieldError.descriptor: ', JSON.stringify(fieldDescriptor));
            }
            if (fieldDescriptor != null) {
                var fieldType = fieldDescriptor.type;
                var fieldClass = fieldDescriptor.class;
                var fieldSelector = fieldDescriptor.selector;
                var fieldErrorClass = fieldDescriptor.errorClass;
                var fieldErrorSelector = fieldDescriptor.errorSelector;

                // tnagy initialize field
                var theFieldSelector;
                if (field.id != null) {
                    theFieldSelector = '#' + field.id;
                } else {
                    theFieldSelector = fieldSelector;
                }
                var $field = $(theFieldSelector, $form);

                // tnagy create error message
                var $fieldError = $('<div>', {
                    class: ERROR_MESSAGE_FIELD_CLASS,
                    'data-wpfs-field-error-for': field.id
                }).html(field.message);

                // tnagy add error class, insert error message
                if (FIELD_TYPE_INPUT === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.insertAfter($field);
                } else if (FIELD_TYPE_INPUT_DECORATED === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.appendTo($field.parent());
                } else if (FIELD_TYPE_INPUT_CHECK_ITEM === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.appendTo($field.parent().parent());
                } else if (FIELD_TYPE_INPUT_GROUP === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.insertAfter($field.closest(fieldErrorSelector));
                } else if (FIELD_TYPE_INPUT_CUSTOM === fieldType) {
                    if (fieldErrorSelector != null) {
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.insertAfter($field);
                } else if (FIELD_TYPE_DROPDOWN === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                        $field.closest('.' + fieldClass).addClass(fieldErrorClass);
                        $(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.appendTo($field.parent());
                } else if (FIELD_TYPE_CHECKBOX === fieldType) {
                    if (fieldErrorSelector != null) {
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.appendTo($field.parent());
                } else if (FIELD_TYPE_CHECKLIST === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                    }
                    $fieldError.insertAfter($field);
                } else if (FIELD_TYPE_PRODUCTS === fieldType) {
                    if (fieldErrorSelector != null) {
                        if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                            fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                        }
                        $(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.insertAfter($field);
                } else if (FIELD_TYPE_TAGS === fieldType) {
                    if (fieldErrorSelector != null) {
                        $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                    }
                    $fieldError.appendTo($field.parent().parent());
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

        function showFormError($form, field, errorTitle, scrollTo) {
            var formType = getFormType($form);
            var fieldDescriptor = getFieldDescriptor(formType, field.name);
            if (fieldDescriptor != null) {
                if (true === fieldDescriptor.hidden) {
                    showErrorGlobalMessage($form, errorTitle, errorMessage);
                } else {
                    showFieldError($form, field, fieldDescriptor, scrollTo);
                }
            }
        }

        function activateTabSelector( tabId ) {
            $('.wpfs-form-tab').removeClass('wpfs-page-tabs__item--active');
            $('.wpfs-form-tab[data-tab-id="' + tabId + '"]').addClass('wpfs-page-tabs__item--active');
        }

        function showFirstErrorTab( $form ) {
            var firstErrorFieldId = $('.wpfs-form-error-message', $form).first().data('wpfs-field-error-for');

            var $formPane = $('#' + firstErrorFieldId).closest( '.wpfs-edit-form-pane' );
            if ( $formPane.length === 1 ) {
                var tabId = $formPane.data('tab-id');

                activateTabSelector( tabId );
                showFormTab( tabId );
            }
        }

        function scrollToFirstFieldError( $form ) {
            var firstErrorFieldId = $('.wpfs-form-error-message', $form).first().data('wpfs-field-error-for');
            if (firstErrorFieldId) {
                scrollToElement($('#' + firstErrorFieldId, $form), false);
            }
        }


        function createGlobalErrorMessage( globalErrors ) {
            var globalErrorMessages = '';

            for (var i = 0; i < globalErrors.errors.length; i++) {
                globalErrorMessages += globalErrors.errors[i] + '<br/>';
            }

            return globalErrorMessages;
        }


        function processValidationErrors( $form, bindingResult) {
            var hasErrors = false;

            if (bindingResult) {
                if (bindingResult.fieldErrors && bindingResult.fieldErrors.errors) {
                    var fieldErrors = bindingResult.fieldErrors.errors;
                    for (var index in fieldErrors) {
                        var fieldError = fieldErrors[index];
                        showFormError($form, fieldError, bindingResult.fieldErrors.title);
                        if (!hasErrors) {
                            hasErrors = true;
                        }
                    }
                    showFirstErrorTab( $form )
                    scrollToFirstFieldError( $form );
                }

                if (bindingResult.globalErrors && bindingResult.globalErrors.errors) {
                    var globalErrorMessages = createGlobalErrorMessage( bindingResult.globalErrors );
                    if ('' !== globalErrorMessages) {
                        showErrorGlobalMessage($form, bindingResult.globalErrors.title, globalErrorMessages);
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

            // You can use the 'hasErrors' variable to see if something needs be be reset
        }

        function clearFieldErrors($form) {
            $('.wpfs-form-error-message', $form).remove();
            $('.wpfs-form-control', $form).removeClass('wpfs-form-control--error');
            $('.wpfs-input-group', $form).removeClass('wpfs-input-group--error');
            $('.wpfs-form-control--error', $form).removeClass('wpfs-form-control--error');
            $('.wpfs-form-check-input--error', $form).removeClass('wpfs-form-check-input--error');
        }

        function clearFormErrors( $form ) {
            clearGlobalMessage( $form );
            clearFieldErrors( $form );
        }

        function makeAjaxCallWithForm( $form ) {
            clearFormErrors( $form );
            showButtonLoader();

            $.ajax({
                type: "POST",
                url: wpfsAdminSettings.ajaxUrl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (responseData) {
                    if (responseData.success) {
                        displaySuccessMessageBanner(responseData.msg);
                        setTimeoutToRedirect( responseData.redirectURL, 1000 );
                    } else {
                        if ( responseData.hasOwnProperty( PROPERTY_NAME_BINDING_RESULT ) ) {
                            processValidationErrors( $form, responseData.bindingResult );
                        } else {
                            showErrorGlobalMessage($form, wpfsAdminL10n.internalError, responseData.msg );
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    logError('wpfs-admin.makeAjaxCallWithForm()', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    hideButtonLoader();
                }
            });
        }

        function initErrorDialog() {
            if ( $('#wpfs-modal-dialog-error').length > 0 ) {
                WPFS.ModalDialogErrorView = Backbone.View.extend({
                    template: _.template($('#wpfs-modal-dialog-error').html()),
                    render: function () {
                        this.$el.html(this.template(this.model.attributes));
                        return this;
                    }
                });
                WPFS.ModalDialogErrorModel = Backbone.Model.extend({});
            }
        }


        function cloneForm( editNewForm ) {
            WPFS.cloneFormDialogModel.set({ 'editNewForm': editNewForm });

            var cloneFormDialogInProgressView = new WPFS.CloneFormDialogInProgressView({
                model: WPFS.cloneFormDialogModel
            });
            $('#wpfs-clone-form-dialog').empty().append( cloneFormDialogInProgressView.render().el );

            makeAjaxCallFromDialog( createCloneFormData( WPFS.cloneFormDialogModel ), '#wpfs-clone-form-dialog' );
        }

        function bindCloneFormData () {
            var displayNameRegex = /^[^\s]{1,}.{0,}$/;
            var nameRegex = /^[\w\-]+$/;

            WPFS.cloneFormDialogModel.set({
                newFormDisplayName: $('input[name="wpfs-new-form-display-name"]').val(),
                newFormName:        $('input[name="wpfs-new-form-name"]').val()
            });

            var bindResult = [];
            if ( !WPFS.cloneFormDialogModel.get( 'newFormDisplayName' ).match( displayNameRegex )) {
                bindResult.push({
                    element:        $('input[name="wpfs-new-form-display-name"]'),
                    errorMessage:   'The display name may contain any characters but it shouldn\'t start with a space.' // todo: localize error message
                });
            }
            if ( !WPFS.cloneFormDialogModel.get( 'newFormName' ).match( nameRegex )) {
                bindResult.push({
                    element:        $('input[name="wpfs-new-form-name"]'),
                    errorMessage:   'The identifier may contain only alphanumeric characters, dashes, and underscores.' // todo: localize error message
                });
            }

            return bindResult;
        }

        function displayDialogBindError( bindError ) {
            alert( bindError.errorMessage );
            bindError.element.focus();
        }

        function initCloneFormDialog() {
            WPFS.CloneFormDialogNormalView = Backbone.View.extend({
                id: 'wpfs-clone-form-dialog',
                className: 'wpfs-dialog-content',
                attributes: {
                    title: wpfsAdminL10n.cloneFormTitle
                },
                template: _.template($('#wpfs-modal-clone-form').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.CloneFormDialogInProgressView = Backbone.View.extend({
                template: _.template($('#wpfs-modal-clone-form-in-progress').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.CloneFormDialogModel = Backbone.Model.extend({} );

            $('.js-clone-form').on( 'click', function (event) {
                event.preventDefault();

                var $this = $(this);
                var $tr   = $this.closest( 'tr.wpfs-data-table__tr' );
                var formData = extractFormDataFromRow( $tr );

                WPFS.cloneFormDialogModel = new WPFS.CloneFormDialogModel({
                    id:                  formData.id,
                    type:                formData.type,
                    layout:              formData.layout,
                    formName:            formData.formName,
                    formDisplayName:     formData.formDisplayName,
                    newFormName:         formData.formName + 'Copy',
                    newFormDisplayName:  formData.formDisplayName + ' ' + wpfsAdminL10n.copyPostfix,
                    editNewForm:         false
                });
                var cloneFormDialogNormalView = new WPFS.CloneFormDialogNormalView({
                    model: WPFS.cloneFormDialogModel
                });
                $('#wpfs-dialog-container').empty().append( cloneFormDialogNormalView.render().el );

                WPFS.Dialog.open('#wpfs-clone-form-dialog');

                $('.js-clone-form-dialog').on( 'click', function (event) {
                    event.preventDefault();

                    var bindResult = bindCloneFormData();
                    if ( bindResult.length === 0 ) {
                        cloneForm(false);
                    } else {
                        displayDialogBindError( bindResult[0] );
                    }
                });

                $('.js-clone-and-edit-form-dialog').on( 'click', function (event) {
                    event.preventDefault();

                    var bindResult = bindCloneFormData();
                    if ( bindResult.length === 0 ) {
                        cloneForm(true);
                    } else {
                        displayDialogBindError( bindResult[0] );
                    }
                });
            });
        }

        function extractFormDataFromRow( $tr ) {
            return {
                id:              $tr.data('form-id'),
                type:            $tr.data('form-type'),
                layout:          $tr.data('form-layout'),
                formName:        $tr.data('form-name'),
                formDisplayName: $tr.data('form-display-name')
            };
        }

        function initDeleteFormDialog() {
            WPFS.DeleteFormDialogNormalView = Backbone.View.extend({
                id: 'wpfs-delete-form-dialog',
                className: 'wpfs-dialog-content',
                attributes: {
                    title: wpfsAdminL10n.deleteFormTitle
                },
                template: _.template($('#wpfs-modal-delete-form').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.DeleteFormDialogInProgressView = Backbone.View.extend({
                template: _.template($('#wpfs-modal-delete-form-in-progress').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.DeleteFormDialogModel = Backbone.Model.extend({} );

            $('.js-delete-form').on( 'click', function (event) {
                event.preventDefault();

                var $this = $(this);
                var $tr   = $this.closest( 'tr.wpfs-data-table__tr' );
                var formData = extractFormDataFromRow( $tr );

                WPFS.deleteFormDialogModel = new WPFS.DeleteFormDialogModel({
                    confirmationMessage: sprintf( wpfsAdminL10n.deleteFormConfirmationMessage, formData.formDisplayName ),
                    id:                  formData.id,
                    type:                formData.type,
                    layout:              formData.layout
                });
                var deleteFormDialogNormalView = new WPFS.DeleteFormDialogNormalView({
                    model: WPFS.deleteFormDialogModel
                });
                $('#wpfs-dialog-container').empty().append( deleteFormDialogNormalView.render().el );

                WPFS.Dialog.open('#wpfs-delete-form-dialog');

                $('.js-delete-form-dialog').on( 'click', function (event) {
                    event.preventDefault();

                    var deleteFormDialogInProgressView = new WPFS.DeleteFormDialogInProgressView({
                        model: WPFS.deleteFormDialogModel
                    });
                    $('#wpfs-delete-form-dialog').empty().append( deleteFormDialogInProgressView.render().el );

                    makeAjaxCallFromDialog( createDeleteFormData( WPFS.deleteFormDialogModel ), '#wpfs-delete-form-dialog' );
                });
            });
        }

        function onManageFormsPage() {
            return $('#wpfs-form-list').length > 0;
        }

        function initManageFormsDialogs() {
            initDeleteFormDialog();
            initCloneFormDialog();
        }

        WPFS.initBannerMessages = function() {
            if ( $('#wpfs-success-message').length > 0 ) {
                WPFS.SuccessMessageView = Backbone.View.extend({
                    className: 'wpfs-floating-message wpfs-floating-message--success wpfs-floating-message--show js-flash-message',
                    template: _.template($('#wpfs-success-message').html()),
                    render: function () {
                        this.$el.html(this.template(this.model.attributes));
                        return this;
                    }
                });
                WPFS.SuccessMessageModel = Backbone.Model.extend({});
            }
        }

        function initFormListFilter() {
            $('.js-form-list-mode-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-forms"]').submit();
            });
        }

        WPFS.initManageForms = function() {
            if ( onManageFormsPage() ) {
                initErrorDialog();
                initManageFormsDialogs();
                initFormListFilter();
            }
        }

        function onOneTimePaymentsPage() {
            return $('div.wpfs-page-one-time-payments').length > 0;
        }

        function onSubscriptionsPage() {
            return $('div.wpfs-page-subscriptions').length > 0;
        }

        function onDonationsPage() {
            return $('div.wpfs-page-donations').length > 0;
        }

        function onSavedCardsPage() {
            return $('div.wpfs-page-saved-cards').length > 0;
        }

        function createCapturePaymentData( model ) {
            return {
                action: 'wpfs-capture-payment',
                id:     model.get('dbId'),
            };
        }

        function createRefundPaymentData( model ) {
            return {
                action: 'wpfs-refund-payment',
                id:     model.get('dbId'),
            };
        }

        function createDeletePaymentData( model ) {
            return {
                action: 'wpfs-delete-payment',
                id:     model.get('dbId'),
            };
        }

        function extractTransactionDataFromRow( $node ) {
            var $tr   = $node.closest( 'tr.wpfs-data-table__tr' );

            return {
                dbId:       $tr.data('db-id'),
                stripeId:   $tr.data('stripe-id')
            };
        }

        function createCancelSubscriptionData( model ) {
            return {
                action: 'wpfs-cancel-subscription',
                id:     model.get('dbId'),
            };
        }

        function createDeleteSubscriptionData( model ) {
            return {
                action: 'wpfs-delete-subscription',
                id:     model.get('dbId'),
            };
        }

        function createRefundDonationData( model ) {
            return {
                action: 'wpfs-refund-donation',
                id:     model.get('dbId'),
            };
        }

        function createCancelDonationData( model ) {
            return {
                action: 'wpfs-cancel-donation',
                id:     model.get('dbId'),
            };
        }

        function createDeleteDonationData( model ) {
            return {
                action: 'wpfs-delete-donation',
                id:     model.get('dbId'),
            };
        }

        function createDeleteSavedCardData( model ) {
            return {
                action: 'wpfs-delete-saved-card',
                id:     model.get('dbId'),
            };
        }

        function initTransactionActionDialog( options ) {
            function onClickConfirm( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    ctx.inProgressView = new ctx.InProgressView({
                        model: ctx.model
                    });
                    $('#' + ctx.options.dialogId).empty().append( ctx.inProgressView.render().el );

                    ctx.options.ajaxCallback( ctx.options.createCommandCallback( ctx.model ), '#' + ctx.options.dialogId );
                };
            }

            function onClickOpen( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    var $this = $(this);
                    var formData = ctx.options.extractDataCallback( $this );

                    ctx.model = new ctx.Model({
                        confirmationMessage: sprintf( ctx.options.confirmationMessage, formData.stripeId ),
                        dbId:                formData.dbId,
                        stripeId:            formData.stripeId,
                    });
                    ctx.normalView = new ctx.NormalView({
                        model: ctx.model
                    });
                    $( ctx.options.dialogContainerSelector ).empty().append( ctx.normalView.render().el );

                    WPFS.Dialog.open('#' + ctx.options.dialogId );

                    $(ctx.options.confirmButtonSelector).on( 'click', onClickConfirm( ctx ));
                }
            }

            var ctx = {};
            ctx.options = options;

            ctx.NormalView = Backbone.View.extend({
                id: options.dialogId,
                className: options.dialogClass,
                attributes: {
                    title: options.dialogTitle
                },
                template: _.template($(options.normalTemplateSelector).html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            ctx.InProgressView = Backbone.View.extend({
                template: _.template($(options.inProgressTemplateSelector).html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            ctx.Model = Backbone.Model.extend( {} );

            $(options.clickSelector).on( 'click', onClickOpen( ctx ));
        }

        function initOneTimePaymentDialogs() {
            var captureOptions = {
                dialogId:                       'wpfs-capture-payment-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.capturePaymentTitle,
                normalTemplateSelector:         '#wpfs-modal-capture-payment',
                inProgressTemplateSelector:     '#wpfs-modal-capture-payment-in-progress',
                clickSelector:                  '.js-capture-payment',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.capturePaymentConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-capture-payment-dialog',
                createCommandCallback:          createCapturePaymentData
            }
            initTransactionActionDialog( captureOptions );

            var refundOptions = {
                dialogId:                       'wpfs-refund-payment-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.refundPaymentTitle,
                normalTemplateSelector:         '#wpfs-modal-refund-payment',
                inProgressTemplateSelector:     '#wpfs-modal-refund-payment-in-progress',
                clickSelector:                  '.js-refund-payment',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.refundPaymentConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-refund-payment-dialog',
                createCommandCallback:          createRefundPaymentData
            }
            initTransactionActionDialog( refundOptions );

            var deleteOptions = {
                dialogId:                       'wpfs-delete-payment-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deletePaymentTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-payment',
                inProgressTemplateSelector:     '#wpfs-modal-delete-payment-in-progress',
                clickSelector:                  '.js-delete-payment',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.deletePaymentConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-payment-dialog',
                createCommandCallback:          createDeletePaymentData
            }
            initTransactionActionDialog( deleteOptions );
        }

        function initSubscriptionDialogs() {
            var cancelOptions = {
                dialogId:                       'wpfs-cancel-subscription-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.cancelSubscriptionTitle,
                normalTemplateSelector:         '#wpfs-modal-cancel-subscription',
                inProgressTemplateSelector:     '#wpfs-modal-cancel-subscription-in-progress',
                clickSelector:                  '.js-cancel-subscription',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.cancelSubscriptionConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-cancel-subscription-dialog',
                createCommandCallback:          createCancelSubscriptionData
            }
            initTransactionActionDialog( cancelOptions );

            var deleteOptions = {
                dialogId:                       'wpfs-delete-subscription-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteSubscriptionTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-subscription',
                inProgressTemplateSelector:     '#wpfs-modal-delete-subscription-in-progress',
                clickSelector:                  '.js-delete-subscription',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.deleteSubscriptionConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-subscription-dialog',
                createCommandCallback:          createDeleteSubscriptionData
            }
            initTransactionActionDialog( deleteOptions );
        }

        function initDonationDialogs() {
            var refundOptions = {
                dialogId:                       'wpfs-refund-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.refundDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-refund-donation',
                inProgressTemplateSelector:     '#wpfs-modal-refund-donation-in-progress',
                clickSelector:                  '.js-refund-donation',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.refundDonationConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-refund-donation-dialog',
                createCommandCallback:          createRefundDonationData
            }
            initTransactionActionDialog( refundOptions );

            var cancelOptions = {
                dialogId:                       'wpfs-cancel-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.cancelDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-cancel-donation',
                inProgressTemplateSelector:     '#wpfs-modal-cancel-donation-in-progress',
                clickSelector:                  '.js-cancel-donation',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.cancelDonationConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-cancel-donation-dialog',
                createCommandCallback:          createCancelDonationData
            }
            initTransactionActionDialog( cancelOptions );

            var deleteOptions = {
                dialogId:                       'wpfs-delete-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-donation',
                inProgressTemplateSelector:     '#wpfs-modal-delete-donation-in-progress',
                clickSelector:                  '.js-delete-donation',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.deleteDonationConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-donation-dialog',
                createCommandCallback:          createDeleteDonationData
            }
            initTransactionActionDialog( deleteOptions );
        }

        function initSavedCardDialogs() {
            var deleteOptions = {
                dialogId:                       'wpfs-delete-saved-card-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteSavedCardTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-saved-card',
                inProgressTemplateSelector:     '#wpfs-modal-delete-saved-card-in-progress',
                clickSelector:                  '.js-delete-saved-card',
                extractDataCallback:            extractTransactionDataFromRow,
                ajaxCallback:                   makeAjaxCallFromDialog,
                confirmationMessage:            wpfsAdminL10n.deleteSavedCardConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-saved-card-dialog',
                createCommandCallback:          createDeleteSavedCardData
            }
            initTransactionActionDialog( deleteOptions );
        }


        function initOneTimePaymentFilter() {
            $('.js-one-time-payments-status-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-one-time-payments"]').submit();
            });

            $('.js-one-time-payments-mode-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-one-time-payments"]').submit();
            });
        }

        function initSubscriptionFilter() {
            $('.js-subscriptions-status-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-subscriptions"]').submit();
            });

            $('.js-subscriptions-mode-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-subscriptions"]').submit();
            });
        }

        function initDonationFilter() {
            $('.js-donations-mode-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-donations"]').submit();
            });
        }

        function initSavedCardFilter() {
            $('.js-saved-card-mode-filter').on('selectmenuchange', function (e) {
                $('form[name="wpfs-search-saved-cards"]').submit();
            });
        }

        function extractTransactionDataFromDetails( $node ) {
            var $div   = $node.closest( 'div.wpfs-side-pane' );

            return {
                dbId:      $div.data('db-id'),
                stripeId:  $div.data('stripe-id')
            };
        }

        function initOneTimePaymentDetailsDialogs() {
            var captureOptions = {
                dialogId:                       'wpfs-capture-payment-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.capturePaymentTitle,
                normalTemplateSelector:         '#wpfs-modal-capture-payment',
                inProgressTemplateSelector:     '#wpfs-modal-capture-payment-in-progress',
                clickSelector:                  '.js-capture-payment-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.capturePaymentConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-capture-payment-dialog',
                createCommandCallback:          createCapturePaymentData
            }
            initTransactionActionDialog( captureOptions );

            var refundOptions = {
                dialogId:                       'wpfs-refund-payment-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.refundPaymentTitle,
                normalTemplateSelector:         '#wpfs-modal-refund-payment',
                inProgressTemplateSelector:     '#wpfs-modal-refund-payment-in-progress',
                clickSelector:                  '.js-refund-payment-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.refundPaymentConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-refund-payment-dialog',
                createCommandCallback:          createRefundPaymentData
            }
            initTransactionActionDialog( refundOptions );

            var deleteOptions = {
                dialogId:                       'wpfs-delete-payment-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deletePaymentTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-payment',
                inProgressTemplateSelector:     '#wpfs-modal-delete-payment-in-progress',
                clickSelector:                  '.js-delete-payment-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.deletePaymentConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-payment-dialog',
                createCommandCallback:          createDeletePaymentData
            }
            initTransactionActionDialog( deleteOptions );

        }

        function initSubscriptionDetailsDialogs() {
            var cancelOptions = {
                dialogId:                       'wpfs-cancel-subscription-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.cancelSubscriptionTitle,
                normalTemplateSelector:         '#wpfs-modal-cancel-subscription',
                inProgressTemplateSelector:     '#wpfs-modal-cancel-subscription-in-progress',
                clickSelector:                  '.js-cancel-subscription-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.cancelSubscriptionConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-cancel-subscription-dialog',
                createCommandCallback:          createCancelSubscriptionData
            }
            initTransactionActionDialog( cancelOptions );

            var deleteOptions = {
                dialogId:                       'wpfs-delete-subscription-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteSubscriptionTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-subscription',
                inProgressTemplateSelector:     '#wpfs-modal-delete-subscription-in-progress',
                clickSelector:                  '.js-delete-subscription-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.deleteSubscriptionConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-subscription-dialog',
                createCommandCallback:          createDeleteSubscriptionData
            }
            initTransactionActionDialog( deleteOptions );
        }

        function initDonationDetailsDialogs() {
            var refundOptions = {
                dialogId:                       'wpfs-refund-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.refundDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-refund-donation',
                inProgressTemplateSelector:     '#wpfs-modal-refund-donation-in-progress',
                clickSelector:                  '.js-refund-donation-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.refundDonationConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-refund-donation-dialog',
                createCommandCallback:          createRefundDonationData
            }
            initTransactionActionDialog( refundOptions );

            var cancelOptions = {
                dialogId:                       'wpfs-cancel-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.cancelDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-cancel-donation',
                inProgressTemplateSelector:     '#wpfs-modal-cancel-donation-in-progress',
                clickSelector:                  '.js-cancel-donation-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.cancelDonationConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-cancel-donation-dialog',
                createCommandCallback:          createCancelDonationData
            }
            initTransactionActionDialog( cancelOptions );

            var deleteOptions = {
                dialogId:                       'wpfs-delete-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-donation',
                inProgressTemplateSelector:     '#wpfs-modal-delete-donation-in-progress',
                clickSelector:                  '.js-delete-donation-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.deleteDonationConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-donation-dialog',
                createCommandCallback:          createDeleteDonationData
            }
            initTransactionActionDialog( deleteOptions );

        }

        function initSavedCardDetailsDialogs() {
            var deleteOptions = {
                dialogId:                       'wpfs-delete-saved-card-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteSavedCardTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-saved-card',
                inProgressTemplateSelector:     '#wpfs-modal-delete-saved-card-in-progress',
                clickSelector:                  '.js-delete-saved-card-details',
                extractDataCallback:            extractTransactionDataFromDetails,
                ajaxCallback:                   makeAjaxCallFromDetailsDialog,
                confirmationMessage:            wpfsAdminL10n.deleteSavedCardConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-saved-card-dialog',
                createCommandCallback:          createDeleteSavedCardData
            }
            initTransactionActionDialog( deleteOptions );

        }

        function createGetOneTimePaymentDetailsData( id ) {
            return {
                action: 'wpfs-get-payment-details',
                id:     id,
            };
        }

        function initTransactionDetails( options ) {
            function showTransactionDetails( ctx, response ) {
                ctx.Model = Backbone.Model.extend({} );
                ctx.model = new ctx.Model( response.data );
                ctx.paneView = new ctx.PaneView({
                    model: ctx.model
                });
                $(ctx.options.dialogContainerSelector).empty().append( ctx.paneView.render().el );

                ctx.options.initDialogsCallback();

                WPFS.Tooltip.init();
                WPFS.SidePane.init();
                WPFS.SidePane.open('details');
            }

            function getTransactionDetailsFromServer( ctx, requestData ) {
                $.ajax({
                    type: "POST",
                    url: wpfsAdminSettings.ajaxUrl,
                    data: requestData,
                    cache: false,
                    dataType: "json",
                    success: function (responseData) {
                        if (responseData.success) {
                            showTransactionDetails( ctx, responseData );
                        } else {
                            showTransactionDetails( ctx, responseData );
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('getTransactionDetailsFromServer()', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                        // no op
                    }
                });
            }

            function onClickRow( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    ctx.PaneView = Backbone.View.extend({
                        className: ctx.options.paneClass,
                        attributes: ctx.options.paneAttributes,
                        template: _.template($(ctx.options.templateSelector).html()),
                        render: function() {
                            this.$el.html(this.template(this.model.attributes));
                            return this;
                        }
                    });

                    var $this           = $(this);
                    var $tr             = $this.closest( 'tr.wpfs-data-table__tr' );
                    var transactionData = extractTransactionDataFromRow( $tr );

                    getTransactionDetailsFromServer( ctx, ctx.options.detailsCommandCallback( transactionData.dbId ));
                };
            }

            var ctx = {};
            ctx.options = options;

            $(options.clickSelector).on( 'click', onClickRow( ctx ));
        }

        function initOneTimePaymentDetails() {
            var paymentDetailsOptions = {
                clickSelector:                  '.js-open-payment-details',
                paneClass:                      'wpfs-side-pane-overlay js-side-pane',
                paneAttributes:                 { 'data-side-pane-id': 'details' },
                templateSelector:               '#wpfs-side-pane-payment-details',
                detailsCommandCallback:         createGetOneTimePaymentDetailsData,
                dialogContainerSelector:        '#payment-details-container',
                initDialogsCallback:            initOneTimePaymentDetailsDialogs
            }
            initTransactionDetails( paymentDetailsOptions );
        }

        function createSubscriptionDetailsData( id ) {
            return {
                action: 'wpfs-get-subscription-details',
                id:     id,
            };
        }

        function initSubscriptionDetails() {
            var subscriptionDetailsOptions = {
                clickSelector:                  '.js-open-subscription-details',
                paneClass:                      'wpfs-side-pane-overlay js-side-pane',
                paneAttributes:                 { 'data-side-pane-id': 'details' },
                templateSelector:               '#wpfs-side-pane-subscription-details',
                detailsCommandCallback:         createSubscriptionDetailsData,
                dialogContainerSelector:        '#subscription-details-container',
                initDialogsCallback:            initSubscriptionDetailsDialogs
            }
            initTransactionDetails( subscriptionDetailsOptions );
        }

        function createDonationDetailsData( id ) {
            return {
                action: 'wpfs-get-donation-details',
                id:     id,
            };
        }

        function initDonationDetails() {
            var donationDetailsOptions = {
                clickSelector:                  '.js-open-donation-details',
                paneClass:                      'wpfs-side-pane-overlay js-side-pane',
                paneAttributes:                 { 'data-side-pane-id': 'details' },
                templateSelector:               '#wpfs-side-pane-donation-details',
                detailsCommandCallback:         createDonationDetailsData,
                dialogContainerSelector:        '#donation-details-container',
                initDialogsCallback:            initDonationDetailsDialogs
            }
            initTransactionDetails( donationDetailsOptions );
        }

        function createSavedCardDetailsData( id ) {
            return {
                action: 'wpfs-get-saved-card-details',
                id:     id,
            };
        }

        function initSavedCardDetails() {
            var savedCardsDetailsOptions = {
                clickSelector:                  '.js-open-saved-card-details',
                paneClass:                      'wpfs-side-pane-overlay js-side-pane',
                paneAttributes:                 { 'data-side-pane-id': 'details' },
                templateSelector:               '#wpfs-side-pane-saved-card-details',
                detailsCommandCallback:         createSavedCardDetailsData,
                dialogContainerSelector:        '#saved-card-details-container',
                initDialogsCallback:            initSavedCardDetailsDialogs
            }
            initTransactionDetails( savedCardsDetailsOptions );
        }

        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function setTabCookie( tab ) {
            setCookie( COOKIE_NAME_TAB_ID, tab, 7 );
        }

        WPFS.initTransactions = function() {
            if ( onOneTimePaymentsPage() ) {
                initErrorDialog();
                initOneTimePaymentDialogs();
                initOneTimePaymentFilter();
                initOneTimePaymentDetails();
                setTabCookie( TRANSACTION_TAB_PAYMENTS );
            }

            if ( onSubscriptionsPage() ) {
                initErrorDialog();
                initSubscriptionDialogs();
                initSubscriptionFilter();
                initSubscriptionDetails();
                setTabCookie( TRANSACTION_TAB_SUBSCRIPTIONS );
            }

            if ( onDonationsPage() ) {
                initErrorDialog();
                initDonationDialogs();
                initDonationFilter();
                initDonationDetails();
                setTabCookie( TRANSACTION_TAB_DONATIONS );
            }

            if ( onSavedCardsPage() ) {
                initErrorDialog();
                initSavedCardDialogs();
                initSavedCardFilter();
                initSavedCardDetails();
                setTabCookie( TRANSACTION_TAB_SAVED_CARDS );
            }
        }

        function initFocusOnTheCreateFormPage() {
            $('input[name="wpfs-form-display-name"]').focus();
        }

        function initEventHandlersOnTheCreateFormPage() {
            $('#wpfs-create-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });
        }

        function onCreateFormPage() {
            return $('#wpfs-create-form').length > 0;
        }

        WPFS.initCreateForm = function() {
            if ( onCreateFormPage() ) {
                initFocusOnTheCreateFormPage();
                initEventHandlersOnTheCreateFormPage();
            }
        }

        function onConfigureStripeAccountPage() {
            return $('div.wpfs-page-settings-configure-stripe-account').length > 0;
        }

        function initEventHandlersOnTheSettingsStripeAccountPage() {
            $('#wpfs-save-stripe-account').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });
        }

        function copyWebhookUrlToClipboardFromNode( $node ) {
            var webhookUrl = $node.data( 'webhook-url' );
            copyToClipboard( webhookUrl );
            displaySuccessMessageBanner(wpfsAdminL10n.webhookUrlCopiedMessage);
        }

        function initWebhookUrlCopyToClipboard() {
            $('.js-copy-webhook-url').on('click', function(e) {
                e.preventDefault();

                var $this = $(this);
                copyWebhookUrlToClipboardFromNode( $this );
            });
        }

        WPFS.initSettingsStripeAccount = function() {
            if ( onConfigureStripeAccountPage() ) {
                WPFS.Toggler.init();
                initEventHandlersOnTheSettingsStripeAccountPage();
                initWebhookUrlCopyToClipboard();
            }
        }

        function initEventHandlersOnTheSettingsMyAccountPage() {
            $('#wpfs-save-my-account').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });

            $('.js-cancel-subscriptions').on('click', function(e) {
                if ( $(this).val() === "1" ) {
                    $('#wpfs-when-cancel-subscriptions-row').show();
                } else {
                    $('#wpfs-when-cancel-subscriptions-row').hide();
                }
            });
        }

        function onSettingsMyAccountPage() {
            return $('div.wpfs-page-settings-my-account').length > 0;
        }

        WPFS.initSettingsMyAccount = function() {
            if ( onSettingsMyAccountPage() ) {
                initEventHandlersOnTheSettingsMyAccountPage();
            }
        }

        function initEventHandlersOnTheSettingsSecurityPage() {
            $('#wpfs-save-security').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });

            $('.js-google-recaptcha-toggle').on('click', function(e) {
                var checked = false;
                $('.js-google-recaptcha-toggle').each( function() {
                   if ( $(this).prop('checked') ) {
                       checked = true;
                   }
                });

                if ( checked ) {
                    $('#google-recaptcha-api-keys').show();
                } else {
                    $('#google-recaptcha-api-keys').hide();
                }
            });
        }

        function onSettingsSecurityPage() {
            return $('div.wpfs-page-settings-security').length > 0;
        }

        WPFS.initSettingsSecurity = function() {
            if ( onSettingsSecurityPage() ) {
                initEventHandlersOnTheSettingsSecurityPage();
            }
        }

        function extractEmailsFromTags( $node ) {
            var emails = [];

            $node.find('.wpfs-tag').each( function( idx ) {
                emails.push( $(this).text().trim() );
            });

            return emails;
        }

        function processCopyToReceiverList( $form ) {
            var emails = extractEmailsFromTags( $form );

            $("input[name='wpfs-email-options-send-copy-to-list-hidden']").val( JSON.stringify( emails ));
        }

        function initEventHandlersOnTheSettingsEmailOptionsPage() {
            $('#wpfs-save-email-options').submit(function (e) {
                e.preventDefault();

                var $form = $(this);

                processCopyToReceiverList( $form );

                makeAjaxCallWithForm( $form );
            });

            $('.js-email-from-address').on('click', function(e) {
                if ( $(this).val() === 'custom' ) {
                    $('.wpfs-from-address-custom-js').show();
                } else {
                    $('.wpfs-from-address-custom-js').hide();
                }
            });
        }

        function onSettingsEmailOptionsPage() {
            return $('div.wpfs-page-settings-email-options').length > 0;
        }

        WPFS.initSettingsEmailOptions = function() {
            if ( onSettingsEmailOptionsPage() ) {
                WPFS.TagsInput.init();
                initEventHandlersOnTheSettingsEmailOptionsPage();
            }
        }

        Object.size = function(obj) {
            var size = 0,
                key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) size++;
            }
            return size;
        };

        function BindingResult( formHash = null ) {
            this.formHash = formHash;
            this.fieldErrors = {};
            this.globalErrors = [];

            this.hasErrors = function() {
                return Object.size(this.fieldErrors) > 0 || this.globalErrors.length > 0;
            }

            this.hasGlobalErrors = function() {
                return this.globalErrors.length > 0;
            }

            this.hasFieldErrors = function( field = null ) {
                if ( field === null ) {
                    return Object.size(this.fieldErrors) > 0;
                } else {
                    return this.fieldErrors.hasOwnProperty(field);
                }
            }

            this.addGlobalError = function(error) {
                this.globalErrors.push(error);
            }

            this.addFieldError = function(field, fieldId, error) {
                if (field === null) {
                    return;
                }

                if ( !this.fieldErrors.hasOwnProperty(field)) {
                    this.fieldErrors[field] = [];
                }
                this.fieldErrors[field].push({
                    id:      fieldId,
                    name:    field,
                    message: error
                })
            }

            this.getGlobalErrors = function() {
                return this.globalErrors;
            }

            this.getFieldErrors = function(field = null) {
                if (field === null) {
                    var fieldErrors = [];

                    for (const element in this.fieldErrors) {
                        if ( this.fieldErrors.hasOwnProperty( element )) {
                            fieldErrors = fieldErrors.concat( this.fieldErrors[element] );
                        }
                    }

                    return fieldErrors;
                } else {
                    if (this.fieldErrors.hasOwnProperty(field)) {
                        return this.fieldErrors[field];
                    } else {
                        return [];
                    }
                }
            }

            this.getFormHash = function() {
                return this.formHash;
            }
        }

        function generateFormElementId( elementName, formHash, index=null ) {
            if (elementName === null) {
                return null;
            }

            var generatedId = elementName + '--' + formHash;
            if (index !== null) {
                generatedId += '--' + index;
            }

            return generatedId;
        }

        function installFormFields( formType, formFields ) {
            if ( !wpfsFormFields.hasOwnProperty( formType ) ) {
                wpfsFormFields[formType] = formFields;
            }
        }

        function findModalForm( dialogId, formType ) {
            return $('#' + dialogId).find( 'form[data-wpfs-form-type="' + formType + '"]' );
        }

        function generateValidationResultFromBindingResult( bindingResult ) {
            return {
                fieldErrors: {
                    title:  'Field validation error',
                    errors: bindingResult.getFieldErrors()
                },
                globalErrors: {
                    title:  'Form error',
                    errors: bindingResult.getGlobalErrors()
                }
            }
        }

        function initClientActionDialog( options ) {
            function onClickConfirm( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    var valid = true;
                    if ( ctx.options.formFields !== undefined && ctx.options.validatorCallback !== undefined ) {
                        var $form         = findModalForm( ctx.options.dialogId, ctx.options.formType );
                        var bindingResult = new BindingResult( ctx.options.formType );

                        clearFormErrors( $form );
                        ctx.options.validatorCallback( bindingResult );

                        if (bindingResult.hasErrors()) {
                            var validationResult = generateValidationResultFromBindingResult( bindingResult );

                            installFormFields( ctx.options.formType, ctx.options.formFields );
                            processValidationErrors( $form, validationResult );

                            valid = false;
                        }
                    }

                    if ( valid ) {
                        var dialogData = {}
                        if ( ctx.options.extractDialogDataCallback !== undefined && ctx.options.extractDialogDataCallback !== null ) {
                            dialogData = ctx.options.extractDialogDataCallback( ctx.options.dialogId, ctx.model );
                            ctx.model.set( 'dialogData', dialogData );
                        }

                        ctx.options.clientCallback( ctx.model, '#' + ctx.options.dialogId );
                    }
                };
            }

            function onClickOpen( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    var $this = $(this);

                    var pageData = {}
                    if ( ctx.options.extractPageDataCallback !== undefined && ctx.options.extractPageDataCallback !== null ) {
                        pageData = ctx.options.extractPageDataCallback( $this );
                    }

                    var model = {
                        pageData: pageData
                    }
                    if ( ctx.options.confirmationMessage !== undefined ) {
                        var confirmationMessage = ctx.options.confirmationMessage;
                        if (pageData.hasOwnProperty('itemName')) {
                            confirmationMessage =  sprintf( confirmationMessage, pageData.itemName );
                        }
                        model.confirmationMessage = confirmationMessage;
                    }
                    ctx.model = new ctx.Model(model);
                    ctx.normalView = new ctx.NormalView({
                        model: ctx.model
                    });
                    $( ctx.options.dialogContainerSelector ).empty().append( ctx.normalView.render().el );

                    if ( ctx.options.attachEventsCallback !== undefined && ctx.options.attachEventsCallback !== null ) {
                        ctx.options.attachEventsCallback( $this, ctx.model );
                    }

                    WPFS.Dialog.open('#' + ctx.options.dialogId );

                    $(ctx.options.confirmButtonSelector).on( 'click', onClickConfirm( ctx ));
                }
            }

            var ctx = {};
            ctx.options = options;

            ctx.NormalView = Backbone.View.extend({
                id: options.dialogId,
                className: options.dialogClass,
                attributes: {
                    title: options.dialogTitle
                },
                template: _.template($(options.normalTemplateSelector).html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            ctx.Model = Backbone.Model.extend( {} );

            $(options.clickSelector).on( 'click', onClickOpen( ctx ));
        }

        function getEmailTemplateById( templateId ) {
            var selectedTemplate;
            if (wpfsAdminSettings.emailTemplates.hasOwnProperty( templateId )) {
                selectedTemplate = wpfsAdminSettings.emailTemplates[ templateId ];
            }
            return selectedTemplate;
        }

        function getDefaultEmailTemplateById( templateId ) {
            var selectedTemplate;
            if (wpfsAdminSettings.defaultEmailTemplates.hasOwnProperty( templateId )) {
                selectedTemplate = wpfsAdminSettings.defaultEmailTemplates[ templateId ];
            }
            return selectedTemplate;
        }

        function getEmailTemplateSubjectBody( templateId ) {
            var subject       = '';
            var body          = '';
            var emailTemplate = getEmailTemplateById( templateId );

            if ( emailTemplate ) {
                subject = emailTemplate.subject;
                body    = emailTemplate.html;
            }

            return [subject, body];
        }

        function updateEmailTemplateSubject( templateId, subject ) {
            if (wpfsAdminSettings.emailTemplates.hasOwnProperty( templateId )) {
                wpfsAdminSettings.emailTemplates[ templateId ].subject = subject;
            } else {
                wpfsAdminSettings.emailTemplates[ templateId ] = {
                    subject: subject,
                    html: ''
                }
            }
        }

        function updateEmailTemplateBody( templateId, body ) {
            if (wpfsAdminSettings.emailTemplates.hasOwnProperty( templateId )) {
                wpfsAdminSettings.emailTemplates[ templateId ].html = body;
            } else {
                wpfsAdminSettings.emailTemplates[ templateId ] = {
                    subject: '',
                    html: body
                }
            }
        }

        function getPlaceholdersForTemplate( templateId ) {
            return WPFS.macros[ templateId ];
        }

        function displayEmailTemplate( templateId ) {
            var templateSelector = '.wpfs-list__item[data-template-id="'+ templateId + '"]';

            $('.js-email-template').removeClass( 'wpfs-list__item--active' );
            $(templateSelector).addClass( 'wpfs-list__item--active' );

            var subject, body;
            [subject, body] = getEmailTemplateSubjectBody( templateId );

            WPFS.emailTemplateModel = new WPFS.EmailTemplateModel({
                name:       $(templateSelector).data('template-name'),
                subject:    subject,
                body:       body
            });
            WPFS.emailTemplateView = new WPFS.EmailTemplateView({
                model: WPFS.emailTemplateModel
            });

            $('#wpfs-email-template-container').empty().append( WPFS.emailTemplateView.render().el );
        }

        function saveCurrentEmailTemplate() {
            if ( WPFS.currentTemplateId !== undefined ) {
                updateEmailTemplateSubject( WPFS.currentTemplateId, $('#wpfs-email-template-subject').val() );
                updateEmailTemplateBody( WPFS.currentTemplateId, $('#wpfs-email-template-body').val() );
            }
        }

        function setCurrentEmailTemplateId( templateId ) {
            WPFS.currentTemplateId = templateId;
        }

        function attachSendTestEmailEvents( $node, model ) {
            WPFS.TagsInput.init();
        }

        function getSenderTestEmailAddresses( dialogSelector, model ) {
            var $node          = $('#' + dialogSelector);
            var emailAddresses = extractEmailsFromTags( $node );

            emailAddresses.forEach(( email ) => {
                console.log(email);
            });
       }

        function sendTestEmail( model, dialogSelector ) {
            $(dialogSelector).dialog('close');
        }

        function initEmailTemplateView( templateId ) {
            setCurrentEmailTemplateId( templateId );
            displayEmailTemplate( templateId );

            var placeholders = getPlaceholdersForTemplate( templateId );
            WPFS.InsertToken.init(placeholders, '.js-insert-token-subject','.js-token-target-subject');
            WPFS.InsertToken.init(placeholders, '.js-insert-token-body','.js-token-target-body');

            $('.js-subject-position-tracking').on( 'blur', function(e) {
                $(this).data('selectionStart', $(this).prop('selectionStart'));

                updateEmailTemplateSubject( WPFS.currentTemplateId, $(this).val() );
            });

            $('.js-body-position-tracking').on( 'blur', function(e) {
                $(this).data('selectionStart', $(this).prop('selectionStart'));

                updateEmailTemplateBody( WPFS.currentTemplateId, $(this).val() );
            });

            var resetTemplateOptions = {
                dialogId:                       'wpfs-reset-template-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.resetTemplateTitle,
                normalTemplateSelector:         '#wpfs-modal-reset-template',
                clickSelector:                  '.js-reset-template',
                extractPageDataCallback:        extractCurrentTemplateId,
                clientCallback:                 resetEmailTemplate,
                confirmationMessage:            wpfsAdminL10n.resetTemplateConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-reset-template-dialog',
            }
            initClientActionDialog( resetTemplateOptions );
        }

        function initEventHandlersOnSettingsEmailTemplates() {
            $('#wpfs-save-email-templates').submit(function (e) {
                e.preventDefault();

                $('.wpfs-email-templates-hidden').val( encodeURIComponent(JSON.stringify( wpfsAdminSettings.emailTemplates )));
                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });

            $('.js-email-template').on('click', function(e) {
                e.preventDefault();

                saveCurrentEmailTemplate();

                var templateId = $(this).data('template-id');
                initEmailTemplateView( templateId );
           });

            $('.js-email-template').first().click();
        }

        function initModelViewOnSettingsEmailTemplates() {
            WPFS.EmailTemplateView = Backbone.View.extend({
                className: 'wpfs-form-block',
                template: _.template($('#wpfs-email-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.EmailTemplateModel = Backbone.Model.extend({} );
        }

        function createMacroDescriptions( macroList, macroDescriptions ) {
            var res = [];

            macroList.forEach( macroName => {
                res.push({
                    value: macroName,
                    desc: macroDescriptions.hasOwnProperty( macroName ) ? macroDescriptions[ macroName ] : ''
                });
            });

            return res;
        }

        function initMacroLookupsOnSettingsEmailTemplates() {
            WPFS.macros = {};

            wpfsAdminSettings.macros.templateIds.forEach( templateId => {
                WPFS.macros[ templateId ] = createMacroDescriptions(
                    wpfsAdminSettings.macros.macroLists[ templateId ],
                    wpfsAdminSettings.macros.descriptions
                );
            })
        }

        function extractCurrentTemplateId() {
            return {
                templateId:  WPFS.currentTemplateId,
            };
        }

        function resetEmailTemplate( model, dialogSelector ) {
            var currentTemplateId = WPFS.currentTemplateId;
            var defaultTemplate = getDefaultEmailTemplateById( currentTemplateId );

            updateEmailTemplateSubject( currentTemplateId, defaultTemplate.subject );
            updateEmailTemplateBody( currentTemplateId, defaultTemplate.html );

            initEmailTemplateView( currentTemplateId );

            $(dialogSelector).dialog('close');
        }

        function onSettingsEmailTemplatesPage() {
            return $('div.wpfs-page-settings-email-templates').length > 0;
        }

        WPFS.initSettingsEmailTemplates = function() {
            if ( onSettingsEmailTemplatesPage() ) {
                initModelViewOnSettingsEmailTemplates();
                initMacroLookupsOnSettingsEmailTemplates();
                initEventHandlersOnSettingsEmailTemplates();
            }
        }

        function initEventHandlersOnSettingsFormsOptions() {
            $('#wpfs-save-forms-options').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });
        }

        function onSettingsFormsOptions() {
            return $('div.wpfs-page-settings-forms-options').length > 0;
        }

        WPFS.initSettingsFormsOptions = function() {
            if ( onSettingsFormsOptions() ) {
                initEventHandlersOnSettingsFormsOptions();
            }
        }

        function initEventHandlersOnSettingsFormsAppearance() {
            $('#wpfs-save-forms-appearance').submit(function (e) {
                e.preventDefault();

                $('.wpfs-custom-css-hidden').val( WPFS.CodeEditor.getEditorValue(0 ));

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });

            WPFS.CodeEditor.init();
        }

        function onSettingsFormsAppearance() {
            return $('div.wpfs-page-settings-forms-appearance').length > 0;
        }

        WPFS.initSettingsFormsAppearance = function() {
            if ( onSettingsFormsAppearance() ) {
                initEventHandlersOnSettingsFormsAppearance();
            }
        }

        function initEventHandlersOnSettingsWordpressDashboard() {
            $('#wpfs-save-wp-dashboard').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                makeAjaxCallWithForm( $form );
            });
        }

        function onSettingsWordpressDashboard() {
            return $('div.wpfs-page-settings-wp-dashboard').length > 0;
        }

        WPFS.initSettingsWordpressDashboard = function() {
            if ( onSettingsWordpressDashboard() ) {
                initEventHandlersOnSettingsWordpressDashboard();
            }
        }

        function createFieldDescriptor( type, name, clazz, selector, errorClass, errorSelector, hidden = false ) {
            return {
                'type':           type,
                'name':           name,
                'class':          clazz,
                'selector':       selector,
                'errorClass':     errorClass,
                'errorSelector':  errorSelector,
                'hidden':         hidden
            };
        }

        function createInputDescriptor( name ) {
            var clazz         = 'wpfs-form-control';
            var selector      = "." + clazz + "[name='" + name + "']";
            var errorClass    = 'wpfs-form-control--error';
            var errorSelector = "." + clazz;

            return createFieldDescriptor( FIELD_TYPE_INPUT, name, clazz, selector, errorClass, errorSelector );
        }

        function createInputDecoratedDescriptor( name ) {
            var clazz         = 'wpfs-form-control';
            var selector      = "." + clazz + "[name='" + name + "']";
            var errorClass    = 'wpfs-form-control--error';
            var errorSelector = "." + clazz;

            return createFieldDescriptor( FIELD_TYPE_INPUT_DECORATED, name, clazz, selector, errorClass, errorSelector );
        }

        function createInputCheckItemDescriptor( name ) {
            var clazz         = 'wpfs-form-control';
            var selector      = "." + clazz + "[name='" + name + "']";
            var errorClass    = 'wpfs-form-control--error';
            var errorSelector = "." + clazz;

            return createFieldDescriptor( FIELD_TYPE_INPUT_CHECK_ITEM, name, clazz, selector, errorClass, errorSelector );
        }

        function createInputGroupDescriptor( name ) {
            var clazz         = 'wpfs-input-group-form-control';
            var selector      = "." + clazz + "[name='" + name + "']";
            var errorClass    = 'wpfs-input-group--error';
            var errorSelector = '.' + 'wpfs-input-group';

            return createFieldDescriptor( FIELD_TYPE_INPUT_GROUP, name, clazz, selector, errorClass, errorSelector );
        }

        function validateAddCustomFieldDialog( bindingResult ) {
            if ( !$('input[name="' + FORM_FIELD_CUSTOM_FIELD_LABEL + '"]').val() ) {
                var fieldId = generateFormElementId( FORM_FIELD_CUSTOM_FIELD_LABEL, bindingResult.getFormHash() );

                bindingResult.addFieldError(FORM_FIELD_CUSTOM_FIELD_LABEL, fieldId, wpfsAdminL10n.fieldNameRequiredMessage );
            }

            var fieldName = $('input[name="' + FORM_FIELD_CUSTOM_FIELD_LABEL + '"]').val();
            if ( [...fieldName].length > 40 ) {
                var longFieldId = generateFormElementId( FORM_FIELD_CUSTOM_FIELD_LABEL, bindingResult.getFormHash() );

                bindingResult.addFieldError(FORM_FIELD_CUSTOM_FIELD_LABEL, longFieldId, wpfsAdminL10n.fieldNameTooLongMessage );
            }
        }

        function extractCustomFieldLabelDialog( dialogId, model ) {
            var dialogData = {
                fieldName: $('#' + dialogId).find('input[name="' + FORM_FIELD_CUSTOM_FIELD_LABEL + '"]').val()
            };

            return dialogData;
        }

        function addCustomFieldMarkup(model, dialogSelector) {
            var dialogData = model.get('dialogData');

            var templateModel = new WPFS.CustomFieldModel({
                name:       dialogData.fieldName,
                typeLabel:  wpfsAdminL10n.textFieldTypeLabel
            });
            var templateView = new WPFS.CustomFieldView({
                model: templateModel
            });
            $('#wpfs-custom-fields').append( templateView.render().el );
        }

        function displayAddCustomField() {
            if ( $('#wpfs-custom-fields .wpfs-field-list__item').length < 10 ) {
                $('#wpfs-add-custom-field').show();
            } else {
                $('#wpfs-add-custom-field').hide();
            }
        }

        function displayCustomFieldRequired() {
            if ( $('#wpfs-custom-fields .wpfs-field-list__item').length > 0 ) {
                $('#wpfs-custom-fields-required').show();
            } else {
                $('#wpfs-custom-fields-required').hide();
            }
        }

        function extractCustomFieldName( $node ) {
            return {
                itemName: $node.closest('.wpfs-field-list__item').data('custom-field-name')
            }
        }

        function deleteCustomField(model, dialogSelector) {
            $('.wpfs-field-list__item[data-custom-field-name="' + model.get('pageData').itemName + '"]' ).remove();
            $(dialogSelector).dialog('close');

            displayCustomFieldRequired();
            displayAddCustomField();
        }

        function initCustomFieldActionDialogs() {
            $('.js-delete-custom-field').off('click');

            var deleteCustomFieldOptions = {
                dialogId:                       'wpfs-delete-custom-field-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteCustomFieldTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-custom-field',
                clickSelector:                  '.js-delete-custom-field',
                extractPageDataCallback:        extractCustomFieldName,
                clientCallback:                 deleteCustomField,
                confirmationMessage:            wpfsAdminL10n.deleteCustomFieldConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-custom-field-dialog',
            }
            initClientActionDialog( deleteCustomFieldOptions );
        }

        function addCustomField(model, dialogSelector) {
            addCustomFieldMarkup(model, dialogSelector);
            $(dialogSelector).dialog('close');

            displayCustomFieldRequired();
            displayAddCustomField();

            initCustomFieldActionDialogs();
        }

        function initAddCustomFieldDialog() {
            var addCustomFieldOptions = {
                dialogId:                       'wpfs-add-custom-field-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.addCustomFieldTitle,
                normalTemplateSelector:         '#wpfs-modal-add-custom-field',
                clickSelector:                  '.js-add-custom-field',
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-add-custom-field-dialog',
                formFields:                     {
                    'wpfs-custom-field-label':  createInputDescriptor( FORM_FIELD_CUSTOM_FIELD_LABEL )
                },
                formType:                       'addCustomField',
                validatorCallback:              validateAddCustomFieldDialog,
                extractDialogDataCallback:      extractCustomFieldLabelDialog,
                clientCallback:                 addCustomField
            }
            initClientActionDialog( addCustomFieldOptions );
        }

        function initAddCustomFieldTemplate() {
            WPFS.CustomFieldView = Backbone.View.extend({
                className: 'wpfs-field-list__item',
                attributes: function() {
                    return {
                        'data-custom-field-name': this.model.get('name')
                    };
                },
                template: _.template($('#wpfs-custom-field-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.CustomFieldModel = Backbone.Model.extend( {} );
        }

        function initEditFormEmailTemplates() {
            WPFS.EmailTemplateDetailsView = Backbone.View.extend({
                tagName: 'div',
                className: 'wpfs-form-block',
                events: {
                    'click #wpfs-send-email-toggle': 'toggleTemplate'
                },
                toggleTemplate: function(e) {
                    this.model.set('enabled', !this.model.get('enabled' ));
                },
                template: _.template($('#wpfs-email-template-details').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    this.delegateEvents();
                    return this;
                }
            });

            WPFS.EmailTemplateView = Backbone.View.extend({
                tagName: 'a',
                className: 'wpfs-list__item',
                initialize: function() {
                    this.listenTo(this.model, 'change', this.render);
                },
                attributes: function() {
                    return {
                        'data-template-type': this.model.get('type')
                    };
                },
                events: {
                    'click': 'changeActiveTemplate',
                },
                changeActiveTemplate: function(e) {
                    $('.wpfs-list__item').removeClass('wpfs-list__item--active');
                    this.$el.addClass('wpfs-list__item--active');

                    if (WPFS.emailTemplateDetailsView !== undefined) {
                        WPFS.emailTemplateDetailsView.undelegateEvents();
                    }
                    WPFS.emailTemplateDetailsView = new WPFS.EmailTemplateDetailsView({
                        model: this.model
                    });
                    $('#wpfs-template-details-container').empty().append( WPFS.emailTemplateDetailsView.render().el );
                },
                template: _.template($('#wpfs-email-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    this.delegateEvents();
                    return this;
                }
            });
            WPFS.EmailTemplatesView = Backbone.View.extend({
                el: '#wpfs-templates-container',
                initialize: function() {
                    this.render();
                },
                render: function() {
                    this.$el.html('');

                    WPFS.emailTemplates.each(function(model) {
                        var emailTemplate = new WPFS.EmailTemplateView({
                            model: model
                        });

                        this.$el.append(emailTemplate.render().el);
                    }, this );

                    return this;                }
            });
            WPFS.EmailTemplateModel = Backbone.Model.extend( {} );
            WPFS.EmailTemplateCollection = Backbone.Collection.extend({
                model: WPFS.EmailTemplateModel
            });

            WPFS.emailTemplates = new WPFS.EmailTemplateCollection();
            wpfsEmailTemplates.forEach( element => WPFS.emailTemplates.add( new WPFS.EmailTemplateModel( element )));

            new WPFS.EmailTemplatesView();
            $('.wpfs-list__item').first().click();
        }

        function transformCustomFields( $form ) {
            var customFieldNames = [];

            $('#wpfs-custom-fields .wpfs-field-list__item').each( function() {
                customFieldNames.push( $(this).data('custom-field-name'));
            });

            $form.find('input[name="wpfs-form-custom-fields"]').val( customFieldNames.join('{{') );
        }

        function transformEmailTemplates( $form ) {
            $form.find('input[name="wpfs-form-email-templates"]').val( encodeURIComponent( JSON.stringify( WPFS.emailTemplates )));
        }

        function showFormTab( tabId ) {
            $('.wpfs-edit-form-pane').hide();
            $('.wpfs-edit-form-pane[data-tab-id="' + tabId + '"]').show();
        }

        function initEditFormTabs() {
            $('.wpfs-form-tab').on('click', function(e) {
                e.preventDefault();

                var $this = $(this);
                var tabId = $this.data('tab-id');

                activateTabSelector( tabId );
                showFormTab( tabId );
           });
        }

        function initTransactionDescriptionSelectionTracking() {
            $('.js-position-tracking-transaction-description').on( 'blur', function(e) {
                $(this).data('selectionStart', $(this).prop('selectionStart'));
            });
        }

        function initEditFormBillingShippingAddress() {
            $('input[name="wpfs-form-collect-billing-address"]').on('click', function(e) {
                var $this = $(this);

                if ( $this.val() === '0' ) {
                    $('input[name="wpfs-form-collect-shipping-address"][value="0"]').click();
                }
            });
            $('input[name="wpfs-form-collect-shipping-address"]').on('click', function(e) {
                var $this = $(this);

                if ( $this.val() === '1' ) {
                    $('input[name="wpfs-form-collect-billing-address"][value="1"]').click();
                }
            });
        }

        function initEditFormTermsOfService() {
            $('input[name="wpfs-form-show-terms-of-service"]').on('click', function(e) {
                var $this = $(this);

                if ( $this.val() === '1' ) {
                    $('.wpfs-tos-section').show();
                } else {
                    $('.wpfs-tos-section').hide();
                }
            });
        }

        function initEditFormCustomFields() {
            initAddCustomFieldDialog();
            initAddCustomFieldTemplate();
            initCustomFieldActionDialogs();

            displayCustomFieldRequired();
            displayAddCustomField();
        }

        function deleteSuggestedDonationAmount( model, dialogSelector) {
            $('.wpfs-field-list__item[data-suggested-donation-amount="' + model.get('pageData').amount + '"]' ).remove();
            $(dialogSelector).dialog('close');
        }

        function extractSuggestedDonationAmount( $node) {
            var amount            = $node.closest('.wpfs-field-list__item').data('suggested-donation-amount');
            var currencyFormatter = createAdminCurrencyFormatter();
            var $currencyNode     = $('select[name="wpfs-form-currency"] option:selected');

            return {
                amount:     amount,
                itemName:   formatSuggestedDonationAmount( amount, $currencyNode, currencyFormatter )
            }
        }

        function initSuggestedDonationAmountActionDialogs() {
            $('.js-delete-suggested-donation-amount').off('click');

            var deleteDonationAmountOptions = {
                dialogId:                       'wpfs-delete-suggested-donation-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.deleteSuggestedDonationTitle,
                normalTemplateSelector:         '#wpfs-modal-delete-suggested-donation-amount',
                clickSelector:                  '.js-delete-suggested-donation-amount',
                extractPageDataCallback:        extractSuggestedDonationAmount,
                clientCallback:                 deleteSuggestedDonationAmount,
                confirmationMessage:            wpfsAdminL10n.deleteSuggestedDonationAmountConfirmationMessage,
                dialogContainerSelector:        '#wpfs-dialog-container',
                confirmButtonSelector:          '.js-delete-suggested-donation-amount-dialog',
            }
            initClientActionDialog( deleteDonationAmountOptions );
        }

        function addSuggestedDonationAmount(model, dialogSelector) {
            var currencyFormatter = createAdminCurrencyFormatter();
            var $currencyNode = $('select[name="wpfs-form-currency"] option:selected');

            var dialogData = model.get('dialogData');
            renderSuggestedDonationAmount( dialogData.amount, $currencyNode, currencyFormatter );

            $(dialogSelector).dialog('close');

            initSuggestedDonationAmountActionDialogs();
        }

        function extractAmount( amountStr, zeroDecimalSupported ) {
            var currencyFormatter = createAdminCurrencyFormatter();
            var amount = currencyFormatter.parse(amountStr);
            if ( !zeroDecimalSupported ) {
                amount *= 100;
            }

            return amount;
        }

        function extractAmountWithCurrencyKey( amountStr, currencyKey ) {
            var zeroDecimalSupport = wpfsCurrencies[ currencyKey ].zeroDecimalSupport;

            return extractAmount( amountStr, zeroDecimalSupport );
        }

        function extractAmountWithCurrencyNode( amountStr, $currency ) {
            var zeroDecimalSupport = $currency.data('zero-decimal-support');

            return extractAmount( amountStr, zeroDecimalSupport );
        }

        function extractSuggestedDonationAmountDialog( dialogId, model ) {
            var $currency = $('select[name="wpfs-form-currency"] option:selected');
            var amountStr = $('input[name="' + FORM_FIELD_SUGGESTED_DONATION_AMOUNT + '"]').val();

            var dialogData = {
                amount: extractAmountWithCurrencyNode( amountStr, $currency )
            };

            return dialogData;
        }

        function validZeroDecimalAmountString(amountStr) {
            var $currency = $('select[name="wpfs-form-currency"] option:selected');
            var zeroDecimalSupport = $currency.data('zero-decimal-support');

            if (zeroDecimalSupport) {
                return amountStr.indexOf('.') === -1 && amountStr.indexOf(',') === -1;
            } else {
                return true;
            }
        }

        function validateAddSuggestedDonationAmountDialog( bindingResult ) {
            var currencyFormatter = createAdminCurrencyFormatter();

            var amountStr = $('input[name="' + FORM_FIELD_SUGGESTED_DONATION_AMOUNT + '"]').val();
            if ( !amountStr ) {
                var fieldId = generateFormElementId( FORM_FIELD_SUGGESTED_DONATION_AMOUNT, bindingResult.getFormHash() );
                bindingResult.addFieldError(FORM_FIELD_SUGGESTED_DONATION_AMOUNT, fieldId, wpfsAdminL10n.suggestedDonationAmountRequiredMessage );
            } else if ( !currencyFormatter.validForParse(amountStr) ) {
                var fieldId = generateFormElementId( FORM_FIELD_SUGGESTED_DONATION_AMOUNT, bindingResult.getFormHash() );
                bindingResult.addFieldError(FORM_FIELD_SUGGESTED_DONATION_AMOUNT, fieldId, wpfsAdminL10n.suggestedDonationAmountInvalidMessage );
            } else if ( !validZeroDecimalAmountString(amountStr) ) {
                var fieldId = generateFormElementId( FORM_FIELD_SUGGESTED_DONATION_AMOUNT, bindingResult.getFormHash() );
                bindingResult.addFieldError(FORM_FIELD_SUGGESTED_DONATION_AMOUNT, fieldId, wpfsAdminL10n.suggestedDonationAmountNotWholeNumberMessage );
            }
        }

        function extractAddAmountCurrencySymbol() {
            var $currency = $('select[name="wpfs-form-currency"] option:selected');
            var currencySymbol = $currency.data('currency-symbol');
            var currencyCode = $currency.data('currency-code');

            return {
                currencySymbol: wpfsAdminSettings.preferences.currencyShowSymbolInsteadOfCode == '1' ? currencySymbol : currencyCode,
            };
        }

        function initAddSuggestedDonationAmountDialog() {
            var addAmountOptions = {
                dialogId:                       'wpfs-add-suggested-donation-amount-dialog',
                dialogClass:                    'wpfs-dialog-content',
                dialogTitle:                    wpfsAdminL10n.addSuggestedDonationAmountTitle,
                normalTemplateSelector:         '#wpfs-modal-add-suggested-donation-amount',
                clickSelector:                  '.js-add-suggested-donation-amount',
                dialogContainerSelector:        '#wpfs-dialog-container',
                extractPageDataCallback:        extractAddAmountCurrencySymbol,
                confirmButtonSelector:          '.js-add-suggested-donation-amount-dialog',
                formFields:                     {
                    'wpfs-suggested-donation-amount':  createInputGroupDescriptor( FORM_FIELD_SUGGESTED_DONATION_AMOUNT )
                },
                formType:                       'addSuggestedDonationAmount',
                validatorCallback:              validateAddSuggestedDonationAmountDialog,
                extractDialogDataCallback:      extractSuggestedDonationAmountDialog,
                clientCallback:                 addSuggestedDonationAmount
            }
            initClientActionDialog( addAmountOptions );
        }

        function extractSuggestedDonationAmounts() {
            var amounts = [];

            $('#wpfs-suggested-donation-amounts .wpfs-field-list__item').each( function() {
                amounts.push( $(this).data('suggested-donation-amount'));
            });

            return amounts;
        }

        function initEditDonationFormCurrencyChange() {
            $('select[name="wpfs-form-currency"]').on('comboboxchange', function() {
                var donationAmounts = extractSuggestedDonationAmounts();

                var currencyFormatter = createAdminCurrencyFormatter();
                var $currencyNode = $('select[name="wpfs-form-currency"] option:selected');

                renderSuggestedDonationAmounts( donationAmounts, $currencyNode, currencyFormatter );
            });
        }

        function initEditDonationFormAmounts() {
            initEditFormSuggestedDonationAmountTemplates();
            initEditFormRenderSuggestedDonationAmounts();
            initAddSuggestedDonationAmountDialog();
            initEditDonationFormCurrencyChange();
        }

        function initEditFormTransactionDescription() {
            initTransactionDescriptionSelectionTracking();

            WPFS.macros = createMacroDescriptions( wpfsAdminSettings.macroKeys, wpfsAdminSettings.macroDescriptions );
            WPFS.InsertToken.init(WPFS.macros, '.js-insert-token-transaction-description','.js-token-target-transaction-description');
        }

        function initEventHandlersOnInlineSaveCardFormEdit() {
            initEditFormTabs();
            initEditFormTransactionDescription();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-inline-save-card-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                makeAjaxCallWithForm( $form );
            });
        }

        function initEventHandlersOnCheckoutSaveCardFormEdit() {
            initEditFormTabs();
            initEditFormTransactionDescription();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-checkout-save-card-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                makeAjaxCallWithForm( $form );
            });
        }

        function initFormCssIdCopyToClipboard() {
            $('.js-copy-form-css-id').on('click', function(e) {
                e.preventDefault();

                var formCssId = $('.js-copy-form-css-id').data( 'form-css-id' );
                copyToClipboard( formCssId );
                displaySuccessMessageBanner(wpfsAdminL10n.formCssIdCopiedMessage);
            });
        }

        function onInlineSaveCardFormEdit() {
            return $('div.wpfs-page-edit-inline-save-card-form').length > 0;
        }

        function initInlineSaveCardFormEdit() {
            if ( onInlineSaveCardFormEdit() ) {
                initEventHandlersOnInlineSaveCardFormEdit();
            }
        }

        function onCheckoutSaveCardFormEdit() {
            return $('div.wpfs-page-edit-checkout-save-card-form').length > 0;
        }

        function initCheckoutSaveCardFormEdit() {
            if ( onCheckoutSaveCardFormEdit() ) {
                initEventHandlersOnCheckoutSaveCardFormEdit();
            }
        }

        function initEditFormSuggestedDonationAmountTemplates() {
            WPFS.SuggestedDonationAmountView = Backbone.View.extend({
                className: 'wpfs-field-list__item ui-sortable-handle',
                attributes: function() {
                    return {
                        'data-suggested-donation-amount': this.model.get('donationAmount')
                    };
                },
                template: _.template($('#wpfs-suggested-donation-amount-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.SuggestedDonationAmountModel = Backbone.Model.extend( {} );
        }

        function formatSuggestedDonationAmount( amount, $currencyNode, currencyFormatter ) {
            var currencySymbol = $currencyNode.data('currency-symbol');
            var currencyCode = $currencyNode.data('currency-code');
            var zeroDecimalSupport = $currencyNode.data('zero-decimal-support');

            return currencyFormatter.format( zeroDecimalSupport ? amount : amount / 100, currencyCode.toUpperCase(), currencySymbol, zeroDecimalSupport );
        }

        function renderSuggestedDonationAmount( amount, $currencyNode, currencyFormatter ) {
            var donationAmountModel = new WPFS.SuggestedDonationAmountModel({
                donationAmount: amount,
                donationAmountLabel: formatSuggestedDonationAmount( amount, $currencyNode, currencyFormatter )
            });
            var donationAmountView = new WPFS.SuggestedDonationAmountView({
                model: donationAmountModel
            });

            $('#wpfs-suggested-donation-amounts').append( donationAmountView.render().el );
        }

        function renderSuggestedDonationAmounts( donationAmounts, $currencyNode, currencyFormatter ) {
            $('#wpfs-suggested-donation-amounts').empty();

            donationAmounts.forEach( function( amount ) {
                renderSuggestedDonationAmount( amount, $currencyNode, currencyFormatter );
            });
        }

        function initEditFormRenderSuggestedDonationAmounts() {
            var currencyFormatter = createAdminCurrencyFormatter();
            var $currencyNode = $('select[name="wpfs-form-currency"] option:selected');

            renderSuggestedDonationAmounts( wpfsSuggestedDonationAmounts, $currencyNode, currencyFormatter );

            initSuggestedDonationAmountActionDialogs();
        }

        function transformDonationAmounts( $form )  {
            var amounts = extractSuggestedDonationAmounts();

            $form.find('input[name="wpfs-form-donation-amounts"]').val( encodeURIComponent(JSON.stringify( amounts )));
        }

        function initEventHandlersOnInlineDonationFormEdit() {
            initEditFormTabs();
            initEditDonationFormAmounts();
            initEditFormTransactionDescription();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-inline-donation-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                transformDonationAmounts( $form )
                makeAjaxCallWithForm( $form );
            });
        }

        function onInlineDonationFormEdit() {
            return $('div.wpfs-page-edit-inline-donation-form').length > 0;
        }

        function initInlineDonationFormEdit() {
            if ( onInlineDonationFormEdit() ) {
                initEventHandlersOnInlineDonationFormEdit();
            }
        }

        function initEventHandlersOnCheckoutDonationFormEdit() {
            initEditFormTabs();
            initEditDonationFormAmounts();
            initEditFormTransactionDescription();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-checkout-donation-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                transformDonationAmounts( $form )
                makeAjaxCallWithForm( $form );
            });
        }

        function onCheckoutDonationFormEdit() {
            return $('div.wpfs-page-edit-checkout-donation-form').length > 0;
        }

        function initCheckoutDonationFormEdit() {
            if ( onCheckoutDonationFormEdit() ) {
                initEventHandlersOnCheckoutDonationFormEdit();
            }
        }

        function initEditFormPaymentTypes() {
            $('input[name="wpfs-form-payment-type"]').on('click', function(e) {
                var $this = $(this);

                if ( $this.val() === 'list_of_amounts' ) {
                    $('#onetime-payment-stripe-product-list').show();
                } else {
                    $('#onetime-payment-stripe-product-list').hide();
                }
            });
        }

        function initOneTimeProductTemplates() {
            WPFS.OnetimeProductView = Backbone.View.extend({
                className: 'wpfs-field-list__item',
                attributes: function() {
                    return {
                        'data-id': this.model.cid,
                        'data-stripe-price-id': this.model.get('stripePriceId')
                    };
                },
                template: _.template($('#wpfs-onetime-product-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.OnetimeProductModel = Backbone.Model.extend( {} );

            WPFS.OnetimeProductsView = Backbone.View.extend({
                el: '#wpfs-onetime-products',
                className: "wpfs-field-list__list js-sortable ui-sortable",
                initialize: function() {
                    this.render();
                    this.listenTo(WPFS.onetimeProducts, 'add', this.render);
                    this.listenTo(WPFS.onetimeProducts, 'remove', this.render);
                },
                events: {
                    'click .js-remove-onetime-product': 'removeOnetimeProduct',
                    'sortstop': 'reorderCollection',
                },
                reorderCollection: function(e, ui) {
                    var cids = this.$el.sortable('toArray', {attribute: 'data-id'});
                    var models = [];

                    cids.forEach(function(cid) {
                        models.push( WPFS.onetimeProducts.get( cid ));
                    });
                    WPFS.onetimeProducts.reset( models );
                },
                removeOnetimeProduct: function(e) {
                    e.preventDefault();

                    var id = $(e.currentTarget).closest('.wpfs-field-list__item').data("id");
                    WPFS.onetimeProducts.remove(id);
                },
                render: function() {
                    this.$el.html('');

                    WPFS.onetimeProducts.each(function(model) {
                        var productView = new WPFS.OnetimeProductView({
                            model: model
                        });

                        this.$el.append(productView.render().el);
                    }, this );

                    return this;
                }
            });
        }

        function formatProductPrice( currencyFormatter, currencyKey, amount ) {
            var currency = wpfsCurrencies[currencyKey];
            var currencySymbol = currency.symbol;
            var currencyCode = currency.code;
            var zeroDecimalSupport = currency.zeroDecimalSupport;

            return currencyFormatter.format( zeroDecimalSupport ? amount : amount / 100, currencyCode.toUpperCase(), currencySymbol, zeroDecimalSupport );
        }

        function getRecurringPriceAndIntervalFormatterLabel( interval, intervalCount ) {
            var formatLabel = '';

            switch( interval ) {
                case 'day': {
                    formatLabel = intervalCount === 1 ?
                        wpfsAdminL10n.recurringPriceWithSingularDayFormatter :
                        wpfsAdminL10n.recurringPriceWithPluralDayFormatter;

                    break;
                }

                case 'week': {
                    formatLabel = intervalCount === 1 ?
                        wpfsAdminL10n.recurringPriceWithSingularWeekFormatter :
                        wpfsAdminL10n.recurringPriceWithPluralWeekFormatter;

                    break;
                }

                case 'month': {
                    formatLabel = intervalCount === 1 ?
                        wpfsAdminL10n.recurringPriceWithSingularMonthFormatter :
                        wpfsAdminL10n.recurringPriceWithPluralMonthFormatter;

                    break;
                }

                case 'year': {
                    formatLabel = intervalCount === 1 ?
                        wpfsAdminL10n.recurringPriceWithSingularYearFormatter :
                        wpfsAdminL10n.recurringPriceWithPluralYearFormatter;

                    break;
                }
            }

            return formatLabel;
        }

        function formatRecurringPriceAndIntervalLabel( currencyFormatter, currencyKey, amount, interval, intervalCount ) {
            var formattedAmount = formatProductPrice( currencyFormatter, currencyKey, amount );
            var formatStr = getRecurringPriceAndIntervalFormatterLabel( interval, intervalCount );
            var resultLabel = '';

            if ( intervalCount === 1 ) {
                resultLabel = sprintf( formatStr, formattedAmount );
            } else {
                resultLabel = sprintf( formatStr, formattedAmount, intervalCount );
            }

            return resultLabel;
        }

        function formatOnetimeProducts( products ) {
            var results = [];
            var currencyFormatter = createAdminCurrencyFormatter();
            var currencyKey = $('select[name="wpfs-form-currency"] option:selected').val();

            products.forEach( function(product) {
                var result = product;
                result.priceLabel = formatProductPrice( currencyFormatter, currencyKey, product.price );

                results.push( result );
            });

            return results;
        }

        function addOnetimeProductsToCollection( products ) {
            products.forEach( function( element ) {
                var filter = {stripePriceId: element.stripePriceId};
                if ( WPFS.onetimeProducts.findWhere(filter) === undefined ) {
                    WPFS.onetimeProducts.add( new WPFS.OnetimeProductModel( element ));
                }
            })
        }

        function getCurrencySymbolByAdminSettings( currencyKey ) {
            var currency = wpfsCurrencies[ currencyKey ];

            return wpfsAdminSettings.preferences.currencyShowSymbolInsteadOfCode == 1 ? currency.symbol : currency.code;
        }

        function initOneTimeProductData() {
            WPFS.OnetimeProductCollection = Backbone.Collection.extend({
                model: WPFS.OnetimeProductModel
            });
            WPFS.onetimeProducts = new WPFS.OnetimeProductCollection();

            var onetimeProducts = formatOnetimeProducts( wpfsOnetimeProducts );
            addOnetimeProductsToCollection( onetimeProducts );

            new WPFS.OnetimeProductsView();
        }

        function showAddProductDialogStep1( dialogSelector ) {
            $( dialogSelector + ' .js-add-product-step-1' ).show();
            $( dialogSelector + ' .js-add-product-step-2' ).hide();
            $( dialogSelector + ' .js-add-product-step-3' ).hide();
        }

        function showAddProductDialogStep2( dialogSelector ) {
            $( dialogSelector + ' .js-add-product-step-1' ).hide();
            $( dialogSelector + ' .js-add-product-step-2' ).show();
            $( dialogSelector + ' .js-add-product-step-3' ).hide();
        }

        function showAddProductDialogStep3( dialogSelector ) {
            $( dialogSelector + ' .js-add-product-step-1' ).hide();
            $( dialogSelector + ' .js-add-product-step-2' ).hide();
            $( dialogSelector + ' .js-add-product-step-3' ).show();
        }

        function initProductSelectorDialog( options ) {
            function onClickOpen( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    WPFS.Dialog.open('#' + ctx.options.dialogId );
                    showAddProductDialogStep1( '#' + ctx.options.dialogId );

                    if ( ctx.options.fetchProductsCallback !== undefined && ctx.options.fetchProductsCallback !== null ) {
                        ctx.options.fetchProductsCallback( ctx );
                    }
                }
            }

            var ctx = {};
            ctx.options = options;

            $(options.clickSelector).on( 'click', onClickOpen( ctx ));
        }

        function displaySelectorProducts( ctx, selectorProducts ) {
            var stripeProductAutocomplete = new WPFS.InlineAutocomplete({
                selector: '#' + ctx.options.dialogId + ' .js-stripe-product-autocomplete',
                source: selectorProducts,
                containerClass: 'wpfs-stripe-product-autocomplete'
            });
            showAddProductDialogStep2( '#' + ctx.options.dialogId );

            if ( ctx.options.attachEventsCallback !== undefined && ctx.options.attachEventsCallback !== null ) {
                ctx.options.attachEventsCallback( ctx );
            }
        }

        function getSelectedProductIds() {
            var ids = [];

            $('.wpfs-stripe-product-autocomplete__item input:checked').each( function(index) {
                ids.push( $(this).val() );
            });

            return ids;
        }

        function getSelectedProducts( productIds ) {
            var productIdLookup = [];
            var selectedProducts = [];

            productIds.forEach( function( productId ) {
               productIdLookup[productId] = productId;
            })

            WPFS.fetchedProducts.forEach( function( fetchedProduct ) {
                if ( productIdLookup.hasOwnProperty( fetchedProduct.stripePriceId ) ) {
                    selectedProducts.push( fetchedProduct );
                }
            });

            return selectedProducts;
        }

        function attachOnetimeSelectorEvents( ctx ) {
            $(ctx.options.addButtonSelector).on('click', function(e) {
                e.preventDefault();

                var selectedProductIds = getSelectedProductIds();
                var selectedProducts = getSelectedProducts( selectedProductIds )
                var formattedProducts = formatOnetimeProducts( selectedProducts );
                addOnetimeProductsToCollection( formattedProducts );

                $('#' + ctx.options.dialogId).dialog('close');
            });
        }

        function fetchProducts( action, ctx ) {
            $.ajax({
                type: "POST",
                url: wpfsAdminSettings.ajaxUrl,
                data: {
                    action: action
                },
                cache: false,
                dataType: "json",
                success: function (responseData) {
                    if (responseData.success) {
                        WPFS.fetchedProducts = responseData.data;
                    } else {
                        console.log( 'Stripe products cannot be downloaded' );
                        WPFS.fetchedProducts = [];
                    }
                    WPFS.selectorProducts = ctx.options.createSelectorProductsCallback( WPFS.fetchedProducts );
                    displaySelectorProducts( ctx, WPFS.selectorProducts );
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    logError('wpfs-admin.fetchProducts()', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    // noop
                }
            });
        }

        function fetchOnetimeProducts( ctx ) {
            fetchProducts( 'wpfs-get-onetime-products', ctx );
        }

        function createOntimeSelectorProducts( products ) {
            var currencyFormatter = createAdminCurrencyFormatter();
            var res = [];

            products.forEach( function( product ) {
                res.push({
                    value: product.stripePriceId,
                    label: product.name,
                    price: formatProductPrice( currencyFormatter, product.currency, product.price )
                });
            });

            return res;
        }

        function initOneTimeAddProduct() {
            var addProductsOptions = {
                dialogId:                       'wpfs-add-onetime-products-dialog',
                clickSelector:                  '.js-add-onetime-product',
                fetchProductsCallback:          fetchOnetimeProducts,
                createSelectorProductsCallback: createOntimeSelectorProducts,
                attachEventsCallback:           attachOnetimeSelectorEvents,
                addButtonSelector:              '.js-add-onetime-products'
            }
            initProductSelectorDialog( addProductsOptions );
        }

        function initEditFormOneTimeProducts() {
            initOneTimeProductTemplates();
            initOneTimeProductData();
            initOneTimeAddProduct();
        }

        function transformOnetimeProducts( $form ) {
            var products = [];

            WPFS.onetimeProducts.each( function( product ) {
               products.push( product.get( 'stripePriceId' ));
            });

            $form.find('input[name="wpfs-form-onetime-products"]').val( encodeURIComponent( JSON.stringify( products )));
        }

        function transformTaxRates( $form ) {
            var taxRates = [];

            WPFS.taxRates.each( function( product ) {
                taxRates.push( product.get( 'taxRateId' ));
            });

            $form.find('input[name="wpfs-form-tax-rates"]').val( encodeURIComponent( JSON.stringify( taxRates )));
        }

        function initTaxRateTemplates() {
            WPFS.TaxRateView = Backbone.View.extend({
                className: 'wpfs-field-list__item',
                attributes: function() {
                    return {
                        'data-id': this.model.cid,
                        'data-tax-rate-id': this.model.get('taxRateId')
                    };
                },
                template: _.template($('#wpfs-tax-rate-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.TaxRateModel = Backbone.Model.extend( {} );

            WPFS.TaxRatesView = Backbone.View.extend({
                el: '#wpfs-tax-rates',
                className: "wpfs-field-list__list js-sortable ui-sortable",
                initialize: function() {
                    this.render();
                    this.listenTo(WPFS.taxRates, 'add', this.render);
                    this.listenTo(WPFS.taxRates, 'remove', this.render);
                },
                events: {
                    'click .js-remove-tax-rate': 'removeTaxRate',
                    'sortstop': 'reorderCollection',
                },
                reorderCollection: function(e, ui) {
                    var cids = this.$el.sortable('toArray', {attribute: 'data-id'});
                    var models = [];

                    cids.forEach(function(cid) {
                        models.push( WPFS.taxRates.get( cid ));
                    });
                    WPFS.taxRates.reset( models );
                },
                removeTaxRate: function(e) {
                    e.preventDefault();

                    var id = $(e.currentTarget).closest('.wpfs-field-list__item').data("id");
                    WPFS.taxRates.remove(id);
                },
                render: function() {
                    this.$el.html('');

                    WPFS.taxRates.each(function(model) {
                        var taxRateView = new WPFS.TaxRateView({
                            model: model
                        });

                        this.$el.append(taxRateView.render().el);
                    }, this );

                    return this;
                }
            });
        }

        function isWholeNumber( num ) {
            return num % 1 === 0;
        }

        function formatTaxRatePercentageLabel( taxRate ) {
            var decimalSeparator = wpfsAdminSettings.preferences.currencyDecimalSeparatorSymbol === WPFS_DECIMAL_SEPARATOR_DOT ? '.' : ',';
            var formattedPercentage;
            var percentageFormatterString;

            if ( isWholeNumber( taxRate.percentage )) {
                formattedPercentage = taxRate.percentage;
            } else {
                formattedPercentage = number_format(taxRate.percentage, 4, decimalSeparator, '');
            }

            if ( taxRate.inclusive ) {
                percentageFormatterString = wpfsAdminL10n.taxRateInclusiveDescription;
            } else {
                percentageFormatterString = wpfsAdminL10n.taxRateExclusiveDescription;
            }

            return sprintf( percentageFormatterString, formattedPercentage, taxRate.displayName )
        }

        function formatTaxRateRegionLabel( taxRate ) {
            var label = '';

            if ( taxRate.countryLabel !== null ) {
                label = taxRate.countryLabel;

                if ( taxRate.stateLabel !== null ) {
                    label += ' / ' + taxRate.stateLabel;
                }
            }

            return label;
        }

        function formatTaxRates( taxRates ) {
            var results = [];

            taxRates.forEach( function(taxRate) {
                var result = taxRate;
                result.regionLabel = formatTaxRateRegionLabel( taxRate );
                result.percentageLabel = formatTaxRatePercentageLabel( taxRate );

                results.push( result );
            });

            return results;
        }

        function addTaxRatesToCollection( taxRates ) {
            taxRates.forEach( function( element ) {
                var filter = {taxRateId: element.taxRateId};
                if ( WPFS.taxRates.findWhere(filter) === undefined ) {
                    WPFS.taxRates.add( new WPFS.TaxRateModel( element ));
                }
            })
        }

        function initTaxRateData() {
            WPFS.TaxRateCollection = Backbone.Collection.extend({
                model: WPFS.TaxRateModel
            });
            WPFS.taxRates = new WPFS.TaxRateCollection();

            var taxRates = formatTaxRates( wpfsTaxRates );
            addTaxRatesToCollection( taxRates );

            new WPFS.TaxRatesView();
        }

        function fetchTaxRates( ctx ) {
            fetchProducts( 'wpfs-get-tax-rates', ctx );
        }

        function createTaxRatesForSelector( taxRates ) {
            var res = [];

            taxRates.forEach( function( taxRate ) {
                res.push({
                    value: taxRate.taxRateId,
                    label: formatTaxRateRegionLabel( taxRate ),
                    price: formatTaxRatePercentageLabel( taxRate )
                });
            });

            return res;
        }

        function getSelectedTaxRates( vatRateIds ) {
            var taxRateIdLookup = [];
            var selectedVatRates = [];

            vatRateIds.forEach( function( vatRateId ) {
                taxRateIdLookup[vatRateId] = vatRateId;
            })

            WPFS.fetchedProducts.forEach( function( fetchedVatRate ) {
                if ( taxRateIdLookup.hasOwnProperty( fetchedVatRate.taxRateId ) ) {
                    selectedVatRates.push( fetchedVatRate );
                }
            });

            return selectedVatRates;
        }

        function attachTaxRateSelectorEvents( ctx ) {
            $(ctx.options.addButtonSelector).on('click', function(e) {
                e.preventDefault();

                var selectedTaxRateIds = getSelectedProductIds();
                var selectedTaxRates = getSelectedTaxRates( selectedTaxRateIds );
                var formattedTaxRates = formatTaxRates( selectedTaxRates );

                addTaxRatesToCollection( formattedTaxRates );

                $('#' + ctx.options.dialogId).dialog('close');
            });
        }

        function initAddTaxRate() {
            var addTaxRatesOptions = {
                dialogId:                       'wpfs-add-tax-rates-dialog',
                clickSelector:                  '.js-add-tax-rate',
                fetchProductsCallback:          fetchTaxRates,
                createSelectorProductsCallback: createTaxRatesForSelector,
                attachEventsCallback:           attachTaxRateSelectorEvents,
                addButtonSelector:              '.js-add-tax-rates'
            }
            initProductSelectorDialog( addTaxRatesOptions );
        }

        function initTaxRateEventHandlers() {
            $('input[name="wpfs-form-tax-rate-type"]').on('click', function(e) {
                var $this = $(this);

                if ( $this.val() === FIELD_VALUE_TAX_RATE_NO_TAX ) {
                    $('#tax-rates-settings').hide();
                } else if ( $this.val() === FIELD_VALUE_TAX_RATE_FIXED ) {
                    $('#tax-rates-settings').show();
                    $('#tax-rates').show();
                } else if ( $this.val() === FIELD_VALUE_TAX_RATE_DYNAMIC ) {
                    $('#tax-rates-settings').show();
                    $('#tax-rates').show();
                } else {
                    $('#tax-rates-settings').hide();
                }
            });

            $('input[name="wpfs-form-collect-customer-tax-id"]').on('click', function(e) {
                var $this = $(this);
            });
        }

        function initEditFormTaxRates() {
            initTaxRateTemplates();
            initTaxRateData();
            initAddTaxRate();
            initTaxRateEventHandlers();
        }

        function initEventHandlersOnInlinePaymentFormEdit() {
            initEditFormTabs();
            initEditFormTransactionDescription();
            initEditFormPaymentTypes();
            initEditFormOneTimeProducts();
            initEditFormTaxRates();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-inline-payment-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformOnetimeProducts( $form );
                transformTaxRates( $form );
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                makeAjaxCallWithForm( $form );
            });
        }

        function onInlinePaymentFormEdit() {
            return $('div.wpfs-page-edit-inline-payment-form').length > 0;
        }

        function initInlinePaymentFormEdit() {
            if ( onInlinePaymentFormEdit() ) {
                initEventHandlersOnInlinePaymentFormEdit();
            }
        }

        function initEventHandlersOnCheckoutPaymentFormEdit() {
            initEditFormTabs();
            initEditFormTransactionDescription();
            initEditFormPaymentTypes();
            initEditFormOneTimeProducts();
            initEditFormTaxRates();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-checkout-payment-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformOnetimeProducts( $form );
                transformTaxRates( $form );
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                makeAjaxCallWithForm( $form );
            });
        }

        function onCheckoutPaymentFormEdit() {
            return $('div.wpfs-page-edit-checkout-payment-form').length > 0;
        }

        function initCheckoutPaymentFormEdit() {
            if ( onCheckoutPaymentFormEdit() ) {
                initEventHandlersOnCheckoutPaymentFormEdit();
            }
        }

        function initEditFormSubscriptionQuantity() {
            $('input[name="wpfs-form-subscription-quantity"]').on('click', function(e) {
                var $this = $(this);

                if ( $this.val() == '1' ) {
                    $('#subscription-maximum-plan-quantity').show();
                } else {
                    $('#subscription-maximum-plan-quantity').hide();
                }
            });
        }

        function initRecurringProductTemplates() {
            WPFS.RecurringProductView = Backbone.View.extend({
                className: 'wpfs-field-list__item',
                attributes: function() {
                    return {
                        'data-id': this.model.cid,
                        'data-stripe-price-id': this.model.get('stripePriceId')
                    };
                },
                template: _.template($('#wpfs-recurring-product-template').html()),
                render: function() {
                    this.$el.html(this.template(this.model.attributes));
                    return this;
                }
            });
            WPFS.RecurringProductModel = Backbone.Model.extend( {} );

            WPFS.RecurringProductsView = Backbone.View.extend({
                el: '#wpfs-recurring-products',
                className: "wpfs-field-list__list js-sortable ui-sortable",
                initialize: function(options) {
                    this.formLayout = options.formLayout;
                    this.render();

                    this.listenTo(WPFS.recurringProducts, 'add', this.render);
                    this.listenTo(WPFS.recurringProducts, 'remove', this.render);
                    this.listenTo(WPFS.recurringProducts, 'change', this.render);
                },
                events: {
                    'click .js-remove-recurring-product': 'removeRecurringProduct',
                    'sortstop': 'reorderCollection',
                },
                reorderCollection: function(e, ui) {
                    var cids = this.$el.sortable('toArray', {attribute: 'data-id'});
                    var models = [];

                    cids.forEach(function(cid) {
                        models.push( WPFS.recurringProducts.get( cid ));
                    });
                    WPFS.recurringProducts.reset( models );
                },
                removeRecurringProduct: function(e) {
                    e.preventDefault();

                    var id = $(e.currentTarget).closest('.wpfs-field-list__item').data("id");
                    WPFS.recurringProducts.remove(id);
                },
                render: function() {
                    this.$el.html('');

                    WPFS.recurringProducts.each(function(model) {
                        var productView = new WPFS.RecurringProductView({
                            model: model
                        });

                        this.$el.append(productView.render().el);
                    }, this );

                    initRecurringEditProduct( this.formLayout );

                    return this;
                }
            });
        }

        function formatRecurringProductDescriptionLine1( currencyFormatter, product ) {
            var recurringPriceLabel = formatRecurringPriceAndIntervalLabel( currencyFormatter, product.currency, product.price, product.interval, product.intervalCount );

            var priceLabel;
            if ( product.setupFee === 0 ) {
                priceLabel = recurringPriceLabel;
            } else {
                var setupFeeLabel = formatProductPrice( currencyFormatter, product.currency, product.setupFee );
                priceLabel = sprintf( wpfsAdminL10n.planAndSetupFeeLabel, recurringPriceLabel, setupFeeLabel );
            }

            return '<b>' + product.name + '</b> - ' + priceLabel;
        }

        function formatRecurringProductDescriptionLine2( currencyFormatter, product ) {
            var result = '';
            var firstSegment = true;

            if ( product.trialDays > 0 ) {
                result += sprintf( wpfsAdminL10n.trialDaysLabel, product.trialDays );
                firstSegment = false;
            }

            if ( firstSegment === false ) {
                result += ' • ';
            }
            if ( product.cancellationCount === 0 ) {
                result += wpfsAdminL10n.runningUntilCanceledLabel;
            } else {
                result += sprintf( wpfsAdminL10n.canceledAfterXOccurrences, product.cancellationCount );
            }
            firstSegment = false;

            return result;
        }

        function formatRecurringProducts( products ) {
            var results = [];
            var currencyFormatter = createAdminCurrencyFormatter();

            products.forEach( function(product) {
                var result = product;

                product.planDescriptionLine1 = formatRecurringProductDescriptionLine1( currencyFormatter, product );
                product.planDescriptionLine2 = formatRecurringProductDescriptionLine2( currencyFormatter, product );

                results.push( result );
            });

            return results;
        }

        function addRecurringProductsToCollection( products ) {
            products.forEach( function( element ) {
                var filter = {stripePriceId: element.stripePriceId};
                if ( WPFS.recurringProducts.findWhere(filter) === undefined ) {
                    WPFS.recurringProducts.add( new WPFS.RecurringProductModel( element ));
                }
            })
        }

        function updateRecurringProductInCollection( product ) {
            var filter = {stripePriceId: product.stripePriceId};
            var productModel = WPFS.recurringProducts.findWhere(filter);

            if ( productModel !== undefined ) {
                productModel.set( product );
            }
        }

        function initRecurringProductData( formLayout ) {
            WPFS.RecurringProductCollection = Backbone.Collection.extend({
                model: WPFS.RecurringProductModel
            });
            WPFS.recurringProducts = new WPFS.RecurringProductCollection();

            var recurringProducts = formatRecurringProducts( wpfsRecurringProducts );
            addRecurringProductsToCollection( recurringProducts );

            new WPFS.RecurringProductsView({formLayout: formLayout});
        }

        function fetchRecurringProducts( ctx ) {
            fetchProducts( 'wpfs-get-recurring-products', ctx );
        }

        function formatDescriptionForRecurringProductSelector( product ) {
            var description = '';

            var pricingMode = PRICE_MODE_STANDARD;
            if ( product.billingScheme === PRICE_BILLING_SCHEME_TIERED ) {
                pricingMode = product.tiersMode;
            }

            if ( pricingMode === PRICE_MODE_STANDARD ) {
                description = wpfsAdminL10n.standardPricingLabel;
            } else if  ( pricingMode === PRICE_MODE_VOLUME ) {
                description = wpfsAdminL10n.volumePricingLabel;
            } else if  ( pricingMode === PRICE_MODE_GRADUATED ) {
                description = wpfsAdminL10n.graduatedPricingLabel;
            }

            var metered = product.usageType === PRICE_USAGE_TYPE_METERED;
            if ( metered ) {
                description += ' • ' + wpfsAdminL10n.meteredBillingLabel;
            }

            return description;
        }

        function createRecurringSelectorProducts( products ) {
            var currencyFormatter = createAdminCurrencyFormatter();
            var res = [];

            products.forEach( function( product ) {
                res.push({
                    value: product.stripePriceId,
                    label: product.name,
                    price: formatRecurringPriceAndIntervalLabel( currencyFormatter, product.currency, product.price, product.interval, product.intervalCount ),
                    description: formatDescriptionForRecurringProductSelector( product )
                });
            });

            return res;
        }

        function renderAddProductDialogStep3( ctx, selectedProduct ) {
            WPFS.PlanPropertiesModel = Backbone.Model.extend( {} );
            WPFS.AddPlanPropertiesView = Backbone.View.extend({
                tagName: 'form',
                className: '',
                attributes: function() {
                    return {
                        'data-id': this.model.cid,
                        'data-stripe-price-id': this.model.get('stripePriceId'),
                        'data-wpfs-form-type': 'addPlanProperties'
                    };
                },
                template: _.template($('#wpfs-add-recurring-product-properties-template').html()),
                render: function() {
                    function getCurrencySymbol( currencyKey ) {
                        return getCurrencySymbolByAdminSettings( currencyKey );
                    }
                    function tsprintf( format, ...arguments ) {
                        return sprintf( format, arguments );
                    }
                    this.$el.html(this.template( _.extend( this.model.attributes, {
                        getCurrencySymbol:  getCurrencySymbol,
                        tsprintf:           tsprintf,
                    }) ));
                    return this;
                }
            });

            WPFS.planPropertiesModel = new WPFS.PlanPropertiesModel( selectedProduct );
            WPFS.planPropertiesModel.set( 'formLayout', ctx.options.formLayout );
            WPFS.addPlanPropertiesView = new WPFS.AddPlanPropertiesView({
                model:  WPFS.planPropertiesModel
            });
            $('#wpfs-add-recurring-product-properties-container').empty().append( WPFS.addPlanPropertiesView.render().el );
        }

        function attachRecurringSelectorEvents( ctx ) {
            $(ctx.options.selectButtonSelector).on('click', function(e) {
                e.preventDefault();

                var selectedProductIds = getSelectedProductIds().slice(0, 1);

                if ( selectedProductIds.length > 0 ) {
                    var selectedProduct = getSelectedProducts( selectedProductIds )[0];
                    WPFS.selectedRecurringProduct = selectedProduct;

                    renderAddProductDialogStep3( ctx, selectedProduct );
                    if ( ctx.options.attachPropertiesEventsCallback !== undefined && ctx.options.attachPropertiesEventsCallback !== null ) {
                        ctx.options.attachPropertiesEventsCallback( ctx );
                    }
                    showAddProductDialogStep3( '#' + ctx.options.dialogId );
                }
            });
        }

        function isNormalInteger(str) {
            var n = Math.floor(Number(str));
            return n !== Infinity && String(n) === str && n >= 0;
        }

        function isPositiveInteger(str) {
            var n = Math.floor(Number(str));
            return n !== Infinity && String(n) === str && n > 0;
        }

        function validZeroDecimalAmountStringWithCurrency(amountStr, currencyKey ) {
            var currency = wpfsCurrencies[ currencyKey ];

            if ( currency.zeroDecimalSupport ) {
                return amountStr.indexOf('.') === -1 && amountStr.indexOf(',') === -1;
            } else {
                return true;
            }
        }

        function validatePlanPropertiesDialog( $form, bindingResult ) {
            var currencyFormatter = createAdminCurrencyFormatter();
            var currency = WPFS.selectedRecurringProduct.currency;
            var fieldId;

            var setupFeeStr = $form.find('input[name="' + FORM_FIELD_PLAN_SETUP_FEE + '"]').val();
            fieldId = generateFormElementId( FORM_FIELD_PLAN_SETUP_FEE, bindingResult.getFormHash() );
            if ( !setupFeeStr ) {
                bindingResult.addFieldError(FORM_FIELD_PLAN_SETUP_FEE, fieldId, wpfsAdminL10n.requiredFieldMessage );
            } else if ( !currencyFormatter.validForParse(setupFeeStr) ) {
                bindingResult.addFieldError(FORM_FIELD_PLAN_SETUP_FEE, fieldId, wpfsAdminL10n.invalidAmountMessage );
            } else if ( !validZeroDecimalAmountStringWithCurrency(setupFeeStr, currency) ) {
                bindingResult.addFieldError(FORM_FIELD_PLAN_SETUP_FEE, fieldId, wpfsAdminL10n.enterWholeNumberMessage );
            }

            var trialDaysStr = $form.find('input[name="' + FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS + '"]').val();
            fieldId = generateFormElementId( FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS, bindingResult.getFormHash() );
            if ( !trialDaysStr ) {
                bindingResult.addFieldError(FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS, fieldId, wpfsAdminL10n.requiredFieldMessage );
            } else if ( !isNormalInteger(trialDaysStr) ) {
                bindingResult.addFieldError(FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS, fieldId, wpfsAdminL10n.enterWholeNumberMessage );
            }

            var endSubscriptionStr = $form.find('input[name="' + FORM_FIELD_END_SUBSCRIPTION + '"]:checked').val();
            if ( endSubscriptionStr === 'wpfs-end-subscription-after-x-occurrences' ) {
                var cancellationCountStr = $form.find('input[name="' + FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT + '"]').val();
                fieldId = generateFormElementId( FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT, bindingResult.getFormHash() );

                if ( !cancellationCountStr ) {
                    bindingResult.addFieldError(FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT, fieldId, wpfsAdminL10n.requiredFieldMessage );
                } else if ( !isPositiveInteger(cancellationCountStr) ) {
                    bindingResult.addFieldError(FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT, fieldId, wpfsAdminL10n.enterPositiveNumberMessage );
                }
            }

            var billingCycleStr = $form.find('input[name="' + FORM_FIELD_BILLING_CYCLE + '"]:checked').val();
            if ( billingCycleStr === 'wpfs-billing-cycle-on-this-day' ) {
                var billingCycleDayStr = $form.find('input[name="' + FORM_FIELD_BILLING_CYCLE_DAY + '"]').val();
                fieldId = generateFormElementId( FORM_FIELD_BILLING_CYCLE_DAY, bindingResult.getFormHash() );

                if ( !billingCycleDayStr ) {
                    bindingResult.addFieldError(FORM_FIELD_BILLING_CYCLE_DAY, fieldId, wpfsAdminL10n.requiredFieldMessage );
                } else if ( !isPositiveInteger(billingCycleDayStr) ) {
                    bindingResult.addFieldError(FORM_FIELD_BILLING_CYCLE_DAY, fieldId, wpfsAdminL10n.enterPositiveNumberMessage );
                } else if ( parseInt(billingCycleDayStr ) > 28 ) {
                    bindingResult.addFieldError(FORM_FIELD_BILLING_CYCLE_DAY, fieldId, wpfsAdminL10n.billingAnchorDayIntervalMessage );
                }
            }
        }

        function extractPlanPropertiesFromForm( $form ) {
            var res = {};

            var setupFeeStr = $form.find('input[name="' + FORM_FIELD_PLAN_SETUP_FEE + '"]').val();
            res.setupFee = parseInt( extractAmountWithCurrencyKey( setupFeeStr, WPFS.selectedRecurringProduct.currency ));

            var trialDaysStr = $form.find('input[name="' + FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS + '"]').val();
            res.trialDays    = parseInt( trialDaysStr );

            var endSubscriptionStr = $form.find('input[name="' + FORM_FIELD_END_SUBSCRIPTION + '"]:checked').val();
            if ( endSubscriptionStr === 'wpfs-end-subscription-after-x-occurrences' ) {
                var cancellationCountStr = $form.find('input[name="' + FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT + '"]').val();
                res.cancellationCount = parseInt( cancellationCountStr );
            } else {
                res.cancellationCount = 0;
            }

            if ( $form.find('input[name="' + FORM_FIELD_BILLING_CYCLE + '"]:checked').length > 0 ) {
                var billingCycleStr = $form.find('input[name="' + FORM_FIELD_BILLING_CYCLE + '"]:checked').val();
                if ( billingCycleStr === 'wpfs-billing-cycle-on-this-day' ) {
                    var billingCycleDayStr = $form.find('input[name="' + FORM_FIELD_BILLING_CYCLE_DAY + '"]').val();
                    res.billingAnchorDay = parseInt( billingCycleDayStr );
                } else {
                    res.billingAnchorDay = 0;
                }
            } else {
                var billingCycleDayHiddenStr = $form.find('input[name="' + FORM_FIELD_BILLING_CYCLE + '"]').val();
                res.billingAnchorDay = parseInt( billingCycleDayHiddenStr );
            }

            if ( $form.find('input[name="' + FORM_FIELD_PRORATE_UNTIL_BILLING_ANCHOR_DAY + '"]:checked').length > 0 ) {
                var prorateStr = $form.find('input[name="' + FORM_FIELD_PRORATE_UNTIL_BILLING_ANCHOR_DAY + '"]:checked').val();
                res.prorateUntilBillingAnchorDay = parseInt(prorateStr) === 1;
            } else {
                var prorateHiddenStr = $form.find('input[name="' + FORM_FIELD_PRORATE_UNTIL_BILLING_ANCHOR_DAY + '"]').val();
                res.prorateUntilBillingAnchorDay = parseInt( prorateHiddenStr ) === 1;
            }

            return res;
        }

        function getAllowedRecurringProductFields() {
            return [
                'stripePriceId',
                'name',
                'currency',
                'interval',
                'intervalCount',
                'price',
                'setupFee',
                'trialDays',
                'cancellationCount',
                'billingAnchorDay',
                'prorateUntilBillingAnchorDay'
            ];
        }

        function attachPlanPropertiesEvents( ctx ) {
            function onClickConfirm( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    var $form         = findModalForm( ctx.options.dialogId, ctx.options.formType );
                    var bindingResult = new BindingResult( ctx.options.formType );

                    clearFormErrors( $form );
                    ctx.options.propertiesValidatorCallback( $form, bindingResult );

                    if (bindingResult.hasErrors()) {
                        var validationResult = generateValidationResultFromBindingResult( bindingResult );

                        installFormFields( ctx.options.formType, ctx.options.formFields );
                        processValidationErrors( $form, validationResult );
                    } else {
                        var planProperties = extractPlanPropertiesFromForm( $form );
                        var selectedProduct = _.pick( WPFS.selectedRecurringProduct, getAllowedRecurringProductFields() );
                        _.extend( selectedProduct, planProperties );

                        var formattedProducts = formatRecurringProducts( [ selectedProduct ] );
                        addRecurringProductsToCollection( formattedProducts );

                        $('#' + ctx.options.dialogId).dialog('close');
                    }
                }
            }

            $(ctx.options.addButtonSelector).on('click', onClickConfirm( ctx ));

            $( 'input[name="wpfs-end-subscription"]' ).on( 'click', function( e ) {
                var $this = $(this);

                if ( $this.val() === 'wpfs-end-subscription-customer-cancels' ) {
                    $( 'input[name="wpfs-subscription-cancellation-count"]' ).attr( 'disabled', true );
                } else {
                    $( 'input[name="wpfs-subscription-cancellation-count"]' ).attr( 'disabled', false );
                }
            });
            $('#wpfs-end-subscription-customer-cancels--addPlanProperties').trigger('click');

            $( 'input[name="wpfs-billing-cycle"]' ).on( 'click', function( e ) {
                var $this = $(this);

                if ( $this.val() === 'wpfs-billing-cycle-customer-subscribed' ) {
                    $( 'input[name="wpfs-billing-cycle-day"]' ).attr( 'disabled', true );
                    $( '#wpfs-prorate-until-billing-anchor-day' ).hide();
                } else {
                    $( 'input[name="wpfs-billing-cycle-day"]' ).attr( 'disabled', false );
                    $( '#wpfs-prorate-until-billing-anchor-day' ).show();
                }
            });
            $('#wpfs-billing-cycle-customer-subscribed--addPlanProperties').trigger('click');

            $('#wpfs-prorate-until-billing-anchor-day-no--addPlanProperties').trigger('click');
        }

        function initProductPropertyEditorDialog( options ) {
            function onClickConfirm( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    var $form         = findModalForm( ctx.options.dialogId, ctx.options.formType );
                    var bindingResult = new BindingResult( ctx.options.formType );

                    clearFormErrors( $form );
                    ctx.options.propertiesValidatorCallback( $form, bindingResult );

                    if (bindingResult.hasErrors()) {
                        var validationResult = generateValidationResultFromBindingResult( bindingResult );

                        installFormFields( ctx.options.formType, ctx.options.formFields );
                        processValidationErrors( $form, validationResult );
                    } else {
                        var planProperties = extractPlanPropertiesFromForm( $form );
                        var selectedProduct = _.pick( ctx.model.toJSON(), getAllowedRecurringProductFields() );
                        _.extend( selectedProduct, planProperties );

                        var formattedProducts = formatRecurringProducts( [ selectedProduct ] );

                        $('#' + ctx.options.dialogId).dialog('close');
                        updateRecurringProductInCollection( formattedProducts[0] );
                    }
                }
            }

            function onClickOpen( ctx ) {
                return function( e ) {
                    e.preventDefault();

                    var $this = $(this);

                    var productData = {}
                    if ( ctx.options.getProductCallback !== undefined && ctx.options.getProductCallback !== null ) {
                        productData = ctx.options.getProductCallback( $this ).toJSON();
                        WPFS.selectedRecurringProduct = productData;
                    }

                    ctx.model = new ctx.Model( productData );
                    ctx.model.set( 'formLayout', ctx.options.formLayout );
                    ctx.view = new ctx.View({
                        model: ctx.model
                    });

                    $( ctx.options.dialogContainerSelector ).empty().append( ctx.view.render().el );

                    if ( ctx.options.attachEventsCallback !== undefined && ctx.options.attachEventsCallback !== null ) {
                        ctx.options.attachEventsCallback( ctx );
                    }
                    $(ctx.options.saveButtonSelector).on('click', onClickConfirm( ctx ));

                    WPFS.Dialog.open('#' + ctx.options.dialogId );
                }
            }

            var ctx = {};
            ctx.options = options;

            ctx.View = Backbone.View.extend({
                id: options.dialogId,
                className: 'wpfs-dialog-content',
                attributes: function() {
                    return {
                        'title': ctx.options.dialogTitle,
                        'data-id': this.model.cid,
                        'data-stripe-price-id': this.model.get('stripePriceId'),
                    };
                },
                template: _.template($(options.templateSelector).html()),
                render: function() {
                    function getCurrencySymbol( currencyKey ) {
                        return getCurrencySymbolByAdminSettings( currencyKey );
                    }
                    function tsprintf( format, ...arguments ) {
                        return sprintf( format, arguments );
                    }
                    function formatAmount( amount, currencyKey ) {
                        return formatAmountWithAdminSettings( amount, currencyKey );
                    }
                    this.$el.html(this.template( _.extend( this.model.attributes, {
                        getCurrencySymbol:  getCurrencySymbol,
                        tsprintf:           tsprintf,
                        formatAmount:       formatAmount
                    }) ));
                    return this;
                }
            });
            ctx.Model = Backbone.Model.extend( {} );

            $(options.clickSelector).on( 'click', onClickOpen( ctx ));
        }

        function formatAmountWithAdminSettings( rawAmount, currencyKey ) {
            var decimalSeparator    = wpfsAdminSettings.preferences.currencyDecimalSeparatorSymbol === WPFS_DECIMAL_SEPARATOR_DOT ? '.' : ',';
            var groupSeparator      = '';
            var zeroDecimalSupport  = wpfsCurrencies[ currencyKey ].zeroDecimalSupport;

            var amount = rawAmount;
            if ( !zeroDecimalSupport ) {
                amount /= 100;
            }

            return number_format(amount, zeroDecimalSupport ? 0 : 2, decimalSeparator, groupSeparator );
        }

        function getRecurringProductByCid( $node ) {
            var $recurringProductItem = $node.closest( 'div.wpfs-field-list__item' );
            var cid = $recurringProductItem.data('id');

            return WPFS.recurringProducts.get( cid );
        }

        function attachProductPropertiesEditorEvents() {
            $( 'input[name="wpfs-end-subscription"]' ).on( 'click', function( e ) {
                var $this = $(this);

                if ( $this.val() === 'wpfs-end-subscription-customer-cancels' ) {
                    $( 'input[name="wpfs-subscription-cancellation-count"]' ).attr( 'disabled', true );
                } else {
                    $( 'input[name="wpfs-subscription-cancellation-count"]' ).attr( 'disabled', false );
                }
            });

            $( 'input[name="wpfs-billing-cycle"]' ).on( 'click', function( e ) {
                var $this = $(this);

                if ( $this.val() === 'wpfs-billing-cycle-customer-subscribed' ) {
                    $( 'input[name="wpfs-billing-cycle-day"]' ).attr( 'disabled', true );
                    $( '#wpfs-prorate-until-billing-anchor-day-edit' ).hide();
                } else {
                    $( 'input[name="wpfs-billing-cycle-day"]' ).attr( 'disabled', false );
                    $( '#wpfs-prorate-until-billing-anchor-day-edit' ).show();
                }
            });
        }

        function initRecurringEditProduct( formLayout ) {
            var editProductPropertiesOptions = {
                dialogId:                           'wpfs-edit-recurring-product-properties-dialog',
                dialogContainerSelector:            '#wpfs-dialog-container',
                dialogTitle:                        'Edit plan properties',
                templateSelector:                   '#wpfs-edit-recurring-product-properties-template',
                clickSelector:                      '.js-edit-recurring-product-properties',
                getProductCallback:                 getRecurringProductByCid,
                attachEventsCallback:               attachProductPropertiesEditorEvents,
                saveButtonSelector:                 '.js-dialog-save-recurring-product-properties-dialog',
                formType:                           'editProductProperties',
                formLayout:                         formLayout,
                formFields:                     {
                    'wpfs-plan-setup-fee':                      createInputGroupDescriptor( FORM_FIELD_PLAN_SETUP_FEE ),
                    'wpfs-plan-trial-period-days':              createInputDecoratedDescriptor( FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS ),
                    'wpfs-subscription-cancellation-count':     createInputCheckItemDescriptor( FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT ),
                    'wpfs-billing-cycle-day':                   createInputCheckItemDescriptor( FORM_FIELD_BILLING_CYCLE_DAY ),
                    'wpfs-prorate-until-billing-anchor-day':    createInputCheckItemDescriptor( FORM_FIELD_PRORATE_UNTIL_BILLING_ANCHOR_DAY ),
                },
                propertiesValidatorCallback:        validatePlanPropertiesDialog,
            }
            initProductPropertyEditorDialog( editProductPropertiesOptions );
        }

        function initRecurringAddProduct( formLayout ) {
            var addProductsOptions = {
                dialogId:                           'wpfs-add-recurring-product-dialog',
                clickSelector:                      '.js-add-recurring-product',
                fetchProductsCallback:              fetchRecurringProducts,
                createSelectorProductsCallback:     createRecurringSelectorProducts,
                attachEventsCallback:               attachRecurringSelectorEvents,
                attachPropertiesEventsCallback:     attachPlanPropertiesEvents,
                selectButtonSelector:               '.js-dialog-select-recurring-product',
                addButtonSelector:                  '.js-dialog-add-recurring-product',
                formType:                           'addPlanProperties',
                formLayout:                         formLayout,
                formFields:                     {
                    'wpfs-plan-setup-fee':                      createInputGroupDescriptor( FORM_FIELD_PLAN_SETUP_FEE ),
                    'wpfs-plan-trial-period-days':              createInputDecoratedDescriptor( FORM_FIELD_PLAN_TRIAL_PERIOD_DAYS ),
                    'wpfs-subscription-cancellation-count':     createInputCheckItemDescriptor( FORM_FIELD_SUBSCRIPTION_CANCELLATION_COUNT ),
                    'wpfs-billing-cycle-day':                   createInputCheckItemDescriptor( FORM_FIELD_BILLING_CYCLE_DAY ),
                    'wpfs-prorate-until-billing-anchor-day':    createInputCheckItemDescriptor( FORM_FIELD_PRORATE_UNTIL_BILLING_ANCHOR_DAY ),
                },
                propertiesValidatorCallback:        validatePlanPropertiesDialog,
            }
            initProductSelectorDialog( addProductsOptions );
        }

        function initEditFormRecurringProducts( formLayout ) {
            initRecurringProductTemplates();
            initRecurringProductData( formLayout );
            initRecurringAddProduct( formLayout );
            //initRecurringEditProduct( formLayout );
        }

        function extractRecurringProductPojo( productModel ) {
            return _.pick( productModel.toJSON(), getAllowedRecurringProductFields() );
        }

        function transformRecurringProducts( $form ) {
            var products = [];

            WPFS.recurringProducts.each( function( product ) {
                products.push( extractRecurringProductPojo( product ));
            });

            $form.find('input[name="wpfs-form-recurring-products"]').val( encodeURIComponent( JSON.stringify( products )));
        }

        function initEventHandlersOnInlineSubscriptionFormEdit() {
            initEditFormTabs();
            initEditFormRecurringProducts( FORM_LAYOUT_INLINE );
            initEditFormSubscriptionQuantity();
            initEditFormTaxRates();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-inline-subscription-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformRecurringProducts( $form );
                transformTaxRates( $form );
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                makeAjaxCallWithForm( $form );
            });
        }

        function onInlineSubscriptionFormEdit() {
            return $('div.wpfs-page-edit-inline-subscription-form').length > 0;
        }

        function initInlineSubscriptionFormEdit() {
            if ( onInlineSubscriptionFormEdit() ) {
                initEventHandlersOnInlineSubscriptionFormEdit();
            }
        }

        function initEventHandlersOnCheckoutSubscriptionFormEdit() {
            initEditFormTabs();
            initEditFormRecurringProducts( FORM_LAYOUT_CHECKOUT );
            initEditFormSubscriptionQuantity();
            initEditFormTaxRates();
            initEditFormBillingShippingAddress();
            initEditFormTermsOfService();
            initEditFormCustomFields();
            initEditFormEmailTemplates();
            initFormCssIdCopyToClipboard();

            $('#wpfs-save-checkout-subscription-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);
                transformRecurringProducts( $form )
                transformTaxRates( $form );
                transformCustomFields( $form );
                transformEmailTemplates( $form );
                makeAjaxCallWithForm( $form );
            });
        }

        function onCheckoutSubscriptionFormEdit() {
            return $('div.wpfs-page-edit-checkout-subscription-form').length > 0;
        }

        function initCheckoutSubscriptionFormEdit() {
            if ( onCheckoutSubscriptionFormEdit() ) {
                initEventHandlersOnCheckoutSubscriptionFormEdit();
            }
        }

        WPFS.initFormEdit = function() {
            initInlineSaveCardFormEdit();
            initCheckoutSaveCardFormEdit();
            initInlineDonationFormEdit();
            initCheckoutDonationFormEdit();
            initInlinePaymentFormEdit();
            initCheckoutPaymentFormEdit();
            initInlineSubscriptionFormEdit();
            initCheckoutSubscriptionFormEdit();
        }

        WPFS.initDemoMessage = function () {
            if ($('div.js-demo-message').length > 0) {
                $('a.wpfs-demo-message__link').on('click', function (e) {
                        e.preventDefault();
                        WPFS.Dialog.open('#wpfs-demo-dialog-container',
                            {
                                wide: true
                            }
                        );
                    }
                );
            }
        }

        $(function() {
            WPFS.InputGroup.init();
            WPFS.Selectmenu.init();
            WPFS.Combobox.init();
            WPFS.Tooltip.init();
            WPFS.FormSearch.init();
            WPFS.InlineMessage.init();
            WPFS.HelpDropdown.init();
            WPFS.Controls.init();
            WPFS.Carousel.init();
            WPFS.ShortcodePopover.init();
            WPFS.Webhook.init();
            WPFS.ToPascalCase.init();
            WPFS.Sortable.init();
            WPFS.Datepicker.init();

            WPFS.initBannerMessages();
            WPFS.initManageForms();
            WPFS.initTransactions();
            WPFS.initCreateForm();
            WPFS.initSettingsStripeAccount();
            WPFS.initSettingsMyAccount();
            WPFS.initSettingsSecurity();
            WPFS.initSettingsEmailOptions();
            WPFS.initSettingsEmailTemplates();
            WPFS.initSettingsFormsOptions();
            WPFS.initSettingsFormsAppearance();
            WPFS.initSettingsWordpressDashboard();
            WPFS.initFormEdit();
            WPFS.initDemoMessage();
        });
    });
})(jQuery);