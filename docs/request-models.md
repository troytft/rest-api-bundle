# Request Models

Модели для запросов создаются с помощью классов, класс должен имплементировать интерфейс `RestApiBundle\RequestModelInteface`.

Чтобы поле стало доступно в запросе необходимо добавить аннотацию с одним из типов. Так же с помощью параметра `nullable` можно запретить/разрешить `null`.

Пример модели:

```php
<?php

namespace App\AcmeBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class InnerModel implements RequestModelInterface
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
