FROM php:7.3-apache
RUN a2enmod rewrite
RUN usermod --non-unique --uid 1000 www-data
CMD ["apache2-foreground"]
