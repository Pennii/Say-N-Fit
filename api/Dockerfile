FROM php:apache

RUN docker-php-ext-install pdo_mysql

# Copiar el .conf a apache
COPY ./default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

#crear los certificados
RUN openssl req -nodes -x509 -newkey rsa:2048 \
    -keyout /etc/ssl/private/say_n_fit_api.key \
    -out /etc/ssl/certs/say_n_fit_api.crt \
    -days 365 \
    -subj "/CN=say_n_fit_api"

# Habilitar ssl y el sitio default
RUN a2enmod ssl && \
    a2enmod rewrite && \
    a2ensite default-ssl

# Copiar mis vistas a la ruta de apache
COPY ./ /var/www/html/