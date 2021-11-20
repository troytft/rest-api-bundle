<?php

namespace RestApiBundle\Services\RequestModel;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class ActionArgumentValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private RestApiBundle\Services\RequestModel\RequestModelHandler $requestHandler)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($argument->getType());
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $requestModel = $this->instantiate($argument->getType());
        $request = $this->requestStack->getCurrentRequest();
        $requestData = $request->getRealMethod() === 'GET' ? $request->query->all() : $request->request->all();

        $this->requestHandler->handle($requestModel, $requestData);

        yield $requestModel;
    }

    private function instantiate(string $class): RestApiBundle\Mapping\RequestModel\RequestModelInterface
    {
        if (!RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($class)) {
            throw new \InvalidArgumentException();
        }

        $requestModel = RestApiBundle\Helper\ReflectionClassStore::get($class)->newInstance();
        if (!$requestModel instanceof RestApiBundle\Mapping\RequestModel\RequestModelInterface) {
            throw new \InvalidArgumentException();
        }

        return $requestModel;
    }
}
