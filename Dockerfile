FROM php:8.2-apache

# 🔹 Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 🔹 Extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql

# 🔹 Definir el DocumentRoot a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 🔹 Activar mod_rewrite
RUN a2enmod rewrite

# 🔹 Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN echo "upload_max_filesize = 5M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 6M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
