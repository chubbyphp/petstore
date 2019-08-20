# petstore

[![Build Status](https://api.travis-ci.org/chubbyphp/petstore.png?branch=slim)](https://travis-ci.org/chubbyphp/petstore)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/petstore/badges/quality-score.png?b=slim)](https://scrutinizer-ci.com/g/chubbyphp/petstore/?branch=slim)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/petstore/badges/coverage.png?b=slim)](https://scrutinizer-ci.com/g/chubbyphp/petstore/?branch=slim)
[![Total Downloads](https://poser.pugx.org/chubbyphp/petstore/downloads.png)](https://packagist.org/packages/chubbyphp/petstore)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/petstore/d/monthly)](https://packagist.org/packages/chubbyphp/petstore)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/petstore/v/stable.png)](https://packagist.org/packages/chubbyphp/petstore)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/petstore/v/unstable)](https://packagist.org/packages/chubbyphp/petstore)

## Description

A simple skeleton to build api's based on the [slimframework][1].

## Requirements

 * php: ~7.1
 * [chubbyphp/chubbyphp-api-http][3]: ^3.1
 * [chubbyphp/chubbyphp-config][4]: ^1.2
 * [chubbyphp/chubbyphp-deserialization][5]: ^2.11
 * [chubbyphp/chubbyphp-doctrine-db-service-provider][6]: ^1.2.1
 * [chubbyphp/chubbyphp-negotiation][8]: ^1.2.4
 * [chubbyphp/chubbyphp-serialization][9]: ^2.8.1
 * [chubbyphp/chubbyphp-slim-psr15][10]: ^1.1
 * [chubbyphp/chubbyphp-validation][11]: ^3.4
 * [doctrine/orm][12]: ^2.6.3
 * [monolog/monolog][13]: ^1.24
 * [ocramius/proxy-manager][14]: ^2.1.1
 * [ramsey/uuid][15]: ^3.8
 * [slim/slim][16]: ^4.4
 * [slim/psr7][17]: ^0.5
 * [swagger-api/swagger-ui][18]: ^3.23
 * [symfony/console][19]: ^4.3.1

## Environment

### Vagrant

There is a vagrant setup provided ([vagrant-php][2]) as a git submodule.

#### Install

```bash
git submodule update --init -- vagrant-php
```

#### Update

```bash
git submodule update --remote -- vagrant-php
```

#### Run

```bash
cd vagrant-php
vagrant up
vagrant ssh
```

#### Host

https://petstore.development

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/petstore][40].

```bash
composer create-project chubbyphp/petstore myproject "dev-slim"
```

## Setup

### Create database

```sh
bin/console dbal:database:create
```

### Create / Update schema

```sh
bin/console orm:schema-tool:update --dump-sql --force
```

## Structure

### ApiHttp

#### Factory

 * [App\ApiHttp\Factory\InvalidParametersFactory][50]
 * [App\ApiHttp\Factory\ResponseFactory][51]

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

### ServiceProvider

 * [App\ServiceProvider\ApiHttpServiceProvider][160]
 * [App\ServiceProvider\ConsoleServiceProvider][161]
 * [App\ServiceProvider\RequestHandlerServiceProvider][162]
 * [App\ServiceProvider\DeserializationServiceProvider][163]
 * [App\ServiceProvider\DoctrineServiceProvider][164]
 * [App\ServiceProvider\FactoryServiceProvider][165]
 * [App\ServiceProvider\MiddlewareServiceProvider][166]
 * [App\ServiceProvider\MonologServiceProvider][167]
 * [App\ServiceProvider\NegotiationServiceProvider][168]
 * [App\ServiceProvider\ProxyManagerServiceProvider][169]
 * [App\ServiceProvider\RespositoryServiceProvider][170]
 * [App\ServiceProvider\SerializationServiceProvider][171]
 * [App\ServiceProvider\ValidationServiceProvider][172]

## Copyright

Dominik Zogg 2018

[1]: https://www.slimframework.com
[2]: https://github.com/vagrant-php/ubuntu

[3]: https://packagist.org/packages/chubbyphp/chubbyphp-api-http
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-config
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-doctrine-db-service-provider
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-lazy-middleware
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-serialization
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-slim-psr15
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-validation
[12]: https://packagist.org/packages/doctrine/orm
[13]: https://packagist.org/packages/monolog/monolog
[14]: https://packagist.org/packages/ocramius/proxy-manager
[15]: https://packagist.org/packages/ramsey/uuid
[16]: https://packagist.org/packages/slim/slim
[17]: https://packagist.org/packages/slim/psr7
[18]: https://packagist.org/packages/swagger-api/swagger-ui
[19]: https://packagist.org/packages/symfony/console

[40]: https://packagist.org/packages/chubbyphp/petstore

[50]: app/ApiHttp/Factory/InvalidParametersFactory.php
[51]: app/ApiHttp/Factory/ResponseFactory.php

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

[160]: app/ServiceProvider/ApiHttpServiceProvider.php
[161]: app/ServiceProvider/ConsoleServiceProvider.php
[162]: app/ServiceProvider/RequestHandlerServiceProvider.php
[163]: app/ServiceProvider/DeserializationServiceProvider.php
[164]: app/ServiceProvider/DoctrineServiceProvider.php
[165]: app/ServiceProvider/FactoryServiceProvider.php
[166]: app/ServiceProvider/MiddlewareServiceProvider.php
[167]: app/ServiceProvider/MonologServiceProvider.php
[168]: app/ServiceProvider/NegotiationServiceProvider.php
[169]: app/ServiceProvider/ProxyManagerServiceProvider.php
[170]: app/ServiceProvider/RespositoryServiceProvider.php
[171]: app/ServiceProvider/SerializationServiceProvider.php
[172]: app/ServiceProvider/ValidationServiceProvider.php
