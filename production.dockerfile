FROM php:8.0-apache

COPY ./docker/php/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf

# Instala dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    vim \
    libxml2-dev \
    zlib1g-dev \
    libpng-dev 


# Id do usuário
ARG USER_ID=1000

# Copiando scripts e config necessários para dentro da imagem.
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copia o diretório da aplicação para o container
COPY ./src /var/www/html

RUN a2enmod rewrite
RUN a2enmod headers

WORKDIR /var/www/html

USER www-data

EXPOSE 80

