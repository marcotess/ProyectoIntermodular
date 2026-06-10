# Modelo de datos

## 1. Objetivo

Este documento describe el modelo de base de datos real del proyecto a partir de las migraciones del repositorio. El objetivo es dejar claro que tablas existen, que campos tiene cada una, como se relacionan y que reglas de integridad soporta el esquema.

La base de datos puede dividirse en dos bloques:

- tablas de negocio, que representan cursos, PR, documentos, variantes, plantillas, notificaciones y chat
- tablas tecnicas de Laravel, necesarias para autenticacion, sesiones, cache, colas y tokens

## 2. Vision general del modelo

El nucleo funcional gira alrededor de esta cadena:

- un `course` agrupa varios `prs`
- cada `pr` puede tener varios `documents`
- cada `document` puede tener varias `document_variants`
- cada `document_variant` tiene un estado en `document_statuses`
- cada cambio de estado puede quedar trazado en `document_status_histories`

Alrededor de ese nucleo aparecen relaciones auxiliares:

- usuarios y roles mediante `role_user`
- docentes asignados a PR mediante `pr_teachers`
- revisores asignados a documentos mediante `document_reviewers`
- alias historicos de nombre documental mediante `document_name_aliases`
- notificaciones internas mediante `notificaciones`
- mensajeria interna mediante `chat_messages`

## 3. Tablas de negocio

### 3.1. `users`

Finalidad:

Almacena a los usuarios autenticables del sistema y sus preferencias de interfaz.

Campos:

- `id`: clave primaria
- `name`: nombre del usuario
- `email`: correo electronico, unico
- `email_verified_at`: fecha de verificacion del correo, nullable
- `password`: hash de la contrasena
- `receive_notification_emails`: booleano, indica si acepta correos de aviso
- `theme_preference`: preferencia visual, por defecto `light`
- `compact_tables`: booleano para tablas compactas
- `reduce_motion`: booleano para reducir animaciones
- `show_quick_notifications`: booleano para avisos rapidos en interfaz
- `remember_token`: token de sesion persistente
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- indice unico en `email`

Relaciones:

- muchos a muchos con `roles` mediante `role_user`
- muchos a muchos con `prs` mediante `pr_teachers` en el papel de docente
- muchos a muchos con `documents` mediante `document_reviewers` en el papel de revisor
- uno a muchos con `notificaciones`
- uno a muchos con `chat_messages` como emisor mediante `sender_id`
- uno a muchos con `chat_messages` como receptor mediante `recipient_id`
- uno a muchos con `document_variants` mediante `created_by`
- uno a muchos con `document_name_aliases` mediante `created_by`
- uno a muchos con `document_status_histories` mediante `user_id`

### 3.2. `roles`

Finalidad:

Define los roles funcionales del sistema.

Campos:

- `id`: clave primaria
- `name`: nombre del rol, unico
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- indice unico en `name`

Relaciones:

- muchos a muchos con `users` mediante `role_user`

Valores esperados en aplicacion:

- `gestor`
- `docente`
- `revisor`

### 3.3. `role_user`

Finalidad:

Tabla pivote entre usuarios y roles.

Campos:

- `id`: clave primaria
- `user_id`: referencia a `users`
- `role_id`: referencia a `roles`

Restricciones e indices:

- clave primaria en `id`
- `user_id` con `cascadeOnDelete`
- `role_id` con `cascadeOnDelete`

Relaciones:

- muchos a uno con `users`
- muchos a uno con `roles`

Observacion:

La migracion no define un indice unico compuesto `user_id + role_id`, asi que la unicidad de asignacion de rol no queda blindada a nivel de base de datos.

### 3.4. `courses`

Finalidad:

Representa los cursos del sistema.

Campos:

- `id`: clave primaria
- `code`: codigo del curso, unico
- `name`: nombre del curso
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- indice unico en `code`

Relaciones:

- uno a muchos con `prs`

### 3.5. `prs`

Finalidad:

Representa cada proyecto o PR asociado a un curso.

Campos:

- `id`: clave primaria
- `course_id`: referencia al curso propietario
- `number`: numero del PR dentro del curso
- `fase`: fase funcional del proyecto
- `nombre`: nombre descriptivo del PR, nullable
- `deadline`: campo legacy tipo timestamp, nullable
- `fecha_limite`: fecha principal de entrega, nullable
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `course_id` hacia `courses` con `restrictOnDelete`
- indice unico compuesto `course_id + number`

Relaciones:

- muchos a uno con `courses`
- muchos a muchos con `users` mediante `pr_teachers`
- uno a muchos con `documents`

Valores de `fase` definidos por migracion:

- `Temario preliminar`
- `Temario final`
- `Generacion de contenidos`
- `Generacion de contenidos y videos`
- `Generacion de videos`
- `Finalizado`

Observaciones:

- `deadline` sigue existiendo en el esquema aunque el campo que parece usarse a nivel funcional es `fecha_limite`
- `nombre` se anadio despues y se relleno inicialmente como `Proyecto N`

### 3.6. `pr_teachers`

Finalidad:

Tabla pivote que asigna docentes a cada PR.

Campos:

- `id`: clave primaria
- `pr_id`: referencia a `prs`
- `user_id`: referencia a `users`
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `pr_id` con `restrictOnDelete`
- clave foranea `user_id` con `restrictOnDelete`
- indice unico compuesto `pr_id + user_id`

Relaciones:

- muchos a uno con `prs`
- muchos a uno con `users`

### 3.7. `plantillas`

Finalidad:

Guarda las plantillas documentales reutilizables.

Campos:

- `id`: clave primaria
- `tipo_documento`: tipo documental asociado
- `prefijo`: prefijo base de nomenclatura
- `version`: version numerica de la plantilla

Restricciones e indices:

- clave primaria en `id`

Relaciones:

- uno a muchos con `documents`

Observaciones:

- en una migracion intermedia se intento restringir `tipo_documento` y `prefijo` mediante `enum`
- en el estado final del esquema ambos quedaron como `string` por una migracion posterior de compatibilidad con SQLite
- no hay timestamps en esta tabla

### 3.8. `documents`

Finalidad:

Representa cada documento funcional ligado a un PR.

Campos:

- `id`: clave primaria
- `pr_id`: referencia a `prs`
- `plantilla_id`: referencia nullable a `plantillas`
- `tema`: entero nullable para documentos que lo soportan
- `type`: tipo de documento
- `short_title`: titulo corto legible
- `canonical_name`: nombre canonico del documento dentro del PR
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `pr_id` con `restrictOnDelete`
- clave foranea `plantilla_id` con `nullOnDelete`
- indice unico compuesto `pr_id + canonical_name`

Relaciones:

- muchos a uno con `prs`
- muchos a uno con `plantillas`
- uno a muchos con `document_variants`
- uno a muchos con `document_name_aliases`
- muchos a muchos con `users` mediante `document_reviewers`

Valores permitidos de `type` en el esquema:

- `DEFINICION`
- `COMPETENCIA`
- `TIMING`
- `PLAN_TRABAJO`
- `ESTIMACION_DEDICACION`
- `GUION100`
- `GUION600`
- `INSTALACION`
- `MANUAL`
- `PRESENTACION`
- `EJERCICIOS`
- `PRACTICA`
- `CUESTIONARIO`

### 3.9. `document_reviewers`

Finalidad:

Tabla pivote que asigna revisores a cada documento.

Campos:

- `id`: clave primaria
- `document_id`: referencia a `documents`
- `user_id`: referencia a `users`
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `document_id` con `restrictOnDelete`
- clave foranea `user_id` con `restrictOnDelete`
- indice unico compuesto `document_id + user_id`

Relaciones:

- muchos a uno con `documents`
- muchos a uno con `users`

### 3.10. `document_name_aliases`

Finalidad:

Mantiene alias o huellas historicas de nombres canonicos de documentos.

Campos:

- `id`: clave primaria
- `document_id`: referencia a `documents`
- `canonical_name`: alias registrado
- `created_by`: usuario que genera el alias
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `document_id` con `restrictOnDelete`
- clave foranea `created_by` hacia `users`

Relaciones:

- muchos a uno con `documents`
- muchos a uno con `users`

Observacion:

No existe restriccion unica sobre `canonical_name`, por lo que la tabla actua mas como historico que como catalogo normalizado.

### 3.11. `document_statuses`

Finalidad:

Normaliza los estados documentales en una tabla propia.

Campos:

- `id`: clave primaria
- `name`: nombre del estado, unico
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- indice unico en `name`

Relaciones:

- uno a muchos con `document_variants`
- uno a muchos con `document_status_histories` como estado origen
- uno a muchos con `document_status_histories` como estado destino

Valores base esperados por la aplicacion:

- `01_desarrollo`
- `02_candidato`
- `03_produccion`
- `04_obsoleto`

### 3.12. `document_variants`

Finalidad:

Representa cada version concreta de un documento.

Campos:

- `id`: clave primaria
- `document_id`: referencia a `documents`
- `version`: numero de version dentro del documento
- `deadline_target`: fecha objetivo de la variante, nullable
- `drive_link_url`: ruta o URL del archivo asociado, nullable
- `created_by`: usuario creador de la variante
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion
- `status_id`: referencia al estado actual

Restricciones e indices:

- clave primaria en `id`
- clave foranea `document_id` con `restrictOnDelete`
- clave foranea `created_by` hacia `users`
- clave foranea `status_id` hacia `document_statuses`
- indice unico compuesto `document_id + version`

Relaciones:

- muchos a uno con `documents`
- muchos a uno con `users`
- muchos a uno con `document_statuses`
- uno a muchos con `document_status_histories`

Observacion:

El esquema original almacenaba el estado como `enum` en la propia tabla. Posteriormente se normalizo a `status_id`.

### 3.13. `document_status_histories`

Finalidad:

Registra la trazabilidad de los cambios de estado aplicados a cada variante documental.

Campos:

- `id`: clave primaria
- `document_variant_id`: referencia a `document_variants`
- `user_id`: usuario que ejecuta el cambio
- `comment`: comentario opcional del cambio
- `created_at`: fecha de creacion del registro
- `updated_at`: fecha de actualizacion
- `from_status_id`: estado origen
- `to_status_id`: estado destino

Restricciones e indices:

- clave primaria en `id`
- clave foranea `document_variant_id` con `restrictOnDelete`
- clave foranea `user_id` hacia `users`
- clave foranea `from_status_id` hacia `document_statuses`
- clave foranea `to_status_id` hacia `document_statuses`

Relaciones:

- muchos a uno con `document_variants`
- muchos a uno con `users`
- muchos a uno con `document_statuses` como origen
- muchos a uno con `document_statuses` como destino

Observacion:

Igual que en `document_variants`, el esquema arranco con campos `enum` y luego se paso a claves foraneas hacia `document_statuses`.

### 3.14. `notificaciones`

Finalidad:

Almacena avisos internos mostrados al usuario.

Campos:

- `id`: clave primaria
- `tema`: asunto o categoria de la notificacion
- `user_id`: usuario destinatario
- `mensaje`: contenido del aviso
- `link`: enlace relacionado, nullable
- `fecha_envio`: fecha de envio, nullable
- `fecha_lectura`: fecha de lectura, nullable
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `user_id` con `restrictOnDelete`

Relaciones:

- muchos a uno con `users`

### 3.15. `chat_messages`

Finalidad:

Guarda los mensajes internos intercambiados entre usuarios.

Campos:

- `id`: clave primaria
- `sender_id`: usuario emisor
- `recipient_id`: usuario receptor
- `message`: cuerpo del mensaje
- `read_at`: fecha de lectura, nullable
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

Restricciones e indices:

- clave primaria en `id`
- clave foranea `sender_id` con `cascadeOnDelete`
- clave foranea `recipient_id` con `cascadeOnDelete`
- indice compuesto `sender_id + recipient_id`
- indice compuesto `recipient_id + created_at`
- indice compuesto `recipient_id + read_at`

Relaciones:

- muchos a uno con `users` como emisor
- muchos a uno con `users` como receptor

## 4. Tablas tecnicas de Laravel

Estas tablas no forman parte del dominio academico, pero si de la base de datos real del proyecto.

### 4.1. `password_reset_tokens`

Finalidad:

Soporte para restablecimiento de contrasena.

Campos:

- `email`: clave primaria
- `token`: token de reseteo
- `created_at`: fecha de generacion, nullable

### 4.2. `sessions`

Finalidad:

Persistencia de sesiones web.

Campos:

- `id`: clave primaria
- `user_id`: usuario asociado, nullable e indexado
- `ip_address`: IP del cliente, nullable
- `user_agent`: agente del navegador, nullable
- `payload`: datos serializados de sesion
- `last_activity`: marca temporal indexada

Observacion:

`user_id` esta indexado pero la migracion no declara una clave foranea explicita.

### 4.3. `personal_access_tokens`

Finalidad:

Tokens de acceso personal de Sanctum para API.

Campos:

- `id`: clave primaria
- `tokenable_type`: tipo del modelo propietario
- `tokenable_id`: id del modelo propietario
- `name`: nombre del token
- `token`: hash del token, unico
- `abilities`: capacidades, nullable
- `last_used_at`: ultima utilizacion, nullable
- `expires_at`: expiracion, nullable e indexada
- `created_at`: fecha de creacion
- `updated_at`: fecha de actualizacion

### 4.4. `cache`

Finalidad:

Almacen de cache persistente.

Campos:

- `key`: clave primaria
- `value`: valor serializado
- `expiration`: expiracion indexada

### 4.5. `cache_locks`

Finalidad:

Bloqueos de cache.

Campos:

- `key`: clave primaria
- `owner`: propietario del bloqueo
- `expiration`: expiracion indexada

### 4.6. `jobs`

Finalidad:

Cola de trabajos pendientes.

Campos:

- `id`: clave primaria
- `queue`: cola de ejecucion, indexada
- `payload`: carga serializada
- `attempts`: numero de intentos
- `reserved_at`: reserva, nullable
- `available_at`: disponibilidad
- `created_at`: fecha de alta

### 4.7. `job_batches`

Finalidad:

Agrupacion de jobs por lote.

Campos:

- `id`: clave primaria de tipo string
- `name`: nombre del lote
- `total_jobs`: total de trabajos
- `pending_jobs`: trabajos pendientes
- `failed_jobs`: trabajos fallidos
- `failed_job_ids`: ids fallidos serializados
- `options`: opciones, nullable
- `cancelled_at`: cancelacion, nullable
- `created_at`: fecha de creacion
- `finished_at`: fecha de finalizacion, nullable

### 4.8. `failed_jobs`

Finalidad:

Registro de trabajos fallidos.

Campos:

- `id`: clave primaria
- `uuid`: identificador unico
- `connection`: conexion usada
- `queue`: cola usada
- `payload`: carga serializada
- `exception`: excepcion capturada
- `failed_at`: fecha de fallo

## 5. Relaciones globales mas importantes

Las cardinalidades funcionales mas relevantes del modelo son estas:

- un `user` puede tener varios `roles` y un `role` puede pertenecer a varios `users`
- un `course` puede tener muchos `prs`, pero cada `pr` pertenece a un solo `course`
- un `pr` puede tener muchos docentes y un docente puede participar en muchos `prs`
- un `pr` puede tener muchos `documents`, pero cada `document` pertenece a un solo `pr`
- una `plantilla` puede usarse en muchos `documents`, pero cada `document` apunta a una sola plantilla o a ninguna
- un `document` puede tener muchos revisores y un revisor puede revisar muchos documentos
- un `document` puede tener muchas `document_variants`, pero cada variante pertenece a un solo documento
- cada `document_variant` tiene un unico estado actual en `document_statuses`
- cada `document_variant` puede tener muchos registros en `document_status_histories`
- un `user` puede recibir muchas `notificaciones`
- un `user` puede enviar y recibir muchos `chat_messages`

## 6. Reglas de integridad y decisiones del esquema

Las restricciones mas importantes que si estan blindadas en base de datos son:

- `users.email` es unico
- `roles.name` es unico
- `courses.code` es unico
- `prs` no permite repetir el mismo `number` dentro del mismo `course_id`
- `pr_teachers` no permite repetir el mismo docente en el mismo PR
- `documents` no permite repetir el mismo `canonical_name` dentro del mismo PR
- `document_reviewers` no permite repetir el mismo revisor en el mismo documento
- `document_variants` no permite repetir la misma `version` dentro del mismo documento
- `document_statuses.name` es unico
- `personal_access_tokens.token` es unico
- `failed_jobs.uuid` es unico

Las decisiones de borrado tambien importan mucho:

- `course_id` en `prs` usa `restrictOnDelete`, asi que no se puede borrar un curso si tiene PR asociados
- `pr_id` en `documents` usa `restrictOnDelete`
- `document_id` en `document_reviewers`, `document_variants` y `document_name_aliases` usa `restrictOnDelete`
- `pr_id` en `pr_teachers` y `user_id` en `document_reviewers` usan `restrictOnDelete`
- `sender_id` y `recipient_id` en `chat_messages` usan `cascadeOnDelete`
- `plantilla_id` en `documents` usa `nullOnDelete`, por lo que al borrar una plantilla el documento sobrevive pero pierde la referencia

## 7. Observaciones importantes sobre el esquema vigente

- El esquema actual es el resultado de varias migraciones evolutivas. Por eso aparecen campos legacy como `prs.deadline` junto a `prs.fecha_limite`.
- Los estados documentales se normalizaron correctamente a tabla propia. Eso mejora integridad y hace mas limpio el historico de cambios.
- La tabla `plantillas` no tiene timestamps y en el estado final usa cadenas libres para `tipo_documento` y `prefijo`.
- La tabla `role_user` no tiene restriccion unica compuesta, por lo que seria posible insertar duplicados si la aplicacion no lo evita.
- La tabla `document_name_aliases` tampoco tiene una regla unica clara, lo que encaja con un uso de historico mas que de catalogo estricto.
- Buena parte de las migraciones tardias recrean tablas para mantener compatibilidad con SQLite al modificar claves foraneas.

## 8. Resumen final

La BBDD del proyecto esta bastante centrada en trazabilidad y control de acceso por relacion. El diseno no se limita a guardar documentos: modela cursos, PR, roles, asignaciones de docentes y revisores, versiones documentales, estados, historico de cambios, notificaciones y chat interno.

Si se mira desde negocio, el corazon del modelo es `courses -> prs -> documents -> document_variants`, con `document_statuses` y `document_status_histories` como capa de seguimiento. Si se mira desde seguridad y colaboracion, el soporte lo ponen `role_user`, `pr_teachers`, `document_reviewers`, `notificaciones` y `chat_messages`.
