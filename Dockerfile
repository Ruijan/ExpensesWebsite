FROM php:7.2-cli
COPY . ./
WORKDIR ./
RUN apt-get update && apt-get install -y \
  curl
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.6.3

RUN curl -s http://getcomposer.org/installer | php \
  && docker-php-ext-install mysqli \
  && docker-php-ext-enable mysqli \
  && php composer.phar install -n
CMD [ "php", "./index.php" ]
