# Types
У всех типов есть параметр `nullable`, если параметр включен, то клиент в запросе сможет передать `null` в качестве значения для этого поля.

### RestApiBundle\Annotation\RequestModel\BooleanType
Accepts json boolean

### RestApiBundle\Annotation\RequestModel\StringType
Accepts json string

### RestApiBundle\Annotation\RequestModel\FloatType
Accepts json float

##### RestApiBundle\Annotation\RequestModel\IntegerType
Accepts json integer

### RestApiBundle\Annotation\RequestModel\Collection
Тип для коллекций, тип эллемента коллекции задачает с помощью параметра `type`.

Поддерживаются все доступные типы.

### RestApiBundle\Annotation\RequestModel\Model
Тип для моделей, класс модели задается ввиду classname с помощью параметра `class`.
Для того, чтобы валидация работала и на встраиваемую модель, необходимо добавить к полю аннотацию `@Assert\Valid`.

Ограничений по уровню вложенности нет.

### RestApiBundle\Annotation\RequestModel\Date
Accepts json string with format, and converts to \DateTime

Options:
 * **format** – string format for date and time, default: `Y-m-d\TH:i:sP`
 * **forceLocalTimezone** – is force \DateTime to local timezone, default: true

### RestApiBundle\Annotation\RequestModel\DateTime
Accepts json string with format, and converts to \DateTime

Options:
 * **format** – string format for date and time, default: `Y-m-d`

### RestApiBundle\Annotation\RequestModel\Timestamp
Accepts json integer, converts to \DateTime
