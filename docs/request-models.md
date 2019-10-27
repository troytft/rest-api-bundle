# Request Models

### Использование
Модели для запросов создаются с помощью классов, класс должен имплементировать интерфейс `RestApiBundle\RequestModelInteface`.

Чтобы поле стало доступно в запросе необходимо добавить аннотацию с одним из типов.

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

### Types
У всех типов есть параметр `nullable`, если параметр включен, то клиент в запросе сможет передать `null` в качестве значения для этого поля.

##### RestApiBundle\Annotation\RequestModel\BooleanType
Accepts json boolean

##### RestApiBundle\Annotation\RequestModel\StringType
Accepts json string

##### RestApiBundle\Annotation\RequestModel\FloatType
Accepts json float

##### RestApiBundle\Annotation\RequestModel\IntegerType
Accepts json integer

##### RestApiBundle\Annotation\RequestModel\Collection
Тип для коллекций, тип эллемента коллекции задачает с помощью параметра `type`.

Поддерживаются все доступные типы.

##### RestApiBundle\Annotation\RequestModel\Model
Тип для моделей, класс модели задается ввиду classname с помощью параметра `class`.
Для того, чтобы валидация работала и на встраиваемую модель, необходимо добавить к полю аннотацию `@Assert\Valid`.

Ограничений по уровню вложенности нет.

##### RestApiBundle\Annotation\RequestModel\Date
Accepts json string with format, and converts to \DateTime

Options:
* **format** – string format for date and time, default: `Y-m-d\TH:i:sP`
* **forceLocalTimezone** – is force \DateTime to local timezone, default: true

##### RestApiBundle\Annotation\RequestModel\DateTime
Accepts json string with format, and converts to \DateTime

Options:
* **format** – string format for date and time, default: `Y-m-d`

##### RestApiBundle\Annotation\RequestModel\Timestamp
Accepts json integer, converts to \DateTime

### Error Response
