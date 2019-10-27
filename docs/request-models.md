# Request Models

Модели для запросов создаются с помощью классов, класс должен имплементировать интерфейс `RestApiBundle\RequestModelInteface`.

Set model properties type with type annotations.

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

А далее указать модель в качестве аргумента экшена в контроллере. Маппинг данных из запроса и валидация модели произойдут автоматически, а внутри функции экшена будет доступна модель с заполненными данными.

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
В случае ошибок маппинга или валидации – клиенту будет возвращен ответ со статусом 400 и ответ в формате json.
Ответ содержит ключ `properties` содержит объект, ключ объекта это path, а значение ключа это массив из строк с текстами ошибок.

Path это путь к полю, в котором произошла ошибка.
Path может быть многоуровневный, уровни объединяются точкой.

Если ошибка произошла в моделе, то путь будет название поля, если ошибка общая и у неё нет поля, то путь будет `*`.
Если ошибка произошла в коллекции, то путь будет числовым индексом.

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
