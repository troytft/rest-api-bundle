# Types
У всех типов есть параметр `nullable`, если параметр включен, то клиент в запросе сможет передать `null` в качестве значения для этого поля.

### RestApiBundle\Annotation\RequestModel\BooleanType
Accepts boolean

##### Options
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\StringType
Accepts string

##### Options
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\FloatType
Accepts float

##### Options
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\IntegerType
Accepts integer

##### Options
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\Collection
Accepts collection with item type, specified by option type

##### Options
 * **nullable** – is null allowed
 * **type** – require type annotation
 
All types are available.


### RestApiBundle\Annotation\RequestModel\Model
Accepts object with data and map to model, specified by option `class`.

##### Options
 * **nullable** – is null allowed
 * **class** – require class name implementing `RestApiBundle\RequestModelInterface`

If you want validate inner level model, add symfony validation annotation `@Assert\Valid`.

Nested level is not limited.

### RestApiBundle\Annotation\RequestModel\Date
Accepts string with format, and converts to \DateTime

##### Options
 * **nullable** – is null allowed
 * **format** – string format for date and time, default: `Y-m-d\TH:i:sP`
 * **forceLocalTimezone** – is force \DateTime to local timezone, default: true

### RestApiBundle\Annotation\RequestModel\DateTime
Accepts json string with format, and converts to \DateTime

Options:
 * **format** – string format for date and time, default: `Y-m-d`

### RestApiBundle\Annotation\RequestModel\Timestamp
Accepts integer, and converts to \DateTime
