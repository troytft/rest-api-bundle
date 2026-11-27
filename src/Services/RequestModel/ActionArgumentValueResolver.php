<?php

declare(strict_types=1);

namespace RestApiBundle\Services\RequestModel;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ActionArgumentValueResolver implements ValueResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private RestApiBundle\Services\Mapper\Mapper $mapper,
    ) {
    }

    /**
     * @return iterable<RestApiBundle\Mapping\RequestModel\RequestModelInterface>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!RestApiBundle\Helper\ReflectionHelper::isRequestModel($argument->getType())) {
            return [];
        }

        $request = $this->requestStack->getCurrentRequest();
        $requestData = \array_merge($request->files->all(), $request->getRealMethod() === 'GET' ? $request->query->all() : $request->request->all());

        $requestModel = $this->instantiate($argument->getType());

        $this->mapper->map($requestModel, $requestData);

        return [$requestModel];
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
