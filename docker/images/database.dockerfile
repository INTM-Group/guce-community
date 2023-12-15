FROM mariadb:10.5

RUN deluser mysql \
  && adduser --system --disabled-password --no-create-home --home /home/mysql --shell /bin/sh --uid 1000 mysql
