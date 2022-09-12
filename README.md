# petstore

[![CI](https://github.com/chubbyphp/petstore/workflows/CI/badge.svg?branch=slim)](https://github.com/chubbyphp/petstore/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=slim)](https://coveralls.io/github/chubbyphp/petstore?branch=slim)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/chubbyphp/petstore/slim)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/petstore/slim)

## Description

A simple skeleton to build api's based on the [slim][1] framework.

## Requirements

 * php: ^7.4
 * [chubbyphp/chubbyphp-api-http][2]: ^4.3
 * [chubbyphp/chubbyphp-clean-directories ][3]: ^1.3
 * [chubbyphp/chubbyphp-cors][4]: ^1.4
 * [chubbyphp/chubbyphp-deserialization][5]: ^3.4.1
 * [chubbyphp/chubbyphp-laminas-config][6]: ^1.3
 * [chubbyphp/chubbyphp-laminas-config-doctrine][7]: ^2.0
 * [chubbyphp/chubbyphp-laminas-config-factory][8]: ^1.3
 * [chubbyphp/chubbyphp-negotiation][9]: ^1.9
 * [chubbyphp/chubbyphp-serialization][10]: ^3.2
 * [chubbyphp/chubbyphp-validation][11]: ^3.12.3
 * [doctrine/orm][12]: ^2.13.1
 * [monolog/monolog][13]: ^2.3.5
 * [ramsey/uuid][14]: ^4.2.3
 * [slim/psr7][15]: ^1.5
 * [slim/slim][16]: ^4.9
 * [swagger-api/swagger-ui][17]: ^4.6.1
 * [symfony/console][18]: ^5.4.11|^6.1.3

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

 * jdbc:postgresql://localhost:5432/petstore?user=root&password=root

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/petstore][40].

```bash
composer create-project chubbyphp/petstore myproject "dev-chubbyphp"
```

## Setup

```sh
composer setup:dev
```

## Structure

### Collection

Collections are sortable, filterable paginated lists of models.

 * [App\Collection][60]

### Factory

Factories to create collections, model or whatever you need to be created.

 * [App\Factory][70]

### Mapping

Mappings are used for deserialization, orm, serialization and validation defintions. They are all done in PHP.

 * [App\Mapping][80]

### Model

Models, entities, documents what ever fits your purpose the best.

 * [App\Model][90]

### Repository

Repositories get data from storages like databases, elasticsearch, redis or whereever your models are stored or cached.

 * [App\Repository][100]

### RequestHandler

RequestHandler alias Controller, or Controller actions to be more precise.
There is a directory with generic crud controllers. If you like the idea adapt them for your generic use case, if not drop them.
I highly recommend to not extend them.

 * [App\RequestHandler][110]

### ServiceFactory

Service factories are the glue code of the dependeny injection container.

 * [App\ServiceFactory][120]

## Copyright

Dominik Zogg 2022

[1]: https://github.com/slimphp/slim

[2]: https://packagist.org/packages/chubbyphp/chubbyphp-api-http
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-clean-directories
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-serialization
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-validation
[12]: https://packagist.org/packages/doctrine/orm
[13]: https://packagist.org/packages/monolog/monolog
[14]: https://packagist.org/packages/ramsey/uuid
[15]: https://packagist.org/packages/slim/psr7
[16]: https://packagist.org/packages/slim/slim
[17]: https://packagist.org/packages/swagger-api/swagger-ui
[18]: https://packagist.org/packages/symfony/console

[40]: https://packagist.org/packages/chubbyphp/petstore

[60]: src/Collection

[70]: src/Factory

[80]: src/Mapping

[90]: src/Model

[100]: src/Repository

[110]: src/RequestHandler

[120]: src/ServiceFactory
