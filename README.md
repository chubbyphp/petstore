# petstore

[![CI](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml/badge.svg?branch=chubbyphp-mongo)](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp-mongo)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp-mongo)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fpetstore%2Fchubbyphp-mongo)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/petstore/chubbyphp-mongo)

## Description

A simple skeleton to build api's based on the [chubbyphp-framework][1].

## Requirements

 * php: ^8.3
 * [chubbyphp/chubbyphp-api][2]: ^1.0
 * [chubbyphp/chubbyphp-clean-directories][3]: ^1.5.1
 * [chubbyphp/chubbyphp-cors][4]: ^1.7.1
 * [chubbyphp/chubbyphp-decode-encode][5]: ^1.4
 * [chubbyphp/chubbyphp-framework][6]: ^6.0.2
 * [chubbyphp/chubbyphp-framework-router-fastroute][7]: ^2.3.3
 * [chubbyphp/chubbyphp-http-exception][8]: ^1.3.2
 * [chubbyphp/chubbyphp-laminas-config][9]: ^1.5.1
 * [chubbyphp/chubbyphp-laminas-config-doctrine][10]: ^3.1.1
 * [chubbyphp/chubbyphp-laminas-config-factory][11]: ^1.5.1
 * [chubbyphp/chubbyphp-negotiation][12]: ^2.3.1
 * [chubbyphp/chubbyphp-parsing][13]: ^2.1.2
 * [doctrine/mongodb-odm][14]: ^2.15.3
 * [monolog/monolog][15]: ^3.10
 * [ramsey/uuid][16]: ^4.9.2
 * [slim/psr7][17]: ^1.8
 * [symfony/console][18]: ^7.4.3|^8.0.3

## Environment

Add the following environment variable to your system, for example within `~/.bashrc` or  `~/.zshrc`:

```sh
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
```

Make sure all the mount points are given

```sh
touch ~/.bash_docker
touch ~/.bash_history
```

```sh
touch ~/.gitconfig
touch ~/.gitignore
```

```sh
mkdir -p ~/.local/share/opencode
[ ! -f ~/.local/share/opencode/auth.json ] && echo '{}' > ~/.local/share/opencode/auth.json
```

```sh
touch ~/.zsh_docker
touch ~/.zsh_history
```

### Docker

```sh
docker-compose up -d
docker-compose exec php bash
```

## Setup

```sh
composer install
composer setup:dev
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

### Database

```sh
mongosh "mongodb://petstore:4aAUfBjDACcdZxNwJgJ6@localhost:27017/petstore?authMechanism=DEFAULT&authSource=admin"
```

## Structure

### Collection

Collections are sortable, filterable paginated lists of models.

 * [App\Pet\Collection][60]

### Dto

A DTO, or Data Transfer Object, is a simple object used to transport data between software application components.

 * [App\Pet\Dto][70]

### Model

Models, entities, documents what ever fits your purpose the best.

 * [App\Pet\Model][90]

### ODM

ODM Mapping definitions.

 * [App\Pet\Odm][100]

### Parsing

Parses and validates data against predefined schemas, ensuring that incoming data conforms to expected structures and criteria.

 * [App\Pet\Parsing][110]

### Repository

Repositories get data from storages like databases, elasticsearch, redis or whereever your models are stored or cached.

 * [App\Pet\Repository][120]

### RequestHandler

RequestHandler alias Controller, or Controller actions to be more precise.
There is a directory with generic crud controllers. If you like the idea adapt them for your generic use case, if not drop them.
I highly recommend to not extend them.

 * [App\Core\RequestHandler][130]

### ServiceFactory

Service factories are the glue code of the dependeny injection container.

 * [App\Core\ServiceFactory][140]
 * [App\Pet\ServiceFactory][141]

## Opensearch

### Policy to delete logstash formatted indicies after 14 days.

```.sh
curl -XPUT 'https://admin:98T722Eqw99oqFCSJCnB@localhost:9200/_plugins/_ism/policies/logstash-policy' \
    -H 'Content-Type: application/json' \
    -H 'Accept: application/json' \
    -d '{
      "policy": {
        "description": "Logstash",
        "default_state": "hot",
        "states": [
          {
            "name": "hot",
            "actions": [],
            "transitions": [
              {
                "state_name": "delete",
                "conditions": {
                  "min_index_age": "14d"
                }
              }
            ]
          },
          {
            "name": "delete",
            "actions": [
              {
                "delete": {}
              }
            ]
          }
        ],
        "ism_template": {
          "index_patterns" : ["logstash-*"],
          "priority": 100
        }
      }
    }' \
    --insecure
```

## Copyright

2026 Dominik Zogg

[1]: https://github.com/chubbyphp/chubbyphp-framework
[2]: https://packagist.org/packages/chubbyphp/chubbyphp-api
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-clean-directories
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-decode-encode
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-fastroute
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-http-exception
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[12]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[13]: https://packagist.org/packages/chubbyphp/chubbyphp-parsing
[14]: https://packagist.org/packages/doctrine/mongodb-odm
[15]: https://packagist.org/packages/monolog/monolog
[16]: https://packagist.org/packages/ramsey/uuid
[17]: https://packagist.org/packages/slim/psr7
[18]: https://packagist.org/packages/symfony/console

[60]: src/Pet/Collection

[70]: src/Pet/Dto

[90]: src/Pet/Model

[100]: src/Pet/Odm

[110]: src/Pet/Parsing

[120]: src/Pet/Repository

[130]: src/Core/RequestHandler

[140]: src/Core/ServiceFactory
[141]: src/Pet/ServiceFactory
