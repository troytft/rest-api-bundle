<?php

namespace RestApiBundle\Model\Mapper;

class Settings
{
    public bool $isPropertiesNullableByDefault = false;
    public bool $isAllowedUndefinedKeysInData = false;
    public bool $isClearMissing = true;
    public bool $stackMappingExceptions = false;
}
