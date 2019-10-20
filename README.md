# REST API Bundle

Прослойка для запросов и ответов, каждый запрос и каждый ответ – это отдельная модель.

### Requirements
* PHP 7.2 or higher

### Installation
```bash
composer require troytft/rest-api-bundle
```

Подключите бандл в `app/AppKernel.php`

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
