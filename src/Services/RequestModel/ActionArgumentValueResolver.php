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
        private RestApiBundle\Services\Mapper\Mapper $mapper
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RestApiBundle\Helper\InterfaceChecker::isRequestModelInterface($argument->getType());
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestData = $request->getRealMethod() === 'GET' ? $request->query->all() : $request->request->all();

        $requestModel = $this->instantiate($argument->getType());

        $this->mapper->map($requestModel, $requestData);

        yield $requestModel;
    }

    private function instantiate(string $class): RestApiBundle\Mapping\RequestModel\RequestModelInterface
    {
        $requestModel = RestApiBundle\Helper\ReflectionClassStore::get($class)->newInstance();
        if (!$requestModel instanceof RestApiBundle\Mapping\RequestModel\RequestModelInterface) {
            throw new \InvalidArgumentException();
        }

        return $requestModel;
    }
}
