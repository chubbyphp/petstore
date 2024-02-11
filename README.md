# petstore

[![CI](https://github.com/chubbyphp/petstore/workflows/CI/badge.svg?branch=chubbyphp-mongo)](https://github.com/chubbyphp/petstore/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp-mongo)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp-mongo)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fpetstore%2Fchubbyphp-mongo)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/petstore/chubbyphp-mongo)

## Description

A simple skeleton to build api's based on the [chubbyphp-framework][1].

## Requirements

 * php: ^8.1
 * [chubbyphp/chubbyphp-clean-directories][2]: ^1.3.1
 * [chubbyphp/chubbyphp-cors][3]: ^1.5
 * [chubbyphp/chubbyphp-decode-encode][4]: ^1.1
 * [chubbyphp/chubbyphp-framework][5]: ^5.1.1
 * [chubbyphp/chubbyphp-framework-router-fastroute][6]: ^2.1
 * [chubbyphp/chubbyphp-http-exception][7]: ^1.1
 * [chubbyphp/chubbyphp-laminas-config][8]: ^1.4
 * [chubbyphp/chubbyphp-laminas-config-doctrine][9]: ^2.2
 * [chubbyphp/chubbyphp-laminas-config-factory][10]: ^1.3
 * [chubbyphp/chubbyphp-negotiation][11]: ^2.0
 * [chubbyphp/chubbyphp-parsing][12]: ^1.0
 * [doctrine/mongodb-odm][13]: ^2.4.2
 * [monolog/monolog][14]: ^3.5
 * [ramsey/uuid][15]: ^4.7.5
 * [slim/psr7][16]: ^1.6.1
 * [symfony/console][17]: ^6.4.2

## Environment

Add the following environment variable to your system, for example within `~/.bash_aliases`:

```sh
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
```

### Docker

```sh
docker-compose up -d
docker-compose exec php bash
```

## Urls

* GET https://localhost/ping
* GET https://localhost/swagger (https://localhost/openapi)

### Pet

* GET https://localhost/api/pets?sort[name]=asc
* POST https://localhost/api/pets
* GET https://localhost/api/pets/8ba9661b-ba7f-436b-bd25-c0606f911f7d
* PUT https://localhost/api/pets/8ba9661b-ba7f-436b-bd25-c0606f911f7d
* DELETE https://localhost/api/pets/8ba9661b-ba7f-436b-bd25-c0606f911f7d

### DBs

 * jdbc:mongodb://petstore:4aAUfBjDACcdZxNwJgJ6@localhost:27017/petstore

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/petstore][40].

```bash
composer create-project chubbyphp/petstore myproject "dev-chubbyphp-mongo"
```

## Setup

```sh
composer setup:dev
```

## Structure

### Collection

Collections are sortable, filterable paginated lists of models.

 * [App\Collection][60]

### Dto

A DTO, or Data Transfer Object, is a simple object used to transport data between software application components.

 * [App\Dto][70]

### Middleware

Middleware functions can execute code, make changes to the request and response objects.
Middleware can generally be added globally or on a per-route basis.

 * [App\Middleware][80]

### Model

Models, entities, documents what ever fits your purpose the best.

 * [App\Model][90]

### ODM

ODM Mapping definitions.

 * [App\ODM][100]

### Parsing

Parses and validates data against predefined schemas, ensuring that incoming data conforms to expected structures and criteria.

 * [App\Parsing][110]

### Repository

Repositories get data from storages like databases, elasticsearch, redis or whereever your models are stored or cached.

 * [App\Repository][120]

### RequestHandler

RequestHandler alias Controller, or Controller actions to be more precise.
There is a directory with generic crud controllers. If you like the idea adapt them for your generic use case, if not drop them.
I highly recommend to not extend them.

 * [App\RequestHandler][130]

### ServiceFactory

Service factories are the glue code of the dependeny injection container.

 * [App\ServiceFactory][140]

## Copyright

2023 Dominik Zogg

[1]: https://github.com/chubbyphp/chubbyphp-framework
[2]: https://packagist.org/packages/chubbyphp/chubbyphp-clean-directories
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-decode-encode
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-fastroute
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-http-exception
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[12]: https://packagist.org/packages/chubbyphp/chubbyphp-parsing
[13]: https://packagist.org/packages/doctrine/mongodb-odm
[14]: https://packagist.org/packages/monolog/monolog
[15]: https://packagist.org/packages/ramsey/uuid
[16]: https://packagist.org/packages/slim/psr7
[17]: https://packagist.org/packages/symfony/console

[40]: https://packagist.org/packages/chubbyphp/petstore

[60]: src/Collection

[70]: src/Dto

[80]: src/Middleware

[90]: src/Model

[100]: src/Odm

[110]: src/Parsing

[120]: src/Repository

[130]: src/RequestHandler

[140]: src/ServiceFactory
