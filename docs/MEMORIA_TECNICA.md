# Memoria tecnica

## 1. Introduccion

El proyecto consiste en una aplicacion web para la gestion academica de cursos, proyectos, documentos, variantes documentales, tareas, notificaciones y comunicacion interna entre usuarios. El sistema busca centralizar un flujo que habitualmente estaria repartido entre hojas de calculo, correos y archivos dispersos.

La aplicacion se ha desarrollado con Laravel y una interfaz Blade apoyada por JavaScript, manteniendo una estructura suficientemente modular para separar dominio, acciones de negocio, controladores y vistas.

## 2. Objetivo general

Construir una plataforma capaz de:

- organizar cursos y proyectos academicos
- gestionar la documentacion asociada a cada PR
- controlar revisores y docentes implicados
- mantener variantes y estados de documentos
- mostrar tareas priorizadas por fechas
- notificar cambios relevantes
- permitir comunicacion interna mediante chat

## 3. Tecnologias utilizadas

### Backend

- PHP 8.3
- Laravel 13
- Laravel Sanctum para autenticacion API

### Frontend

- Blade
- JavaScript
- Vite
- Tailwind CSS

### Persistencia y pruebas

- SQLite en desarrollo local y pruebas
- PHPUnit para pruebas funcionales

## 4. Arquitectura general

La aplicacion sigue una arquitectura basada en capas simples:

- `Models`: representan entidades como usuarios, cursos, PR, documentos, variantes, roles, plantillas y notificaciones.
- `Controllers`: exponen rutas web y API, coordinan validacion y respuesta.
- `Actions`: concentran reglas de negocio reutilizables, por ejemplo creacion de documentos, variantes, notificaciones o PR.
- `Views`: renderizan la interfaz Blade.
- `Middleware`: aplican autenticacion, roles y registro de actividad.

Este reparto evita concentrar toda la logica en controladores extensos y facilita la defensa tecnica del proyecto.

## 5. Modulos funcionales

### 5.1. Autenticacion y perfil

Permite iniciar sesion, cerrar sesion, visualizar datos del usuario y modificar ajustes personales.

### 5.2. Cursos y PR

Cada usuario visualiza solo los cursos y proyectos a los que tiene acceso por rol y relacion real con el contenido.

### 5.3. Gestion documental

Cada PR puede contener varios documentos. Cada documento puede tener varias variantes, revisores y estados. Esta parte es el nucleo funcional del sistema.

### 5.4. Tareas

Se genera un listado priorizado a partir de fechas limite de PR y documentos asignados.

### 5.5. Notificaciones

El sistema informa al usuario de cambios relevantes, tanto documentales como de mensajeria interna.

### 5.6. Chat interno

Se ha incorporado un modulo de conversacion entre gestores, docentes y revisores, con historial persistente, contador de mensajes no leidos y notificaciones asociadas.

### 5.7. API

La aplicacion expone rutas JSON protegidas con Sanctum para login, logout y consulta o modificacion de recursos segun permisos.

## 6. Seguridad y control de acceso

El proyecto trabaja con roles funcionales:

- gestor
- docente
- revisor

Ademas del rol, se comprueba la relacion real del usuario con el recurso. Por ejemplo, un revisor no debe modificar un PR ajeno solo por tener el rol adecuado. Durante el desarrollo final se ha reforzado precisamente este punto en rutas de mutacion de PR.

## 7. Accesibilidad y usabilidad

Se han introducido mejoras tecnicas en vistas clave:

- controles con atributos ARIA
- paneles desplegables con `aria-expanded` y `aria-hidden`
- regiones semanticas para configuracion y formularios
- mensajes de error y confirmacion diferenciados
- etiquetado claro en formularios y chat

Estas medidas no convierten la aplicacion en una auditoria completa WCAG, pero si demuestran un trabajo real de accesibilidad aplicada.

## 8. Calidad del codigo

El proyecto incorpora una capa de pruebas funcionales automatizadas que valida autenticacion, permisos, API, chat, notificaciones, tareas, PR y flujo documental. La suite final disponible en el proyecto supera los 20 tests y ronda el centenar de aserciones.

Tambien se ha mantenido una estructura razonablemente modular, evitando meter demasiada logica de dominio directamente en las vistas o en los controladores.

## 9. Viabilidad del proyecto

### 9.1. Viabilidad de mercado y necesidad

El proyecto responde a una necesidad clara dentro del contexto academico: centralizar la gestion de cursos, PR, documentos, revisiones, tareas y comunicacion interna en una unica herramienta. En muchos entornos educativos este flujo sigue repartiendose entre correo electronico, carpetas compartidas y hojas de calculo, lo que provoca perdida de trazabilidad y mas tiempo de coordinacion.

Aunque la aplicacion se ha planteado para un caso de uso academico, el problema que resuelve no es puntual. Cualquier organizacion con revision documental, estados, responsables y seguimiento de tareas puede beneficiarse de una plataforma similar. Esto da al proyecto una base razonable de utilidad real y posibilidad de evolucion.

### 9.2. Viabilidad tecnica y operativa

La viabilidad tecnica es alta porque se apoya en tecnologias maduras y ampliamente documentadas: Laravel, PHP, Blade, Vite, Tailwind y PHPUnit. El sistema ya implementa autenticacion, control de permisos, persistencia, API, notificaciones y pruebas funcionales, por lo que no se trata de una idea teorica sino de una solucion ejecutada y comprobable.

Desde el punto de vista operativo, la aplicacion es asumible para un centro educativo o un equipo pequeno, ya que no exige una infraestructura compleja para ponerse en marcha. Puede desplegarse sobre un servidor web estandar con base de datos relacional y un mantenimiento tecnico razonable.

### 9.3. Analisis economico

El coste principal del proyecto se concentra en horas de analisis, desarrollo, pruebas y documentacion. Al utilizar software libre y herramientas ampliamente extendidas, no aparecen costes de licencias relevantes en la fase inicial.

Una estimacion simplificada de costes seria la siguiente:

- analisis, desarrollo y pruebas: coste principal del proyecto
- infraestructura basica: alojamiento, dominio y copias de seguridad
- mantenimiento evolutivo: correccion de incidencias y nuevas funcionalidades
- formacion o adaptacion del equipo usuario: coste reducido por tratarse de una interfaz web convencional

En comparacion con procesos manuales o muy fragmentados, la herramienta puede reducir tiempos de seguimiento, errores de coordinacion y perdida de informacion. Por tanto, aunque el desarrollo tiene un coste, existe retorno en eficiencia organizativa y trazabilidad.

### 9.4. Financiacion y sostenibilidad

Para un proyecto academico, la financiacion es asumible porque el desarrollo se realiza con recursos formativos y sin necesidad de adquisiciones costosas. En un escenario real, la financiacion podria cubrirse mediante presupuesto interno del centro, una partida de digitalizacion o una implantacion progresiva por fases.

La sostenibilidad del sistema tambien es razonable: el stack tecnologico tiene soporte amplio, existe abundante documentacion y la arquitectura del proyecto permite incorporar mejoras sin rehacer la base completa de la aplicacion.

### 9.5. Riesgos y medidas de mitigacion

Se identifican varios riesgos principales:

- dependencia de una correcta definicion de roles y permisos, mitigada con validaciones en backend y pruebas automatizadas
- resistencia al cambio por parte de usuarios acostumbrados a procesos manuales, mitigada con una interfaz sencilla y documentacion de uso
- crecimiento futuro del sistema, mitigado con una arquitectura modular y separacion de responsabilidades
- necesidad de despliegue y copias de seguridad en un entorno real, mitigada con planificacion de infraestructura y procedimientos de mantenimiento

En conjunto, el proyecto puede considerarse viable porque resuelve una necesidad real, tiene una base tecnica ya funcional, presenta costes de entrada moderados y mantiene riesgos controlables con medidas razonables.

## 10. Limitaciones actuales

- la interfaz sigue siendo principalmente server-rendered y no una SPA completa
- la accesibilidad puede seguir ampliandose con revision de contraste, teclado y lectores de pantalla
- faltaria un despliegue real en produccion para cerrar un ciclo completo DevOps

## 11. Posibles mejoras futuras

- auditoria de accesibilidad mas profunda
- pruebas E2E visuales
- paneles estadisticos mas avanzados
- adjuntos y comentarios enriquecidos en chat
- historico mas detallado de cambios documentales

## 12. Conclusiones

El resultado final es una aplicacion completa y defendible para un proyecto fin de ciclo. No se limita a mostrar pantallas: implementa reglas de negocio, control de permisos, persistencia, API, notificaciones, pruebas y mejoras de accesibilidad. Esto permite argumentar que el trabajo no es solo funcional, sino tambien tecnicamente razonado.