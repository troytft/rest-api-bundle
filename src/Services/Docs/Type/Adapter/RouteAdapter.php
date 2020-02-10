<?php

namespace RestApiBundle\Services\Docs\Type\Adapter;

use RestApiBundle;
use Symfony\Component\Routing\Route;
use function array_diff;
use function array_keys;
use function preg_match_all;

class RouteAdapter
{
    /**
     * @param Route $route
     *
     * @return string[]
     * @throws RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException
     */
    public function getValidParameterNamesFromPath(Route $route): array
    {
        $result = $this->parseRoutePathParameterNames($route->getPath());
        if (array_diff(array_keys($route->getRequirements()), $result)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException();
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function parseRoutePathParameterNames(string $path): array
    {
        $matches = null;
        if (!preg_match_all('/{([^}]+)}/', $path, $matches)) {
            return [];
        }

        return $matches[1];
    }
}
