FROM php:8.2-apache

# Variables de entorno necesarias para instalación no interactiva
ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# Instalación de dependencias del sistema
RUN apt-get update && apt-get install -y \
    curl gnupg2 lsb-release apt-transport-https ca-certificates software-properties-common \
    unzip git nano zip \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
    unixodbc-dev gcc g++ make autoconf \
    && apt-get clean

# Agregar repositorio oficial de Microsoft para Ubuntu 22.04
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/ubuntu/22.04/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools

# Exportar mssql-tools al PATH (opcional, por si se quiere usar en consola)
ENV PATH="${PATH}:/opt/mssql-tools/bin"

# Instalar extensiones PDO y SQLSRV de PHP
RUN pecl install pdo_sqlsrv sqlsrv && \
    docker-php-ext-enable pdo_sqlsrv sqlsrv && \
    docker-php-ext-install pdo mbstring zip exif pcntl bcmath

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js (v18.x) y npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto Laravel (o montar volumen)
COPY . .

# Instalar dependencias de Laravel e Inertia
RUN composer install --no-interaction --prefer-dist --optimize-autoloader && \
    npm install && npm run build

# Asignar permisos adecuados
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
