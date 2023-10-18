FROM php:8.1-fpm
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install -j$(nproc) iconv
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN chmod +x /var/www
RUN chmod +x /var
RUN chmod -R 755 /var
WORKDIR /var/www/html
COPY php.ini /usr/local/etc/php/php.ini
