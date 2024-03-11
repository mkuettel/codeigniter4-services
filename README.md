# CodeIgniter4 Services

This library should make it easier to facilitate a Service layer for CodeIgniter4 applications.
Additionally there are some tools to interact with the model layer, in particular
this library contains a Service to have some nicer and more reliable syntax for database transactions, like:


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

*NOTE:* Not all database systems and engines support transactions, especially strict ones.
   

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

TODO: add publishable configuration

### Transaction

* `strictMode`: Whether to use strict transactions, meaning other connections cannot see your modifications until you commit?
* `testMode`: Whether to rollback the transaction on exception? tbd.
* `connection_type`: tbd

## Contributing

https://github.com/mkuettel/codeigniter4-services
