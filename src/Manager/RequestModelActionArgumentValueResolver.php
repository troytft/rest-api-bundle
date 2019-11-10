<?php

namespace RestApiBundle\Manager;

use RestApiBundle\Helper\RequestModelHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class RequestModelActionArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RequestModelManager
     */
    private $requestModelManager;

    public function __construct(RequestStack $requestStack, RequestModelManager $requestModelManager)
    {
        $this->requestStack = $requestStack;
        $this->requestModelManager = $requestModelManager;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        return RequestModelHelper::isRequestModel($className);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $className = $argument->getType();

        $requestModel = RequestModelHelper::instantiate($className);
        $this->requestModelManager->handle($requestModel, $this->getRequestData());

        yield $requestModel;
    }

    private function getRequestData(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->getRealMethod() === 'GET' ? $request->query->all() : $request->request->all();
    }
}
