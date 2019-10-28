# Request Models

Create model with interface `RestApiBundle\RequestModelInteface`, and add type annotation to model properties.

Full list of type annotations can be found at [`request-model-type-annotations.md`](request-model-type-annotations.md).

##### Example:

```php
<?php

namespace App\AcmeBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class CreateMovie implements RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\StringType()
     */
    private $name;

    /**
     * @var string[]|null
     *
     * @Mapper\CollectionType(type=@Mapper\StringType(), nullable=true)
     */
    private $genres;
    
    ... getters and setters
}
```

Add model to the action as argument. Mapper will map json request date and validate model.

##### Example:

```php
<?php

namespace App\AcmeBundle\Controller;

use App;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/movies")
 */
class MovieController extends BaseController
{
    /**
     * @Route(methods="POST")
     */
    public function createAction(App\AcmeBundle\RequestModel\CreateMovie $requestModel)
    {
        var_dump($requestModel->getName(), $requestModel->getGenrese());
    }
}
```

### Errors
If error happens, that client will receive response with 400 status code and json body.

##### Example:

```http request
HTTP/1.1 400
Content-Type: application/json

{
    "properties": {
        "*": ["Error message"], 
        "genres.3": ["Error message"], 
        "name": ["Error message 1", "Error message 2"],
        "revenue.0.countryName": ["Error message"]
    }
}
```
