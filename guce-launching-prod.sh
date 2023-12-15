#!/bin/bash
docker-compose -f ./infrastructure.prod.yml --env-file ./docker/.env up -d nginx
