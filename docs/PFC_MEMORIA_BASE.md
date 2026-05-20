# Proyecto Fin de Ciclo 2025-2026

## Base de memoria para la documentacion final

Este documento sirve como base de trabajo para redactar la memoria final del Proyecto Fin de Ciclo conforme a la rubrica del curso 2025-2026. El contenido esta orientado al proyecto de gestion documental academica desarrollado en este repositorio.

## Portada

Sustituye los campos siguientes antes de exportar a PDF:

- Centro: [nombre y logo del centro]
- Titulo del trabajo: Gestion documental academica para cursos, PR y revisiones
- Tipo de trabajo: Proyecto Fin de Ciclo
- Alumno: [nombre completo]
- Tutor/a: [Luisa Angeles Sanchez Mendez o Manuel Louro Meneses]
- Convocatoria: Junio 2026

## Abstract

### Resumen

Este Proyecto Fin de Ciclo presenta el diseno y desarrollo de una aplicacion web orientada a la gestion documental academica. La solucion permite organizar cursos, PR, documentos, plantillas, variantes, revisiones y notificaciones dentro de un flujo de trabajo centralizado. El sistema incorpora autenticacion, control de acceso por roles, seguimiento del estado de los documentos y trazabilidad de cambios, con el objetivo de mejorar la coordinacion entre los distintos perfiles implicados y reducir el trabajo manual de supervision.

### Abstract

This final project presents the design and development of a web application focused on academic document management. The solution allows the organization of courses, PR records, documents, templates, variants, reviews and notifications within a centralized workflow. The system includes authentication, role-based access control, document status tracking and change traceability, with the aim of improving coordination between the different user profiles and reducing manual supervision tasks.

## Indice recomendado

1. Introduccion
2. Tecnologias utilizadas
3. Arquitectura
4. Analisis de requisitos
5. Diseno estatico
6. Modelo de datos
7. Codificacion
8. Manuales
9. Viabilidad
10. Pruebas de software
11. Conclusiones y mejoras futuras
12. Bibliografia y webgrafia

## 1. Introduccion

El proyecto surge de la necesidad de disponer de una herramienta que permita centralizar y controlar la documentacion generada en un entorno academico. En muchos contextos docentes, la informacion sobre cursos, documentos, revisiones y versiones se gestiona de forma dispersa, lo que dificulta el seguimiento del trabajo y aumenta la probabilidad de errores, duplicidades o perdida de trazabilidad.

La aplicacion propuesta resuelve este problema mediante una plataforma web en la que cada curso puede contener distintos PR, y cada PR puede agrupar varios documentos, plantillas asociadas, revisores, docentes y variantes versionadas. De este modo, el sistema facilita una gestion mas ordenada, auditable y escalable del ciclo de vida documental.

### Perfiles funcionales

- Gestor: administra cursos, PR, plantillas, documentos y asignaciones.
- Docente: consulta y supervisa los PR vinculados a sus cursos.
- Revisor: accede a los documentos que tiene asignados y participa en su validacion.

## 2. Tecnologias utilizadas

### Stack principal

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- Blade
- JavaScript
- Vite
- Tailwind CSS
- PHPUnit 12
- Laravel Pint

### Justificacion tecnica

Laravel se ha elegido por ofrecer una arquitectura MVC consolidada, validacion integrada, ORM, middleware, sistema de rutas, migraciones y facilidades para autenticacion y autorizacion. Sanctum encaja con la necesidad de exponer endpoints protegidos por token. Tailwind y Blade permiten construir una interfaz funcional con rapidez. PHPUnit aporta una base solida para pruebas automatizadas.

## 3. Arquitectura

La aplicacion sigue una arquitectura en capas:

- Presentacion: vistas Blade y respuestas JSON.
- Control: controladores HTTP que coordinan las peticiones.
- Negocio: acciones especializadas en app/Actions.
- Persistencia: modelos Eloquent y migraciones.
- Infraestructura transversal: middleware, notificaciones y registro de actividad.

### Evidencias del repositorio

- Controladores: app/Http/Controllers
- Acciones: app/Actions
- Modelos: app/Models
- Middleware: app/Http/Middleware
- Rutas: routes/web.php y routes/api.php

## 4. Analisis de requisitos

### Objetivo general

Desarrollar una aplicacion web que permita gestionar de forma centralizada la documentacion de cursos y PR, manteniendo control de versiones, asignacion de revisores, estados y notificaciones.

### Requisitos funcionales

- El sistema debe autenticar usuarios.
- El sistema debe restringir accesos segun rol.
- El usuario debe poder consultar sus cursos accesibles.
- El gestor debe poder crear PR asociados a un curso.
- El gestor debe poder crear documentos dentro de un PR.
- El sistema debe permitir asociar plantillas a los documentos.
- El sistema debe permitir crear variantes versionadas.
- El sistema debe registrar estados e historico de cambios.
- El gestor debe poder asignar o retirar revisores.
- El sistema debe generar notificaciones ante cambios de estado.

### Requisitos no funcionales

- Seguridad en autenticacion y autorizacion.
- Trazabilidad de operaciones relevantes.
- Interfaz clara y comprensible.
- Codigo mantenible y modular.

### Metodo de captacion de requisitos

Puedes redactarlo asi en la memoria final:

"Los requisitos se definieron a partir de un analisis del flujo documental que se queria informatizar, identificando los actores implicados, las operaciones recurrentes y los puntos de control necesarios para asegurar trazabilidad y coordinacion. Posteriormente se refinaron en iteraciones sucesivas a medida que se avanzaba en el desarrollo del prototipo funcional."

## 5. Diseno estatico

En este apartado conviene insertar:

- un diagrama de casos de uso con gestor, docente y revisor;
- un diagrama de clases con User, Role, Course, PR, Document, DocumentVariant, Plantilla, Notificacion y estados;
- un diagrama de secuencia de un caso real, por ejemplo: crear documento y generar primera variante.

### Casos de uso recomendados

- Iniciar sesion
- Consultar cursos accesibles
- Crear PR
- Crear documento
- Asignar revisores
- Actualizar estado de variante
- Consultar notificaciones

## 6. Modelo de datos

### Entidades principales

- users
- roles
- role_user
- courses
- prs
- pr_teachers
- documents
- document_reviewers
- document_variants
- document_statuses
- document_status_histories
- plantillas
- notificacions

### Explicacion resumida

- Un curso puede tener varios PR.
- Un PR pertenece a un curso y puede tener varios docentes.
- Un PR contiene varios documentos.
- Cada documento puede tener varios revisores y varias variantes.
- Cada variante tiene un estado y un historico de cambios.
- Las plantillas ayudan a estandarizar el nombre y configuracion documental.

## 7. Codificacion

### Control de errores y excepciones

La aplicacion utiliza validaciones de entrada y control de errores en operaciones de negocio. Por ejemplo, al crear documentos se valida el tipo recibido y se lanza una excepcion cuando el tipo seleccionado no admite tema o cuando la plantilla necesaria no esta disponible.

### Accesibilidad y usabilidad

La interfaz presenta navegacion visible, formularios claros y mensajes de error en el login. La estructura de pantallas separa cursos, PR y documentos, lo que reduce la carga cognitiva del usuario y facilita localizar cada accion dentro del flujo de trabajo.

### Comentarios y organizacion del codigo

El proyecto utiliza controladores, middleware, acciones y modelos con responsabilidades separadas. Esta division facilita la lectura y permite documentar de forma clara el comportamiento de cada capa.

## 8. Manuales

### Manual de instalacion

1. Instalar dependencias con Composer.
2. Copiar el archivo de entorno.
3. Generar la clave de aplicacion.
4. Configurar la base de datos.
5. Ejecutar migraciones.
6. Instalar dependencias de frontend.
7. Compilar los assets.

### Manual de usuario

Recorrido recomendado para capturas:

1. Pantalla de login.
2. Listado de cursos accesibles.
3. Vista de PR por curso.
4. Vista de documentos por PR.
5. Alta de documento.
6. Asignacion de revisores.
7. Gestion de variantes y estados.
8. Consulta de notificaciones.

## 9. Viabilidad

### Viabilidad tecnica

El proyecto es viable porque se apoya en tecnologias maduras, con buena documentacion y amplio uso en el mercado. El alcance funcional es adecuado para un PFC y permite demostrar competencias de backend, base de datos, frontend, seguridad y modelado.

### Viabilidad economica

El coste de licencias es reducido o inexistente al emplear herramientas open source. Los recursos principales son tiempo de desarrollo, equipo informatico, conexion a internet y servicio de alojamiento si se despliega una demo.

### Riesgos

- retrasos por complejidad en el modelado;
- errores en autorizacion por roles;
- inconsistencias entre variantes y estados;
- dependencia de una correcta configuracion del entorno.

### Medidas preventivas

- desarrollo iterativo;
- validaciones de datos;
- control de acceso por middleware y metodos de dominio;
- pruebas funcionales sobre flujos criticos.

## 10. Pruebas de software

### Plan de pruebas propuesto

- Prueba 1: login con credenciales validas y no validas.
- Prueba 2: acceso restringido segun rol.
- Prueba 3: creacion de documento con tipo valido.
- Prueba 4: rechazo de documento con tema no permitido.
- Prueba 5: asignacion y eliminacion de revisores.
- Prueba 6: cambio de estado de variante y generacion de notificacion.

### Ejemplo de redaccion

"Se realizaron pruebas funcionales sobre los flujos criticos del sistema, verificando autenticacion, autorizacion, creacion de entidades, consistencia del modelo y respuesta ante entradas no validas."

## 11. Conclusiones y mejoras futuras

El proyecto ha permitido construir una base funcional para la gestion documental academica, integrando control de acceso, estructura por roles, versionado y notificaciones. Como mejoras futuras se plantea ampliar la cobertura de pruebas, incorporar filtros avanzados, generar informes y reforzar la experiencia de usuario en algunos flujos de gestion.

## 12. Bibliografia y webgrafia

- Laravel Documentation: https://laravel.com/docs
- Laravel Sanctum: https://laravel.com/docs/sanctum
- Tailwind CSS Documentation: https://tailwindcss.com/docs
- Vite Documentation: https://vite.dev/guide
- PHPUnit Documentation: https://docs.phpunit.de

## Recomendacion final

Para que esta base se convierta en una memoria con nota alta, anade capturas reales de tu aplicacion, diagramas elaborados por ti y una comparativa honesta entre la planificacion inicial y el estado actual del proyecto.