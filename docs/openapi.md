# OpenAPI Generation

Using request models, response models, and one attribute on an action method, the bundle builds a complete OpenAPI specification (`.yaml` or `.json`). You do not need to write separate schema descriptions; they are inferred from model field types and Symfony assertions.

## Marking an Endpoint

Every action that should be included in the specification must be marked with `#[OpenApi\Endpoint]` next to `#[Route]`:

```php
use RestApiBundle\Mapping\OpenApi;
use Symfony\Component\Routing\Annotation\Route;

class BookController
{
    #[OpenApi\Endpoint(summary: 'Create book', tags: ['books'])]
    #[Route('/books', methods: 'POST')]
    public function create(CreateBookRequest $request): Book
    {
        // ...
    }
}
```

Attribute parameters:

| Parameter | Type | Purpose |
|-----------|------|---------|
| `summary` | `string` | Required. Short operation title in the specification. |
| `description` | `?string` | Detailed operation description. |
| `tags` | `?string[]` | Tags used to group the operation in documentation UIs. |
| `deprecated` | `bool` | Marks the operation as deprecated. By default, this is also enabled when the method has `@deprecated` in PHPDoc. |
| `httpStatusCode` | `?int` | Explicit success HTTP code in the specification, for example `201`. By default it is inferred from the return type: `200` for a model or array, `204` for `null`/`void`. |
| `requestModelInterface` | `?string` | Request model FQCN when it cannot be inferred from action arguments. Rarely needed in practice. |

`httpStatusCode` affects only the generated OpenAPI specification. The actual runtime HTTP code is still the one returned by the response handler: `200` for a model/array, `204` for `null`. If the API must actually return, for example, `201 Created`, return `Symfony\Component\HttpFoundation\Response` directly.

For a method to be included in the specification, it must have:

- a class in a file named like `*Controller.php`;
- a public method with both `#[Route(...)]` and `#[OpenApi\Endpoint(...)]`.

## Request Schema Contents

The source of fields is the request model in the action arguments. The rest depends on the HTTP method and model contents:

- `GET` - each model field becomes a separate query parameter.
- Other methods - model fields form the JSON request body.
- If the model has an `UploadedFile` field, the body automatically becomes `multipart/form-data`.

For `GET` models, arrays are described as query parameters named `field[]`, while nested models are described as object parameters with `style: deepObject`.

If the path contains placeholders (`/books/{id}`, `/users/{slug}`), they become path parameters:

- if the action has a scalar argument with the same name, it defines the parameter type;
- if the placeholder name matches a Doctrine entity argument, the parameter type is inferred from the entity `id` field;
- if the placeholder name does not match a Doctrine entity argument, the placeholder name is treated as the entity field used by Symfony to resolve the object. For example, for `/books/{slug}` and a `Book $book` argument, the path parameter type is taken from `Book::$slug`.

### Transferring Symfony Assertions to the Schema

Regular `Assert\*` constraints work on request model fields, and some constraints on scalar or inline fields are transferred to the OpenAPI schema:

| Assertion | Schema output |
|-----------|---------------|
| `Assert\Length(min, max)` | `minLength`, `maxLength` |
| `Assert\Range(min, max)` | `minimum`, `maximum` |
| `Assert\Choice(choices: [...])` | `enum` with the list of values |
| `Assert\NotBlank`, `Assert\NotNull` | `nullable: false` even when the type is `?T` |

Other assertions are not transferred to the schema, but they are still validated at runtime.

## Response Schema Contents

The response model contributes:

- all public properties, using the property name;
- all public methods with the `get` prefix; the field name is the method name without the prefix and with a lowercased first letter;
- the automatic `__typename` field with a fixed value: the type name.

If a method or property has `@deprecated` in PHPDoc, the field is marked as `deprecated: true` in the schema.

The action return type determines the response shape in the specification:

| Return type | Response in the specification |
|-------------|-------------------------------|
| `Book` (model) | `200`, JSON object |
| `Book[]` (`@return Book[]`) | `200`, JSON array |
| `null` / `void` | `204`, empty body |
| `RedirectResponse` | `302`, `Location` header |
| `BinaryFileResponse` | `200`, `Content-Type` header for file download |

Dates, enums, nested models, and collections are typed by the same rules as model fields (see [response-models.md](response-models.md)).

## Running Generation

Two entry points are available. They do the same thing, so choose whichever is more convenient.

Through Symfony console:

```bash
bin/console rest-api:generate-documentation src/Controller var/openapi.yaml
```

Through the standalone binary from `vendor` (useful, for example, in CI without booting the application):

```bash
vendor/bin/generate-docs src/Controller var/openapi.yaml
```

Arguments:

- `input` - directory with controllers; `*Controller.php` files are scanned recursively;
- `output` - output file path. The format is determined by the extension: `.yaml` / `.yml` -> YAML, `.json` -> JSON.

Options:

- `--template <path>` - base specification to merge with (see below);
- `--exclude-path <pattern>` - paths to exclude. Can be passed multiple times. Accepts a string or a regular expression, for example:

  ```bash
  bin/console rest-api:generate-documentation src/Controller var/openapi.yaml \
      --exclude-path Admin \
      --exclude-path '/.*Legacy.*/'
  ```

## Base Specification Template

`--template` attaches a prepared YAML/JSON file with the "static" part of the specification: `info`, `servers`, `security`, shared `tags` with descriptions, fixed `components.securitySchemes`, and similar data. Content generated from controllers is added to the template:

- `paths` - controller operations are added to existing paths. If the template already contains an operation with the same `path + method`, generation fails;
- `tags` - tags from `#[OpenApi\Endpoint]` are merged with the template tag list, and tag descriptions from the template are preserved;
- `components.schemas` - model schemas are added to schemas from the template. If a schema name is already used in the template, generation fails.

The `openapi` version is taken from the template. If no template is provided, the specification is generated with version `3.1.0`.

Minimal template:

```yaml
openapi: 3.0.0
info:
    title: My API
    version: 1.0.0
security:
    - bearer_auth: []
components:
    securitySchemes:
        bearer_auth:
            type: http
            scheme: bearer
tags:
    - {name: books, description: 'Books CRUD'}
```

## Notes and Limitations

- **Namespace for response models and enums.** Model classes must be in a namespace containing the `ResponseModel` segment, and enum classes must be in a namespace containing the `Enum` segment. Namespace parts after those segments are used to build `__typename` values and schema names: response model schemas receive the `ResponseModel` suffix, and enum schemas receive the `Enum` suffix (see [response-models.md](response-models.md)). Without the expected location, the generator fails.
- **Model field descriptions are not transferred to the schema.** A PHPDoc comment above a property or getter is not copied into the OpenAPI specification. Only the operation itself has a description, through the `description` parameter of `#[OpenApi\Endpoint]`.
- **Plain `Symfony\Component\HttpFoundation\Response` is not described by the generator.** OpenAPI generation supports only response models, arrays of models, `null`/`void`, `RedirectResponse`, and `BinaryFileResponse`.
- **Bare scalars and associative arrays from controllers are not supported** at runtime or in the schema; the generator reports an error for such return values.
