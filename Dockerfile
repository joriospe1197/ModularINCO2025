FROM php:8.1-cli

# Instala extensiones necesarias y herramientas del sistema, incluyendo Python
RUN apt-get update && apt-get install -y curl unzip git python3 python3-pip \
    && ln -s /usr/bin/python3 /usr/bin/python \
    && docker-php-ext-install mysqli

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establece el directorio de trabajo
WORKDIR /app

# Copia composer.json y composer.lock para aprovechar cache
COPY composer.json composer.lock* /app/
RUN composer install --no-dev --optimize-autoloader

# Copia el archivo requirements.txt e instala dependencias de Python
COPY requirements.txt /app/
RUN pip install --break-system-packages -r requirements.txt

# Copia el resto del proyecto
COPY . /app

# Expone el puerto del servidor PHP embebido
EXPOSE 10000

# Comando para iniciar el servidor PHP apuntando a /public
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
