# Presentacion y demo

## 1. Objetivo

Este documento propone una estructura clara para la exposicion oral del proyecto y un recorrido de demostracion que permita enseñar valor funcional sin perder tiempo en detalles secundarios.

## 2. Estructura recomendada de la presentacion

### 2.1. Introduccion

- nombre del proyecto
- problema que resuelve
- contexto academico del desarrollo
- objetivo general

### 2.2. Necesidad detectada

- gestion documental dispersa
- dificultad para coordinar gestor, docente y revisor
- falta de trazabilidad en revisiones y estados
- ausencia de un punto unico para tareas y comunicacion

### 2.3. Solucion propuesta

Explicar que la aplicacion centraliza:

- cursos
- PR
- documentos y variantes
- plantillas
- tareas
- notificaciones
- chat interno

### 2.4. Arquitectura y tecnologias

- Laravel 13
- PHP 8.3
- Blade + JavaScript
- Vite + Tailwind
- SQLite en desarrollo
- Sanctum para API
- PHPUnit para pruebas

### 2.5. Seguridad y roles

- gestor
- docente
- revisor
- control de acceso por middleware y reglas de negocio

### 2.6. Calidad del proyecto

- pruebas automatizadas
- validacion de permisos
- mejora de accesibilidad en vistas clave
- suite verificada con 24 tests y 97 assertions

## 3. Guion de demo recomendado

### Paso 1. Login

- mostrar inicio de sesion
- entrar como gestor
- comentar que existen distintos roles

### Paso 2. Perfil

- enseñar resumen del usuario
- abrir ajustes
- comentar preferencias y accesibilidad

### Paso 3. Cursos y PR

- entrar en cursos
- abrir un curso
- mostrar PR asociados
- crear un PR si interesa enseñar una operacion de gestion

### Paso 4. Gestion documental

- abrir un PR
- mostrar documentos
- crear un documento
- enseñar variantes, estados y revisores

### Paso 5. Tareas

- abrir la vista de tareas
- explicar orden por urgencia y resumen de vencimientos

### Paso 6. Notificaciones

- mostrar notificaciones del sistema
- abrir una y enseñar marcado de lectura

### Paso 7. Chat

- enviar un mensaje
- mostrar contador de no leidos
- enseñar la conversacion guardada

### Paso 8. API y pruebas

- mencionar que la aplicacion tambien expone API protegida con Sanctum
- enseñar brevemente que existe una suite automatizada
- citar el resultado de la ejecucion completa de tests

## 4. Reparto orientativo del tiempo

Para una defensa de unos 10 a 15 minutos:

- 2 minutos: problema y objetivos
- 2 minutos: arquitectura y tecnologias
- 6 minutos: demo funcional
- 2 minutos: pruebas, seguridad y cierre

## 5. Preguntas que conviene preparar

- por que Laravel y no otro framework
- como se controlan los permisos
- como se asegura que no haya estados documentales incoherentes
- por que se ha elegido SQLite en desarrollo
- como se podria escalar el sistema en el futuro
- que se ha probado de forma automatizada

## 6. Mensajes fuertes para cerrar bien

- el proyecto resuelve un problema real de organizacion y seguimiento;
- la aplicacion no solo muestra datos, sino que controla permisos y trazabilidad;
- existe evidencia tecnica mediante pruebas automatizadas y control de acceso;
- el sistema es ampliable y tiene margen claro de mejora futura.
