# petstore

[![CI](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml/badge.svg?branch=chubbyphp)](https://github.com/chubbyphp/petstore/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fpetstore%2Fchubbyphp)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/petstore/chubbyphp)

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
 * [chubbyphp/chubbyphp-oidc][20]: ^1.0
 * [chubbyphp/chubbyphp-parsing][13]: ^3.0
 * [doctrine/orm][14]: ^3.6.8
 * [guzzlehttp/guzzle][21]: ^7.10
 * [monolog/monolog][15]: ^3.10
 * [ramsey/uuid][16]: ^4.9.3
 * [slim/psr7][17]: ^1.8
 * [symfony/console][18]: ^7.4.16|^8.1.4
 * [symfony/var-exporter][19]: ^7.4.16|^8.1.4

## Environment

Add the following environment variable to your system, for example within `~/.bashrc` or  `~/.zshrc`:

```sh
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
```

### Mount points

Creates every file which gets mounted into the php container (shell rc/history, git, ssh, npm and the coding
agent auth/settings files) without overwriting existing ones. Adjust the seeded settings files afterwards to your
liking, they stay on the host and get mounted.

```sh
./setup-mount-points.sh
```

### Coding agents

The following coding agents (harnesses) are preinstalled within the php container, their auth and settings files
get mounted from the host (see `docker-compose.yml`):

 * [Claude Code](https://www.npmjs.com/package/@anthropic-ai/claude-code): `~/.claude.json`, `~/.claude/.credentials.json`, `~/.claude/settings.json`
 * [Codex](https://www.npmjs.com/package/@openai/codex): `~/.codex/auth.json`, `~/.codex/config.toml`
 * [Opencode](https://www.npmjs.com/package/opencode-ai): `~/.config/opencode/opencode.jsonc`, `~/.config/opencode/tui.json`, `~/.local/share/opencode/auth.json`
 * [PI](https://www.npmjs.com/package/@earendil-works/pi-coding-agent) incl. [pi-llama](https://github.com/huggingface/pi-llama): `~/.pi/agent/auth.json`

#### llama.cpp

PI can run against a local model via [pi-llama](https://github.com/huggingface/pi-llama), start a
[llama.cpp](https://llama.app/) server on the host, for example:

```sh
llama-server \
    -hf lmstudio-community/Qwen3.6-35B-A3B-GGUF:Q4_K_M \
    -c 32768 \
    -ngl 999 \
    --flash-attn on \
    --host 0.0.0.0 \
    --port 9931
```

### Docker

```sh
docker compose up -d
docker compose exec php bash
```

## Setup

```sh
composer install
composer setup:dev
```

## Urls

* GET https://localhost/ping
* GET https://localhost/swagger (https://localhost/openapi)

### Pet (oidc protected)

* GET https://localhost/api/pets?sort[name]=asc
* POST https://localhost/api/pets
* GET https://localhost/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d
* PUT https://localhost/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d
* DELETE https://localhost/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d

### Database

```sh
psql "postgresql://petstore:4aAUfBjDACcdZxNwJgJ6@localhost:5432/petstore"
```

## Oidc (keycloak)

All routes below `/api` are protected by [chubbyphp/chubbyphp-oidc][20], only `/ping` and `/openapi` are public.
The keycloak container acts as the identity provider,
the realm `petstore` gets imported from `docker/development/keycloak/import/petstore-realm.json` on startup
(delete and recreate the keycloak container to reimport after changes) and contains two users:

* `john.doe` (password: `johndoe1234`): a regular end user, meant to log in via the browser based frontend
  (`petstore-frontend` client, see below).
* `petstore` (password: `GBanBPatEBRZ7hf7cAxKn8Ptt`): a technical user for requesting tokens via password grant
  while testing (see the curl example below).

and two clients:

* `petstore-frontend`: public client for a separate (browser based) frontend codebase, which authenticates against
  keycloak via authorization code flow + PKCE (S256) and sends the resulting access token as
  `Authorization: Bearer <token>` header to this api. The cors setup allows the `Authorization` header for
  localhost origins in development.
* `petstore` (secret: `5FbFAgTAWyVAWSQtDPqCLZzY`): confidential client for backend integrations and for requesting
  tokens via password grant while testing.

Both clients use an audience mapper, so that the access token contains `aud: petstore`, which this api requires.

Admin console: http://keycloak:8080 (admin / TCUJyCbLtLbBc4eXYYzD9ecm). Keycloak is configured with the fixed
hostname `keycloak`, so that the issuer claim is always `http://keycloak:8080/realms/petstore`; requests via
`http://localhost:8080` get redirected to that hostname. Add `127.0.0.1 keycloak` to `/etc/hosts` on the host to use
the admin console or to request tokens from the host:

```sh
ACCESS_TOKEN=$(curl -s http://keycloak:8080/realms/petstore/protocol/openid-connect/token \
  -d 'grant_type=password' \
  -d 'client_id=petstore' \
  -d 'client_secret=5FbFAgTAWyVAWSQtDPqCLZzY' \
  -d 'username=petstore' \
  -d 'password=GBanBPatEBRZ7hf7cAxKn8Ptt' | sed -E 's/.*"access_token":"([^"]+)".*/\1/')

curl --insecure -H "Authorization: Bearer ${ACCESS_TOKEN}" -H 'Accept: application/json' https://localhost/api/pets
```

The integration tests run against keycloak as well (no auth mocking): `tests/Helper/AuthHelper.php` waits for the
discovery endpoint of `OIDC_ISSUER` to be reachable and requests tokens via password grant with the `petstore`
client and user. Within the php container keycloak is reachable as `keycloak`, in ci a keycloak container gets
started and published on the docker bridge gateway (see `.github/workflows/ci.yml`).

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
[14]: https://packagist.org/packages/doctrine/orm
[15]: https://packagist.org/packages/monolog/monolog
[16]: https://packagist.org/packages/ramsey/uuid
[17]: https://packagist.org/packages/slim/psr7
[18]: https://packagist.org/packages/symfony/console
[19]: https://packagist.org/packages/symfony/var-exporter
[20]: https://packagist.org/packages/chubbyphp/chubbyphp-oidc
[21]: https://packagist.org/packages/guzzlehttp/guzzle

[60]: src/Pet/Collection

[70]: src/Pet/Dto

[90]: src/Pet/Model

[100]: src/Pet/Orm

[110]: src/Pet/Parsing

[120]: src/Pet/Repository

[130]: src/Core/RequestHandler

[140]: src/Core/ServiceFactory
[141]: src/Pet/ServiceFactory
