# Error Format and Configuration

## Error Response Format

If the input data cannot be mapped or validated, the controller is not called and the client receives:

```http
HTTP/1.1 400 Bad Request
Content-Type: application/json
```

```json
{
  "properties": {
    "title": ["This value should not be blank."],
    "year": ["This value should be an integer."],
    "address.city": ["This value should not be blank."],
    "genres.0": ["An entity with specified value not found."]
  }
}
```

`properties` contains a map of `field name -> message list`. Keys are built with dot notation:

- `address.city` - the `city` field inside the nested `address` model;
- `genres.0` - the first element of the `genres` array.

One response may contain errors for multiple fields at once.

## Error Types (Default Messages)

The bundle itself returns the following English messages. Additional messages may come from Symfony Validator constraints such as `Length`, `NotBlank`, or `Range`; their wording is defined by Symfony.

| Situation | Message |
|-----------|---------|
| Required field is missing / `null` for a non-nullable field | `This value should not be null.` |
| Extra key in input data | `The key is not defined in the model.` |
| Expected an array, got a non-array value | `This value should be a collection.` |
| Expected an object, got a non-object value | `This value should be an object.` |
| Wrong type: expected a string | `This value should be a string.` |
| Wrong type: expected an integer | `This value should be an integer.` |
| Wrong type: expected a float | `This value should be a float.` |
| Wrong type: expected a boolean | `This value should be a boolean.` |
| Expected an ID array of integers | `This value should be a collection of integers.` |
| Expected an ID array of strings | `This value should be a collection of strings.` |
| Date cannot be parsed with the configured format | `This value should be valid date with format "{format}".` |
| Date matches the format but is invalid (`2024-02-30`) | `This value is not a valid date.` |
| Same for date-time values | `This value should be valid date time with format "{format}".` / `This value is not a valid datetime.` |
| Value does not match any enum case | `The value you selected is not a valid choice.` |
| Doctrine entity was not found | `An entity with specified value not found.` |
| At least one entity in an entity collection was not found | `One entity of entities collection not found.` |
| Duplicate values in an entity identifier array | `Values should be unique.` |
| Required file was not provided | `Select a file.` |

## Localization

Messages are localized through Symfony Translation. The translation domain is `exceptions`. The bundle ships with `en` and `ru` translations.

To override texts for your own locales, create a `translations/exceptions.<locale>.yaml` file in the application. Translation keys are the fully-qualified exception class names from the bundle. Example:

```yaml
# translations/exceptions.en.yaml
RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException: 'This field is required.'
RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException: 'Expected an integer.'
RestApiBundle\Exception\Mapper\Transformer\InvalidDateFormatException: 'Date must use the "{format}" format.'
```

The `{format}` parameter is available in date-related messages and contains the expected field format.

The full list of exception FQCNs is available in `vendor/troytft/rest-api-bundle/src/Exception/`.

## Other HTTP Codes

- `422 Unprocessable Entity` - the POST/PUT/PATCH/DELETE request body cannot be parsed as JSON. The response has no structured body; this is Symfony's standard behavior.
- `404`, `500`, and other codes are handled by Symfony as usual; the bundle does not interfere with them.

## Bundle Configuration

Parameters are defined in `config/packages/rest_api.yaml`. All keys are optional; the minimal configuration is `rest_api: ~`.

```yaml
rest_api:
    # Automatic 400 response for request mapping and validation errors.
    # false means errors are propagated further so they can be caught and
    # formatted manually (see the section below).
    is_request_validation_exception_handler_enabled: true

    # Convert request datetime values to the server's local timezone.
    # false keeps the timezone from the input value.
    is_force_request_datetime_to_local_timezone: true

    # Format for Mapper\Date fields in requests.
    default_request_date_format: 'Y-m-d'

    # Format for \DateTime fields in requests. Defaults to ISO 8601 with timezone.
    default_request_datetime_format: 'Y-m-d\TH:i:sP'

    # Format for ResponseModel\Date fields in responses.
    response_model_date_format: 'Y-m-d'

    # Automatic conversion of controller return values into JSON responses.
    # false disables automatic response model serialization, so the controller
    # must return a Symfony Response itself.
    is_response_handler_enabled: true

    # json_encode options for response serialization.
    # Accepts: an integer, a constant name as a string, or an array of constants/numbers.
    response_json_encode_options:
        - JSON_UNESCAPED_UNICODE
        - JSON_UNESCAPED_SLASHES
```

The date-time format in responses is fixed to ISO 8601 in UTC and cannot be changed through configuration.

## Disabling the Automatic Mapping Error Response

Sometimes you need a custom error body, for example `{"errors": [...]}` instead of `{"properties": {...}}`, or extra fields such as `code` or `type`. In that case:

1. Disable the automatic response in configuration:

   ```yaml
   rest_api:
       is_request_validation_exception_handler_enabled: false
   ```

2. Add your own exception handler that catches `RestApiBundle\Exception\Mapper\MappingException`:

   ```php
   class MappingExceptionListener
   {
       public function __invoke(ExceptionEvent $event): void
       {
           $exception = $event->getThrowable();
           if (!$exception instanceof MappingException) {
               return;
           }

           $event->setResponse(new JsonResponse([
               'errors' => $this->formatErrors($exception->getProperties()),
           ], 400));
       }
   }
   ```

`$exception->getProperties()` returns the same `field name -> message list` map shown in the first section of this file.
