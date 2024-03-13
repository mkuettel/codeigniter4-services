# CodeIgniter4 Services


[![pipeline status](https://gitlab.hostmax.ch/mku/codeigniter4-services/badges/main/pipeline.svg)](https://gitlab.hostmax.ch/mku/hostmax-website/-/commits/main)
[![coverage report](https://gitlab.hostmax.ch/mku/codeigniter4-services/badges/main/coverage.svg)](https://gitlab.hostmax.ch/mku/hostmax-website/-/commits/main)

This library should make it easier to facilitate a Service layer for CodeIgniter4 application.
Additionally there are some tools to interact with the model layer, in particular
this library contains a Service to have some nicer and more reliable syntax for database transactions.


## Installation

You can add this library to your project using [composer](https://getcomposer.org):

```sh
composer require mkuettel/codeigniter4-services
```

That's it, but you might want to change some of the configuration, as described in the next section.

## Configuration

There are multiple configuration files available in the [src/Config](https://github.com/mkuettel/codeigniter4-services/branch/main/tree/src/Config) directories.
You can override these default configuration by copying the configuration file into your
project and adjusting the values in your editor.

_TODO: add publishable configuration_

## Service Interface

This Library provides the interface `\MKU\Services\Service`.

The idea is that you extend from this interface by defining another interface for your own service.
In this interface you define all the methods your service will have:

```php
use \MKU\Services\Service;

interface MyBusinessService extends Service {
    public function placeOrder(Order $order): Result;
    public function sendOrderConfirmationMail(Order $order): Result;
    // ...
}
```

You then implement this interface with your own service class.

```php

class SimpleBusinessService extends MyBusinessService {
    
    private OrderModel $orderModel;
    private MailService $mailService;

    public function __construct(OrderModel $orderModel, MailService $mail) { /* ... */ }
    public function shortname(): string { return 'simple_business'; } 
    public function placeOrder(Order $order): Result { /* */ }
    public function sendOrderConfirmationMail(Order $order): Result { /* */ }
    // ...
}
```

You'll also need to implement `shortname(): string` method returning a unique identifier for
this service implementation.

The shortname will later be used to get or create an instance the implementing service class using:

```php
 service('<shortname>'); 
 \Config\Services::shortname();
```


But for this to work you must for now register it as follows
```php
class Services extends BaseService {
    
    // ....
    
    // use the same parameters as required by the constructor of the service class,
    // but make them null by default.
    public function business_simple() (
        OrderModel $orderModel = null,
        MailService $mail = null,
        bool $getShared = true // add extra parameter to reuse past instance if available (e.g. a singleton instance)
    ): SimpleBusinessService {
        if ($getShared) return self::getSharedInstance('simple_business', $config, $db);
        return new SimpleBusinessService(
            $orderModel ?? model(OrderModel::class);
            $mail ?? self::mail_service(),
        );
    }
    
    // use the interface as return type here
    public function business(): MyBusinessService {
        // change which service class to use for the MyBusinessService interface here
        return self::business_simple();
    }
    
    // ....
}
```

TODO(so this must not be done manually): add ServiceContainer as a base class for \Config\Services which autoconfigures the services depending on their constructor types
or annotations and creates the builder methods in \Config\Services.

### Transaction Service

This library provides a TransactionService, which allows you to execute a custom function during a database transaction:

```php
// PHP 7.4+ using arrow functions
service('transaction')->transact(fn() => do_database_operation())

// Using anonymous function (or any Closure)
service('transaction')->transact(function() use ($db) {
    $db->insert( /* update */ ); ...
    $db->update( /* ...  */); ...
});
```

Used in this manner, the transaction service begins a transaction before executing the given closure.
If an exception occurs during the transaction, the transaction will be rolled back.

Transactions can be nested.

_NOTE: Not all database systems and engines support transactions by default, especially transactions with strict isolation between connections._


Future configuration options:

* `strictMode`: Whether to use strict transactions, meaning other connections cannot see your modifications until you commit?
* `testMode`: Whether to rollback the transaction on exception? tbd.
* `connection_type`: tbd

## Contributing

https://github.com/mkuettel/codeigniter4-services
