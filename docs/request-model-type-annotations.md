# Type Annotations

### RestApiBundle\RequestModel\Annotation\BooleanType
Accepts boolean

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\RequestModel\Annotation\StringType
Accepts string

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\RequestModel\Annotation\FloatType
Accepts float

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\RequestModel\Annotation\IntegerType
Accepts integer

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\RequestModel\Annotation\Collection
Accepts collection with item type, specified by option type

##### Options:
 * **nullable** – is null allowed
 * **type** – require type annotation
 
All types are available.

### RestApiBundle\RequestModel\Annotation\Model
Accepts object with data and map to model, specified by option `class`.

##### Options:
 * **nullable** – is null allowed
 * **class** – require class name implementing `RestApiBundle\RequestModelInterface`

If you want validate inner level model, add symfony validation annotation `@Assert\Valid`.

Nested level is not limited.

### RestApiBundle\RequestModel\Annotation\Date
Accepts string with format, and converts to \DateTime

##### Options:
 * **nullable** – is null allowed
 * **format** – string format for date and time, default: `Y-m-d\TH:i:sP`
 * **forceLocalTimezone** – is force \DateTime to local timezone, default: true

### RestApiBundle\RequestModel\Annotation\DateTime
Accepts JSON string with format, and converts to a \DateTime

##### Options:
 * **format** – string format for date and time, default: `Y-m-d`

### RestApiBundle\RequestModel\Annotation\Timestamp
Accepts integer, and converts to a \DateTime
