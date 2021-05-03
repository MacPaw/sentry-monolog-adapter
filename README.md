Symfony Sentry Monolog Adapter
=================================

Installation
============

Step 1: Download the Bundle
----------------------------------
Open a command console, enter your project directory and execute:

###  Applications that use Symfony Flex (In Progress)

```console
$ composer require macpaw/sentry-monolog-adapter
```

### Applications that don't use Symfony Flex

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require macpaw/sentry-monolog-adapter
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
----------------------------------
Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            SentryMonologAdapter\SentryMonologAdapterBundle::class => ['all' => true],
        );

        // ...
    }

    // ...
}
```

Create Sentry Monolog Adapter Config:
----------------------------------
`config/packages/sentry_monolog_adapter.yaml `

Configurating logging strategies of MessengerLoggingMiddleware - all strategies you can see [here](https://github.com/MacPaw/sentry-monolog-adapter/tree/master/src/Messenger/LoggingStrategy).

```yaml
sentry_monolog_adapter:
    messenger_logging_middleware:
        logging_strategies: // array of logging strategies
            - id: sentry_monolog_adapter.log_after_position_strategy
              options:
                  position: 3
            - id: sentry_monolog_adapter.log_all_failed_strategy

```

Step 3: Configuration
=============

Monolog:
----------------------------------
`config/packages/monolog.yaml `

```yaml
        sentry:
            type: service
            id: sentry_monolog_adapter.monolog_handler_decorator
```


Messenger:
----------------------------------
`config/packages/messenger.yaml `
```
        middleware:
            ....
            - sentry_monolog_adapter.messenger_logging_middleware
            ....
```

Step 4: Additional settings
=============
It is possible to add preprocessors for putting your parameters to the additional data.
Our library provides a [basic implementation](https://github.com/MacPaw/sentry-monolog-adapter/blob/feat/addDefaultProcessor/src/Processor/ExceptionProcessor.php), but at any time we can replace it with our own implementation
```yaml
sentry_monolog_adapter:
    monolog_handler_decorator:
        processors:
            - sentry_monolog_adapter.exception_processor
```
