@echo off
docker-compose -f ./infrastructure.dev.yml --env-file ./docker/.env %*
