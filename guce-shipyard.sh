#!/bin/bash
docker-compose -f ./infrastructure.dev.yml --env-file ./docker/.env $@
