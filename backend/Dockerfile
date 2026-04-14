FROM php:8.2-fpm

ARG INSTALL_DEV=false

RUN apt-get update && apt-get install -y \
  git \
  unzip \
  libicu-dev \
  libzip-dev \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  libonig-dev \
  libxml2-dev \
  default-mysql-client \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) bcmath intl zip pdo_mysql gd mbstring exif pcntl \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=2000

WORKDIR /var/www/html

# Pre-create storage and bootstrap/cache directories
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
  chown -R www-data:www-data storage bootstrap/cache

COPY composer.json composer.lock ./
RUN if [ "$INSTALL_DEV" = "true" ]; then \
  composer install --no-interaction --no-progress --no-scripts; \
  else \
  composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader; \
  fi

COPY . .
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
