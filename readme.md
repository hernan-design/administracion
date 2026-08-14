## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Back-end

Ejecutar desde la terminal (consola) en la ruta del proyecto **-- composer update --**.

### Front-end

1. Image Uploader Crop:

Ejecutar desde la terminal (consola) en la ruta del proyecto **-- bower install --** (se instala las dependencias para el front-end en la ruta **-- /administrador/resources/assets/libs**).

2. Pulse Editor React:

Ejecutar desde la terminal (consola) en la ruta del proyecto **-- npm install --** (se instala las dependencias para el front-end en la ruta **-- /administrador/node_modules --**).



------------

#### Proceso para actualizar master desde nuestro pruyecto instalado en local

1. Luego de hacer `git pull`:
2. De la carpeta public se debe revisar rev-manifest y solucionar conflictos <br> en tal caso. Se solucionan los incidentes de todos los otros archivos fuera de <br> public, si los hay.
3. Se ejecutan los siguientes comandos Git:
4.     rm -rf composer.lock
5.     rm -rf boostrap/cache/*
6.     composer install
7.     php artisan config:cache
8.     composer dump-autoload
9.     php artisan cache:clear
10.     gulp --production
11.     sudo chmod -R 777 storage/*
12. Probar el proyecto, login, logout, algunas funcionalidades.