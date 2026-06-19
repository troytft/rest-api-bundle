<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;
use Tests;

class Deprecated implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    /**
     * @deprecated
     */
    public Inner $deprecatedModelProperty;

    /**
     * @deprecated
     */
    public Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString $deprecatedEnumProperty;

    /**
     * @deprecated
     */
    public function getDeprecatedModel(): Inner
    {
        return new Inner();
    }

    /**
     * @deprecated
     */
    public function getDeprecatedEnum(): Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString
    {
        return Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED;
    }
}
