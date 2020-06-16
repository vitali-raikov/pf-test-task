# We use bitnami image here
FROM bitnami/php-fpm:7.2

RUN apt-get update

# Install Postgre PDO
RUN apt-get install -y libpq-dev

# Enable PHP extension
RUN echo "extension=pgsql.so" >> /opt/bitnami/php/etc/php.ini
RUN echo "extension=pdo_pgsql.so" >> /opt/bitnami/php/etc/php.ini
#RUN echo "clear_env = no" >> /opt/bitnami/php/etc/php-fpm.d/www.conf
RUN sed -i 's,^;clear_env =.*$,clear_env = no,' /opt/bitnami/php/etc/php-fpm.d/www.conf

ENV APP_DIR /var/www/html
WORKDIR ${APP_DIR}
COPY . ${APP_DIR}

EXPOSE 9000
