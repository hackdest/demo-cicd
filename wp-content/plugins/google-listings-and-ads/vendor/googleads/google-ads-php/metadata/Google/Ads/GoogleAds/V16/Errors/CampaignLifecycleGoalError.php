<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v16/errors/campaign_lifecycle_goal_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V16\Errors;

class CampaignLifecycleGoalError
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
�
Cgoogle/ads/googleads/v16/errors/campaign_lifecycle_goal_error.protogoogle.ads.googleads.v16.errors"�
CampaignLifecycleGoalErrorEnum"�
CampaignLifecycleGoalError
UNSPECIFIED 
UNKNOWN
CAMPAIGN_MISSING
INVALID_CAMPAIGN2
.CUSTOMER_ACQUISITION_INVALID_OPTIMIZATION_MODE!
INCOMPATIBLE_BIDDING_STRATEGY
MISSING_PURCHASE_GOAL4
0CUSTOMER_ACQUISITION_INVALID_HIGH_LIFETIME_VALUE2
.CUSTOMER_ACQUISITION_UNSUPPORTED_CAMPAIGN_TYPE&
"CUSTOMER_ACQUISITION_INVALID_VALUE	&
"CUSTOMER_ACQUISITION_VALUE_MISSING
=
9CUSTOMER_ACQUISITION_MISSING_EXISTING_CUSTOMER_DEFINITION?
;CUSTOMER_ACQUISITION_MISSING_HIGH_VALUE_CUSTOMER_DEFINITIONB�
#com.google.ads.googleads.v16.errorsBCampaignLifecycleGoalErrorProtoPZEgoogle.golang.org/genproto/googleapis/ads/googleads/v16/errors;errors�GAA�Google.Ads.GoogleAds.V16.Errors�Google\\Ads\\GoogleAds\\V16\\Errors�#Google::Ads::GoogleAds::V16::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

