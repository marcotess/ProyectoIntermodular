# Arquitectura y diseno tecnico

## 1. Vision general

La aplicacion sigue una arquitectura web basada en Laravel, con renderizado principal en servidor mediante Blade y apoyo de JavaScript para operaciones interactivas. La estructura se apoya en un modelo MVC reforzado con una capa de acciones para encapsular logica de negocio.

## 2. Tecnologias empleadas

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- Blade
- JavaScript
- Vite
- Tailwind CSS
- PHPUnit
- SQLite en desarrollo local

## 3. Capas principales

### 3.1. Capa de presentacion

Se compone de vistas Blade ubicadas en `resources/views/`. Las pantallas principales son login, perfil, cursos, PR, documentos, tareas, notificaciones y chat.

### 3.2. Capa de control

Los controladores HTTP reciben la peticion, validan acceso y coordinan la logica necesaria. Algunos controladores destacados son:

- `AuthController`
- `PRController`
- `PRDocumentController`
- `VariantController`
- `TaskController`
- `ChatController`
- `NotificacionController`

### 3.3. Capa de negocio

La logica reutilizable y de dominio se concentra en `app/Actions/`. Esta capa evita controladores demasiado grandes y mejora la separacion de responsabilidades.

Acciones relevantes:

- `CreatePRAction`
- `CreateDocumentAction`
- `CreateVariantAction`
- `UpdateDocumentAction`
- `PRsAction`
- `NotificacionAction`
- `PlantillasAction`
- `DocumentFilesystemAction`
- `EnsureVariantStatusAvailabilityAction`

### 3.4. Capa de datos

Se basa en modelos Eloquent y migraciones Laravel. El proyecto define entidades para usuarios, roles, cursos, PR, documentos, variantes, estados, plantillas, notificaciones y mensajes de chat.

## 4. Flujo tecnico principal

### 4.1. Acceso al sistema

1. El usuario se autentica.
2. Laravel crea sesion web o token API.
3. El sistema calcula cursos y recursos accesibles segun rol.

### 4.2. Creacion documental

1. El gestor solicita crear un documento.
2. `CreateDocumentAction` valida el tipo.
3. Se selecciona la ultima plantilla disponible.
4. Se crea el documento.
5. `CreateVariantAction` genera la primera variante.
6. `DocumentFilesystemAction` copia el archivo base de plantilla.

### 4.3. Cambio de estado de variante

1. Se solicita el nuevo estado.
2. `UpdateDocumentAction` delega la regla de exclusividad.
3. `EnsureVariantStatusAvailabilityAction` bloquea estados incompatibles.
4. Si el cambio es valido, la variante se guarda y el archivo se recoloca en su carpeta de estado.
5. `NotificacionAction` puede generar avisos a usuarios afectados.

## 5. Seguridad y control de acceso

El sistema combina varios niveles:

- middleware `auth` para usuarios autenticados;
- middleware `role` para restringir operaciones por rol;
- metodos de negocio en `User` como `canAccessCourse`, `canAccessPr`, `canAccessDocument` y `canAccessVariant`;
- comprobaciones adicionales en controladores para impedir accesos a PR no autorizados aunque el rol global sea correcto.

Tambien existe una API protegida con Sanctum, que trabaja con tokens Bearer y capacidades asociadas al rol.

## 6. Frontend y accesibilidad

La mayor parte de la interfaz se renderiza desde servidor. JavaScript se usa para:

- paneles de ajustes;
- dropdown del usuario;
- operaciones asincronas sobre PR, documentos y variantes;
- soporte de logout y algunas interacciones del dashboard.

Se han incorporado mejoras de accesibilidad en controles clave mediante:

- `aria-controls`;
- `aria-expanded`;
- `aria-hidden`;
- `role="region"` y `role="log"`;
- mensajes con `role="status"` y `role="alert"`.

## 7. Pruebas y calidad

El proyecto incluye pruebas automatizadas de caracter funcional. En el estado actual, la suite valida autenticacion, permisos, PR, documentos, variantes, tareas, notificaciones, chat y API.

Resultado actual validado en local:

- 24 tests correctos
- 97 assertions superadas

## 8. Ventajas del diseno aplicado

- separacion razonable entre interfaz, control y logica;
- reutilizacion de acciones de negocio;
- trazabilidad documental mediante variantes y estados;
- posibilidad de usar web y API sin duplicar toda la logica;
- facilidad para ampliar pruebas y documentacion.

## 9. Limitaciones actuales

- la interfaz sigue siendo principalmente server-rendered;
- no existe un panel estadistico avanzado;
- la integracion con almacenamiento externo real no esta cerrada para entornos institucionales;
- no hay sistema de auditoria formal para todas las mutaciones sensibles.
