FROM php:7.4-fpm

# Instalando dependências e extensões do PHP
RUN apt update
RUN apt install -y libxml2 libxml2-dev libpng-dev libjpeg-dev unzip git
RUN docker-php-ext-install mysqli pdo_mysql xml json gd

# Instalando Composer
RUN curl "https://getcomposer.org/installer" > composer-setup.php && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Configurando timezone do container
RUN ln -fs /usr/share/zoneinfo/America/Sao_Paulo /etc/timezone
