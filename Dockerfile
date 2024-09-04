# from where I will modify
FROM php:8.2-apache
# update/upgrade via terminal
RUN apt-get update && apt-get upgrade -y
# intall certain extensions to allow the use of a database, and database manager
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo_mysql
# exposes the port 80
EXPOSE 80