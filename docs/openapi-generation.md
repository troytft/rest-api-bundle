# OpenApi Generation

### Generate
You can generate a specification with command `vendor/bin/generate-docs`, command available in your application after installation.

##### Examples
```bash
vendor/bin/generate-docs src/Controller docs/openapi.yaml
vendor/bin/generate-docs src/Controller docs/openapi.json --format=json
vendor/bin/generate-docs src/Controller docs/openapi.json --namespace-filter=PublicApp
```
