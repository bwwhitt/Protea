<?php

class MM_WPFS_Pricing {
    private $debugLog = false;

    public function __construct() {
    }

    public static function determineTaxIdType($taxId, $taxCountry) {
        $result = null;

        if (!empty($taxId) && !empty($taxCountry)) {
            switch ($taxCountry) {
                case 'AU':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'au_abn';
                    break;

                case 'AT':
                case 'BE':
                case 'BG':
                case 'HR':
                case 'CY':
                case 'CZ':
                case 'DK':
                case 'EE':
                case 'FI':
                case 'FR':
                case 'DE':
                case 'GR':
                case 'HU':
                case 'IE':
                case 'IT':
                case 'LV':
                case 'LT':
                case 'LU':
                case 'MT':
                case 'NL':
                case 'PL':
                case 'PT':
                case 'RO':
                case 'SK':
                case 'SI':
                case 'ES':
                case 'SE':
                    $result = 'eu_vat';
                    break;

                case 'BR':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'br_cpf';
                    break;

                case 'CA':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'ca_bn';
                    break;

                case 'CL':
                    $result = 'cl_tin';
                    break;

                case 'HK':
                    $result = 'hk_br';
                    break;

                case 'IN':
                    $result = 'in_gst';
                    break;

                case 'ID':
                    $result = 'id_npwp';
                    break;

                case 'IL':
                    $result = 'il_vat';
                    break;

                case 'JP':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'jp_cn';
                    break;

                case 'KR':
                    $result = 'kr_brn';
                    break;

                case 'LI':
                    $result = 'li_uid';
                    break;

                case 'MY':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'my_frp';
                    break;

                case 'MX':
                    $result = 'mx_rfc';
                    break;

                case 'NZ':
                    $result = 'nz_gst';
                    break;

                case 'NO':
                    $result = 'no_vat';
                    break;

                case 'RU':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'ru_inn';
                    break;

                case 'SA':
                    $result = 'sa_vat';
                    break;

                case 'SG':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'sg_gst';
                    break;

                case 'ZA':
                    $result = 'za_vat';
                    break;

                case 'CH':
                    $result = 'ch_vat';
                    break;

                case 'TW':
                    $result = 'tw_vat';
                    break;

                case 'TH':
                    $result = 'th_vat';
                    break;

                case 'AE':
                    $result = 'ae_trn';
                    break;

                case 'GB':
                    // todo: improve type discovery using a regex on the tax id
                    $result = 'gb_vat';
                    break;

                case 'US':
                    $result = 'us_ein';
                    break;
            }
        }

        return apply_filters('fullstripe_determine_tax_id_type', $result, $taxId, $taxCountry);
    }

    public static function createFormPriceCalculator( $pricingData ) {
        switch ( $pricingData->formType ) {
            case MM_WPFS::FORM_TYPE_INLINE_PAYMENT:
                return new MM_WPFS_InlinePaymentPriceCalculator( $pricingData );

            case MM_WPFS::FORM_TYPE_CHECKOUT_PAYMENT:
                return new MM_WPFS_CheckoutPaymentPriceCalculator( $pricingData );

            case MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION:
                return new MM_WPFS_InlineSubscriptionPriceCalculator( $pricingData );

            case MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION:
                return new MM_WPFS_CheckoutSubscriptionPriceCalculator( $pricingData );

            default:
                MM_WPFS_Utils::log( __CLASS__ . '.' . __FUNCTION__ . ": unknown form type '{$pricingData->formType}'!" );
                throw new Exception( "Unknown form type '{$pricingData->formType}'!" );
        }
    }

    public static function extractSimplifiedPricingFromInvoiceLineItems($lineItems ) {
        $result         = new \StdClass;
        $amountTotal    = 0;
        $taxTotal       = 0;
        $discountTotal  = 0;

        foreach ( $lineItems as $lineItem ) {
            $amountTotal += $lineItem->amount;

            foreach ( $lineItem->discount_amounts as $discountAmount ) {
                $discountTotal += $discountAmount->amount;
            }

            foreach ( $lineItem->tax_amounts as $taxAmount ) {
                $taxTotal += $taxAmount->amount;
            }
        }

        $result->totalAmount = $amountTotal;
        $result->taxAmount = $taxTotal;
        $result->discountAmount = $discountTotal;

        return $result;
    }

    public static function extractSubscriptionPricingFromInvoiceLineItems( $lineItems ) {
        $result  = new \StdClass;
        $result->product = null;
        $result->setupFee = null;

        foreach ( $lineItems as $lineItem ) {
            $statItem = new \StdClass;
            $statItem->quantity = $lineItem->quantity;
            $statItem->amount = 0;
            $statItem->tax = 0;
            $statItem->discount = 0;

            $statItem->amount += $lineItem->amount;

            foreach ( $lineItem->discount_amounts as $discountAmount ) {
                $statItem->discount += $discountAmount->amount;
            }

            foreach ( $lineItem->tax_amounts as $taxAmount ) {
                $statItem->tax += $taxAmount->amount;
            }

            $metaData = $lineItem->metadata;
            if ( $metaData !== null && $metaData['type'] === 'setupFee' ) {
                $result->setupFee = $statItem;
            } else {
                $result->product = $statItem;
            }
        }

        return $result;
    }

    public static function extractTaxRateIdsStatic( $taxRates ) {
        $taxRateIds = [];

        foreach ( $taxRates as $taxRate ) {
            array_push( $taxRateIds, $taxRate->taxRateId );
        }

        return $taxRateIds;
    }

    /**
     * @param $savedProducts array
     * @return array
     */
    public static function extractPriceIdsFromProductsStatic($savedProducts) {
        $priceIds = array();

        foreach ($savedProducts as $savedProduct) {
            array_push($priceIds, $savedProduct->stripePriceId);
        }

        return $priceIds;
    }
}

abstract class MM_WPFS_PriceCalculator {
    /** @var $stripe MM_WPFS_Stripe */
    protected $stripe;
    /** @var $db MM_WPFS_Database */
    protected $db = null;
    protected $pricingData;

    public function __construct( $pricingData ) {
        $this->pricingData = $pricingData;
        $this->stripe      = new MM_WPFS_Stripe( MM_WPFS::getStripeAuthenticationToken() );
        $this->db          = new MM_WPFS_Database();
    }

    protected abstract function getFormFromDatabase();
    protected abstract function getApplicableTaxRates( $form );
    protected abstract function getProductBuckets( $form );
    protected abstract function prepareProductPricingStripeParams( $pricingData, $products, $taxRateIds );
    protected abstract function extractPaymentDetailsFromInvoiceLineItems( $lineItems, $taxRates, $products );

    /**
     * @param $form
     * @param $pricingData
     * @return array|mixed
     */
    public static function getApplicableInlineTaxRatesStatic($form, $pricingData ) {
        $taxRates = json_decode($form->vatRates);

        $applicableTaxRates = [];
        if ($form->vatRateType === MM_WPFS::FIELD_VALUE_TAX_RATE_FIXED) {
            $applicableTaxRates = $taxRates;
        } else if ($form->vatRateType === MM_WPFS::FIELD_VALUE_TAX_RATE_DYNAMIC) {
            $applicableTaxRates = MM_WPFS_PriceCalculator::filterApplicableTaxRatesStatic(
                $pricingData->country,
                $pricingData->state,
                $taxRates,
                $pricingData->taxId);
        }

        return $applicableTaxRates;
    }

    /**
     * @param $country
     * @param $state
     * @param $taxRates
     * @param $taxId
     *
     * @return array
     */
    public static function filterApplicableTaxRatesStatic($country, $state, $taxRates, $taxId ) {
        $applicableTaxRates = [];
        if ( ! empty( $country ) ) {
            if ( $country === MM_WPFS::COUNTRY_CODE_UNITED_STATES && ! empty( $state ) ) {
                foreach ( $taxRates as $taxRate ) {
                    if ( $taxRate->country === $country &&
                        $taxRate->state === $state ) {
                        array_push( $applicableTaxRates, $taxRate );
                    }
                }
            } else if ( $country !== MM_WPFS::COUNTRY_CODE_UNITED_STATES ) {
                foreach ( $taxRates as $taxRate ) {
                    if ( $taxRate->country === $country ) {
                        array_push( $applicableTaxRates, $taxRate );
                    }
                }
            }
        }

        $params = [
            'country'       => $country,
            'state'         => $state,
            'taxId'         => $taxId
        ];
        $filteredTaxRates = apply_filters('fullstripe_determine_tax_rates', $applicableTaxRates, $params );

        return $filteredTaxRates;
    }

    /**
     * @param $country
     * @param $state
     * @param $taxRates
     * @param $taxId
     *
     * @return array
     */
    protected function selectApplicableTaxRates( $country, $state, $taxRates, $taxId ) {
        return self::filterApplicableTaxRatesStatic( $country, $state, $taxRates, $taxId );
    }

    protected function extractCurrencyFromProducts( $products ) {
        $result = MM_WPFS::CURRENCY_USD;

        if ( count( $products ) > 0 ) {
            $result = ($products[0])->currency;
        }

        return $result;
    }

    protected function getProductPricesFromStripe( $form, $taxRates ) {
        $productPricing = [];

        $productBuckets = $this->getProductBuckets( $form );

        foreach ( $productBuckets as $productBucket ) {
            $invoice = null;
            try {
                $invoiceParams = $this->prepareProductPricingStripeParams(
                    $this->pricingData,
                    $productBucket,
                    MM_WPFS_Pricing::extractTaxRateIdsStatic( $taxRates )
                );

                $invoice = $this->stripe->getUpcomingInvoice( $invoiceParams );
            } catch ( Exception $ex ) {
                MM_WPFS_Utils::logException( $ex );
            }

            if ( $invoice !== null ) {
                $productPricing = array_merge( $productPricing, $this->extractPaymentDetailsFromInvoiceLineItems( $invoice->lines->data, $taxRates, $productBucket ));
            }
        }

        return $productPricing;
    }

    public function getProductPrices() {
        $form     = $this->getFormFromDatabase();
        $taxRates = $this->getApplicableTaxRates( $form );

        return $this->getProductPricesFromStripe( $form, $taxRates );
    }
}

abstract class MM_WPFS_PaymentPriceCalculator extends MM_WPFS_PriceCalculator {
    protected function getProductBuckets( $form ) {
        $decoratedProducts = MM_WPFS_Utils::decodeJsonArray( $form->decoratedProducts );

        if ( $this->pricingData->couponPercentOff ) {
            return [ $decoratedProducts ];
        }

        $result = [];
        foreach ( $decoratedProducts as $decoratedProduct ) {
            array_push( $result, [ $decoratedProduct ] );
        }

        return $result;
    }

    protected function prepareProductPricingStripeParams( $pricingData, $products, $taxRateIds ) {
        $invoiceParams = [];

        $address = [
            'country'   => $pricingData->country
        ];
        if ( ! empty( $pricingData->state ) ) {
            $address['state'] = $pricingData->state;
        }
        $invoiceParams['customer_details'] = [
            'address'   => $address
        ];

        if ( !empty( $pricingData->couponCode )  ) {
            $invoiceParams['discounts'] = [
                [
                    'coupon'    => $pricingData->couponCode
                ]
            ];
        }

        $invoiceItems = [];
        foreach ( $products as $product ) {
            $invoiceItem = [
                'price'         => $product->stripePriceId,
                'tax_rates'     => $taxRateIds
            ];

            array_push( $invoiceItems, $invoiceItem );
        }
        if ( ! is_null( $pricingData->customAmount )) {
            $invoiceItem = [
                'amount'        => $pricingData->customAmount,
                'currency'      => $this->extractCurrencyFromProducts( $products ),
                'description'   => __('Other amount', 'wp-full-stripe'),
                'metadata'      => [
                    'type'  => 'customAmount'
                ],
                'tax_rates'     => $taxRateIds
            ];

            array_push( $invoiceItems, $invoiceItem );
        }

        $invoiceParams['invoice_items'] = $invoiceItems;

        return $invoiceParams;
    }

    protected function extractPaymentDetailsFromInvoiceLineItems($lineItems, $taxRates, $products ) {
        $productPricing = [];

        foreach ( $lineItems as $lineItem ) {
            $dislayItems = [];

            $priceId    = $lineItem->price['id'];
            $metaData   = $lineItem->metadata;
            if ( $metaData !== null &&
                $metaData['type'] === 'customAmount' ) {
                $priceId = 'customAmount';
            }
            $currency       = $lineItem->currency;
            $displayName    = $lineItem->description;
            $amount         = $lineItem->amount;

            $displayItem = new \StdClass;
            $displayItem->type          = 'product';
            $displayItem->id            = $priceId;
            $displayItem->displayName   = $displayName;
            $displayItem->currency      = $currency;
            $displayItem->amount        = $amount;
            array_push( $dislayItems, $displayItem );

            $discountTotal = 0;
            foreach ( $lineItem->discount_amounts as $discountAmount ) {
                $discountTotal += $discountAmount->amount;
            }
            if ( $discountTotal > 0 ) {
                $displayItem = new \StdClass;
                $displayItem->type          = 'discount';
                $displayItem->id            = null;
                $displayItem->displayName   = __('Discount', 'wp-full-stripe');
                $displayItem->currency      = $currency;
                $displayItem->amount        = -$discountTotal;
                array_push( $dislayItems, $displayItem );
            }

            $taxAmountLookup = [];
            foreach ( $lineItem->tax_amounts as $taxAmount ) {
                $taxAmountLookup[ $taxAmount->tax_rate ] = $taxAmount->amount;
            }
            foreach ( $taxRates as $taxRate ) {
                $taxAmount = array_key_exists( $taxRate->taxRateId, $taxAmountLookup ) ? $taxAmountLookup[ $taxRate->taxRateId ] : 0;

                if ( $taxAmount > 0 ) {
                    $displayItem = new \StdClass;
                    $displayItem->type          = 'tax';
                    $displayItem->id            = $taxRate->taxRateId;
                    $displayItem->displayName   = $taxRate->displayName;
                    $displayItem->currency      = $currency;
                    $displayItem->amount        = $taxAmount;
                    $displayItem->percentage    = $taxRate->percentage;
                    array_push( $dislayItems, $displayItem );
                }
            }

            $productPricing[ $priceId ] = $dislayItems;
        }

        return $productPricing;
    }

    /**
     * @param $productPricing
     */
    protected function printPaymentDetails( $productPricing ) {
        foreach ($productPricing as $productId => $lineItems) {
            $totalAmount = 0;
            $currency = '';

            foreach ($lineItems as $lineItem) {
                switch ($lineItem->type) {
                    case 'product':
                        $amountLabel = MM_WPFS_Currencies::formatAndEscapeByAdmin($lineItem->currency, $lineItem->amount, false, true);
                        MM_WPFS_Utils::log( sprintf("%s    %s", $lineItem->displayName, $amountLabel ));

                        $totalAmount += $lineItem->amount;
                        $currency = $lineItem->currency;
                        break;

                    case 'discount':
                        $amountLabel = MM_WPFS_Currencies::formatAndEscapeByAdmin($lineItem->currency, $lineItem->amount, false, true);
                        MM_WPFS_Utils::log( sprintf("%s    %s", $lineItem->displayName, $amountLabel ));

                        $totalAmount += $lineItem->amount;
                        break;

                    case 'tax':
                        $amountLabel = MM_WPFS_Currencies::formatAndEscapeByAdmin($lineItem->currency, $lineItem->amount, false, true);
                        MM_WPFS_Utils::log( sprintf("%s    %s", $lineItem->displayName, $amountLabel ));

                        $totalAmount += $lineItem->amount;
                        break;
                }
            }

            $amountLabel = MM_WPFS_Currencies::formatAndEscapeByAdmin($currency, $totalAmount, false, true);
            MM_WPFS_Utils::log( sprintf("%s    %s", 'Total', $amountLabel ));
        }
    }
}

trait MM_WPFS_InlineTaxCalculator {

    protected function getApplicableTaxRates( $form ) {
        return MM_WPFS_PriceCalculator::getApplicableInlineTaxRatesStatic($form, $this->pricingData);
    }
}

trait MM_WPFS_CheckoutTaxCalculator {
    protected function getApplicableTaxRates( $form ) {
        $taxRates = json_decode( $form->vatRates );

        $applicableTaxRates = [];
        if ($form->vatRateType === MM_WPFS::FIELD_VALUE_TAX_RATE_FIXED) {
            $applicableTaxRates = $taxRates;
        } else if ($form->vatRateType === MM_WPFS::FIELD_VALUE_TAX_RATE_DYNAMIC) {
            $applicableTaxRates = [];
        }

        return $applicableTaxRates;
    }
}

class MM_WPFS_InlinePaymentPriceCalculator extends MM_WPFS_PaymentPriceCalculator {
    use MM_WPFS_InlineTaxCalculator;

    protected function getFormFromDatabase() {
        return $this->db->getInlinePaymentFormByName( $this->pricingData->formId );
    }
}

class MM_WPFS_CheckoutPaymentPriceCalculator extends MM_WPFS_PaymentPriceCalculator {
    use MM_WPFS_CheckoutTaxCalculator;

    protected function getFormFromDatabase() {
        return $this->db->getCheckoutPaymentFormByName( $this->pricingData->formId );
    }
}

abstract class MM_WPFS_SubscriptionPriceCalculator extends MM_WPFS_PriceCalculator {
    protected function getProductBuckets( $form ) {
        $result = [];
        $decoratedPlans = MM_WPFS_Utils::decodeJsonArray( $form->decoratedPlans );

        if ( $this->pricingData->couponPercentOff ) {
            $hashedBuckets = [];
            foreach ( $decoratedPlans as $decoratedPlan ) {
                $key = $decoratedPlan->currency . $decoratedPlan->interval . $decoratedPlan->intervalCount;

                if ( array_key_exists( $key, $hashedBuckets ) ) {
                    $hashedBucket = $hashedBuckets[ $key ];
                    array_push( $hashedBucket, $decoratedPlan );
                    $hashedBuckets[ $key ] = $hashedBucket;
                } else {
                    $hashedBuckets[ $key ] = [ $decoratedPlan ];
                }
            }

            foreach ( $hashedBuckets as $hashedBucket ) {
                array_push( $result, $hashedBucket );
            }
        } else {
            foreach ( $decoratedPlans as $decoratedPlan ) {
                array_push( $result, [ $decoratedPlan] );
            }
        }

        return $result;
    }

    /**
     * @param $pricingData
     * @param $products
     * @param $taxRateIds
     * @return array
     */
    protected static function prepareProductPricingStripeParamsStatic( $pricingData, $products, $taxRateIds ): array {
        $invoiceParams = [];

        $address = [
            'country' => $pricingData->country
        ];
        if (!empty($pricingData->state)) {
            $address['state'] = $pricingData->state;
        }
        $invoiceParams['customer_details'] = [
            'address' => $address
        ];

        if (!empty($pricingData->couponCode)) {
            $invoiceParams['coupon'] = $pricingData->couponCode;
        }

        $invoiceItems = [];
        $subscriptionItems = [];
        foreach ($products as $product) {
            $subscriptionItem = [
                'price' => $product->stripePriceId,
                'quantity' => $pricingData->quantity,
                'tax_rates' => $taxRateIds
            ];
            array_push($subscriptionItems, $subscriptionItem);

            if ($product->setupFee > 0) {
                $invoiceItem = [
                    'amount' => $product->setupFee * $pricingData->quantity,
                    'currency' => $product->currency,
                    'description' => __('Setup fee', 'wp-full-stripe'),
                    'metadata' => [
                        'type' => 'setupFee',
                        'priceId' => $product->stripePriceId
                    ],
                    'tax_rates' => $taxRateIds
                ];
                array_push($invoiceItems, $invoiceItem);
            }
        }

        $invoiceParams['invoice_items'] = $invoiceItems;
        $invoiceParams['subscription_items'] = $subscriptionItems;

        return $invoiceParams;
    }

    protected function prepareProductPricingStripeParams( $pricingData, $products, $taxRateIds ) {
        return self::prepareProductPricingStripeParamsStatic( $pricingData, $products, $taxRateIds );
    }

    /**
     * @param $lineItems
     * @return mixed
     */
    protected function extractCurrencyFromLineItems( $lineItems ) {
        return $lineItems[0]->currency;
    }

    /**
     * @param $label
     * @param $quantity
     * @return string
     */
    protected function createLineItemDisplayName( $label, $quantity ) {
        return $quantity === 1 ? $label : "${quantity}x ${label}";
    }

    /**
     * @param $lineItems
     * @param $taxRates
     * @param $products
     * @return array
     */
    protected function extractPaymentDetailsFromInvoiceLineItems( $lineItems, $taxRates, $products ) {
        $productPricing = [];

        $productLookup = [];
        foreach ( $products as $product ) {
            $productLookup[ $product->stripePriceId ] = $product;
        }

        $priceGroupLookup = [];
        foreach ( $lineItems as $lineItem ) {
            $priceId    = $lineItem->price['id'];
            $isSetupFee = false;

            $metaData   = $lineItem->metadata;
            if ( $metaData !== null && $metaData['type'] === 'setupFee' ) {
                $isSetupFee = true;
                $priceId    = $metaData['priceId'];
            }

            if ( ! array_key_exists( $priceId, $priceGroupLookup ) ) {
                $priceGroupLookup[ $priceId ] = [];
            }

            $priceItems = $priceGroupLookup[ $priceId ];
            if ( count( $priceItems ) == 0 || count( $priceItems ) > 1 ) {
                array_push( $priceItems, $lineItem );
            } else if ( count( $priceItems ) === 1 ) {
                if ( $isSetupFee ) {
                    array_unshift( $priceItems, $lineItem );
                } else {
                    array_push( $priceItems, $lineItem );
                }
            }
            $priceGroupLookup[ $priceId ] = $priceItems;
        }

        foreach( $priceGroupLookup as $priceId => $lineItems ) {
            $dislayItems = [];
            $discountTotal = 0;
            $taxAmountLookup = [];

            foreach( $lineItems as $lineItem ) {
                $priceId        = $lineItem->price['id'];
                $type           = 'product';
                $displayName    = '';

                $metaData   = $lineItem->metadata;
                if ( $metaData !== null && $metaData['type'] === 'setupFee' ) {
                    $priceId = $metaData[$priceId];
                    $type    = 'setupFee';
                    $displayName = $this->createLineItemDisplayName( __('Setup fee', 'wp-full-stripe'), $lineItem->quantity );
                } else {
                    $displayName = $this->createLineItemDisplayName( $productLookup[$priceId]->name, $lineItem->quantity );
                }

                $displayItem = new \StdClass;
                $displayItem->type          = $type;
                $displayItem->id            = $priceId;
                $displayItem->displayName   = $displayName;
                $displayItem->currency      = $lineItem->currency;
                $displayItem->amount        = $lineItem->amount;
                array_push( $dislayItems, $displayItem );

                foreach ( $lineItem->discount_amounts as $discountAmount ) {
                    $discountTotal += $discountAmount->amount;
                }

                foreach ( $lineItem->tax_amounts as $taxAmount ) {
                    if ( array_key_exists( $taxAmount->tax_rate, $taxAmountLookup ) ) {
                        $taxAmountLookup[ $taxAmount->tax_rate ] += $taxAmount->amount;
                    } else {
                        $taxAmountLookup[ $taxAmount->tax_rate ] = $taxAmount->amount;
                    }
                }
            }

            if ( $discountTotal > 0 ) {
                $displayItem = new \StdClass;
                $displayItem->type          = 'discount';
                $displayItem->id            = null;
                $displayItem->displayName   = __('Discount', 'wp-full-stripe');
                $displayItem->currency      = $this->extractCurrencyFromLineItems( $lineItems );
                $displayItem->amount        = -$discountTotal;
                array_push( $dislayItems, $displayItem );
            }

            foreach ( $taxRates as $taxRate ) {
                $taxAmount = array_key_exists( $taxRate->taxRateId, $taxAmountLookup ) ? $taxAmountLookup[ $taxRate->taxRateId ] : 0;

                if ( $taxAmount > 0 ) {
                    $displayItem = new \StdClass;
                    $displayItem->type          = 'tax';
                    $displayItem->id            = $taxRate->taxRateId;
                    $displayItem->displayName   = $taxRate->displayName;
                    $displayItem->currency      = $this->extractCurrencyFromLineItems( $lineItems );;
                    $displayItem->amount        = $taxAmount;
                    $displayItem->percentage    = $taxRate->percentage;
                    array_push( $dislayItems, $displayItem );
                }
            }

            $productPricing[ $priceId ] = $dislayItems;
        }

        return $productPricing;
    }
}

class MM_WPFS_InlineSubscriptionPriceCalculator extends MM_WPFS_SubscriptionPriceCalculator {
    use MM_WPFS_InlineTaxCalculator;

    protected function getFormFromDatabase() {
        return $this->db->getInlineSubscriptionFormByName( $this->pricingData->formId );
    }
}

class MM_WPFS_CheckoutSubscriptionPriceCalculator extends MM_WPFS_SubscriptionPriceCalculator {
    use MM_WPFS_CheckoutTaxCalculator;

    protected function getFormFromDatabase() {
        return $this->db->getCheckoutSubscriptionFormByName( $this->pricingData->formId );
    }
}

