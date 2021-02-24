<?php

namespace RestApiBundle\SettingsProvider;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class BaseSettingsProvider
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
}
