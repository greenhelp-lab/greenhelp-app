FROM php:8.2-apache

# Copia projeto para /var/www/html
COPY . /var/www/html

# Habilita mod_rewrite, se usar rotas
RUN a2enmod rewrite

# Define diretório padrão do apache
WORKDIR /var/www/html