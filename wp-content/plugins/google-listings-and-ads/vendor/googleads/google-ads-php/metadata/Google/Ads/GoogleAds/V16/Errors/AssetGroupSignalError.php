<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v16/errors/asset_group_signal_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V16\Errors;

class AssetGroupSignalError
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
>google/ads/googleads/v16/errors/asset_group_signal_error.protogoogle.ads.googleads.v16.errors"�
AssetGroupSignalErrorEnum"�
AssetGroupSignalError
UNSPECIFIED 
UNKNOWN
TOO_MANY_WORDS!
SEARCH_THEME_POLICY_VIOLATION&
"AUDIENCE_WITH_WRONG_ASSET_GROUP_IDB�
#com.google.ads.googleads.v16.errorsBAssetGroupSignalErrorProtoPZEgoogle.golang.org/genproto/googleapis/ads/googleads/v16/errors;errors�GAA�Google.Ads.GoogleAds.V16.Errors�Google\\Ads\\GoogleAds\\V16\\Errors�#Google::Ads::GoogleAds::V16::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

