<?php

namespace RestApiBundle\Services\Request;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class ActionArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RestApiBundle\Services\Request\RequestHandler
     */
    private $requestHandler;

    public function __construct(RequestStack $requestStack, RestApiBundle\Services\Request\RequestHandler $requestHandler)
    {
        $this->requestStack = $requestStack;
        $this->requestHandler = $requestHandler;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        return RestApiBundle\Services\Request\RequestModelRegistry::isRequestModel($className);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $className = $argument->getType();

        $requestModel = RestApiBundle\Services\Request\RequestModelRegistry::instantiate($className);
        $this->requestHandler->handle($requestModel, $this->getRequestData());

        yield $requestModel;
    }

    private function getRequestData(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->getRealMethod() === 'GET' ? $request->query->all() : $request->request->all();
    }
}
