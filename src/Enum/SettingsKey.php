<?php

namespace RestApiBundle\Enum;

class SettingsKey
{
    public const IS_REQUEST_PROPERTIES_NULLABLE_BY_DEFAULT = 'is_request_properties_nullable_by_default';
    public const IS_REQUEST_UNDEFINED_KEYS_ALLOWED = 'is_request_undefined_keys_allowed';
    public const IS_REQUEST_CLEAR_MISSING_ENABLED = 'is_request_clear_missing_enabled';
    public const IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED = 'is_request_validation_exception_handler_enabled';
    public const IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE = 'is_force_request_datetime_to_local_timezone';
    public const DEFAULT_REQUEST_DATETIME_FORMAT = 'default_request_datetime_format';
    public const DEFAULT_REQUEST_DATE_FORMAT = 'default_request_date_format';
    public const IS_RESPONSE_HANDLER_ENABLED = 'is_response_handler_enabled';
    public const RESPONSE_JSON_ENCODE_OPTIONS = 'response_json_encode_options';
}
