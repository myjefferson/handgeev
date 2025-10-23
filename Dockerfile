# Base image PHP-FPM
FROM php:8.4-rc-fpm

# Argumentos para o usuário
ARG user=handgeev
ARG uid=1000

# Instalar dependências do sistema e ferramentas necessárias
RUN apt-get update && apt-get install -y \
    supervisor \
    procps \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    gnupg \
    lsb-release \
    libpq-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Node.js e NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Instalar extensões PHP necessárias para Laravel
RUN docker-php-ext-install pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd sockets zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Redis PHP extension
RUN pecl install -o -f redis && docker-php-ext-enable redis

# Criar usuário para rodar a aplicação
RUN useradd -u $uid -G www-data,root -m -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Cria e dá permissão nos diretórios de log/run do Supervisor
RUN mkdir -p /var/log/supervisor /var/run/supervisor /run/php \
    && chown -R $user:$user /var/log/supervisor /var/run/supervisor /run/php

# Copiar configuração do Supervisor para conf.d
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar código do Laravel para a pasta do servidor
COPY . /var/www

# Ajustar permissões do Laravel (pasta storage e bootstrap/cache)
RUN chown -R $user:www-data /var/www/storage /var/www/bootstrap/cache

# Copiar o entrypoint e ajustar permissões
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expor portas HTTP e WebSocket
EXPOSE 9000

RUN chown -R $user:www-data /var/www/public

# Trocar para usuário não-root para segurança
USER handgeev

# Definir entrada do container para rodar o Supervisor corretamente
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]