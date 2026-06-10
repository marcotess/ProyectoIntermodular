# Requisitos y objetivos

## 1. Objetivo general

El objetivo del proyecto es desarrollar una aplicacion web para centralizar la gestion academica de cursos, proyectos, documentacion, revisiones, tareas, notificaciones y comunicacion interna entre usuarios. La aplicacion busca evitar el seguimiento disperso en hojas de calculo, correos aislados o mensajes externos, ofreciendo un unico entorno de trabajo.

## 2. Objetivos especificos

- Gestionar cursos visibles segun el rol del usuario.
- Crear y consultar PR asociados a cada curso.
- Gestionar documentos de un PR con plantillas, nombres canonicos, tema y revisores.
- Mantener variantes documentales con estados y trazabilidad.
- Mostrar tareas priorizadas segun fechas limite.
- Generar notificaciones internas cuando se producen eventos relevantes.
- Permitir chat interno entre gestor, docente y revisor.
- Proteger las operaciones segun autenticacion y rol.
- Exponer parte de la funcionalidad tambien mediante API JSON protegida con Sanctum.

## 3. Alcance funcional

El sistema cubre los siguientes bloques:

- autenticacion web y API;
- perfil de usuario con ajustes;
- cursos y proyectos;
- gestion documental;
- variantes y estados;
- plantillas;
- tareas;
- notificaciones;
- chat interno.

No se contempla en esta version:

- multiorganizacion;
- firma electronica;
- integracion con sistemas externos institucionales;
- panel de analitica avanzada;
- control documental multiidioma.

## 4. Requisitos funcionales

### 4.1. Usuarios y seguridad

- El sistema debe permitir iniciar y cerrar sesion.
- El sistema debe identificar el rol del usuario autenticado.
- El sistema debe impedir accesos no autorizados a vistas y acciones.
- El sistema debe permitir autenticacion API con token.

### 4.2. Cursos y PR

- El gestor debe poder crear PR nuevos para un curso.
- El gestor y el revisor autorizado deben poder modificar fase, nombre y fecha limite del PR.
- El docente solo debe acceder a los PR relacionados con sus cursos.

### 4.3. Gestion documental

- El gestor debe poder crear documentos dentro de un PR.
- Cada documento debe quedar asociado a una plantilla valida.
- El sistema debe generar una variante inicial al crear un documento.
- El sistema debe permitir asignar y retirar revisores.
- El sistema debe permitir actualizar el tema cuando el tipo documental lo admita.

### 4.4. Variantes y estados

- El sistema debe permitir crear variantes nuevas.
- El sistema debe mantener estados documentales coherentes.
- No deben coexistir estados incompatibles en el mismo documento.
- Debe poder abrirse el archivo asociado a una variante si existe.

### 4.5. Tareas y notificaciones

- El sistema debe mostrar tareas ordenadas por urgencia.
- El sistema debe resumir tareas vencidas, de hoy y de los proximos siete dias.
- El sistema debe crear notificaciones internas ante cambios relevantes.
- El sistema debe marcar notificaciones como leidas al abrirlas.

### 4.6. Chat

- El sistema debe permitir conversaciones internas entre usuarios validos.
- Debe conservarse la lista de conversaciones iniciadas.
- Debe mostrarse contador de mensajes no leidos.
- Debe generarse notificacion al recibir un mensaje nuevo.

## 5. Requisitos no funcionales

- La aplicacion debe ejecutarse en entorno web con interfaz accesible desde navegador moderno.
- La arquitectura debe ser mantenible y separada por controladores, acciones, modelos y vistas.
- El proyecto debe poder instalarse en local con SQLite.
- El sistema debe ofrecer una base de pruebas automatizadas defendible.
- Las vistas principales deben cuidar semantica, etiquetas y estados accesibles.

## 6. Actores del sistema

- Gestor: administra cursos, PR, documentos, plantillas y asignaciones.
- Docente: sigue PR y tareas de los cursos a los que pertenece.
- Revisor: revisa documentos asignados y participa en el flujo de estados.

## 7. Criterios de exito

Se considera que el proyecto cumple su objetivo si:

- un usuario puede autenticarse y acceder solo a lo que le corresponde;
- un gestor puede crear y mantener PR y documentos;
- un revisor puede revisar documentos y estados permitidos;
- las tareas y notificaciones ayudan a priorizar el trabajo;
- el chat interno permite coordinacion sin salir del sistema;
- la aplicacion puede demostrarse y probarse con estabilidad.
