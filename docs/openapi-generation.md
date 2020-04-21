# OpenApi Generation

### Generate
You can generate a specification with command `rest-api:generate-docs`, command available in your application after installation.

##### Examples
```bash
bin/console rest-api:generate-docs > docs/openapi.yaml
bin/console rest-api:generate-docs --format=json > docs/openapi.yaml
bin/console rest-api:generate-docs --namespace-filter=PublicApp > docs/openapi.yaml
```
