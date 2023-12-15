FROM nginx:mainline-alpine

ENV NGINX_VERSION=1.18.0

RUN apk del nginx \
  nginx-module-geoip \
  nginx-module-image-filter \
  nginx-module-njs \
  nginx-module-xslt \
  && apk add --no-cache --upgrade \
  memcached \
  nginx \
  nginx-mod-http-dav-ext \
  nginx-mod-http-echo \
  nginx-mod-http-fancyindex \
  nginx-mod-http-geoip \
  nginx-mod-http-geoip2 \
  nginx-mod-http-headers-more \
  nginx-mod-http-image-filter \
  nginx-mod-http-nchan \
  # nginx-mod-http-perl \
  nginx-mod-http-redis2 \
  nginx-mod-http-set-misc \
  nginx-mod-http-upload-progress \
  nginx-mod-http-xslt-filter \
  nginx-mod-mail \
  nginx-mod-rtmp \
  nginx-mod-stream \
  nginx-mod-stream-geoip \
  nginx-mod-stream-geoip2 \
  nginx-vim \
  && deluser nginx \
  && adduser -DH -h /var/cache/nginx -s /sbin/nologin -u 1000 nginx \
  && chown -R nginx /var/lib/nginx
