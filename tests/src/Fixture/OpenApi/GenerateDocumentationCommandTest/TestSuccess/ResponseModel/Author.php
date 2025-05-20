<?php
declare(strict_types=1);

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel;

use RestApiBundle;

class Author implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getId(): int
    {
        return 1;
    }

    /**
     * @deprecated
     */
    public function getGenresCount(): int
    {
        return 0;
    }
}
