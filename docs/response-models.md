# Response Models

A controller response is described by a PHP class. The controller returns an instance of that class, and the bundle converts the object to JSON.

## Ways to Describe a Model

### Flat Model with Promoted Properties (Preferred)

List all fields as `public` constructor parameters. The model does not know where the data comes from; that responsibility stays in the controller or in a dedicated mapper.

```php
namespace App\ResponseModel;

use RestApiBundle\Mapping\ResponseModel\ResponseModelInterface;

class Book implements ResponseModelInterface
{
    public function __construct(
        public int $id,
        public string $title,
        public ?int $year,
    ) {}
}
```

```php
class BookController
{
    #[Route('/books/{id}', methods: 'GET')]
    public function show(BookEntity $entity): Book
    {
        return new Book(
            id: $entity->getId(),
            title: $entity->getTitle(),
            year: $entity->getYear(),
        );
    }
}
```

```json
{"id": 42, "title": "Pride and Prejudice", "year": 1813, "__typename": "Book"}
```

### Entity Wrapper Model

The constructor receives a domain entity. Fields are exposed through public getters that know how to read data from the entity.

```php
class Book implements ResponseModelInterface
{
    public function __construct(private BookEntity $entity) {}

    public function getId(): int { return $this->entity->getId(); }
    public function getTitle(): string { return $this->entity->getTitle(); }
    public function getYear(): ?int { return $this->entity->getYear(); }
}
```

```php
return new Book($entity);
```

The resulting JSON is the same as in the previous example.

### Choosing an Approach

A flat model clearly separates the HTTP layer from the domain layer: a response model is the structure of fields exposed externally, and nothing more. Knowledge about how to read those fields from an entity stays in the controller or in a dedicated mapper.

An entity wrapper is shorter, but it blurs responsibilities: the HTTP layer starts to know the entity structure, and that knowledge lives in `ResponseModel\...` rather than where entity handling is described explicitly. This approach works, but it is more coupled.

The bundle does not care which approach you use; it serializes both variants the same way.

## How Fields Get Into JSON

JSON output includes:

- all public properties of the class, using the property name;
- all public methods whose name starts with `get`; the field name is derived from the method name: `getId` -> `id`, `getFirstName` -> `firstName`.

Names are not transformed. `camelCase` stays `camelCase`.

Each object also receives an automatic `__typename` field: a string class identifier that helps clients distinguish types. Its value is derived from the class namespace (see "Class Location Requirements").

## Field Types

### Scalars

`int`, `string`, `bool`, `float`, and `null` for nullable fields are passed through as is.

### Dates and Times

For date-time values, use the regular PHP type `\DateTime` or `\DateTimeImmutable`:

```php
public function getCreatedAt(): \DateTime { ... }
```

It is serialized to ISO 8601 in UTC (`2024-12-25T10:30:00+00:00`). This format is fixed.

For date-only values, use the dedicated `RestApiBundle\Mapping\ResponseModel\Date` class. Create it through the factory method:

```php
use RestApiBundle\Mapping\ResponseModel\Date;

public function getPublishedAt(): Date
{
    return Date::from($this->entity->getPublishedAt());
}
```

The default format is `Y-m-d`; it can be configured in the bundle configuration (see [errors-and-config.md](errors-and-config.md)).

Note: requests use `RestApiBundle\Mapping\Mapper\Date`, while responses use `RestApiBundle\Mapping\ResponseModel\Date`. These are different classes.

### Enums

PHP 8.1 backed enum:

```php
enum Status: string { case DRAFT = 'draft'; case PUBLISHED = 'published'; }

public function getStatus(): Status { return Status::PUBLISHED; }
```

The backing value is written to JSON: `"status": "published"`. For int enums, the JSON value is an integer.

The polyfill variant is also supported: a class extending `RestApiBundle\Mapping\ResponseModel\BaseEnum`. It is serialized the same way.

### Nested Models

A field may be another model implementing `ResponseModelInterface`:

```php
class Author implements ResponseModelInterface
{
    public function __construct(public int $id, public string $name) {}
}

class Book implements ResponseModelInterface
{
    public function __construct(
        public int $id,
        public string $title,
        public Author $author,
    ) {}
}
```

```json
{
  "id": 42,
  "title": "Pride and Prejudice",
  "author": {"id": 7, "name": "Jane Austen", "__typename": "Author"},
  "__typename": "Book"
}
```

Nested depth is not limited. Each nested object receives its own `__typename` field.

### Collections

Arrays of objects are supported. The element type is specified with PHPDoc through `@return Type[]`; without it, the element type cannot be determined:

```php
class Book implements ResponseModelInterface
{
    /**
     * @return Author[]
     */
    public function getCoAuthors(): array { ... }
}
```

```json
{
  "coAuthors": [
    {"id": 7, "name": "Jane Austen", "__typename": "Author"},
    {"id": 8, "name": "Mary Shelley", "__typename": "Author"}
  ]
}
```

The same applies to public properties; use `@var Type[]` for them.

## What a Controller Can Return

| Return value | Result |
|--------------|--------|
| `ResponseModelInterface` object | Serialized as a JSON object, HTTP 200 |
| Array of `ResponseModelInterface` objects | Serialized as a JSON array, HTTP 200 |
| `null` | HTTP 204, empty body |
| `Symfony\Component\HttpFoundation\Response` object | Passed to the client as is; the bundle does not interfere |

Bare scalars (`int`, `string`, `bool`) and associative arrays are not allowed and will cause an error. If you need a custom response, return `Response` directly.

## HTTP Statuses and Headers

The HTTP status is determined automatically:

- `200 OK` when an object or array is returned;
- `204 No Content` when `null` is returned.

There is no runtime customization for this behavior, such as returning `201 Created`. If you need another status code, return `Response` directly. For OpenAPI documentation, the success code can be overridden with `#[OpenApi\Endpoint(httpStatusCode: ...)]`, but this does not change the actual application response.

`Content-Type` is set to `application/json`. Additional headers can be supplied through the `_response_headers` request attribute:

```php
public function show(Request $request, ...): Book
{
    $request->attributes->set('_response_headers', ['X-Total-Count' => '42']);
    return new Book(...);
}
```

## Class Location Requirements

A response model class must be in a namespace that contains a `ResponseModel` segment. This is used for the `__typename` value and for the OpenAPI schema name.

The `__typename` value is built from namespace parts **after** the `ResponseModel` segment, joined with dots:

| Namespace | `__typename` |
|-----------|--------------|
| `App\ResponseModel\Book` | `Book` |
| `App\ResponseModel\Book\Detail` | `Book.Detail` |
| `App\Api\V1\ResponseModel\User\Profile` | `User.Profile` |

The OpenAPI schema name is built from the same base, with the `ResponseModel` suffix. For example: `BookResponseModel` or `Book.DetailResponseModel`.

If the class namespace does not contain a `ResponseModel` segment, serialization will fail.
