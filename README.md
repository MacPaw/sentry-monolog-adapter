Symfony Sentry Monolog Adapter
=================================

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |
| `develop`| [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

The Sentry Monolog Adapter is a Symfony bundle designed to enhance and provide granular control over logging to Sentry. It is particularly powerful for applications that use the Symfony Messenger component, allowing you to implement intelligent logging strategies to avoid log floods and focus on critical errors.

### Key Features

*   **Advanced Log Processing**: Utilizes a decorator for Monolog's Sentry handler to process and enrich log records before they are sent.
*   **Customizable Processors**: Add your own processors to include or modify contextual data, with a built-in `ExceptionProcessor` to get you started.
*   **Intelligent Messenger Logging**: A dedicated middleware for the Symfony Messenger component that allows you to control which messages are logged based on flexible strategies.
*   **Built-in Logging Strategies**: A suite of strategies is provided out-of-the-box, including:
    *   `LogAllFailedStrategy`: Only logs messages that fail.
    *   `LogAfterPositionStrategy`: Logs messages after a specific number of retries.
    *   `ArithmeticProgressionStrategy`: Logs based on an arithmetic sequence of retry attempts.
    *   And several others to fit your needs.
*   **Modern and Compatible**: Built for PHP 8.1+ and compatible with `sentry/sentry-symfony` v5.


Installation
============

Step 1: Download the Bundle
----------------------------------
Open a command console, enter your project directory and execute:

###  Applications that use Symfony Flex

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
[master Build Status]: https://github.com/macpaw/sentry-monolog-adapter/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/sentry-monolog-adapter/workflows/CI/badge.svg?branch=master
[develop Build Status]: https://github.com/macpaw/sentry-monolog-adapter/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/sentry-monolog-adapter/workflows/CI/badge.svg?branch=develop
[master Code Coverage]: https://codecov.io/gh/macpaw/sentry-monolog-adapter/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/sentry-monolog-adapter/master?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/sentry-monolog-adapter/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/sentry-monolog-adapter/develop?logo=codecov
