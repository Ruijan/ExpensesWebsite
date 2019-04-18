FROM php:7.2-cli
COPY . ./
WORKDIR ./
CMD [ "php", "./index.php" ]
