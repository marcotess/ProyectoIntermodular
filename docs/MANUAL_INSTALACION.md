# Manual de instalacion

## 1. Objetivo del documento

Este manual describe el proceso de instalacion y puesta en marcha de la aplicacion web de gestion de documentacion academica desarrollada para el Proyecto Fin de Ciclo. El objetivo es dejar preparada una instancia funcional en entorno local para poder probar el sistema, revisar el codigo y realizar demostraciones de uso.

## 2. Descripcion general de la aplicacion

La aplicacion permite gestionar cursos, proyectos academicos, documentos, variantes versionadas, plantillas, revisores, docentes, tareas, notificaciones y conversaciones internas entre usuarios. El sistema trabaja con distintos perfiles funcionales:

- gestor
- docente
- revisor

La aplicacion ha sido desarrollada con Laravel y se ejecuta como aplicacion web con interfaz Blade y apoyo de JavaScript para determinadas interacciones de la interfaz.

## 3. Requisitos previos

Para realizar la instalacion correctamente es necesario disponer del siguiente software:

- PHP 8.3 o superior.
- Composer.
- Node.js.
- npm.
- Un navegador web moderno.
- Permisos de lectura y escritura sobre la carpeta del proyecto.

En esta configuracion del proyecto se utiliza SQLite por defecto como base de datos local, por lo que no es necesario instalar un servidor de base de datos adicional si se mantiene esa configuracion.

## 4. Tecnologias implicadas en la puesta en marcha

Las tecnologias principales relacionadas con la instalacion y ejecucion son las siguientes:

- PHP 8.3.
- Laravel 13.
- Laravel Sanctum.
- Blade.
- JavaScript.
- Vite.
- Tailwind CSS.
- PHPUnit.

## 5. Estructura basica necesaria para la ejecucion

Los elementos principales implicados en la ejecucion son estos:

- `app/`: logica de negocio, modelos, acciones, controladores y middleware.
- `resources/views/`: vistas Blade de la aplicacion.
- `resources/js/`: codigo JavaScript del frontend.
- `database/migrations/`: estructura de base de datos.
- `public/`: punto de entrada web y recursos publicos.
- `routes/web.php`: rutas web de la aplicacion.
- `composer.json`: dependencias PHP y scripts de Composer.
- `package.json`: dependencias frontend y scripts de Vite.

## 6. Instalacion paso a paso

### 6.1. Obtener el proyecto

El proyecto puede obtenerse de dos formas:

1. Descomprimiendo el archivo entregado.
2. Clonando el repositorio en una carpeta local.

Una vez disponible, se debe abrir una terminal en la raiz del proyecto.

### 6.2. Instalar dependencias PHP

Ejecutar:

```bash
composer install
```

Este comando descargara Laravel y el resto de dependencias necesarias para el backend.

### 6.3. Crear el archivo de entorno

Si no existe el archivo `.env`, se puede crear a partir del ejemplo:

```bash
copy .env.example .env
```

En sistemas donde `copy` no este disponible con esa sintaxis, puede hacerse manualmente duplicando el archivo `.env.example` y renombrando la copia como `.env`.

### 6.4. Generar la clave de aplicacion

Ejecutar:

```bash
php artisan key:generate
```

Este paso genera la clave interna utilizada por Laravel para cifrado, sesiones y seguridad general del sistema.

### 6.5. Preparar la base de datos SQLite

La configuracion local del proyecto esta orientada a SQLite. Debe existir el archivo:

```text
database/database.sqlite
```

Si no existe, puede crearse vacio antes de migrar.

### 6.6. Ejecutar migraciones

Ejecutar:

```bash
php artisan migrate
```

Este comando crea toda la estructura del modelo de datos: usuarios, roles, cursos, proyectos, documentos, variantes, historicos, plantillas, notificaciones y chat.

Importante: tras los ultimos cambios del proyecto, tambien se crea la tabla de mensajes de chat y el campo de lectura de mensajes.

### 6.7. Instalar dependencias del frontend

Ejecutar:

```bash
npm install
```

Este paso instala Vite, Tailwind CSS, Axios y el resto de dependencias JavaScript.

### 6.8. Compilar los recursos frontend

Ejecutar:

```bash
npm run build
```

La compilacion genera el `manifest.json` de Vite y los recursos necesarios para que la interfaz cargue correctamente el JavaScript principal y los estilos.

### 6.9. Iniciar la aplicacion

La forma mas simple de arrancar el proyecto es:

```bash
php artisan serve
```

A continuacion, se accede desde navegador a la direccion indicada por Laravel, normalmente:

```text
http://127.0.0.1:8000
```

## 7. Alternativa de desarrollo con varios servicios

El proyecto incluye un script de desarrollo en Composer:

```bash
composer run dev
```

Este script lanza varios procesos a la vez:

- servidor Laravel
- escucha de cola
- visor de logs
- servidor Vite en modo desarrollo

Es una opcion util para trabajar durante el desarrollo o durante la preparacion de una demo local.

## 8. Comandos utiles

### 8.1. Ejecutar pruebas

```bash
php artisan test
```

### 8.2. Limpiar configuracion cacheada

```bash
php artisan config:clear
```

### 8.3. Formatear codigo

```bash
./vendor/bin/pint
```

### 8.4. Compilar recursos frontend

```bash
npm run build
```

## 9. Verificacion de la instalacion

La instalacion puede considerarse correcta si se cumplen estas comprobaciones:

1. La aplicacion abre la pantalla de inicio de sesion.
2. Es posible autenticarse con un usuario valido.
3. El perfil carga correctamente.
4. Se muestran cursos accesibles segun el rol del usuario.
5. Las vistas principales cargan sin errores: cursos, proyectos, documentos, tareas, chat y notificaciones.

## 10. Problemas frecuentes y solucion

### 10.1. No existe el archivo `database.sqlite`

Si al migrar aparece un error relacionado con SQLite, debe crearse manualmente el archivo:

```text
database/database.sqlite
```

Despues se vuelve a ejecutar `php artisan migrate`.

### 10.2. No carga el JavaScript del dashboard

Si la interfaz no responde en ciertos botones o interacciones, puede deberse a que falta el build frontend. En ese caso debe ejecutarse:

```bash
npm install
npm run build
```

El proyecto incluye ademas un script de respaldo para algunos comportamientos, pero la situacion ideal es disponer del build generado por Vite.

### 10.3. Error al ejecutar `npm`

Si la terminal no reconoce `npm`, es necesario instalar Node.js o revisar que este correctamente añadido al PATH del sistema.

### 10.4. Error al migrar despues de cambios del chat

Si se descarga una version mas reciente del proyecto y el chat deja de funcionar correctamente, debe ejecutarse otra vez:

```bash
php artisan migrate
```

Esto aplica especialmente a los cambios que añadieron mensajes de chat y seguimiento de lectura.

### 10.5. No aparecen estilos o scripts en produccion local

Debe comprobarse la existencia del archivo:

```text
public/build/manifest.json
```

Si no existe, el build frontend no se ha generado todavia.

## 11. Cierre

Siguiendo estos pasos, la aplicacion queda instalada en local y lista para ser usada en tareas de desarrollo, demostracion o validacion funcional. El procedimiento de instalacion es deliberadamente sencillo para facilitar la puesta en marcha por parte del tribunal o del profesorado revisor del proyecto.
