# Development image: PHP CLI with the same version and extensions as docker/Dockerfile,
# plus Composer. Node/Yarn and the non-root `vscode` user come from devcontainer features.
FROM php:8.5.12-cli

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        curl \
        git \
        sqlite3 \
        unzip \
        zip \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev \
        libzip-dev && \
    docker-php-ext-configure gd --with-jpeg --with-freetype && \
    docker-php-ext-install pdo_mysql gd zip && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.10.3 /usr/bin/composer /usr/bin/composer
