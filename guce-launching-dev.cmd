@echo off
docker-compose -f ./infrastructure.dev.yml --env-file ./docker/.env up -d nginx
npm run serve
