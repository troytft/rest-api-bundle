# План оптимизации тестов

## Текущее состояние

### Проблемы
1. **Все тесты бутят Symfony kernel** - даже простые unit-тесты трансформеров
2. **БД пересоздается на каждый тест** - overhead для тестов, которым БД не нужна
3. **Избыточный try-catch boilerplate** - 5-7 строк вместо 1-2
4. **Нет переиспользования тестовых данных** - одинаковые кейсы дублируются
5. **Смешаны unit и integration тесты** - нельзя запустить быстрые unit-тесты отдельно
6. **Глубокая вложенность фикстур** - сложно найти нужную фикстуру

### Метрики
- Всего тестов: ~15 файлов
- Всего строк: ~800
- Фикстур: 43 файла
- Время выполнения: ~5-10 сек (может быть 1-2 сек для unit)

---

## Предложения по улучшению

### 1. Разделение Unit и Integration тестов

**Обоснование:**
- **Скорость**: Unit-тесты без kernel выполняются в 10-50x быстрее (0.001s vs 0.1s)
- **CI оптимизация**: Можно запускать только unit при каждом коммите, integration - реже
- **Изоляция**: Unit-тесты проверяют логику в изоляции, integration - интеграцию компонентов
- **Отладка**: Проще найти источник проблемы (логика vs конфигурация)
- **TDD**: Быстрая обратная связь при разработке

**Текущий подход:**
```php
// tests/cases/RequestModel/ScalarTransformersTest.php
class ScalarTransformersTest extends Tests\BaseTestCase // <- бутит kernel + БД
{
    public function testIntegerTransformer()
    {
        // Создаем объект напрямую, DI не нужен
        $transformer = new RestApiBundle\Services\Mapper\Transformer\IntegerTransformer();
        $this->assertSame(10, $transformer->transform(10));

        // ПРОБЛЕМА: Kernel бутится + БД создается, хотя не используется
        // Время: ~0.1s вместо 0.001s
    }
}
```

**Предлагаемый подход:**
```php
// tests/Unit/Services/Mapper/Transformer/IntegerTransformerTest.php
namespace Tests\Unit\Services\Mapper\Transformer;

use PHPUnit\Framework\TestCase; // <- БЫСТРО: без kernel
use RestApiBundle\Services\Mapper\Transformer\IntegerTransformer;

class IntegerTransformerTest extends TestCase
{
    private IntegerTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new IntegerTransformer();
    }

    public function testTransformInteger(): void
    {
        $this->assertSame(10, $this->transformer->transform(10));
        // Время: 0.001s
    }
}

// tests/Integration/Services/Mapper/MapperIntegrationTest.php
namespace Tests\Integration\Services\Mapper;

use Tests\Integration\IntegrationTestCase; // <- kernel только здесь

class MapperIntegrationTest extends IntegrationTestCase
{
    public function testMapWithDatabaseEntity(): void
    {
        // Тут нужен kernel + БД для Doctrine entities
        $mapper = $this->getContainer()->get(Mapper::class);
        // ...
    }
}
```

**Структура директорий:**
```
tests/
├── Unit/                          # Быстрые тесты без dependencies
│   ├── UnitTestCase.php          # extends PHPUnit\Framework\TestCase
│   └── Services/
│       ├── Mapper/
│       │   └── Transformer/
│       │       ├── IntegerTransformerTest.php
│       │       ├── BooleanTransformerTest.php
│       │       └── StringTransformerTest.php
│       └── OpenApi/
│           └── SchemaSerializerTest.php
│
├── Integration/                   # Медленные тесты с kernel + БД
│   ├── IntegrationTestCase.php   # extends BaseTestCase
│   ├── Mapper/
│   │   ├── MapperTest.php
│   │   └── ValidationTest.php
│   └── OpenApi/
│       └── GenerateDocumentationCommandTest.php
│
└── Fixture/                       # Общие фикстуры
    ├── Entity/
    ├── Enum/
    └── TestApp/
```

**Результат:**
- Unit-тесты: 0.5-1s (было 5-7s)
- Integration тесты: 3-5s
- Можно запускать: `vendor/bin/phpunit tests/Unit` - только быстрые

---

### 2. Data Providers вместо повторяющихся assertion'ов

**Обоснование:**
- **DRY**: Один метод вместо множества похожих assertion'ов
- **Читаемость**: Тестовые кейсы видны как данные, не размазаны по коду
- **Расширяемость**: Добавить новый кейс = одна строка в массиве
- **Отчетность**: PHPUnit показывает какой именно dataset failed
- **Меньше кода**: 30-50 строк → 15-20 строк

**Текущий подход:**
```php
public function testIntegerTransformer()
{
    $transformer = new IntegerTransformer();

    // ПРОБЛЕМА: 3 похожих assertion'а
    $this->assertSame(10, $transformer->transform(10));
    $this->assertSame(10, $transformer->transform(10.0));
    $this->assertSame(10, $transformer->transform('10'));

    // ПРОБЛЕМА: Если один падает - не узнаем про остальные
    // ПРОБЛЕМА: Добавить кейс = копировать строку и менять значения
}

public function testFloatTransformer()
{
    $transformer = new FloatTransformer();

    // ПРОБЛЕМА: Та же структура повторяется
    $this->assertSame(10.0, $transformer->transform(10.0));
    $this->assertSame(10.0, $transformer->transform(10));
    $this->assertSame(10.0, $transformer->transform('10'));
    $this->assertSame(10.1, $transformer->transform('10.1'));
}
```

**Предлагаемый подход:**
```php
/**
 * @dataProvider validIntegerProvider
 */
public function testTransformValidInteger($input, int $expected): void
{
    $result = $this->transformer->transform($input);
    $this->assertSame($expected, $result);
}

public function validIntegerProvider(): array
{
    return [
        'integer value' => [10, 10],
        'float without fraction' => [10.0, 10],
        'numeric string' => ['10', 10],
        'zero' => [0, 0],
        'negative' => [-5, -5],
        'negative string' => ['-5', -5],
    ];
}

/**
 * @dataProvider invalidIntegerProvider
 */
public function testTransformInvalidInteger($input): void
{
    $this->expectException(IntegerRequiredException::class);
    $this->transformer->transform($input);
}

public function invalidIntegerProvider(): array
{
    return [
        'float with fraction' => [10.1],
        'string float' => ['10.1'],
        'boolean' => [true],
        'null' => [null],
        'empty string' => [''],
        'non-numeric string' => ['abc'],
        'array' => [[]],
        'object' => [new \stdClass()],
    ];
}
```

**Преимущества:**
1. **Именованные кейсы**: `'float with fraction' => [10.1]` - сразу понятно что тестируется
2. **Независимость**: Каждый dataset = отдельный тест, один failed не останавливает другие
3. **PHPUnit output**:
   ```
   IntegerTransformerTest::testTransformValidInteger with data set "integer value" ✓
   IntegerTransformerTest::testTransformValidInteger with data set "float without fraction" ✓
   IntegerTransformerTest::testTransformValidInteger with data set "numeric string" ✓
   ```
4. **Легко добавить edge cases**: Просто новая строка в массиве

---

### 3. expectException вместо try-catch блоков

**Обоснование:**
- **Краткость**: 1-2 строки вместо 5-7
- **Читаемость**: Видно сразу "ожидается exception"
- **Безопасность**: Не забудешь `$this->fail()` (иначе тест всегда green)
- **Лучшие ошибки**: PHPUnit показывает "expected X but got Y"
- **Меньше уровней вложенности**: Нет try-catch блока

**Текущий подход:**
```php
// ПРОБЛЕМА: 7 строк для одной проверки
try {
    $transformer->transform(10.1);
    $this->fail(); // ОПАСНО: Легко забыть эту строку!
} catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
    // Тест проходит, но ничего не проверяется
}

// ПРОБЛЕМА: Повторяется 5-10 раз в одном тесте
try {
    $transformer->transform('10.1');
    $this->fail();
} catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
}

try {
    $transformer->transform(true);
    $this->fail();
} catch (RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException $exception) {
}
```

**Предлагаемый подход:**
```php
// РЕШЕНИЕ: 2 строки
public function testTransformFloatWithFractionThrowsException(): void
{
    $this->expectException(IntegerRequiredException::class);
    $this->transformer->transform(10.1);
}

// Можно проверить и message
public function testTransformNullThrowsExceptionWithMessage(): void
{
    $this->expectException(IntegerRequiredException::class);
    $this->expectExceptionMessage('Integer value required');
    $this->transformer->transform(null);
}

// С data provider (лучший вариант):
/**
 * @dataProvider invalidValuesProvider
 */
public function testTransformInvalidValue($input): void
{
    $this->expectException(IntegerRequiredException::class);
    $this->transformer->transform($input);
}
```

**Сравнение:**
```php
// БЫЛО: 35 строк
try {
    $transformer->transform(10.1);
    $this->fail();
} catch (IntegerRequiredException $exception) {}

try {
    $transformer->transform('10.1');
    $this->fail();
} catch (IntegerRequiredException $exception) {}

try {
    $transformer->transform(true);
    $this->fail();
} catch (IntegerRequiredException $exception) {}

try {
    $transformer->transform(null);
    $this->fail();
} catch (IntegerRequiredException $exception) {}

try {
    $transformer->transform('');
    $this->fail();
} catch (IntegerRequiredException $exception) {}

// СТАЛО: 14 строк (с именами кейсов!)
/** @dataProvider invalidValuesProvider */
public function testTransformInvalidValue($input): void
{
    $this->expectException(IntegerRequiredException::class);
    $this->transformer->transform($input);
}

public function invalidValuesProvider(): array
{
    return [
        'float with fraction' => [10.1],
        'string float' => ['10.1'],
        'boolean' => [true],
        'null' => [null],
        'empty string' => [''],
    ];
}
```

---

### 4. Использование setUp/tearDown для инициализации

**Обоснование:**
- **DRY**: Общая инициализация в одном месте
- **Стандарт PHPUnit**: Явное место для setup/cleanup
- **Изоляция тестов**: Каждый тест получает свежие объекты
- **Читаемость**: В тесте только логика теста, не setup
- **Удобство**: Можно легко добавить моки/stubs в одном месте

**Текущий подход:**
```php
class ScalarTransformersTest extends BaseTestCase
{
    public function testBooleanTransformer()
    {
        // ПРОБЛЕМА: Создаем в каждом тесте
        $transformer = new BooleanTransformer();
        $this->assertSame(true, $transformer->transform(true));
    }

    public function testIntegerTransformer()
    {
        // ПРОБЛЕМА: Снова создаем
        $transformer = new IntegerTransformer();
        $this->assertSame(10, $transformer->transform(10));
    }

    public function testFloatTransformer()
    {
        // ПРОБЛЕМА: И снова
        $transformer = new FloatTransformer();
        $this->assertSame(10.0, $transformer->transform(10.0));
    }
}
```

**Предлагаемый подход:**
```php
class IntegerTransformerTest extends TestCase
{
    private IntegerTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new IntegerTransformer();
    }

    public function testTransformInteger(): void
    {
        // Чистая логика теста
        $this->assertSame(10, $this->transformer->transform(10));
    }

    public function testTransformFloat(): void
    {
        $this->assertSame(10, $this->transformer->transform(10.0));
    }
}

// Для тестов с моками:
class MapperTest extends TestCase
{
    private Mapper $mapper;
    private TransformerInterface $mockTransformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockTransformer = $this->createMock(TransformerInterface::class);
        $this->mapper = new Mapper(
            transformers: [$this->mockTransformer],
            validator: $this->createMock(ValidatorInterface::class)
        );
    }

    public function testMap(): void
    {
        $this->mockTransformer
            ->expects($this->once())
            ->method('transform')
            ->willReturn(10);

        // Тест использует уже настроенный mapper
        $result = $this->mapper->map(...);
    }
}
```

**Для интеграционных тестов:**
```php
class ValidationIntegrationTest extends IntegrationTestCase
{
    private Mapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = $this->getContainer()->get(Mapper::class);
    }

    public function testNestedValidation(): void
    {
        $model = new TestNestedValidationModel();

        $exception = null;
        try {
            $this->mapper->map($model, ['childModel' => []]);
        } catch (MappingException $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception);
        $this->assertSame([
            'childModel.field' => ['This value is not valid.'],
        ], $exception->getProperties());
    }
}
```

---

### 5. Тестовые Traits для переиспользования логики

**Обоснование:**
- **Переиспользование**: Общие проверки в одном месте
- **Консистентность**: Одинаковые assertion'ы для схожих классов
- **Меньше кода**: Не дублируем проверки
- **Легче поддерживать**: Изменение в trait = изменение везде

**Текущая проблема:**
```php
// В каждом TransformerTest повторяется:
class IntegerTransformerTest
{
    public function testTransformNull()
    {
        $this->expectException(IntegerRequiredException::class);
        $this->transformer->transform(null);
    }
}

class FloatTransformerTest
{
    public function testTransformNull()
    {
        $this->expectException(FloatRequiredException::class);
        $this->transformer->transform(null);
    }
}

class StringTransformerTest
{
    public function testTransformNull()
    {
        $this->expectException(StringRequiredException::class);
        $this->transformer->transform(null);
    }
}
```

**Предлагаемый подход:**
```php
// tests/Unit/Traits/TransformerTestTrait.php
trait TransformerTestTrait
{
    abstract protected function getTransformer(): TransformerInterface;
    abstract protected function getExpectedException(): string;

    public function testTransformNullThrowsException(): void
    {
        $this->expectException($this->getExpectedException());
        $this->getTransformer()->transform(null);
    }

    /**
     * @dataProvider validTransformProvider
     */
    public function testTransformValid($input, $expected): void
    {
        $result = $this->getTransformer()->transform($input);
        $this->assertSame($expected, $result);
    }

    abstract public function validTransformProvider(): array;
}

// Использование:
class IntegerTransformerTest extends TestCase
{
    use TransformerTestTrait;

    private IntegerTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new IntegerTransformer();
    }

    protected function getTransformer(): TransformerInterface
    {
        return $this->transformer;
    }

    protected function getExpectedException(): string
    {
        return IntegerRequiredException::class;
    }

    public function validTransformProvider(): array
    {
        return [
            [10, 10],
            [10.0, 10],
            ['10', 10],
        ];
    }

    // Только специфичные для Integer тесты
    public function testTransformFloatWithFraction(): void
    {
        $this->expectException(IntegerRequiredException::class);
        $this->transformer->transform(10.5);
    }
}
```

**Другие полезные traits:**
```php
// tests/Unit/Traits/OpenApiSchemaAssertionsTrait.php
trait OpenApiSchemaAssertionsTrait
{
    protected function assertValidOpenApiSchema(array $schema): void
    {
        $this->assertArrayHasKey('openapi', $schema);
        $this->assertArrayHasKey('info', $schema);
        $this->assertArrayHasKey('paths', $schema);
    }

    protected function assertSchemaHasEndpoint(array $schema, string $path, string $method): void
    {
        $this->assertArrayHasKey($path, $schema['paths']);
        $this->assertArrayHasKey(strtolower($method), $schema['paths'][$path]);
    }
}

// Использование:
class SchemaGeneratorTest extends IntegrationTestCase
{
    use OpenApiSchemaAssertionsTrait;

    public function testGenerateSchema(): void
    {
        $schema = $this->generator->generate([...]);

        $this->assertValidOpenApiSchema($schema);
        $this->assertSchemaHasEndpoint($schema, '/books', 'GET');
    }
}
```

---

### 6. Группировка тестов с @group annotations

**Обоснование:**
- **Гибкость CI**: Разные группы на разных этапах CI
- **Быстрая обратная связь**: Запускать только нужные тесты
- **Изоляция медленных тестов**: Отделить database, external API, etc
- **Проще отладка**: Запустить только тесты конкретной фичи

**Текущая проблема:**
```php
// Невозможно запустить только тесты трансформеров
// Или только тесты OpenAPI
// Или только быстрые тесты
```

**Предлагаемый подход:**
```php
/**
 * @group transformer
 * @group unit
 * @group fast
 */
class IntegerTransformerTest extends TestCase
{
    // ...
}

/**
 * @group mapper
 * @group integration
 * @group database
 */
class MapperIntegrationTest extends IntegrationTestCase
{
    // ...
}

/**
 * @group openapi
 * @group integration
 */
class SchemaGeneratorTest extends IntegrationTestCase
{
    // ...
}

/**
 * @group command
 * @group integration
 * @group slow
 */
class GenerateDocumentationCommandTest extends IntegrationTestCase
{
    // ...
}
```

**Команды запуска:**
```bash
# Только быстрые unit-тесты (для TDD)
vendor/bin/phpunit --group=fast

# Только тесты трансформеров
vendor/bin/phpunit --group=transformer

# Все кроме медленных (для pre-commit hook)
vendor/bin/phpunit --exclude-group=slow

# Только тесты с БД
vendor/bin/phpunit --group=database

# Только OpenAPI тесты
vendor/bin/phpunit --group=openapi

# Unit тесты (без integration)
vendor/bin/phpunit --exclude-group=integration
```

**CI конфигурация:**
```yaml
# .github/workflows/tests.yml
jobs:
  fast-tests:
    runs-on: ubuntu-latest
    steps:
      - run: vendor/bin/phpunit --group=fast
      # Выполняется за 1-2 сек

  unit-tests:
    runs-on: ubuntu-latest
    steps:
      - run: vendor/bin/phpunit --exclude-group=integration
      # Все unit без kernel

  integration-tests:
    runs-on: ubuntu-latest
    needs: unit-tests  # Запускается только если unit прошли
    steps:
      - run: vendor/bin/phpunit --group=integration
      # Медленные тесты с БД
```

---

### 7. Фабрики для тестовых данных

**Обоснование:**
- **Переиспользование**: Создание сложных объектов в одном месте
- **Гибкость**: Легко кастомизировать нужные поля
- **Читаемость**: Явное указание только важных для теста данных
- **Консистентность**: Одинаковые дефолтные значения везде
- **Меньше setup кода**: Не нужно вручную создавать все зависимости

**Текущая проблема:**
```php
public function testMap(): void
{
    // ПРОБЛЕМА: Много boilerplate для создания тестового объекта
    $entity = new Book();
    $entity->setId(1);
    $entity->setTitle('Test Book');
    $entity->setAuthor('Test Author');
    $entity->setIsbn('123-456-789');
    $entity->setPublishedAt(new \DateTime('2024-01-01'));

    // И так в каждом тесте...
}

public function testAnotherScenario(): void
{
    // ПРОБЛЕМА: Дублируем тот же код
    $entity = new Book();
    $entity->setId(2);
    $entity->setTitle('Another Book');
    $entity->setAuthor('Another Author');
    $entity->setIsbn('987-654-321');
    $entity->setPublishedAt(new \DateTime('2024-02-01'));
}
```

**Предлагаемый подход:**
```php
// tests/Fixture/Factory/BookFactory.php
class BookFactory
{
    public static function create(array $overrides = []): Book
    {
        $defaults = [
            'id' => 1,
            'title' => 'Default Title',
            'author' => 'Default Author',
            'isbn' => '123-456-789',
            'publishedAt' => new \DateTime('2024-01-01'),
        ];

        $data = array_merge($defaults, $overrides);

        $book = new Book();
        $book->setId($data['id']);
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setIsbn($data['isbn']);
        $book->setPublishedAt($data['publishedAt']);

        return $book;
    }

    public static function createMany(int $count, array $overrides = []): array
    {
        return array_map(
            fn(int $i) => self::create(array_merge($overrides, ['id' => $i])),
            range(1, $count)
        );
    }
}

// Использование:
public function testMap(): void
{
    // Чисто и читаемо - используем дефолты
    $book = BookFactory::create();

    // Или переопределяем только нужное
    $book = BookFactory::create(['title' => 'Custom Title']);

    $result = $this->mapper->map($model, ['book' => $book->getId()]);
}

public function testMapWithMultipleBooks(): void
{
    // Легко создать много объектов
    $books = BookFactory::createMany(5);

    // Или с кастомными данными
    $books = BookFactory::createMany(3, ['author' => 'Same Author']);
}
```

**Фабрики для request/response моделей:**
```php
// tests/Fixture/Factory/RequestModelFactory.php
class RequestModelFactory
{
    public static function createBookRequest(array $data = []): CreateBookRequest
    {
        $defaults = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '123-456-789',
        ];

        $merged = array_merge($defaults, $data);

        $request = new CreateBookRequest();
        $request->title = $merged['title'];
        $request->author = $merged['author'];
        $request->isbn = $merged['isbn'];

        return $request;
    }
}

// Использование:
public function testValidation(): void
{
    // Создаем валидный request
    $request = RequestModelFactory::createBookRequest();

    // Проверяем что валидация проходит
    $this->assertEmpty($this->validator->validate($request));

    // Создаем невалидный - переопределяем только нужное поле
    $invalidRequest = RequestModelFactory::createBookRequest(['title' => '']);

    $violations = $this->validator->validate($invalidRequest);
    $this->assertCount(1, $violations);
}
```

---

### 8. Упрощение структуры фикстур

**Обоснование:**
- **Проще найти**: Меньше уровней вложенности
- **Переиспользование**: Общие фикстуры в одном месте
- **Меньше дублирования**: Не создаем BookController в 5 местах
- **Логичнее**: Группировка по типу (Entity, Enum, Controller), не по тесту

**Текущая структура:**
```
tests/src/Fixture/
├── OpenApi/
│   ├── RequestModelResolverTest/
│   │   ├── PhpEnumTestModel.php
│   │   ├── PolyfillEnumTestModel.php
│   │   └── TestUploadedFileModel.php
│   ├── SchemaGeneratorTest/
│   │   └── DefaultController.php
│   └── GenerateDocumentationCommandTest/
│       ├── TestSuccess/
│       │   ├── BookController.php          # Дублирование
│       │   ├── WriterController.php
│       │   ├── RequestModel/
│       │   ├── ResponseModel/
│       │   └── Resources/
│       └── TestInvalidDefinition/
│           └── DefaultController.php
└── Mapper/
    ├── ValidationTest/
    ├── EntityTransformerTest/
    └── EnumTransformerTest/

# ПРОБЛЕМА: Чтобы найти BookController нужно знать какой тест его использует
# ПРОБЛЕМА: BookController может быть в нескольких местах
# ПРОБЛЕМА: Глубокая вложенность: Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess/RequestModel
```

**Предлагаемая структура:**
```
tests/Fixture/
├── Controller/
│   ├── BookController.php           # Один раз, используется везде
│   ├── WriterController.php
│   └── DefaultController.php
│
├── Entity/
│   ├── Book.php
│   ├── Author.php
│   └── Publisher.php
│
├── Enum/
│   ├── BookStatus.php
│   └── UserRole.php
│
├── RequestModel/
│   ├── CreateBookRequest.php
│   ├── UpdateBookRequest.php
│   └── SearchBooksRequest.php
│
├── ResponseModel/
│   ├── BookResponse.php
│   ├── BooksListResponse.php
│   └── ErrorResponse.php
│
├── OpenApi/
│   ├── valid-schema.yaml
│   └── invalid-schema.yaml
│
├── Factory/                         # НОВОЕ: Фабрики для тестовых данных
│   ├── BookFactory.php
│   ├── RequestModelFactory.php
│   └── ResponseModelFactory.php
│
└── TestApp/                         # Тестовое приложение
    ├── TestAppBundle.php
    ├── Repository/
    └── Resources/

# Преимущества:
# 1. BookController в Fixture/Controller - очевидно где искать
# 2. Все Entity в одном месте
# 3. Нет дублирования
# 4. Меньше вложенности: Fixture/RequestModel вместо Fixture/OpenApi/Test/TestSuccess/RequestModel
```

**Использование:**
```php
// Было:
use Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\BookController;
use Tests\Fixture\OpenApi\SchemaGeneratorTest\DefaultController;

// Стало:
use Tests\Fixture\Controller\BookController;
use Tests\Fixture\Controller\DefaultController;

// Проще импорты:
use Tests\Fixture\Entity\Book;
use Tests\Fixture\Factory\BookFactory;
use Tests\Fixture\RequestModel\CreateBookRequest;
```

---

### 9. Custom Assertions для domain-специфичных проверок

**Обоснование:**
- **Выразительность**: `assertValidBook()` понятнее чем 10 assertion'ов
- **Переиспользование**: Сложные проверки в одном месте
- **Лучшие ошибки**: Кастомные сообщения, показывающие что именно не так
- **Инкапсуляция**: Логика проверки отделена от тестов
- **DRY**: Не дублируем одинаковые наборы assertion'ов

**Текущая проблема:**
```php
public function testBookResponse(): void
{
    $response = $this->handler->handle($model);

    // ПРОБЛЕМА: Повторяется в каждом тесте
    $this->assertIsArray($response);
    $this->assertArrayHasKey('id', $response);
    $this->assertArrayHasKey('title', $response);
    $this->assertArrayHasKey('author', $response);
    $this->assertArrayHasKey('isbn', $response);
    $this->assertIsInt($response['id']);
    $this->assertIsString($response['title']);
    $this->assertIsString($response['author']);
    $this->assertMatchesRegularExpression('/^\d{3}-\d{3}-\d{3}$/', $response['isbn']);
}

public function testAnotherBookResponse(): void
{
    $response = $this->handler->handle($anotherModel);

    // ПРОБЛЕМА: Дублируем те же проверки
    $this->assertIsArray($response);
    $this->assertArrayHasKey('id', $response);
    // ... еще 7 строк
}
```

**Предлагаемый подход:**
```php
// tests/Unit/Assertion/ResponseAssertions.php
trait ResponseAssertions
{
    protected function assertValidBookResponse(array $response, array $expected = []): void
    {
        // Структура
        $this->assertArrayHasKey('id', $response, 'Book response must have id');
        $this->assertArrayHasKey('title', $response, 'Book response must have title');
        $this->assertArrayHasKey('author', $response, 'Book response must have author');
        $this->assertArrayHasKey('isbn', $response, 'Book response must have isbn');

        // Типы
        $this->assertIsInt($response['id'], 'Book id must be integer');
        $this->assertIsString($response['title'], 'Book title must be string');
        $this->assertIsString($response['author'], 'Book author must be string');

        // Формат
        $this->assertMatchesRegularExpression(
            '/^\d{3}-\d{3}-\d{3}$/',
            $response['isbn'],
            'ISBN must match format XXX-XXX-XXX'
        );

        // Значения (если указаны)
        foreach ($expected as $key => $value) {
            $this->assertSame(
                $value,
                $response[$key],
                "Book $key should be $value, got {$response[$key]}"
            );
        }
    }

    protected function assertValidBooksListResponse(array $response): void
    {
        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertIsArray($response['items']);
        $this->assertIsInt($response['total']);

        foreach ($response['items'] as $i => $book) {
            $this->assertValidBookResponse($book, [], "Item $i is not valid book");
        }
    }

    protected function assertValidationError(
        MappingException $exception,
        string $field,
        string $messageContains
    ): void {
        $properties = $exception->getProperties();

        $this->assertArrayHasKey(
            $field,
            $properties,
            "Expected validation error for field '$field', got: " . json_encode($properties)
        );

        $messages = $properties[$field];
        $found = false;
        foreach ($messages as $message) {
            if (str_contains($message, $messageContains)) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(
            $found,
            "Expected error message containing '$messageContains' for field '$field', got: " .
            implode(', ', $messages)
        );
    }
}

// Использование:
class BookResponseTest extends TestCase
{
    use ResponseAssertions;

    public function testBookResponse(): void
    {
        $response = $this->handler->handle($model);

        // ЧИСТО: Одна строка вместо 10
        $this->assertValidBookResponse($response);
    }

    public function testBookResponseWithExpectedValues(): void
    {
        $response = $this->handler->handle($model);

        // Проверяем структуру + конкретные значения
        $this->assertValidBookResponse($response, [
            'title' => 'Expected Title',
            'author' => 'Expected Author',
        ]);
    }

    public function testBooksListResponse(): void
    {
        $response = $this->handler->handle($model);

        // Проверяет структуру списка + каждую книгу
        $this->assertValidBooksListResponse($response);
    }

    public function testValidationError(): void
    {
        try {
            $this->mapper->map($model, ['title' => '']);
            $this->fail('Expected MappingException');
        } catch (MappingException $e) {
            // Выразительная проверка ошибки
            $this->assertValidationError($e, 'title', 'cannot be blank');
        }
    }
}
```

**OpenAPI assertions:**
```php
// tests/Integration/Assertion/OpenApiAssertions.php
trait OpenApiAssertions
{
    protected function assertValidOpenApiSchema(array $schema): void
    {
        // Обязательные поля OpenAPI 3.0
        $this->assertArrayHasKey('openapi', $schema);
        $this->assertSame('3.0.0', $schema['openapi']);
        $this->assertArrayHasKey('info', $schema);
        $this->assertArrayHasKey('paths', $schema);

        // Info section
        $this->assertArrayHasKey('title', $schema['info']);
        $this->assertArrayHasKey('version', $schema['info']);
    }

    protected function assertSchemaHasEndpoint(
        array $schema,
        string $path,
        string $method,
        int $expectedStatusCode = 200
    ): void {
        $this->assertArrayHasKey($path, $schema['paths'], "Path $path not found in schema");

        $method = strtolower($method);
        $this->assertArrayHasKey(
            $method,
            $schema['paths'][$path],
            "Method $method not found for path $path"
        );

        $endpoint = $schema['paths'][$path][$method];
        $this->assertArrayHasKey('responses', $endpoint);
        $this->assertArrayHasKey(
            (string)$expectedStatusCode,
            $endpoint['responses'],
            "Expected response code $expectedStatusCode not found"
        );
    }

    protected function assertSchemaHasRequestBody(
        array $schema,
        string $path,
        string $method,
        string $schemaRef
    ): void {
        $endpoint = $schema['paths'][$path][strtolower($method)];

        $this->assertArrayHasKey('requestBody', $endpoint);
        $this->assertArrayHasKey('content', $endpoint['requestBody']);
        $this->assertArrayHasKey('application/json', $endpoint['requestBody']['content']);

        $content = $endpoint['requestBody']['content']['application/json'];
        $this->assertArrayHasKey('schema', $content);
        $this->assertSame($schemaRef, $content['schema']['$ref']);
    }
}

// Использование:
class SchemaGeneratorTest extends IntegrationTestCase
{
    use OpenApiAssertions;

    public function testGenerateSchema(): void
    {
        $schema = $this->generator->generate([...]);

        $this->assertValidOpenApiSchema($schema);
        $this->assertSchemaHasEndpoint($schema, '/books', 'GET');
        $this->assertSchemaHasEndpoint($schema, '/books/{id}', 'GET', 200);
        $this->assertSchemaHasEndpoint($schema, '/books', 'POST', 201);
        $this->assertSchemaHasRequestBody(
            $schema,
            '/books',
            'POST',
            '#/components/schemas/CreateBookRequest'
        );
    }
}
```

---

### 10. Оптимизация BaseTestCase - переиспользование kernel

**Обоснование:**
- **Скорость**: Kernel boot занимает 50-200ms, переиспользование дает 10-100x ускорение
- **Меньше памяти**: Один kernel вместо N
- **Doctrine**: Можно использовать transactions для быстрого rollback
- **Лучше для CI**: Меньше нагрузка, быстрее pipeline

**Текущая проблема:**
```php
// tests/src/BaseTestCase.php
public function getKernel(): KernelInterface
{
    $this->bootKernel();
    $this->setupDatabase(); // <- СОЗДАЕТ БД КАЖДЫЙ РАЗ!

    return static::$kernel;
}

private function setupDatabase(): void
{
    $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
    $schemaTool = new SchemaTool($entityManager);
    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

    if (!empty($metadata)) {
        $schemaTool->createSchema($metadata); // <- МЕДЛЕННО
    }
}

// ПРОБЛЕМА:
// Test 1: boot kernel (100ms) + create DB (50ms) = 150ms
// Test 2: boot kernel (100ms) + create DB (50ms) = 150ms
// Test 3: boot kernel (100ms) + create DB (50ms) = 150ms
// Total: 450ms OVERHEAD для setup
```

**Предлагаемый подход:**
```php
// tests/Integration/IntegrationTestCase.php
abstract class IntegrationTestCase extends KernelTestCase
{
    use MatchesSnapshots;

    private static bool $databaseInitialized = false;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(RestApiBundle::class);
        $kernel->addTestBundle(TestAppBundle::class);
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestConfig(__DIR__ . '/../Fixture/TestApp/Resources/config/config.yaml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // ОПТИМИЗАЦИЯ: Kernel бутится один раз
        self::bootKernel();

        // ОПТИМИЗАЦИЯ: БД создается один раз
        if (!self::$databaseInitialized) {
            $this->initializeDatabase();
            self::$databaseInitialized = true;
        }

        // ОПТИМИЗАЦИЯ: Начинаем транзакцию для каждого теста
        $this->getEntityManager()->beginTransaction();
    }

    protected function tearDown(): void
    {
        // ОПТИМИЗАЦИЯ: Rollback вместо recreate DB
        $em = $this->getEntityManager();
        if ($em->getConnection()->isTransactionActive()) {
            $em->rollback();
        }

        // Очистка identity map
        $em->clear();

        parent::tearDown();
    }

    private function initializeDatabase(): void
    {
        $em = $this->getEntityManager();
        $schemaTool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            // Дропаем если есть (для reruns)
            $schemaTool->dropSchema($metadata);
            // Создаем один раз
            $schemaTool->createSchema($metadata);
        }
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get(EntityManagerInterface::class);
    }

    protected function getMapper(): Mapper
    {
        return $this->getContainer()->get(Mapper::class);
    }

    // ... OpenAPI assertions методы
}

// РЕЗУЛЬТАТ:
// Test 1: boot kernel (100ms) + create DB (50ms) + test (10ms) = 160ms
// Test 2: begin transaction (1ms) + test (10ms) + rollback (1ms) = 12ms
// Test 3: begin transaction (1ms) + test (10ms) + rollback (1ms) = 12ms
// Total: 184ms (было 450ms) - в 2.5x быстрее!
```

**Для тестов требующих чистую БД:**
```php
class SpecialTest extends IntegrationTestCase
{
    public function testRequiresCleanDatabase(): void
    {
        // Если нужна чистая БД - можно явно сделать rollback + begin
        $this->getEntityManager()->rollback();
        $this->getEntityManager()->clear();
        $this->getEntityManager()->beginTransaction();

        // Тест с пустой БД
    }
}
```

**Конфигурация для тестовой БД:**
```yaml
# tests/Fixture/TestApp/Resources/config/config.yaml
doctrine:
  dbal:
    driver: 'pdo_sqlite'
    path: ':memory:'  # <- В памяти, еще быстрее
    # Или для отладки:
    # path: '%kernel.cache_dir%/test.db'
```

---

### 11. PHPUnit Configuration для групп и фильтров

**Обоснование:**
- **Удобство**: Predefined test suites
- **Консистентность**: Все используют одинаковые команды
- **CI ready**: Легко настроить разные jobs
- **Документация**: Конфиг показывает какие группы тестов есть

**Текущая конфигурация:**
```xml
<!-- phpunit.xml -->
<phpunit ...>
    <testsuites>
        <testsuite name="Test Suite">
            <directory>tests/cases</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**Предлагаемая конфигурация:**
```xml
<!-- phpunit.xml -->
<phpunit
    bootstrap="tests/autoload.php"
    colors="true"
    stopOnFailure="false"
    cacheResultFile=".phpunit.cache/test-results">

    <testsuites>
        <!-- Быстрые unit-тесты для TDD -->
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>

        <!-- Интеграционные тесты с kernel -->
        <testsuite name="integration">
            <directory>tests/Integration</directory>
        </testsuite>

        <!-- Все тесты -->
        <testsuite name="all">
            <directory>tests/Unit</directory>
            <directory>tests/Integration</directory>
        </testsuite>

        <!-- Только трансформеры (для TDD конкретной фичи) -->
        <testsuite name="transformers">
            <directory>tests/Unit/Services/Mapper/Transformer</directory>
        </testsuite>

        <!-- Только OpenAPI -->
        <testsuite name="openapi">
            <directory>tests/Unit/Services/OpenApi</directory>
            <directory>tests/Integration/OpenApi</directory>
        </testsuite>
    </testsuites>

    <groups>
        <exclude>
            <group>slow</group>  <!-- По умолчанию skip медленных -->
        </exclude>
    </groups>

    <php>
        <env name="SYMFONY_DEPRECATIONS_HELPER" value="weak"/>
        <env name="APP_ENV" value="test"/>
        <env name="KERNEL_CLASS" value="Nyholm\BundleTest\TestKernel"/>
    </php>
</phpunit>
```

**Использование:**
```bash
# Запустить unit-тесты
vendor/bin/phpunit --testsuite=unit

# Запустить integration-тесты
vendor/bin/phpunit --testsuite=integration

# Только трансформеры (для TDD)
vendor/bin/phpunit --testsuite=transformers

# Только OpenAPI
vendor/bin/phpunit --testsuite=openapi

# Все тесты включая медленные
vendor/bin/phpunit --exclude-group=""

# Только быстрые unit
vendor/bin/phpunit --testsuite=unit --group=fast
```

**Makefile:**
```makefile
# Makefile
.PHONY: test test-unit test-integration test-fast test-all

test: test-fast  # По умолчанию только быстрые

test-fast:
	vendor/bin/phpunit --testsuite=unit

test-unit:
	vendor/bin/phpunit --testsuite=unit

test-integration:
	vendor/bin/phpunit --testsuite=integration

test-all:
	vendor/bin/phpunit --exclude-group=""

test-transformers:
	vendor/bin/phpunit --testsuite=transformers

test-openapi:
	vendor/bin/phpunit --testsuite=openapi
```

---

## Приоритизация

### Критические (делать первым делом):
1. ✅ **Разделение Unit/Integration** - максимальный эффект на скорость
2. ✅ **Data Providers** - сразу уменьшит кол-во кода
3. ✅ **expectException** - быстро, везде применимо

### Важные (делать следом):
4. ✅ **setUp/tearDown** - улучшает структуру
5. ✅ **Оптимизация BaseTestCase** - ускорит integration тесты
6. ✅ **PHPUnit конфигурация** - сделает удобным запуск

### Полезные (опционально):
7. ⚪ **Test Traits** - если видим много дублирования
8. ⚪ **Фабрики** - когда создание объектов усложняется
9. ⚪ **Custom Assertions** - по мере роста тестов
10. ⚪ **Группировка @group** - для CI оптимизации
11. ⚪ **Упрощение фикстур** - если фикстур становится много

---

## Ожидаемый результат

### Было:
```
tests/
├── cases/                    # 813 строк
│   ├── OpenApi/
│   ├── RequestModel/
│   └── ResponseModel/
└── src/
    ├── BaseTestCase.php      # Все наследуют kernel
    └── Fixture/              # 43 файла, глубокая вложенность

Время выполнения: ~5-10 секунд
Kernel bootов: N (по числу тестов)
```

### Станет:
```
tests/
├── Unit/                     # ~400 строк, быстрые тесты
│   ├── UnitTestCase.php     # extends PHPUnit\Framework\TestCase
│   ├── Services/
│   ├── Traits/              # Переиспользуемая логика
│   └── Assertion/           # Custom assertions
│
├── Integration/              # ~400 строк, медленные тесты
│   ├── IntegrationTestCase.php  # extends KernelTestCase
│   ├── Mapper/
│   └── OpenApi/
│
└── Fixture/                  # Плоская структура
    ├── Controller/
    ├── Entity/
    ├── Enum/
    ├── Factory/              # Тестовые фабрики
    └── TestApp/

Время выполнения:
- Unit: ~0.5-1 секунда
- Integration: ~3-4 секунды
- Всего: ~4-5 секунд (было 5-10)

Kernel bootов: 1 (переиспользуется)
```

### Метрики:
- **Код**: -20-30% строк (за счет DRY)
- **Скорость unit**: 10-50x быстрее
- **Скорость integration**: 2-3x быстрее
- **Читаемость**: Data providers + custom assertions
- **Поддерживаемость**: Фабрики + traits + плоские фикстуры
- **CI**: Можно запускать только unit (~1 сек)
