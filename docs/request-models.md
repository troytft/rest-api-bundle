# Request Models

An incoming request is described by a PHP class. The controller receives an object that has already been parsed, cast to the required types, and validated.

## Minimal Example

```php
namespace App\RequestModel\Book;

use RestApiBundle\Mapping\Mapper;
use RestApiBundle\Mapping\RequestModel\RequestModelInterface;

#[Mapper\ExposeAll]
class CreateBookRequest implements RequestModelInterface
{
    public string $title;
    public ?int $year;
}
```

```php
class BookController
{
    #[Route('/books', methods: 'POST')]
    public function create(CreateBookRequest $request): ResponseModelInterface
    {
        // $request->title and $request->year are ready to use
    }
}
```

Request:

```http
POST /books
Content-Type: application/json

{"title": "Pride and Prejudice", "year": 1813}
```

The controller receives an object with populated fields. If the JSON contains a wrong type, an extra key, or a validation failure, the client receives a response with error details and the controller is not called.

## Declaring Fields

For a field to participate in mapping, either the class must be marked with `#[Mapper\ExposeAll]`, or each field must be marked with `#[Mapper\Expose]`.

```php
// Option 1: all public properties are exposed automatically
#[Mapper\ExposeAll]
class Filter implements RequestModelInterface
{
    public ?string $query;
    public ?int $offset;
}

// Option 2: fields are listed explicitly
class Filter implements RequestModelInterface
{
    #[Mapper\Expose]
    public ?string $query;

    #[Mapper\Expose]
    public ?int $offset;

    public string $internalState; // will not be mapped
}
```

### Public Properties and Private Properties with Getters/Setters

The simplest option is to use public properties. The field name in JSON matches the property name; there is no camelCase <-> snake_case conversion.

```php
public string $firstName; // JSON key: "firstName"
```

If a property is private or protected, it must have a public setter `setFirstName()` and a public getter. Supported getter names are `getFirstName()`, `firstName()`, and `isFirstName()`. If suitable methods are missing, this is a model class configuration error and the bundle refuses to work with that model.

### Nullable

A nullable type (`?int`, `?string`, ...) means the field may be provided as `null` or omitted completely. A non-nullable type requires the value to be provided in the request.

```php
public string $title;   // required
public ?int $year;      // optional
```

### Default Values

If a field is missing from the request, it is set to `null` by default. Therefore optional fields must be nullable:

```php
public ?int $offset = null;
```

For partial updates (PATCH-style behavior, where missing fields should keep their previous value instead of being reset to `null`), the automatic flow is not suitable; the model must be filled manually in that case.

## Field Types

### Strings, Numbers, Booleans

| Type | Accepted input |
|------|----------------|
| `string` | string or number; numbers are cast to strings |
| `int` | integer or numeric string without a fractional part (`10`, `"10"`); `10.1` or `"10.1"` is an error |
| `float` | number or numeric string (`10`, `"10.1"`) |
| `bool` | `true` / `false`, strings `"true"` / `"false"` / `"1"` / `"0"` / `"yes"` / `"no"` / `"on"` / `"off"` case-insensitively, numbers `1` / `0`; an empty string is `false` |

The accepted boundary values are chosen so the same model class works for both JSON bodies and query strings, where everything arrives as strings.

### Dates and Times

For date-time values, use `\DateTime`:

```php
public \DateTime $createdAt;
```

The default format is ISO 8601 with timezone (`2024-12-25T10:30:00+03:00`). It can be changed globally in configuration or locally with `#[Mapper\DateFormat(...)]` (see below).

Do not use `DateTimeImmutable` in request models: the mapper creates regular `\DateTime` objects.

For date-only values, use the dedicated `RestApiBundle\Mapping\Mapper\Date` type:

```php
public Mapper\Date $publishedAt;
```

The default format is `Y-m-d` (`2024-12-25`). The resulting time is set to `00:00:00`.

If the format does not match or the date is invalid (for example, `2024-02-30`), the client receives an error for that field.

### Enums

PHP 8.1 backed enums are supported:

```php
enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}

class Filter implements RequestModelInterface
{
    public ?Status $status;
}
```

The JSON value is the backing value: `{"status": "draft"}`. If the value does not match any case, this is an error.

A legacy enum variant is also supported: a class extending `RestApiBundle\Mapping\ResponseModel\BaseEnum`. Its behavior and interface are equivalent to a PHP enum.

### Doctrine Entities

If a field type is a Doctrine entity, the bundle looks it up in the database by the provided value. By default, lookup uses the `id` field:

```php
public ?Author $author;
```

```json
{"author": 42}
```

Use `#[Mapper\FindByField(...)]` to look up by another field:

```php
#[Mapper\FindByField('slug')]
public ?Book $book;
```

```json
{"book": "pride-and-prejudice"}
```

The lookup field must be of type `int` or `string`. If the entity is not found, the corresponding field receives an error.

### Arrays

Array element types are specified in PHPDoc with `@var Type[]`. Without PHPDoc, the element type cannot be determined.

Array of Doctrine entities:

```php
/** @var Genre[] */
public array $genres;
```

```json
{"genres": [1, 2, 3]}
```

Behavior: values are checked for uniqueness (duplicates are an error), all entities are loaded in one query, and if at least one entity is missing from the database, this is an error. The result order follows the input ID order. `#[Mapper\FindByField(...)]` works the same way.

Array of scalar values:

```php
/** @var string[] */
public array $tags;
```

Arrays of nested models are described below.

### Nested Models

A field may be another model. The nested class implements `RestApiBundle\Mapping\Mapper\ModelInterface` or `RequestModelInterface` if the same model is also used as a root model in a controller:

```php
#[Mapper\ExposeAll]
class Address implements ModelInterface
{
    public string $city;
    public ?string $street;
}

#[Mapper\ExposeAll]
class CreatePersonRequest implements RequestModelInterface
{
    public string $name;
    public ?Address $address;
}
```

```json
{"name": "John", "address": {"city": "Moscow", "street": "Arbat"}}
```

Arrays of nested models are specified through PHPDoc:

```php
/** @var Address[] */
public array $addresses;
```

Nested depth is not limited.

### Uploaded Files

The field type is `Symfony\Component\HttpFoundation\File\UploadedFile`:

```php
#[Mapper\ExposeAll]
class UploadCoverRequest implements RequestModelInterface
{
    public UploadedFile $cover;
    public ?UploadedFile $thumbnail;
    public string $title;
}
```

The request must be `multipart/form-data`. Text fields and files are submitted together in regular multipart format, and the bundle assembles them into one model.

## Additional Field Attributes

### `#[Mapper\Trim]`

Trims whitespace from both sides of a string value. Applies only to `string` fields.

```php
#[Mapper\Trim]
public string $name; // "  John  " -> "John"
```

### `#[Mapper\EmptyToNull]`

Replaces an empty string value with `null`. Applies only to nullable `?string` fields. Often used together with `Trim`: first the value is trimmed, then an empty string becomes `null`.

```php
#[Mapper\Trim]
#[Mapper\EmptyToNull]
public ?string $note; // "" -> null, "   " -> null
```

### `#[Mapper\FindByField(...)]`

Changes the field used to look up a Doctrine entity. The default is `id`.

```php
#[Mapper\FindByField('slug')]
public ?Book $book;
```

Works for both single entities and arrays.

### `#[Mapper\DateFormat(...)]`

Overrides the date or date-time format for a specific field. The format is a PHP `date()`-style string.

```php
#[Mapper\DateFormat('d.m.Y')]
public Mapper\Date $birthday; // expects "25.12.1990"
```

Global default formats are configured in the bundle configuration (see [errors-and-config.md](errors-and-config.md)).

## Validation

The model is validated with the standard Symfony Validator. Any assertions can be used on fields and on the class:

```php
use Symfony\Component\Validator\Constraints as Assert;

enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}

#[Mapper\ExposeAll]
class CreateBookRequest implements RequestModelInterface
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $title;

    #[Assert\Range(min: 1500, max: 2100)]
    public ?int $year;

    // Only DRAFT and PUBLISHED are available in the public API; ARCHIVED is not.
    #[Assert\Choice(choices: [Status::DRAFT, Status::PUBLISHED])]
    public ?Status $status;
}
```

Nested models and model collection elements are validated automatically. Errors are returned with dot-separated paths:

```
address.city
genres.0.name
```

The error response format is described in [errors-and-config.md](errors-and-config.md).

## Data Sources

The bundle determines where to read model fields from:

| HTTP method | Field source |
|-------------|--------------|
| `GET` | query string |
| `POST` / `PUT` / `PATCH` / `DELETE` | JSON request body |
| any method with `multipart/form-data` | text fields + uploaded files |

The same model class can be used for both GET and other methods; types are cast correctly in both cases.

### Additional Behavior

- If the request contains a key that does not exist in the model, the client receives an unknown-field error. Extra fields are not silently ignored.
- If the JSON body is invalid JSON, the response has status 422 without details; this is Symfony's standard behavior.
- If a non-nullable field is missing, the mapper treats it as `null` and returns an error that the value must not be `null`.
