# Request Models

Модели для запросов создаются с помощью классов, класс должен имплементировать интерфейс `RestApiBundle\RequestModelInteface`.

Чтобы поле стало доступно в запросе необходимо добавить аннотацию с одним из типов. Так же с помощью параметра `nullable` можно запретить/разрешить `null`.

Пример модели:

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

А далее указать модель в качестве аргумента экшена в контроллере. Маппинг данных из запроса и валидация модели произойдут автоматически, а внутри функции экшена будет доступна модель с заполненными данными.

Пример контроллера:

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