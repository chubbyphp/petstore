#!/bin/bash

bin/console dbal:database:drop --if-exists --force --env=dev
bin/console dbal:database:create --env=dev
bin/console orm:schema-tool:create --env=dev
bin/console orm:validate-schema --env=dev

bin/console config:clean-directories cache log
