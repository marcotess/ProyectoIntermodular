# Plan de pruebas

## 1. Objetivo

Este documento recoge la estrategia de pruebas aplicada al proyecto, los tipos de comprobaciones realizadas y una seleccion de casos de prueba representativos. Su objetivo es demostrar que la aplicacion no solo implementa funcionalidades, sino que tambien ha sido validada de forma controlada.

## 2. Enfoque general

La validacion del sistema se ha realizado principalmente mediante pruebas funcionales automatizadas con PHPUnit sobre Laravel. Estas pruebas simulan el comportamiento real de distintos usuarios y verifican autenticacion, permisos, flujos principales, notificaciones, chat, gestion documental y API.

Se ha complementado este enfoque con comprobaciones manuales de interfaz, navegacion y accesibilidad en las vistas principales.

## 3. Tipos de pruebas realizadas

### 3.1. Pruebas funcionales

Se comprueba que el sistema responde correctamente a acciones reales del usuario:

- inicio de sesion y cierre de sesion
- acceso al perfil
- visibilidad de cursos segun rol
- acceso de revisores solo a documentos asignados
- creacion de PR por parte del gestor
- creacion de documentos con su variante inicial
- generacion y lectura de notificaciones
- mensajeria interna en chat
- actualizacion de PR y estados documentales
- acceso autenticado a la API

### 3.2. Pruebas de permisos

Se valida que un usuario no pueda ejecutar acciones fuera de su ambito funcional:

- un docente no puede usar rutas exclusivas de gestor
- un revisor no puede modificar un PR que no le pertenece
- las rutas API protegidas por rol rechazan usuarios sin permisos suficientes

### 3.3. Pruebas de accesibilidad basica

Se revisan elementos importantes de interfaz para mejorar usabilidad y defensa tecnica:

- controles con `aria-controls`
- estados `aria-expanded` y `aria-hidden`
- regiones con `role="region"`
- historico de mensajes con `role="log"`
- mensajes de error y estado con `role="alert"` y `role="status"`

## 4. Herramientas utilizadas

- Laravel PHPUnit
- SQLite en entorno de pruebas
- RefreshDatabase para aislamiento entre tests
- Storage fake para validar flujos documentales sin depender del sistema de ficheros real

## 5. Estado actual de la suite

En el momento de redaccion de este documento, la validacion automatizada principal del proyecto queda resumida asi:

- 24 pruebas superadas
- 97 aserciones correctas
- sin errores detectados en la ejecucion completa de `php artisan test`

## 6. Casos de prueba representativos

### Caso 1. Inicio de sesion correcto

- Objetivo: comprobar que un usuario valido puede autenticarse.
- Precondiciones: el usuario existe y tiene credenciales correctas.
- Entrada: correo y contrasena validos.
- Resultado esperado: redireccion al perfil.
- Resultado obtenido: correcto.

### Caso 2. Inicio de sesion incorrecto

- Objetivo: comprobar que el sistema rechaza credenciales erroneas.
- Precondiciones: el usuario existe.
- Entrada: correo valido y contrasena incorrecta.
- Resultado esperado: vuelta al login con error de validacion.
- Resultado obtenido: correcto.

### Caso 3. Restriccion de rutas de gestor

- Objetivo: comprobar que un docente no puede usar una operacion exclusiva de gestor.
- Precondiciones: usuario con rol docente autenticado.
- Entrada: llamada a la ruta de creacion de PR.
- Resultado esperado: respuesta 403.
- Resultado obtenido: correcto.

### Caso 4. Creacion de PR por gestor

- Objetivo: validar el alta de un nuevo PR dentro de un curso.
- Precondiciones: usuario con rol gestor y curso existente.
- Entrada: peticion de creacion.
- Resultado esperado: nuevo PR con numero correlativo y nombre por defecto.
- Resultado obtenido: correcto.

### Caso 5. Creacion de documento con variante inicial

- Objetivo: comprobar el flujo documental completo de alta.
- Precondiciones: gestor autenticado, PR existente y plantilla disponible para ese tipo documental.
- Entrada: creacion de documento tipo MANUAL.
- Resultado esperado: documento creado, plantilla asignada, variante inicial generada y archivo fisico disponible.
- Resultado obtenido: correcto.

### Caso 6. Actualizacion de PR por revisor autorizado

- Objetivo: validar que un revisor con acceso real al PR puede modificar ciertos datos permitidos.
- Precondiciones: revisor asignado a un documento del PR.
- Entrada: cambio de fase, nombre y fecha limite.
- Resultado esperado: cambios persistidos.
- Resultado obtenido: correcto.

### Caso 7. Revisor no autorizado sobre PR ajeno

- Objetivo: comprobar el bloqueo de acceso sobre recursos no asignados.
- Precondiciones: revisor autenticado sin relacion con el PR.
- Entrada: intento de modificar nombre del PR.
- Resultado esperado: respuesta 403 y ausencia de cambios.
- Resultado obtenido: correcto.

### Caso 8. Conflicto de estados de variantes

- Objetivo: validar la regla de negocio que impide estados activos incompatibles.
- Precondiciones: documento con varias variantes y una variante ya en estado activo.
- Entrada: intento de poner otra variante en un estado incompatible.
- Resultado esperado: respuesta 422 y mensaje de error.
- Resultado obtenido: correcto.

### Caso 9. Notificacion interna al recibir chat

- Objetivo: verificar que un mensaje de chat genera tambien una notificacion.
- Precondiciones: dos usuarios validos con roles habilitados para chat.
- Entrada: envio de un mensaje.
- Resultado esperado: insercion en `chat_messages` y en `notificaciones`.
- Resultado obtenido: correcto.

### Caso 10. API con token Sanctum

- Objetivo: verificar autenticacion y acceso a recursos JSON.
- Precondiciones: usuario valido.
- Entrada: login por API, uso del Bearer token y acceso a rutas protegidas.
- Resultado esperado: token emitido, acceso permitido a recursos propios y bloqueo en rutas de gestor si no corresponde.
- Resultado obtenido: correcto.

## 7. Pruebas manuales recomendadas para la demo

Ademas de la suite automatizada, se recomienda realizar durante la presentacion las siguientes comprobaciones visuales:

1. iniciar sesion con distintos roles
2. acceder a cursos y PR visibles segun rol
3. crear un documento como gestor
4. revisar tareas y notificaciones
5. abrir el chat y enviar un mensaje
6. mostrar el contador de mensajes no leidos

## 8. Conclusiones

El proyecto dispone de una base de pruebas suficiente para defender que los flujos principales se han validado de forma seria. Ademas de cubrir autenticacion y roles, la suite actual demuestra reglas de negocio, proteccion de recursos, generacion de notificaciones y acceso API con token.

Como mejora futura, podria ampliarse la cobertura con pruebas E2E visuales y con escenarios mas amplios de subida de archivos y plantillas.# Plan de pruebas

## 1. Objetivo

Este documento recoge la estrategia basica de pruebas aplicada al proyecto, los tipos de comprobacion realizados y varios casos concretos que permiten defender la calidad funcional de la aplicacion.

## 2. Estrategia de pruebas

Se han combinado dos niveles:

- pruebas automatizadas con Laravel y PHPUnit;
- comprobaciones manuales orientadas a interfaz y navegacion.

El objetivo no ha sido cubrir el 100 % del sistema, sino asegurar los flujos principales y las zonas con mayor impacto funcional o de seguridad.

## 3. Alcance cubierto por pruebas automatizadas

La suite actual valida:

- acceso de invitado y autenticacion;
- login correcto e incorrecto;
- logout;
- visibilidad de cursos segun rol;
- acceso de revisor a documentos asignados;
- creacion de PR por gestor;
- creacion de documento con variante inicial y archivo;
- edicion de fase, nombre y fecha limite de PR;
- bloqueo de mutaciones sobre PR no autorizados;
- cambio de estado de variantes y bloqueo de conflictos;
- lectura de notificaciones;
- tareas y resumen por vencimientos;
- envio y lectura de mensajes de chat;
- controles de accesibilidad en vistas criticas;
- login API y acceso con token Sanctum;
- logout API y revocacion de token;
- restriccion de rutas API segun rol.

## 4. Resultado validado

Resultado de la ultima ejecucion completa:

- 24 tests superados
- 97 assertions correctas
- 0 errores en la suite ejecutada

Comando de validacion utilizado:

```bash
php artisan test
```

## 5. Casos de prueba representativos

### Caso 1. Login correcto

Objetivo:

- comprobar que un usuario valido puede autenticarse y acceder al perfil.

Datos de entrada:

- email valido
- contrasena correcta

Resultado esperado:

- redireccion al perfil
- sesion autenticada

Resultado obtenido:

- correcto

### Caso 2. Login incorrecto

Objetivo:

- comprobar que el sistema rechaza credenciales invalidas.

Datos de entrada:

- email existente
- contrasena incorrecta

Resultado esperado:

- redireccion al login
- mensaje de error asociado al campo email

Resultado obtenido:

- correcto

### Caso 3. Restriccion de rutas de gestor

Objetivo:

- comprobar que un docente no puede acceder a una operacion exclusiva de gestor.

Accion:

- invocar la ruta de creacion de PR

Resultado esperado:

- respuesta 403

Resultado obtenido:

- correcto

### Caso 4. Creacion de documento

Objetivo:

- verificar que un gestor puede crear un documento completo.

Precondiciones:

- curso existente
- PR existente
- plantilla existente para el tipo documental

Resultado esperado:

- documento creado
- variante inicial creada
- archivo copiado desde la plantilla

Resultado obtenido:

- correcto

### Caso 5. Actualizacion autorizada de PR por revisor

Objetivo:

- verificar que un revisor con acceso real al PR puede editar su fase, nombre y fecha limite.

Resultado esperado:

- operaciones aceptadas
- datos persistidos en base de datos

Resultado obtenido:

- correcto

### Caso 6. Bloqueo de acceso a PR no autorizado

Objetivo:

- impedir que un revisor modifique un PR ajeno solo por tener el rol.

Resultado esperado:

- respuesta 403
- datos del PR sin cambios

Resultado obtenido:

- correcto

### Caso 7. Cambio de estado de variante

Objetivo:

- comprobar actualizacion de estado y cumplimiento de exclusividad.

Escenario:

- se cambia una variante a candidato
- se intenta repetir estado activo conflictivo en otra variante del mismo documento

Resultado esperado:

- primer cambio correcto
- segundo cambio bloqueado con error 422

Resultado obtenido:

- correcto

### Caso 8. API con token Sanctum

Objetivo:

- comprobar login API, acceso a recurso protegido y logout con revocacion.

Resultado esperado:

- recepcion de token
- acceso permitido con Bearer token
- token eliminado tras logout

Resultado obtenido:

- correcto

## 6. Pruebas manuales recomendadas para la defensa

Durante la demostracion se recomienda realizar estas comprobaciones manuales:

- iniciar sesion como gestor;
- entrar en cursos y PR;
- crear un documento desde la interfaz;
- revisar tareas;
- abrir notificaciones;
- enviar un mensaje por el chat;
- mostrar contador de mensajes no leidos;
- abrir el panel de ajustes del perfil.

## 7. Incidencias corregidas detectadas gracias a pruebas

Durante el desarrollo y la ampliacion de cobertura se localizaron y corrigieron, entre otras, estas incidencias:

- mapeo incorrecto de la tabla de notificaciones en el modelo;
- falta de comprobacion de acceso especifico en endpoints de mutacion de PR;
- estados de accesibilidad incompletos en controles de paneles y dropdowns.

## 8. Conclusion

El proyecto dispone de una base de pruebas suficiente para defender el funcionamiento de los flujos mas importantes. Aunque siempre es posible ampliar cobertura, el estado actual ya permite justificar la calidad tecnica del sistema con evidencia ejecutable y medible.
