# OpenApi Generation

### Generate file
You can generate a file with command `rest-api:generate-docs`, command available in your application after installation.

##### Examples
```bash
bin/console rest-api:generate-docs docs/openapi.yaml
bin/console rest-api:generate-docs docs/openapi.json --file-format=json
bin/console rest-api:generate-docs docs/openapi.yaml --namespace-filter=PublicApp
```
