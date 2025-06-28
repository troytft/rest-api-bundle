# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Symfony Bundle** (`troytft/rest-api-bundle`) that provides abstraction layers for REST API requests/responses and auto-generates OpenAPI documentation. It's a PHP library requiring PHP 8.1+ and Symfony 5.4+.

## Development Commands

### Testing & Quality Assurance
```bash
# Run unit tests with detailed output
make test-unit

# Update test snapshots when expectations change
make save-unit

# Check coding standards (ECS - Easy Coding Standard)
make test-cs

# Fix coding standards automatically
make fix-cs

# Run static analysis (PHPStan level 5)
vendor/bin/phpstan analyse

# Performance benchmarking
make benchmark
```

### Code Generation
```bash
# Generate OpenAPI documentation
bin/generate-docs
```

## Architecture Overview

### Core Request/Response Flow
1. **HTTP Request** → **Event Subscriber** → **Mapper** → **Request Model** → **Controller**
2. **Controller** → **Response Model** → **Response Handler** → **JSON Response**

### Key Components

**Request Abstraction** (`src/Services/Request/`):
- Request models implement `RequestModelInterface`
- Automatic type transformation via transformers in `src/Services/Request/RequestTransformer/`
- Validation using Symfony Validator

**Response Abstraction** (`src/Services/Response/`):
- Response models implement `ResponseModelInterface`
- Auto-serialization via public getters
- HTTP response handling in `ResponseFactory`

**OpenAPI Documentation** (`src/Services/Docs/`):
- Automatic schema generation from request/response models
- Endpoint discovery via `#[OpenApi\Endpoint]` attributes
- OpenAPI spec export via CLI command

**Type System** (`src/Services/Request/RequestTransformer/`):
- Transformers for: string, int, float, datetime, enums, Doctrine entities
- Enum support for both PHP 8.1 enums and polyfill enums
- Complex type resolution via `src/Helper/`

### Directory Structure

- `src/CacheWarmer/` - Schema caching for performance
- `src/Command/` - CLI commands (documentation generation)
- `src/EventSubscriber/` - HTTP kernel event handling for request/response lifecycle
- `src/Exception/` - Exception hierarchy with translations
- `src/Helper/` - Utilities (reflection, type analysis, OpenAPI generation)
- `src/Mapping/` - Annotations/attributes for configuration
- `src/Model/` - Data models and context objects
- `src/Services/` - Core business logic (mappers, transformers, documentation)

### Testing

Tests are in `tests/cases/` with fixture-based testing using realistic examples. The test application includes entities, enums, and repositories for comprehensive integration testing.

## Usage Patterns

Controllers receive strongly-typed request models and return response models:

```php
#[OpenApi\Endpoint]
public function action(SomeRequestModel $request): SomeResponseModel
{
    // Request is already validated and transformed
    // Return response model for automatic serialization
}
```

Request/response models use annotations or PHP 8 attributes for configuration and are automatically discovered for OpenAPI documentation generation.