# Type Annotations

### RestApiBundle\Annotation\RequestModel\BooleanType
Accepts boolean

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\StringType
Accepts string

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\FloatType
Accepts float

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\IntegerType
Accepts integer

##### Options:
 * **nullable** – is null allowed

### RestApiBundle\Annotation\RequestModel\ArrayType
Accepts array with item type, specified by option type

##### Options:
 * **nullable** – is null allowed
 * **type** – require type annotation
 
All types are available.

### RestApiBundle\Annotation\RequestModel\RequestModelType
Accepts data and map to request model, specified by option `class`.

##### Options:
 * **nullable** – is null allowed
 * **class** – require class name implementing `RestApiBundle\RequestModelInterface`

### RestApiBundle\Annotation\RequestModel\DateType
Accepts string with format, and converts to \DateTime

##### Options:
 * **nullable** – is null allowed
 * **format** – string format for date and time, default: `Y-m-d\TH:i:sP`
 * **forceLocalTimezone** – is force \DateTime to local timezone, default: true

### RestApiBundle\Annotation\RequestModel\DateTimeType
Accepts JSON string with format, and converts to a \DateTime

##### Options:
 * **format** – string format for date and time, default: `Y-m-d`

### RestApiBundle\Annotation\RequestModel\TimestampType
Accepts integer, and converts to a \DateTime

### RestApiBundle\Annotation\RequestModel\EntityType
Accepts scalar, and find an entity by `field`

##### Options:
 * **class** – class name of an entity
 * **field** – field specified for find an entity, default: `id`
 
 ### RestApiBundle\Annotation\RequestModel\ArrayOfEntitiesType
 Accepts array of scalars, and find items by `field`
 
 ##### Options:
  * **class** – class name of an entity
  * **field** – field specified for find an entity, default: `id`
