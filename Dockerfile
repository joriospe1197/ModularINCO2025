FROM php:8.1-cli

# Instala extensiones necesarias y otras herramientas
RUN apt-get update && apt-get install -y curl unzip git \
    && docker-php-ext-install mysqli

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establece directorio de trabajo
WORKDIR /app

# Copia archivos composer para aprovechar cache de Docker
COPY composer.json composer.lock* /app/

# Instala dependencias de PHP con Composer
RUN composer install --no-dev --optimize-autoloader

# Copia el resto del proyecto
COPY . /app

# Expone el puerto del servidor embebido
EXPOSE 10000

# Comando para iniciar el servidor PHP embebido apuntando a /public
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
