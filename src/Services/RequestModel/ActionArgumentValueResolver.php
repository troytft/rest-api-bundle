<?php

declare(strict_types=1);

namespace RestApiBundle\Services\RequestModel;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ActionArgumentValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private RestApiBundle\Services\Mapper\Mapper $mapper,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RestApiBundle\Helper\ReflectionHelper::isRequestModel($argument->getType());
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestData = \array_merge($request->files->all(), $request->getRealMethod() === 'GET' ? $request->query->all() : $request->request->all());

        $requestModel = $this->instantiate($argument->getType());

        $this->mapper->map($requestModel, $requestData);

        yield $requestModel;
    }

    private function instantiate(string $class): RestApiBundle\Mapping\RequestModel\RequestModelInterface
    {
        $requestModel = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class)->newInstance();
        if (!$requestModel instanceof RestApiBundle\Mapping\RequestModel\RequestModelInterface) {
            throw new \InvalidArgumentException();
        }

        return $requestModel;
    }
}
