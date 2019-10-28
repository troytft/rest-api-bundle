# REST API Bundle

[![Build Status](https://travis-ci.org/troytft/rest-api-bundle.svg?branch=master)](https://travis-ci.org/troytft/rest-api-bundle)

*Work in progress, first stable release will be soon.*

REST API Bundle is abstraction layer for requests and responses. Requests and responses are described by classes. 

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
