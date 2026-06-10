# Manual de usuario

## 1. Objetivo del documento

Este manual explica el funcionamiento general de la aplicacion desde el punto de vista del usuario final. Se describen las pantallas principales, el flujo habitual de trabajo y las acciones que puede realizar cada perfil dentro del sistema.

## 2. Perfiles de usuario

La aplicacion diferencia tres perfiles funcionales:

- gestor: administra cursos, proyectos, documentos, plantillas y asignaciones.
- docente: consulta los proyectos y tareas vinculados a sus cursos.
- revisor: revisa documentos, consulta variantes y participa en el flujo de validacion.

No todos los usuarios ven exactamente las mismas acciones. Algunas operaciones, como crear proyectos o documentos, solo estan disponibles para el perfil gestor.

## 3. Acceso a la aplicacion

### 3.1. Inicio de sesion

El acceso se realiza desde la pantalla de login.

Ruta principal:

```text
/login
```

En esta pantalla el usuario debe introducir:

- correo electronico
- contrasena

Si las credenciales son correctas, el sistema redirige al perfil del usuario. Si son incorrectas, la aplicacion muestra un mensaje de error.

## 4. Pantalla de perfil

La pantalla de perfil es la vista inicial tras autenticarse.

Funciones principales:

- mostrar nombre, correo y roles del usuario;
- mostrar resumen de cursos accesibles;
- mostrar actividad reciente;
- abrir el panel de ajustes;
- cambiar la contrasena;
- cerrar sesion.

Desde esta pantalla se puede acceder facilmente al resto de apartados mediante la barra superior de navegacion.

## 5. Ajustes del perfil

Dentro del perfil existe un panel de ajustes que permite modificar preferencias personales.

Opciones disponibles:

- tema claro u oscuro;
- recepcion de correos de notificacion;
- tablas compactas;
- reduccion de animaciones;
- visibilidad del panel rapido de notificaciones.

Tambien existe una seccion de seguridad para cambiar la contrasena actual.

## 6. Cursos accesibles

La vista de cursos muestra el listado de cursos visibles para el usuario autenticado.

Informacion mostrada:

- codigo del curso;
- nombre del curso;
- ultimo proyecto asociado;
- fecha limite del proyecto mas reciente;
- docentes relacionados;
- fecha de ultima actividad.

Desde cada fila es posible entrar al detalle de proyectos del curso.

## 7. Gestion de proyectos del curso

La vista de proyectos muestra los PR asociados a un curso concreto.

Funciones habituales:

- consultar los proyectos existentes;
- revisar la fase de cada proyecto;
- editar la fecha limite;
- ver docentes asignados;
- crear un nuevo proyecto, en caso de ser gestor.

Si el usuario tiene permisos suficientes, puede actualizar la fase del proyecto y la informacion asociada desde la propia tabla.

## 8. Gestion documental del proyecto

La vista documental de un proyecto es una de las partes principales del sistema.

Permite:

- crear nuevos documentos dentro del proyecto;
- asignar una plantilla al documento;
- indicar tema cuando el tipo documental lo requiere;
- consultar el nombre visible, el titulo corto y el nombre canonico;
- revisar el estado resumido del documento;
- asignar o retirar revisores;
- desplegar las variantes del documento;
- abrir el archivo actual;
- eliminar documentos en determinadas condiciones.

Cada documento puede contener varias variantes versionadas, y cada una de ellas tiene su propio estado dentro del flujo documental.

## 9. Variantes de documento

Las variantes representan versiones concretas de un documento.

Operaciones que pueden aparecer segun el rol:

- crear una nueva variante;
- abrir una variante existente;
- cambiar el estado de la variante;
- eliminar una variante;
- consultar fechas y datos asociados.

El objetivo de esta parte del sistema es mantener trazabilidad sobre la evolucion documental del proyecto.

## 10. Tareas

La vista de tareas muestra al usuario un resumen priorizado de pendientes con fecha.

La pantalla ofrece:

- numero total de tareas con fecha;
- tareas vencidas;
- tareas para hoy;
- tareas previstas para los proximos siete dias;
- acceso directo a proyectos o documentos pendientes.

Esta vista resulta especialmente util para docentes y revisores, ya que ordena los elementos por urgencia.

## 11. Notificaciones

La vista de notificaciones muestra todos los avisos generados por el sistema.

Cada notificacion puede incluir:

- tema del aviso;
- mensaje descriptivo;
- fecha de envio;
- fecha de lectura;
- enlace al elemento relacionado.

Las notificaciones sirven para informar sobre cambios importantes, por ejemplo:

- cambios de estado de documentos;
- mensajes nuevos de chat;
- movimientos relevantes dentro del flujo documental.

## 12. Chat

La aplicacion incluye un apartado de chat interno entre usuarios.

Funciones disponibles:

- buscar contactos disponibles;
- abrir conversaciones existentes;
- enviar mensajes;
- mantener visible la lista de conversaciones iniciadas;
- mostrar mensajes sin leer en la barra superior;
- generar notificacion cuando se recibe un mensaje nuevo.

Cuando un usuario abre una conversacion concreta, los mensajes pendientes de esa conversacion quedan marcados como leidos.

## 13. Plantillas

La vista de plantillas permite consultar los modelos documentales disponibles.

En el caso del perfil gestor, tambien permite:

- crear nuevas plantillas;
- subir el archivo asociado;
- relacionar el tipo documental con el prefijo correspondiente.

Las plantillas son utilizadas por el sistema en la generacion y gestion de documentos.

## 14. Flujo de uso recomendado

El flujo habitual del sistema puede resumirse asi:

1. El usuario inicia sesion.
2. Accede a su perfil.
3. Consulta los cursos disponibles.
4. Entra en los proyectos del curso.
5. Abre la gestion documental de un proyecto.
6. Crea o revisa documentos y variantes segun su rol.
7. Consulta tareas y notificaciones pendientes.
8. Usa el chat si necesita comunicarse con otro usuario.

## 15. Recomendaciones de uso

Para un uso correcto del sistema se recomienda:

- revisar con frecuencia las notificaciones;
- consultar la vista de tareas al comenzar la jornada;
- mantener actualizadas las fechas limite;
- utilizar el chat interno para centralizar la comunicacion relacionada con el proyecto;
- no cerrar formularios sin guardar si se han realizado cambios.

## 16. Cierre

La aplicacion ha sido diseñada para centralizar el seguimiento academico de cursos, proyectos y documentacion asociada. El usuario final dispone de un entorno unificado donde puede consultar informacion, colaborar con otros perfiles y mantener trazabilidad sobre el estado de los materiales.
