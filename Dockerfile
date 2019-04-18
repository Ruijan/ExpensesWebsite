FROM php:7.2-cli
COPY . ./
WORKDIR ./

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.6.3

RUN curl -s http://getcomposer.org/installer | php \
  php composer.phar install -n
CMD [ "php", "./index.php" ]
