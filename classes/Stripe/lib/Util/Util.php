<?php

namespace FitSpokane\Stripe\Util;

use FitSpokane\Stripe\StripeObject;

abstract class Util
{
    private static $isMbstringAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     *
     * @param array|mixed $array
     * @return boolean True if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

      // TODO: generally incorrect, but it's correct given Stripe's response
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively converts the PHP Stripe object to an array.
     *
     * @param array $values The PHP Stripe object to convert.
     * @return array
     */
    public static function convertStripeObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }
            if ($v instanceof StripeObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertStripeObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the Stripe API to the corresponding PHP object.
     *
     * @param array $resp The response from the Stripe API.
     * @param array $opts
     * @return StripeObject|array
     */
    public static function convertToStripeObject($resp, $opts)
    {
        $types = array(
            'account' => 'FitSpokane\\Stripe\\Account',
            'alipay_account' => 'FitSpokane\\Stripe\\AlipayAccount',
            'apple_pay_domain' => 'FitSpokane\\Stripe\\ApplePayDomain',
            'bank_account' => 'FitSpokane\\Stripe\\BankAccount',
            'balance_transaction' => 'FitSpokane\\Stripe\\BalanceTransaction',
            'card' => 'FitSpokane\\Stripe\\Card',
            'charge' => 'FitSpokane\\Stripe\\Charge',
            'country_spec' => 'FitSpokane\\Stripe\\CountrySpec',
            'coupon' => 'FitSpokane\\Stripe\\Coupon',
            'customer' => 'FitSpokane\\Stripe\\Customer',
            'dispute' => 'FitSpokane\\Stripe\\Dispute',
            'list' => 'FitSpokane\\Stripe\\Collection',
            'invoice' => 'FitSpokane\\Stripe\\Invoice',
            'invoiceitem' => 'FitSpokane\\Stripe\\InvoiceItem',
            'event' => 'FitSpokane\\Stripe\\Event',
            'file' => 'FitSpokane\\Stripe\\FileUpload',
            'token' => 'FitSpokane\\Stripe\\Token',
            'transfer' => 'FitSpokane\\Stripe\\Transfer',
            'transfer_reversal' => 'FitSpokane\\Stripe\\TransferReversal',
            'order' => 'FitSpokane\\Stripe\\Order',
            'order_return' => 'FitSpokane\\Stripe\\OrderReturn',
            'plan' => 'FitSpokane\\Stripe\\Plan',
            'product' => 'FitSpokane\\Stripe\\Product',
            'recipient' => 'FitSpokane\\Stripe\\Recipient',
            'refund' => 'FitSpokane\\Stripe\\Refund',
            'sku' => 'FitSpokane\\Stripe\\SKU',
            'source' => 'FitSpokane\\Stripe\\Source',
            'subscription' => 'FitSpokane\\Stripe\\Subscription',
            'subscription_item' => 'FitSpokane\\Stripe\\SubscriptionItem',
            'three_d_secure' => 'FitSpokane\\Stripe\\ThreeDSecure',
            'fee_refund' => 'FitSpokane\\Stripe\\ApplicationFeeRefund',
            'bitcoin_receiver' => 'FitSpokane\\Stripe\\BitcoinReceiver',
            'bitcoin_transaction' => 'FitSpokane\\Stripe\\BitcoinTransaction',
        );
        if (self::isList($resp)) {
            $mapped = array();
            foreach ($resp as $i) {
                array_push($mapped, self::convertToStripeObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = 'FitSpokane\\Stripe\\StripeObject';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded. Ask your system " .
                    "administrator to enable the mbstring extension, or write to " .
                    "support@stripe.com if you have any questions.", E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }
}
