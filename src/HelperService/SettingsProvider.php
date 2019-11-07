<?php

namespace RestApiBundle\HelperService;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsProvider
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getMapperIsNullableByDefault(): bool
    {
        return $this->parameterBag->get('rest_api')['mapper']['nullable_by_default'];
    }

    public function getMapperIsAllowUndefinedKeys(): bool
    {
        return $this->parameterBag->get('rest_api')['mapper']['allow_undefined_keys'];
    }

    public function getMapperIsClearMissingKeys(): bool
    {
        return $this->parameterBag->get('rest_api')['mapper']['clear_missing'];
    }

    public function getIsHandleRequestModelMappingException(): bool
    {
        return $this->parameterBag->get('rest_api')['handle_request_model_mapping_exception'];
    }
}
