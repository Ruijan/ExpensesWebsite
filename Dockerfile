FROM php:7.2-cli
COPY . ./
WORKDIR ./
RUN docker pull composer/composer \
  php composer.phar install -n
CMD [ "php", "./index.php" ]
