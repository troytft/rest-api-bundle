# rest-api-bundle

A Symfony bundle for building REST APIs.

It covers the typical flow:

1. incoming JSON/query/multipart data is mapped into a request model;
2. the request model is cast to PHP types and validated;
3. the controller returns a response model;
4. the response model is serialized to JSON;
5. an OpenAPI specification can be generated from the same models.

Minimal configuration:

```yaml
rest_api: ~
```

## Documentation

Recommended reading order:

1. [request-models.md](request-models.md) - incoming request models
2. [response-models.md](response-models.md) - response models
3. [errors-and-config.md](errors-and-config.md) - error format and configuration
4. [openapi.md](openapi.md) - OpenAPI generation
