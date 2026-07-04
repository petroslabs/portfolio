FROM dunglas/frankenphp:php8.4

# pdo_pgsql : requis par doctrine/doctrine-bundle (DATABASE_URL postgresql://…)
RUN install-php-extensions pdo_pgsql
