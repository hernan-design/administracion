# Administrador TuProyecto

Panel de control y administración backoffice para la gestión de usuarios, roles, instituciones, convocatorias, proyectos, finanzas, métricas y reportes del ecosistema TuProyecto.

---

## 🚀 Stack Tecnológico Actualizado

### Backend
* **Lenguaje:** [PHP 8.4](https://www.php.net/)
* **Framework:** [Laravel 13.x](https://laravel.com/)
* **Manejador de Paquetes:** [Composer 2.x](https://getcomposer.org/)
* **Bases de Datos:**
  * **PostgreSQL:** Base de datos relacional transaccional (usuarios, roles, instituciones, auditoría).
  * **MongoDB:** Base de datos documental NoSQL (`mongodb/laravel-mongodb`, extensión PECL `mongodb`).
* **Seguridad y Control de Accesos:**
  * `santigarcor/laratrust`: Gestión de roles y permisos (RBAC).
  * `tymon/jwt-auth`: Autenticación API mediante tokens JWT.
* **Librerías y Servicios Clave:**
  * `yajra/laravel-datatables-oracle`: Integración de DataTables con paginación y búsqueda server-side.
  * `maatwebsite/excel` & `phpoffice/phpspreadsheet`: Importación y exportación de reportes masivos en Excel.
  * `barryvdh/laravel-dompdf`: Generación de certificados y documentos en PDF.
  * `algolia/algoliasearch-client-php` (^4.0): Búsquedas e indexación rápida.
  * `spatie/laravel-activitylog`: Auditoría completa y trazabilidad de acciones administrativas.
  * `laravel/cashier`: Gestión de cobros y suscripciones.
  * `spatie/laravel-google-calendar`: Integración con Google Calendar.
* **Testing:** PHPUnit 13.x / Mockery 1.6

### Frontend
* **Empaquetador / Bundler:** [Vite 5.x](https://vitejs.dev/) con `laravel-vite-plugin` y `@vitejs/plugin-react` *(modernizado desde Gulp / Elixir / Bower)*
* **Librería UI:** [React 18.2](https://react.dev/) + React DOM
* **Tablas Interactivas:** DataTables.net 1.13+ (Responsive, Buttons, FixedHeader y KeyTable)
* **Gráficas y Dashboards:** Apache ECharts (^5.4)
* **Editor WYSIWYG:** Summernote + React Summernote + Summernote Cleaner
* **Estilos:** Sass (Dart Sass `^1.72`), Bootstrap Sass 3.4, Bourbon 7.3 y tema administrativo Gentelella
* **Entorno JS:** Node.js 18.x / 20.x LTS

---

## 📋 Requisitos del Entorno

1. **PHP >= 8.4** con extensiones:
   * `pdo_pgsql`, `pgsql`
   * `mongodb` (vía PECL)
   * `gd` (con soporte Freetype y JPEG)
   * `zip`, `soap`, `bcmath`, `exif`, `calendar`
2. **Composer >= 2.2**
3. **Node.js >= 18.x o 20.x** y **npm >= 9.x**
4. **PostgreSQL >= 14**
5. **MongoDB >= 5.0 / 6.0 / 7.0**

---

## ⚙️ Instalación y Puesta en Marcha

### 1. Clonar el Repositorio
```bash
# Vía HTTPS
git clone https://github.com/hernan-design/administracion.git administrator
cd administrator

# O vía SSH
git clone git@github.com:hernan-design/administracion.git administrator
cd administrator
```

### 2. Configurar Variables de Entorno
Copiar el archivo de entorno y configurar los accesos de base de datos y servicios:
```bash
cp .env.example .env
```

Parámetros clave en `.env`:
```env
APP_NAME=Administrator
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8001

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tuproyecto_db
DB_USERNAME=postgres
DB_PASSWORD=secret

MONGO_DB_HOST=127.0.0.1
MONGO_DB_PORT=27017
MONGO_DB_DATABASE=tuproyecto_mongo
MONGO_DB_USERNAME=
MONGO_DB_PASSWORD=
```

### 3. Instalación de Dependencias Backend
```bash
composer install
```

### 4. Generación de Claves
```bash
php artisan key:generate
php artisan jwt:secret
```

### 5. Migraciones, Roles y Permisos
```bash
php artisan migrate
# Ejecutar seeders para roles y usuario administrador inicial
php artisan db:seed
```

### 6. Instalación de Dependencias Frontend
```bash
npm install
```

---

## 💻 Desarrollo y Compilación Frontend (Vite)

La gestión y compilación de scripts y hojas de estilo se realiza a través de **Vite**:

* **Modo Desarrollo (HMR y recarga en caliente):**
  ```bash
  npm run dev
  ```
  Inicia el servidor Vite sirviendo los assets (`resources/assets/sass/` y `resources/assets/js/`) en tiempo real.

* **Modo Producción (Compilación final):**
  ```bash
  npm run build
  ```
  Genera los bundles de producción optimizados en `public/build/` con el mapa de manifiesto para Laravel.

---

## 🖥️ Ejecución del Servidor Backend

### Desarrollo Local con Artisan:
```bash
php artisan serve --port=8001
```
El panel estará disponible en `http://localhost:8001`.

### Servidor Web Apache:
Configurar un VirtualHost apuntando al directorio `public/` de `administrator` con soporte para `mod_rewrite`.

### Vía Docker:
```bash
docker build -t tuproyecto-administrator:latest .
docker run -d -p 8081:80 --name tuproyecto_administrator_app tuproyecto-administrator:latest
```

---

## 🔄 Proceso para Actualizar Master / Despliegue en Local o Servidor

Procedimiento paso a paso para actualizar la rama de trabajo o desplegar cambios:

1. **Obtener los últimos cambios:**
   ```bash
   git pull origin master
   ```
2. **Revisar conflictos y directorio público:**
   Verificar que no existan conflictos en archivos de configuración ni en el directorio `public/build`.
3. **Limpiar cachés del framework:**
   ```bash
   rm -rf bootstrap/cache/*
   ```
4. **Actualizar dependencias de PHP:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
5. **Regenerar mapeo de clases y caché de configuración:**
   ```bash
   composer dump-autoload
   php artisan config:cache
   php artisan route:cache
   php artisan cache:clear
   ```
6. **Compilar assets de frontend para producción:**
   ```bash
   npm install
   npm run build
   ```
7. **Ajustar permisos de almacenamiento y caché:**
   ```bash
   sudo chmod -R 775 storage bootstrap/cache
   ```
8. **Verificación final:**
   Probar inicio de sesión, navegación de paneles, filtros de DataTables y cierre de sesión.

---

## 📁 Estructura del Proyecto

* `app/`:
  * `Models/`: Modelos Eloquent de PostgreSQL y colecciones de MongoDB.
  * `Http/Controllers/`: Controladores de paneles, reportes, usuarios y APIs.
  * `Classes/` & `Custom/`: Lógica auxiliar y servicios especializados.
* `resources/assets/`:
  * `js/`: Scripts de inicialización, componentes React (`main.jsx`), integración de DataTables, gráficos ECharts y editores Summernote.
  * `sass/`: Hojas de estilo Sass (`app.scss`, `vendor.scss`, personalización Gentelella).
* `resources/views/`: Vistas Blade del panel administrativo y layouts base.
* `routes/`: Rutas web y de API del panel administrativo.
* `public/build/`: Assets compilados por Vite listos para distribución.
* `vite.config.mjs`: Configuración de Vite adaptada a Laravel y compilación de React/Sass.