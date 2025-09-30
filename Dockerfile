FROM php:8.1-cli

# Instala extensiones necesarias
RUN docker-php-ext-install mysqli

# Establece directorio de trabajo
WORKDIR /app

# Copia todo el contenido del proyecto
COPY . /app

# Expone el puerto donde correrá el servidor PHP embebido
EXPOSE 10000

# Inicia el servidor embebido, apuntando a la carpeta pública
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
