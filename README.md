# petstore

[![CI](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml/badge.svg?branch=chubbyphp-mongo)](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp-mongo)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp-mongo)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fpetstore%2Fchubbyphp-mongo)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/petstore/chubbyphp-mongo)

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

A simple skeleton to build api's based on the [chubbyphp-framework][1].

## Requirements

 * php: ^8.3
 * [chubbyphp/chubbyphp-api][2]: ^1.2
 * [chubbyphp/chubbyphp-clean-directories][3]: ^1.5.2
 * [chubbyphp/chubbyphp-cors][4]: ^1.7.2
 * [chubbyphp/chubbyphp-decode-encode][5]: ^1.4.1
 * [chubbyphp/chubbyphp-framework][6]: ^6.0.3
 * [chubbyphp/chubbyphp-framework-router-fastroute][7]: ^2.3.4
 * [chubbyphp/chubbyphp-http-exception][8]: ^1.3.3
 * [chubbyphp/chubbyphp-laminas-config][9]: ^1.5.2
 * [chubbyphp/chubbyphp-laminas-config-doctrine][10]: ^3.1.3
 * [chubbyphp/chubbyphp-laminas-config-factory][11]: ^1.5.2
 * [chubbyphp/chubbyphp-negotiation][12]: ^2.3.2
 * [chubbyphp/chubbyphp-parsing][13]: ^3.0
 * [doctrine/mongodb-odm][14]: ^2.16.3
 * [monolog/monolog][15]: ^3.10
 * [ramsey/uuid][16]: ^4.9.3
 * [slim/psr7][17]: ^1.8
 * [symfony/console][18]: ^7.4.16|^8.1.4

## Environment

Add the following environment variable to your system, for example within `~/.bashrc` or  `~/.zshrc`:

```sh
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
```

### Mount points

#### bash

```sh
touch ~/.bash_docker
touch ~/.bash_history
```

#### zsh

```sh
touch ~/.zsh_docker
touch ~/.zsh_history
```

#### git

```sh
touch ~/.gitconfig
touch ~/.gitignore
```

#### npm

```sh
touch ~/.npmrc
```

#### Coding agents

##### Claude

```sh
if [ ! -f ~/.claude.json ]; then
    cat > ~/.claude.json <<'EOF'
{}
EOF
fi

mkdir -p ~/.claude

if [ ! -f ~/.claude/.credentials.json ]; then
    cat > ~/.claude/.credentials.json <<'EOF'
{}
EOF
fi

if [ ! -f ~/.claude/settings.json ]; then
    cat > ~/.claude/settings.json <<'EOF'
{
    "fileCheckpointingEnabled": false,
    "permissions": {
        "defaultMode": "bypassPermissions"
    },
    "skipDangerousModePermissionPrompt": true,
    "spinnerTipsEnabled": false,
    "switchModelsOnFlag": false,
    "theme": "auto"
}
EOF
fi

chmod 600 \
    ~/.claude/.credentials.json \
    ~/.claude/settings.json
```

##### Codex

```sh
mkdir -p ~/.codex

if [ ! -f ~/.codex/auth.json ]; then
    cat > ~/.codex/auth.json <<'EOF'
{}
EOF
fi

if [ ! -f ~/.codex/config.toml ]; then
    cat > ~/.codex/config.toml <<'EOF'
approval_policy = "never"
sandbox_mode = "danger-full-access"

[notice]
hide_full_access_warning = true
EOF
fi

chmod 600 \
    ~/.codex/auth.json
    ~/.codex/config.toml
```

##### Opencode

```sh
mkdir -p ~/.config/opencode ~/.local/share/opencode

if [ ! -f ~/.config/opencode/opencode.jsonc ]; then
    cat > ~/.config/opencode/opencode.jsonc <<'EOF'
{
    "$schema": "https://opencode.ai/config.json",
    "permission": {
        "*": "allow"
    }
}
EOF
fi

if [ ! -f ~/.config/opencode/tui.json ]; then
    cat > ~/.config/opencode/tui.json <<'EOF'
{
    "$schema": "https://opencode.ai/tui.json",
    "theme": "system",
    "tips": false
}
EOF
fi

if [ ! -f ~/.local/share/opencode/auth.json ]; then
    printf '{}\n' > ~/.local/share/opencode/auth.json
fi

chmod 600 \
    ~/.config/opencode/opencode.jsonc \
    ~/.config/opencode/tui.json \
    ~/.local/share/opencode/auth.json
```

##### PI

```sh
mkdir -p ~/.pi/agent
[ ! -f ~/.pi/agent/auth.json ] && echo '{}' > ~/.pi/agent/auth.json
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

### Dashboard

Before you start, produce at least one error, [produce a 404](https://localhost/api/unknown).

[Create Index Pattern](http://localhost:5601/app/management/opensearch-dashboards/indexPatterns/create)

- Username: admin
- Password: 98T722Eqw99oqFCSJCnB
- Index pattern name: logstash-*
- Time field: @timestamp

[Discover](http://localhost:5601/app/data-explorer/discover)


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
