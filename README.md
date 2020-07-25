# REST API Bundle

[![Build Status](https://github.com/troytft/rest-api-bundle/workflows/Tests/badge.svg)](https://github.com/troytft/rest-api-bundle/workflows/Tests/badge.svg)

REST API Bundle is abstraction layer for requests and responses.

Work in progress.

##### Roadmap
* First stable release

### Installation
```bash
composer require troytft/rest-api-bundle
```

Add bundle to `AppKernel.php`

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new RestApiBundle\RestApiBundle(),
        ];

        // ...
    }
}
```

### Documentation
Full documentation can be found at [`docs/main.md`](docs/main.md)
