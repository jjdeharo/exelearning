ARG ARCH=
ARG VERSION=v0.0.0-alpha

FROM ${ARCH}erseco/alpine-php-webserver:3.20.7

LABEL maintainer="INTEF <cedec@educacion.gob.es>"
LABEL org.opencontainers.image.title="eXeLearning"
LABEL org.opencontainers.image.description="eXeLearning Docker Image"
LABEL org.opencontainers.image.version="${VERSION}"
LABEL org.opencontainers.image.licenses="GPL-3.0-or-later"

ENV VERSION=${VERSION} \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US:en \
    nginx_root_directory=/app/public \
    SITE_URL=http://localhost \
    DEBUG=false \
    client_max_body_size=50M \
    post_max_size=50M \
    upload_max_filesize=50M \
    max_input_vars=5000 \
    # Mercure configuration variables
    SERVER_NAME=:80 \
    MERCURE_PUBLISHER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey! \
    MERCURE_PUBLISHER_JWT_ALG=HS256 \
    MERCURE_SUBSCRIBER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey! \
    MERCURE_SUBSCRIBER_JWT_ALG=HS256 \
    USE_FORWARDED_HEADERS=1 \
    XDG_CONFIG_HOME=/mnt/data/mercure/config \
    XDG_DATA_HOME=/mnt/data/mercure/data

# Set working directory
WORKDIR /app

USER root

# Install Composer and required dependencies
RUN apk add --no-cache \
    php83-pdo_mysql \
    php83-pdo_sqlite \
    php83-pdo_pgsql \
    php83-mbstring \
    php83-exif \
    php83-pcntl \
    php83-bcmath \
    php83-gd \
    php83-zip \
    php83-intl \
    php83-xmlwriter \
    php83-ctype \
    php83-fileinfo \
    php83-tokenizer \
    php83-xml \
    php83-simplexml \
    php83-dom \
    php83-session \
    composer && \
# Install xdebug (TODO: consider removing in production environment)
    apk add --no-cache php83-pecl-xdebug; \
    sed -i 's/;zend_extension=xdebug.so/zend_extension=xdebug.so/' /etc/php83/conf.d/50_xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /etc/php83/conf.d/50_xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /etc/php83/conf.d/50_xdebug.ini && \
    echo "xdebug.log=/dev/stdout" >> /etc/php83/conf.d/50_xdebug.ini && \
# This directory is required if debugging with xdebug in Visual Studio Code
# in an attached container (see development environment documentation)
    mkdir /.vscode-server && chmod -R 777 /.vscode-server && \
# Clean APK cache
    rm -rf /var/cache/apk/* && \
# Create /mnt/data directory and change ownership to nobody
    mkdir -p /mnt/data && chown -R nobody:nobody /mnt/data && \
# Create mercure service directory with proper ownership for user 'nobody'
    mkdir -p /etc/service/mercure && chown nobody:nobody /etc/service/mercure && \
# Create /app/vendor directory and change ownership to nobody
    mkdir -p /app/vendor && chown -R nobody:nobody /app/vendor && \
    mkdir -p /app/var/cache /app/var/log && chown -R nobody:nobody /app/var/cache /app/var/log

# nginx configuration for assets, files and iDevices
COPY --chown=nobody assets.conf /etc/nginx/server-conf.d/assets.conf
COPY --chown=nobody idevices.conf /etc/nginx/server-conf.d/idevices.conf
COPY --chown=nobody subdir.conf.template /etc/nginx/server-conf.d/subdir.conf.template

# Copy Mercure binary and configuration from the official container because Mercure is not yet available as an Alpine package
COPY --from=dunglas/mercure:latest /usr/bin/caddy /usr/bin/mercure
COPY --from=dunglas/mercure:latest /etc/caddy/Caddyfile /etc/caddy/Caddyfile
COPY --from=dunglas/mercure:latest /etc/caddy/dev.Caddyfile /etc/caddy/dev.Caddyfile

# Set up Mercure service
COPY mercure.run /etc/service/mercure/run
RUN chmod +x /etc/service/mercure/run && \
    chown nobody:nobody /etc/service/mercure/run
COPY --chown=nobody mercure.conf /etc/nginx/server-conf.d/mercure.conf

USER nobody

# Copy composer.json and composer.lock before the source code to leverage cache
COPY --chown=nobody composer.json composer.lock ./

# Copy Symfony configuration script (e.g., migrations and user setup)
COPY --chown=nobody 02-configure-symfony.sh /docker-entrypoint-init.d/

# Run composer (TODO: consider using --no-dev in production environment)
RUN composer install --optimize-autoloader --no-interaction --no-progress && \
    composer clear-cache

# Copy the rest of the source code
COPY --chown=nobody . .

# Remove the script from /app to avoid duplication
RUN rm /app/02-configure-symfony.sh

HEALTHCHECK --interval=1m --timeout=15s --start-period=1m --retries=3 \
  CMD curl -f http://localhost:8080/healthcheck || exit 1
