# petstore

[![CI](https://github.com/chubbyphp/petstore/workflows/CI/badge.svg?branch=chubbyphp)](https://github.com/chubbyphp/petstore/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp)

## Description

A simple skeleton to build api's based on the [chubbyphp-framework][1].

## Requirements

 * php: ^7.4
 * [chubbyphp/chubbyphp-api-http][2]: ^4.1
 * [chubbyphp/chubbyphp-clean-directories][3]: ^1.1
 * [chubbyphp/chubbyphp-cors][4]: ^1.3
 * [chubbyphp/chubbyphp-deserialization][5]: ^3.1
 * [chubbyphp/chubbyphp-framework][6]: ^3.4
 * [chubbyphp/chubbyphp-framework-router-fastroute][7]: ^1.1
 * [chubbyphp/chubbyphp-laminas-config][8]: ^1.2
 * [chubbyphp/chubbyphp-laminas-config-doctrine][9]: ^1.2
 * [chubbyphp/chubbyphp-laminas-config-factory][10]: ^1.1
 * [chubbyphp/chubbyphp-negotiation][11]: ^1.8
 * [chubbyphp/chubbyphp-serialization][12]: ^3.1
 * [chubbyphp/chubbyphp-validation][13]: ^3.12
 * [doctrine/orm][14]: ^2.8.2
 * [monolog/monolog][15]: ^2.2
 * [ramsey/uuid][16]: ^4.1.1
 * [slim/psr7][17]: ^1.3
 * [swagger-api/swagger-ui][18]: ^3.43
 * [symfony/console][19]: ^5.2.3

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

### Urls

* http://localhost:10080
* https://localhost:10443

### DBs

 * jdbc:postgresql://localhost:15432/petstore?user=root&password=root

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

Dominik Zogg 2021

[1]: https://github.com/chubbyphp/chubbyphp-framework

[2]: https://packagist.org/packages/chubbyphp/chubbyphp-api-http
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-clean-directories
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-fastroute
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[12]: https://packagist.org/packages/chubbyphp/chubbyphp-serialization
[13]: https://packagist.org/packages/chubbyphp/chubbyphp-validation
[14]: https://packagist.org/packages/doctrine/orm
[15]: https://packagist.org/packages/monolog/monolog
[16]: https://packagist.org/packages/ramsey/uuid
[17]: https://packagist.org/packages/slim/psr7
[18]: https://packagist.org/packages/swagger-api/swagger-ui
[19]: https://packagist.org/packages/symfony/console

[40]: https://packagist.org/packages/chubbyphp/petstore

[60]: src/Collection

[70]: src/Factory

[80]: src/Mapping

[90]: src/Model

[100]: src/Repository

[110]: src/RequestHandler

[120]: src/ServiceFactory
