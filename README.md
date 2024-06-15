# Sprint 5: api rest con Laravel

Pasos a seguir para poder correr el proyecto:

## Clonar el Proyecto

Primero, clona el proyecto desde GitHub:

    git clone https://github.com/ivanlegranbizarro/sprint5-api-laravel
    cd sprint5-api-laravel

## Instalar Dependencias

Una vez clonado el repositorio, instala las dependencias necesarias ejecutando el siguiente comando:

    composer install

## Generar Llave de encriptación de Laravel

Después de instalar las dependencias, genera las llaves de Laravel con el siguiente comando:

    php artisan key:generate

## Generar Cliente de Passport

Finalmente, genera el cliente de Passport con el siguiente comando:

    php artisan passport:client

### La url de landing del proyecto te redirigirá hacia la documentación interactiva de la API
