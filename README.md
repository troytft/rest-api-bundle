# REST API Bundle

[![Build Status](https://travis-ci.org/troytft/rest-api-bundle.svg?branch=master)](https://travis-ci.org/troytft/rest-api-bundle)

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
