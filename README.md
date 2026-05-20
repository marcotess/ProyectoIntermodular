# Proyecto Fin de Ciclo 2025-2026

## Gestion de documentacion academica para cursos, PR y revisiones

Este repositorio contiene el desarrollo del Proyecto Fin de Ciclo de DAM/DAW del curso 2025-2026. La aplicacion implementa una plataforma web para centralizar la gestion de cursos, PR, documentos, plantillas, variantes, revisiones y notificaciones dentro de un entorno academico.

El objetivo del sistema es mejorar el seguimiento del trabajo documental asociado a cada curso y a cada PR, reducir tareas manuales de coordinacion y aportar trazabilidad sobre el estado de los materiales, los revisores asignados y la evolucion de cada variante generada.

## Resumen del proyecto

La aplicacion permite:

- autenticar usuarios y segmentar el acceso por roles;
- consultar los cursos asignados a cada usuario;
- crear y gestionar PR por curso;
- registrar documentos por PR con plantilla asociada y tema cuando procede;
- generar variantes versionadas de cada documento;
- asignar revisores y docentes responsables;
- controlar estados de revision y mantener historico;
- emitir notificaciones cuando cambia el estado de una variante.

## Contexto academico

Este proyecto se presenta como Trabajo de Fin de Ciclo y se ha orientado a un escenario academico realista. En lugar de plantear una jerarquia empresarial de jefe-empleado, la aplicacion se documenta y estructura alrededor de perfiles funcionales propios del dominio educativo:

- gestor: administra cursos, PR, documentos y asignaciones;
- docente: participa en el seguimiento de los PR asociados a sus cursos;
- revisor: valida documentos y colabora en el control de calidad del material.

Este enfoque encaja mejor con una defensa academica porque permite justificar requisitos, roles y flujo de trabajo desde una necesidad organizativa concreta y verificable.

## Tecnologias utilizadas

- Backend: PHP 8.3 y Laravel 13.
- Autenticacion API: Laravel Sanctum.
- Base de datos: motor relacional compatible con migraciones de Laravel.
- Frontend: Blade, JavaScript, Vite y Tailwind CSS.
- Pruebas: PHPUnit 12.
- Herramientas de calidad: Laravel Pint.

## Motivos de eleccion

Laravel aporta una estructura MVC clara, sistema de rutas, ORM, validacion, colas, autenticacion y migraciones, lo que permite desarrollar un proyecto de alcance medio con una base mantenible y bien documentada. Sanctum simplifica la emision de tokens para el consumo de la API. Vite y Tailwind facilitan una interfaz moderna con tiempos de desarrollo reducidos. PHPUnit y Pint cubren la base de verificacion y calidad del codigo.

## Arquitectura de la solucion

La aplicacion sigue una arquitectura web en capas sobre Laravel:

- capa de presentacion: vistas Blade para la interfaz web y endpoints JSON para consumo desde frontend;
- capa de control: controladores HTTP responsables de autenticacion, navegacion y operaciones de negocio;
- capa de negocio: acciones en app/Actions para encapsular casos de uso como creacion de documentos, asignacion de revisores o actualizacion de estados;
- capa de persistencia: modelos Eloquent y migraciones para cursos, PR, documentos, variantes, roles, plantillas, estados e historicos;
- capa transversal: middleware, notificaciones y registro de actividad.

## Modelo funcional

Las entidades principales del sistema son:

- Course: representa una unidad academica o curso.
- PR: agrupa el trabajo documental asociado a un curso y su fase de desarrollo.
- Document: define un documento concreto dentro de un PR.
- DocumentVariant: almacena las versiones o entregas de un documento.
- DocumentStatus y DocumentStatusHistory: gestionan estado actual e historico.
- Plantilla: vincula prefijos y configuracion documental.
- User y Role: controlan autenticacion y permisos.
- Notificacion: registra avisos derivados de cambios relevantes.

## Flujo principal de uso

1. El usuario inicia sesion y el sistema determina los cursos accesibles segun su rol.
2. Desde un curso se consultan los PR disponibles y su fase actual.
3. En cada PR se crean documentos y se asocian plantillas, tema y revisores.
4. Cada documento puede generar variantes versionadas con su estado de revision.
5. Los cambios de estado desencadenan notificaciones para mantener la coordinacion.

## Requisitos de ejecucion

- PHP 8.3 o superior.
- Composer.
- Node.js y npm.
- Base de datos compatible con Laravel.

## Instalacion

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Si se quiere trabajar en desarrollo con todos los servicios locales:

```bash
composer run dev
```

## Comandos utiles

```bash
php artisan serve
php artisan test
./vendor/bin/pint
```

## Estructura relevante del repositorio

```text
app/
	Actions/            Casos de uso de negocio
	Http/Controllers/   Controladores web y API
	Http/Middleware/    Control de acceso y trazabilidad
	Models/             Modelos de dominio
	Mail/               Correos de notificacion
database/
	migrations/         Esquema y evolucion del modelo de datos
resources/
	views/              Interfaz Blade
routes/
	web.php             Rutas web
	api.php             Rutas API protegidas con Sanctum
tests/
	Unit/               Pruebas unitarias
```

## Estado actual

El proyecto sigue en desarrollo y la documentacion final en PDF todavia debe completarse conforme a la rubrica del PFC. Este README actua como base tecnica y narrativa para la memoria, y deja definidos el contexto academico, la arquitectura, el alcance funcional y la justificacion tecnologica.

## Correspondencia con la rubrica del PFC

Este repositorio ya ofrece material reutilizable para varios apartados de la memoria:

- Introduccion: contexto del problema, objetivo y usuarios.
- Tecnologias utilizadas: stack y motivos de eleccion.
- Arquitectura: organizacion por capas y responsabilidades.
- Modelo de datos: entidades principales y relaciones.
- Codificacion: validaciones, middleware, control de acceso y acciones.
- Manuales: instalacion, puesta en marcha y flujo general.

## Lineas de mejora para la memoria final

- anadir diagramas UML de clases, secuencia y casos de uso;
- documentar captacion de requisitos e historias de usuario;
- ampliar el plan de pruebas con ejemplos ejecutables;
- incorporar estudio de viabilidad economica, tecnica y de riesgos;
- cerrar conclusiones y mejoras futuras con comparativa entre plan inicial y resultado real.
