<?php

namespace RestApiBundle\Services\Request;

use Mapper;
use RestApiBundle;

class MapperInitiator
{
    /**
     * @var RestApiBundle\Services\SettingsProvider
     */
    private $settingsProvider;

    /**
     * @var Mapper\Mapper|null
     */
    private $cachedInstance;

    public function __construct(RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    public function getMapper(): Mapper\Mapper
    {
        if (!$this->cachedInstance) {
            $settings = new Mapper\DTO\Settings();
            $settings
                ->setIsPropertiesNullableByDefault($this->settingsProvider->isRequestPropertiesNullableByDefault())
                ->setIsAllowedUndefinedKeysInData($this->settingsProvider->isRequestUndefinedKeysAllowed())
                ->setIsClearMissing($this->settingsProvider->isRequestClearMissingEnabled())
                ->setStackMappingExceptions(true);

            $this->cachedInstance = new Mapper\Mapper($settings);
        }

        return $this->cachedInstance;
    }

    public function addTransformer(Mapper\Transformer\TransformerInterface $transformer): void
    {
        $this->getMapper()->addTransformer($transformer);
    }
}
