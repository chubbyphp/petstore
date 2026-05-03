# petstore

[![CI](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml/badge.svg?branch=slim)](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=slim)](https://coveralls.io/github/chubbyphp/petstore?branch=slim)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fpetstore%slim)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/petstore/slim)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-petstore&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-petstore)

## Description

A simple skeleton to build api's based on the [slim][1] framework.

## Requirements

 * php: ^8.3
 * [chubbyphp/chubbyphp-api][2]: ^1.1
 * [chubbyphp/chubbyphp-clean-directories][3]: ^1.5.1
 * [chubbyphp/chubbyphp-cors][4]: ^1.7.1
 * [chubbyphp/chubbyphp-decode-encode][5]: ^1.4
 * [chubbyphp/chubbyphp-http-exception][6]: ^1.3.2
 * [chubbyphp/chubbyphp-laminas-config][7]: ^1.5.1
 * [chubbyphp/chubbyphp-laminas-config-doctrine][8]: ^3.1.1
 * [chubbyphp/chubbyphp-laminas-config-factory][9]: ^1.5.1
 * [chubbyphp/chubbyphp-negotiation][10]: ^2.3.1
 * [chubbyphp/chubbyphp-parsing][11]: ^2.5.2
 * [doctrine/orm][12]: ^3.6.2
 * [monolog/monolog][13]: ^3.10
 * [ramsey/uuid][14]: ^4.9.2
 * [slim/psr7][15]: ^1.8
 * [slim/slim][16]: ^4.15.1
 * [symfony/console][17]: ^7.4.7|^8.0.7
 * [symfony/var-exporter][18]: ^7.4|^8.0

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
mkdir -p ~/.config/opencode
[ ! -f ~/.local/share/opencode/auth.json ] && echo '{}' > ~/.config/opencode/tui.json
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
* GET https://localhost/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d
* PUT https://localhost/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d
* DELETE https://localhost/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d

### Database

```sh
psql "postgresql://petstore:4aAUfBjDACcdZxNwJgJ6@localhost:5432/petstore"
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

### ORM

ORM Mapping definitions.

 * [App\Pet\Orm][100]

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
curl -XPUT 'https://localhost:9200/_plugins/_ism/policies/logstash-policy' \
    -u 'admin:98T722Eqw99oqFCSJCnB' \
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

[1]: https://github.com/slimphp/slim

[2]: https://packagist.org/packages/chubbyphp/chubbyphp-api
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-clean-directories
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-decode-encode
[6]: https://packagist.org/packages/chubbyphp/chubbyphp/chubbyphp-http-exception
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-parsing
[12]: https://packagist.org/packages/doctrine/orm
[13]: https://packagist.org/packages/monolog/monolog
[14]: https://packagist.org/packages/ramsey/uuid
[15]: https://packagist.org/packages/slim/psr7
[16]: https://packagist.org/packages/slim/slim
[17]: https://packagist.org/packages/symfony/console
[18]: https://packagist.org/packages/symfony/var-exporter

[60]: src/Pet/Collection

[70]: src/Pet/Dto

[90]: src/Pet/Model

[100]: src/Pet/Orm

[110]: src/Pet/Parsing

[120]: src/Pet/Repository

[130]: src/Core/RequestHandler

[140]: src/Core/ServiceFactory
[141]: src/Pet/ServiceFactory
