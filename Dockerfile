FROM php:7.3-apache
RUN a2enmod rewrite
RUN usermod --non-unique --uid 1000 www-data
RUN docker-php-ext-install pdo_mysql
RUN apt update
RUN apt install -y dnsutils vim
CMD ["apache2-foreground"]
