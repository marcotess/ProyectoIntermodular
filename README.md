# Herramienta de Gestion de Cursos

Aplicacion web de gestion academica y documental desarrollada como Proyecto Fin de Ciclo para los ciclos DAM/DAW 2025-2026.

El sistema centraliza la gestion de cursos, proyectos academicos, documentos, variantes documentales, revisiones, tareas, notificaciones y comunicacion interna entre usuarios con distintos perfiles.

## Resumen

La aplicacion nace de una necesidad real detectada en una empresa dedicada a la formacion. El objetivo del proyecto es reducir la dispersion de informacion entre correos, carpetas compartidas y seguimiento manual, ofreciendo un unico entorno de trabajo para gestores, docentes y revisores.

## Funcionalidades principales

- autenticacion web y API mediante Laravel Sanctum
- gestion de cursos y PR asociados
- alta de documentos con plantilla y variante inicial automatica
- control de variantes y estados documentales
- asignacion de revisores y docentes
- tareas priorizadas por fechas limite
- notificaciones internas
- chat interno entre usuarios autorizados
- control de acceso por rol y por relacion real con el recurso

## Roles del sistema

- gestor: administra cursos, PR, documentos, plantillas y asignaciones
- docente: consulta los PR y tareas de los cursos en los que participa
- revisor: revisa documentos asignados y participa en el flujo documental

## Arquitectura

El proyecto sigue una arquitectura MVC sobre Laravel, reforzada con una capa de acciones de negocio para separar responsabilidades.

- `app/Models`: entidades y relaciones del dominio
- `app/Http/Controllers`: coordinacion de peticiones web y API
- `app/Actions`: logica de negocio reutilizable
- `resources/views`: interfaz Blade
- `database/migrations`: definicion del esquema de base de datos
- `routes/web.php` y `routes/api.php`: puntos de entrada funcionales

## Stack tecnologico

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- Blade
- JavaScript
- Tailwind CSS
- Vite
- SQLite en desarrollo local y pruebas
- PHPUnit

## Instalacion rapida

### Requisitos

- PHP 8.3 o superior
- Composer
- Node.js y npm
- SQLite

### Pasos

1. Instalar dependencias PHP:

```bash
composer install
```

2. Instalar dependencias frontend:

```bash
npm install
```

3. Crear el archivo de entorno a partir de `.env.example`.

4. Generar la clave de aplicacion:

```bash
php artisan key:generate
```

5. Crear la base de datos SQLite en `database/database.sqlite` si no existe.

6. Ejecutar migraciones:

```bash
php artisan migrate
```

7. Compilar recursos:

```bash
npm run build
```

8. Iniciar la aplicacion:

```bash
php artisan serve
```

## Pruebas

La aplicacion incluye pruebas funcionales para autenticacion, permisos, flujo documental, tareas, notificaciones, API y chat.

Ejecucion:

```bash
php artisan test
```

## Estructura funcional del modelo de datos

El nucleo de la aplicacion se organiza alrededor de esta cadena:

- un curso contiene varios PR
- cada PR contiene varios documentos
- cada documento puede tener varias variantes
- cada variante tiene un estado actual y puede generar historico

Alrededor de ese nucleo se gestionan roles, asignaciones de docentes y revisores, notificaciones y chat interno.

## Documentacion disponible

La carpeta `docs/` incluye la documentacion extensa del proyecto:

- memoria tecnica
- requisitos y objetivos
- arquitectura y diseno
- modelo de datos
- manual de instalacion
- manual de usuario
- plan de pruebas
- guia de defensa y presentacion

## Estado del proyecto

El proyecto es funcional y defendible academicamente, con especial foco en trazabilidad documental, control de acceso, modularidad y validacion automatizada.