FROM php:8.2-apache

# Instala extensões
RUN docker-php-ext-install pdo pdo_mysql

# Copia o projeto
COPY . /var/www/html

# Permissões simples (ambiente dev)
RUN chmod -R 777 /var/www/html

# Habilita mod_rewrite
RUN a2enmod rewrite

# Porta exposta
EXPOSE 8080

# Apache roda por padrão no foreground 
CMD ["apache2-foreground"]