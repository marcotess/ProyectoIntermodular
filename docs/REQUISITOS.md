# Requisitos del sistema

## 1. Introduccion

Este documento resume los requisitos funcionales y no funcionales principales del proyecto. Sirve como base para justificar el alcance del desarrollo y comprobar que las funcionalidades implementadas responden a necesidades concretas.

## 2. Requisitos funcionales

### RF-01. Autenticacion de usuarios

El sistema debe permitir iniciar sesion y cerrar sesion de forma segura.

### RF-02. Gestion de perfiles y roles

El sistema debe diferenciar entre gestores, docentes y revisores, aplicando permisos distintos a cada uno.

### RF-03. Consulta de cursos accesibles

Cada usuario debe poder ver solo los cursos que le correspondan segun su rol y relacion academica.

### RF-04. Consulta y gestion de PR

El sistema debe permitir visualizar proyectos por curso y, cuando proceda, crear nuevos PR o modificar datos de seguimiento.

### RF-05. Gestion documental

El sistema debe permitir crear documentos dentro de un PR, asociarles plantilla, revisar su estado y mantener nombres documentales consistentes.

### RF-06. Gestion de variantes

Cada documento debe admitir varias variantes o versiones, con trazabilidad y control de estado.

### RF-07. Asignacion de revisores y docentes

El sistema debe permitir asignar y retirar revisores o docentes en funcion del flujo de trabajo.

### RF-08. Tareas priorizadas

El sistema debe mostrar tareas ordenadas por urgencia a partir de fechas limite relevantes.

### RF-09. Notificaciones

El usuario debe recibir avisos internos cuando se produzcan cambios de interes, por ejemplo sobre documentos o mensajes nuevos.

### RF-10. Chat interno

El sistema debe permitir la comunicacion entre usuarios habilitados, manteniendo conversaciones persistentes y contador de mensajes no leidos.

### RF-11. API autenticada

La aplicacion debe ofrecer endpoints JSON protegidos mediante token para integraciones y consumo desde JavaScript.

## 3. Requisitos no funcionales

### RNF-01. Seguridad

Las operaciones deben requerir autenticacion y comprobacion de permisos segun rol y recurso.

### RNF-02. Integridad de datos

Las reglas de negocio deben impedir estados documentales incompatibles o accesos no autorizados.

### RNF-03. Usabilidad

La interfaz debe resultar clara, con navegacion consistente y acceso rapido a modulos principales.

### RNF-04. Accesibilidad basica

Los formularios y paneles principales deben incluir etiquetado y estados accesibles.

### RNF-05. Mantenibilidad

El codigo debe estar organizado en modelos, controladores, acciones y vistas para facilitar cambios futuros.

### RNF-06. Testabilidad

Las funcionalidades criticas deben poder validarse mediante pruebas automatizadas.

## 4. Casos de uso resumidos

### CU-01. Iniciar sesion

- Actor: cualquier usuario registrado
- Flujo: introduce credenciales, el sistema valida y redirige al perfil

### CU-02. Consultar cursos

- Actor: docente, revisor o gestor
- Flujo: accede al listado y visualiza solo cursos permitidos

### CU-03. Crear PR

- Actor: gestor
- Flujo: selecciona curso y crea un nuevo proyecto con numeracion automatica

### CU-04. Crear documento

- Actor: gestor
- Flujo: selecciona tipo documental, el sistema asigna plantilla y crea la primera variante

### CU-05. Revisar documento

- Actor: revisor
- Flujo: accede a documentos asignados y actualiza estados permitidos

### CU-06. Consultar tareas

- Actor: docente o revisor
- Flujo: abre la pantalla de tareas y revisa pendientes por proximidad temporal

### CU-07. Usar chat interno

- Actor: gestor, docente o revisor
- Flujo: abre una conversacion, envia mensajes y recibe notificaciones

### CU-08. Consumir API autenticada

- Actor: cliente web o integracion
- Flujo: login API, obtencion de token y acceso a recursos protegidos

## 5. Conclusiones

Los requisitos definidos cubren tanto la parte funcional visible para el usuario como aspectos tecnicos de calidad, seguridad y mantenibilidad. Esto permite relacionar claramente el desarrollo implementado con los criterios de evaluacion del proyecto.