# Guia de defensa y demo

## 1. Objetivo

Este documento propone una estructura clara para la exposicion oral y la demostracion practica del proyecto. La idea es que la defensa no sea solo una enumeracion de pantallas, sino una explicacion tecnica y funcional con orden.

## 2. Estructura recomendada de la defensa

### 2.1. Presentacion inicial

Explicar en menos de un minuto:

- que problema resuelve la aplicacion
- para que tipo de usuarios esta pensada
- que tecnologias principales se han utilizado

### 2.2. Problema y necesidad

Plantear que la gestion de proyectos y documentacion academica puede dispersarse entre correos, ficheros y seguimiento manual, y que la aplicacion centraliza:

- cursos
- PR
- documentos y variantes
- revisiones
- tareas
- notificaciones
- chat

### 2.3. Arquitectura tecnica

Mostrar de forma breve:

- Laravel como base del backend
- Blade y JavaScript en frontend
- Actions para reglas de negocio
- SQLite en local
- Sanctum para API
- PHPUnit para pruebas

### 2.4. Modulos principales

Se recomienda explicar este orden:

1. login y perfil
2. cursos y proyectos
3. documentos y variantes
4. tareas
5. notificaciones
6. chat
7. API y pruebas

## 3. Recorrido recomendado de la demo

### Paso 1. Login

Entrar con un usuario valido y explicar que el sistema diferencia roles.

### Paso 2. Perfil

Mostrar:

- datos del usuario
- panel de ajustes
- notificaciones recientes
- acceso al resto del sistema

### Paso 3. Cursos y PR

Entrar en cursos y despues en un PR para explicar el seguimiento por curso.

### Paso 4. Documentos

Mostrar la tabla documental y explicar:

- tipo documental
- plantilla
- tema
- revisores
- variantes
- estados

### Paso 5. Crear un documento

Si la demo lo permite, crear un documento como gestor para enseñar un flujo completo.

### Paso 6. Tareas

Explicar como el sistema calcula prioridades por fechas limite.

### Paso 7. Chat y notificaciones

Mostrar un mensaje nuevo, el contador de no leidos y la notificacion asociada.

### Paso 8. Pruebas y API

Cerrar la demo mostrando que el proyecto no solo funciona visualmente, sino que tiene validacion automatizada y endpoints protegidos con token.

## 4. Aspectos tecnicos que conviene destacar

- separacion entre controladores y acciones de negocio
- control de permisos por rol y recurso
- validacion automatizada con pruebas funcionales
- accesibilidad basica añadida en vistas clave
- API protegida con Sanctum

## 5. Preguntas que puede hacer el tribunal

### Por que Laravel

Porque acelera autenticacion, routing, ORM, validacion y testing, permitiendo dedicar mas esfuerzo a reglas de negocio reales.

### Como controlas permisos

Mediante autenticacion, middleware por rol y comprobaciones de acceso sobre cursos, PR, documentos y variantes.

### Como sabes que funciona

Porque el proyecto incluye pruebas funcionales que cubren login, roles, PR, documentos, notificaciones, chat, tareas y API.

### Que mejorarias

- despliegue productivo
- mas pruebas E2E
- accesibilidad mas profunda
- mas analitica y estadisticas

## 6. Cierre recomendado

Terminar con una idea simple y defendible:

El proyecto no se queda en una interfaz bonita, sino que implementa un flujo academico completo con control documental, permisos, comunicacion interna, API y pruebas, por lo que cumple un alcance tecnico apropiado para un proyecto fin de ciclo.