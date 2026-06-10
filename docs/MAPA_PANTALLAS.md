# Mapa de pantallas

## 1. Objetivo

Este documento resume las pantallas principales de la aplicacion y el flujo de navegacion entre ellas. Su finalidad es servir como apoyo al manual de usuario y a la defensa del proyecto.

## 2. Pantallas principales

### 2.1. Inicio de sesion

Ruta:

```text
/login
```

Funcion:

- autenticar al usuario y permitir el acceso al sistema.

### 2.2. Perfil

Ruta:

```text
/profile
```

Funcion:

- mostrar informacion personal del usuario;
- abrir ajustes;
- cambiar contrasena;
- ofrecer accesos rapidos al resto del sistema.

### 2.3. Cursos accesibles

Ruta:

```text
/courses
```

Funcion:

- listar los cursos visibles segun el rol del usuario.

### 2.4. Proyectos del curso

Ruta:

```text
/courses/{course}/pr
```

Funcion:

- mostrar los proyectos academicos asociados a un curso.

### 2.5. Documentos del proyecto

Ruta:

```text
/pr/{pr}/documentos
```

Funcion:

- gestionar documentos, variantes, estados, revisores y plantillas.

### 2.6. Tareas

Ruta:

```text
/tareas
```

Funcion:

- mostrar tareas priorizadas con fecha limite.

### 2.7. Chat

Ruta:

```text
/chat
```

Funcion:

- gestionar conversaciones entre usuarios y mensajes pendientes.

### 2.8. Notificaciones

Ruta:

```text
/notificaciones
```

Funcion:

- listar avisos del sistema y abrir los elementos relacionados.

### 2.9. Plantillas

Ruta:

```text
/plantillas
```

Funcion:

- consultar y administrar plantillas documentales.

## 3. Flujo principal de navegacion

```text
Login
  -> Perfil
      -> Cursos accesibles
          -> Proyectos del curso
              -> Documentos del proyecto
                  -> Variantes del documento
      -> Tareas
      -> Chat
      -> Notificaciones
      -> Plantillas
```

## 4. Flujo orientado al gestor

```text
Perfil
  -> Cursos
      -> Proyectos del curso
          -> Crear proyecto
          -> Entrar en proyecto
              -> Crear documento
              -> Asignar plantilla
              -> Asignar revisores
              -> Crear variantes
              -> Revisar estados
  -> Chat
  -> Notificaciones
  -> Plantillas
```

## 5. Flujo orientado al docente

```text
Perfil
  -> Cursos
      -> Proyectos del curso
          -> Entrar en proyecto
              -> Consultar documentos
  -> Tareas
  -> Chat
  -> Notificaciones
```

## 6. Flujo orientado al revisor

```text
Perfil
  -> Tareas
      -> Documento pendiente
          -> Revisar variantes
          -> Cambiar estado
  -> Chat
  -> Notificaciones
```

## 7. Pantallas recomendadas para la demo

Para una demostracion del proyecto se recomienda mostrar este recorrido:

1. Inicio de sesion.
2. Perfil.
3. Cursos accesibles.
4. Proyectos del curso.
5. Documentos del proyecto.
6. Tareas.
7. Chat.
8. Notificaciones.

## 8. Cierre

El mapa de pantallas refleja una navegacion centrada en el seguimiento academico. La estructura busca que el usuario pueda pasar rapidamente de la identificacion inicial a la consulta de cursos, proyectos, documentos, tareas, mensajes y avisos del sistema.
