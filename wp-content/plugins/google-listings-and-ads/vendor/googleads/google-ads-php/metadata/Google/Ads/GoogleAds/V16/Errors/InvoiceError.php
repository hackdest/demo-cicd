<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v16/errors/invoice_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V16\Errors;

class InvoiceError
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
�
3google/ads/googleads/v16/errors/invoice_error.protogoogle.ads.googleads.v16.errors"�
InvoiceErrorEnum"�
InvoiceError
UNSPECIFIED 
UNKNOWN
YEAR_MONTH_TOO_OLD
NOT_INVOICED_CUSTOMER
BILLING_SETUP_NOT_APPROVED*
&BILLING_SETUP_NOT_ON_MONTHLY_INVOICING
NON_SERVING_CUSTOMERB�
#com.google.ads.googleads.v16.errorsBInvoiceErrorProtoPZEgoogle.golang.org/genproto/googleapis/ads/googleads/v16/errors;errors�GAA�Google.Ads.GoogleAds.V16.Errors�Google\\Ads\\GoogleAds\\V16\\Errors�#Google::Ads::GoogleAds::V16::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

