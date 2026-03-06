# Usamos la imagen oficial de PHP
FROM php:8.2-cli

# Instalamos las dependencias necesarias del sistema y el driver de PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Traemos Composer para instalar las dependencias de Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Nos colocamos en la carpeta /app dentro del contenedor
WORKDIR /app

# Copiamos todos los archivos de tu proyecto al contenedor
COPY . .

# Instalamos los paquetes de Laravel
RUN composer install --no-dev --optimize-autoloader

# Damos permisos a las carpetas que Laravel necesita para escribir (caché, logs)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Exponemos el puerto que Render nos asigne
EXPOSE $PORT

# El comando que arranca tu API cuando Render encienda la máquina
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}