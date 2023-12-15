#!/bin/bash

set -e

cd $(dirname $(realpath $0))

CONTAINER_BK_FILE="/docker-entrypoint-initdb.d/bk-$(date +%Y%m%d%H%M).sql"

docker-compose --env-file ./docker/.env exec -T db \
  mysqldump --add-drop-table --add-drop-trigger -i -a --skip-extended-insert -f -n --password=toor \
  --result-file=$CONTAINER_BK_FILE --dump-date \
  guce

echo "Bakup: $CONTAINER_BK_FILE "

cd ./docker/storage/database/mariadb/init/

BK_LIST=(./*.sql)

if [ ${#BK_LIST[@]} -gt 7 ]
then
  rm -f $(ls -t | tail -1)
fi

