# Elasticsearch compilation target for RulerZ [![Build Status](https://travis-ci.org/rulerz-php/elasticsearch.svg?branch=master)](https://travis-ci.org/rulerz-php/elasticsearch)

Elasticsearch compilation target for [RulerZ](https://github.com/K-Phoen/rulerz).

Usage
-----

[elastic/elasticsearch-php](https://github.com/elastic/elasticsearch-php) is one
of the targets supported by RulerZ. It allows the engine to query an Elasticsearch
server.

This cookbook will show you how to retrieve objects using the official client
for Elasticsearch and RulerZ.

Here is a summary of what you will have to do:

 * [configure Eloquent](#configure-eloquent);
 * [configure RulerZ](#configure-rulerz);
 * [filter your target](#filter-your-target).

## Configure elastic/elasticsearch-php

This subject won't be directly treated here. You can either follow the [official
documentation](http://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_installation_2.html)
or use a bundle/module/whatever the framework you're using promotes.

### Configure RulerZ

Once Eloquent is installed and configured we can the RulerZ engine:

```php
$rulerz = new RulerZ(
    $compiler, [
        new \RulerZ\Elasticsearch\Target\Elasticsearch(), // this line is Elasticsearch-specific
        // other compilation targets...
    ]
);
```

The only Elasticsearch-related configuration is the `Elasticsearch` target being added to the
list of the known compilation targets.

### Filter your target

Now that both elastic/elasticsearch-php and RulerZ are ready, you can use them
to retrieve data.

The `Elasticsearch` instance that we previously injected into the RulerZ engine
only knows how to use `Elasticsearch\Client` objects so the first step is
creating one:

```php
$client = new Elasticsearch\Client();
```

And as usual, we call RulerZ with our target (the `Search` object) and our
rule.
RulerZ will build the right executor for the given target and use it to filter
the data, or in our case to retrieve data from Elasticsearch.

```php
$rule  = 'gender = :gender and points > :points';
$parameters = [
    'points' => 30,
    'gender' => 'M',
];
$executionContext = [
    'index' => 'index_name',
    'type'  => 'type_name',
];

var_dump(
    iterator_to_array($rulerz->filter($client, $rule, $parameters, $executionContext))
);
```

**N.B**: you'll notice an unusual variable named `$executionContext`. It
contains a few parameters needed by the `Elasticsearch` in order to make
the request and are mandatory.

That's it!

License
-------

This library is under the [MIT](LICENSE) license.
