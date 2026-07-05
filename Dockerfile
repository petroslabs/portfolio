FROM dunglas/frankenphp:php8.4

# pdo_pgsql : requis par doctrine/doctrine-bundle (DATABASE_URL postgresql://…)
RUN install-php-extensions pdo_pgsql

# Composer : absent de l'image de base, requis pour composer install en prod
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
