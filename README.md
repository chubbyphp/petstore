# petstore

[![Build Status](https://api.travis-ci.org/chubbyphp/petstore.png?branch=chubbyphp-framework)](https://travis-ci.org/chubbyphp/petstore)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp-framework)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp-framework)
[![Total Downloads](https://poser.pugx.org/chubbyphp/petstore/downloads.png)](https://packagist.org/packages/chubbyphp/petstore)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/petstore/d/monthly)](https://packagist.org/packages/chubbyphp/petstore)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/petstore/v/stable.png)](https://packagist.org/packages/chubbyphp/petstore)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/petstore/v/unstable)](https://packagist.org/packages/chubbyphp/petstore)

## Description

A simple skeleton to build api's based on the [chubbyphp-framework][1].

## Requirements

 * php: ^7.2
 * [chubbyphp/chubbyphp-api-http][3]: ^3.3
 * [chubbyphp/chubbyphp-config][4]: ^2.1
 * [chubbyphp/chubbyphp-container][5]: ^1.0
 * [chubbyphp/chubbyphp-cors][6]: ^1.1
 * [chubbyphp/chubbyphp-deserialization][7]: ^2.15
 * [chubbyphp/chubbyphp-doctrine-db-service-provider][8]: ^1.5
 * [chubbyphp/chubbyphp-framework][9]: ^2.5
 * [chubbyphp/chubbyphp-negotiation][10]: ^1.5
 * [chubbyphp/chubbyphp-serialization][11]: ^2.12
 * [chubbyphp/chubbyphp-validation][12]: ^3.6
 * [doctrine/orm][13]: ^2.7
 * [monolog/monolog][14]: ^2.0.1
 * [nikic/fast-route][15]: ^1.3
 * [ocramius/proxy-manager][16]: ^2.2.3
 * [ramsey/uuid][17]: ^3.9.1
 * [slim/psr7][18]: ^0.6
 * [swagger-api/swagger-ui][19]: ^3.24.3
 * [symfony/console][20]: ^4.4.1|^5.0.1

## Environment

Add the following environment variable to your system, for example within `~./bash_aliases`:

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

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/petstore][40].

```bash
composer create-project chubbyphp/petstore myproject "dev-chubbyphp-framework"
```

## Setup

```sh
composer setup:dev
```

## Structure

### ApiHttp

#### Factory

 * [App\ApiHttp\Factory\InvalidParametersFactory][50]

### Collection

 * [App\Collection\PetCollection][60]

### Config

 * [App\Config\DevConfig][70]
 * [App\Config\PhpunitConfig][71]
 * [App\Config\ProdConfig][72]

### RequestHandler

 * [App\RequestHandler\IndexRequestHandler][80]
 * [App\RequestHandler\PingRequestHandler][81]

#### Crud

 * [App\RequestHandler\Crud\CreateRequestHandler][82]
 * [App\RequestHandler\Crud\DeleteRequestHandler][83]
 * [App\RequestHandler\Crud\ListRequestHandler][84]
 * [App\RequestHandler\Crud\ReadRequestHandler][85]
 * [App\RequestHandler\Crud\UpdateRequestHandler][86]

#### Swagger

 * [App\RequestHandler\Swagger\IndexRequestHandler][87]
 * [App\RequestHandler\Swagger\YamlRequestHandler][88]

### Factory

#### Collection

 * [App\Factory\Collection\PetCollectionFactory][100]

#### Model

 * [App\Factory\Model\PetFactory][101]

### Mapping

 * [App\Mapping\MappingConfig][110]

#### Deserialization

 * [App\Mapping\Deserialization\PetCollectionMapping][111]
 * [App\Mapping\Deserialization\PetMapping][112]

#### Orm

 * [App\Mapping\Orm\PetMapping][113]

#### Serialization

 * [App\Mapping\Serialization\PetCollectionMapping][114]
 * [App\Mapping\Serialization\PetMapping][115]

#### Validation

 * [App\Mapping\Validation\PetCollectionMapping][116]
 * [App\Mapping\Validation\PetMapping][117]

##### Constraint

* [App\Mapping\Validation\Constraint\SortConstraint][118]

### Model

 * [App\Model\Pet][140]

### Repository

 * [App\Repository\PetRepository][150]

### ServiceFactory

 * [App\ServiceFactory\ApiHttpServiceFactory][160]
 * [App\ServiceFactory\ConsoleServiceFactory][161]
 * [App\ServiceFactory\RequestHandlerServiceFactory][162]
 * [App\ServiceFactory\DeserializationServiceFactory][163]
 * [App\ServiceFactory\DoctrineServiceFactory][164]
 * [App\ServiceFactory\FactoryServiceFactory][165]
 * [App\ServiceFactory\MiddlewareServiceFactory][166]
 * [App\ServiceFactory\MonologServiceFactory][167]
 * [App\ServiceFactory\NegotiationServiceFactory][168]
 * [App\ServiceFactory\ProxyManagerServiceFactory][169]
 * [App\ServiceFactory\RespositoryServiceFactory][170]
 * [App\ServiceFactory\SerializationServiceFactory][171]
 * [App\ServiceFactory\ValidationServiceFactory][172]

## Copyright

Dominik Zogg 2018

[1]: https://github.com/chubbyphp/chubbyphp-framework

[3]: https://packagist.org/packages/chubbyphp/chubbyphp-api-http
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-config
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-container
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-doctrine-db-service-provider
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-serialization
[12]: https://packagist.org/packages/chubbyphp/chubbyphp-validation
[13]: https://packagist.org/packages/doctrine/orm
[14]: https://packagist.org/packages/monolog/monolog
[15]: https://packagist.org/packages/nikic/fast-route
[16]: https://packagist.org/packages/ocramius/proxy-manager
[17]: https://packagist.org/packages/ramsey/uuid
[18]: https://packagist.org/packages/slim/psr7
[19]: https://packagist.org/packages/swagger-api/swagger-ui
[20]: https://packagist.org/packages/symfony/console

[40]: https://packagist.org/packages/chubbyphp/petstore

[50]: app/ApiHttp/Factory/InvalidParametersFactory.php

[60]: app/Collection/PetCollection.php

[70]: app/Config/DevConfig.php
[71]: app/Config/PhpunitConfig.php
[72]: app/Config/ProdConfig.php

[80]: app/RequestHandler/IndexRequestHandler.php
[81]: app/RequestHandler/PingRequestHandler.php
[82]: app/RequestHandler/Crud/CreateRequestHandler.php
[83]: app/RequestHandler/Crud/DeleteRequestHandler.php
[84]: app/RequestHandler/Crud/ListRequestHandler.php
[85]: app/RequestHandler/Crud/ReadRequestHandler.php
[86]: app/RequestHandler/Crud/UpdateRequestHandler.php
[87]: app/RequestHandler/Swagger/IndexRequestHandler.php
[88]: app/RequestHandler/Swagger/YamlRequestHandler.php

[100]: app/Factory/Collection/PetCollectionFactory.php
[101]: app/Factory/Model/PetFactory.php

[110]: app/Mapping/MappingConfig.php
[111]: app/Mapping/Deserialization/PetCollectionMapping.php
[112]: app/Mapping/Deserialization/PetMapping.php
[113]: app/Mapping/Orm/PetMapping.php
[114]: app/Mapping/Serialization/PetCollectionMapping.php
[115]: app/Mapping/Serialization/PetMapping.php
[116]: app/Mapping/Validation/PetCollectionMapping.php
[117]: app/Mapping/Validation/PetMapping.php
[118]: app/Mapping/Validation/Constraint/SortConstraint.php

[140]: app/Model/Pet.php

[150]: app/Repository/PetRepository.php

[160]: app/ServiceFactory/ApiHttpServiceFactory.php
[161]: app/ServiceFactory/ConsoleServiceFactory.php
[162]: app/ServiceFactory/RequestHandlerServiceFactory.php
[163]: app/ServiceFactory/DeserializationServiceFactory.php
[164]: app/ServiceFactory/DoctrineServiceFactory.php
[165]: app/ServiceFactory/FactoryServiceFactory.php
[166]: app/ServiceFactory/MiddlewareServiceFactory.php
[167]: app/ServiceFactory/MonologServiceFactory.php
[168]: app/ServiceFactory/NegotiationServiceFactory.php
[169]: app/ServiceFactory/ProxyManagerServiceFactory.php
[170]: app/ServiceFactory/RespositoryServiceFactory.php
[171]: app/ServiceFactory/SerializationServiceFactory.php
[172]: app/ServiceFactory/ValidationServiceFactory.php
