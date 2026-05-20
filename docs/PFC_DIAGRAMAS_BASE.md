# Diagramas base para la memoria del PFC

## Uso de este documento

Estos diagramas estan pensados como base reutilizable para tu memoria. Puedes copiarlos a Mermaid, Draw.io, PlantUML o a la herramienta que prefieras para generar versiones mas visuales y pulidas.

## 1. Diagrama de casos de uso

```mermaid
flowchart LR
    Gestor[Gestor]
    Docente[Docente]
    Revisor[Revisor]

    UC1((Iniciar sesion))
    UC2((Consultar cursos))
    UC3((Crear PR))
    UC4((Crear documento))
    UC5((Asignar revisores))
    UC6((Actualizar tema))
    UC7((Crear variante))
    UC8((Cambiar estado de variante))
    UC9((Consultar notificaciones))
    UC10((Gestionar docentes del PR))

    Gestor --> UC1
    Gestor --> UC2
    Gestor --> UC3
    Gestor --> UC4
    Gestor --> UC5
    Gestor --> UC6
    Gestor --> UC7
    Gestor --> UC8
    Gestor --> UC9
    Gestor --> UC10

    Docente --> UC1
    Docente --> UC2
    Docente --> UC9

    Revisor --> UC1
    Revisor --> UC2
    Revisor --> UC6
    Revisor --> UC8
    Revisor --> UC9
```

## 2. Diagrama de clases UML

```mermaid
classDiagram
    class User {
        +id
        +name
        +email
        +password
        +hasRole()
        +hasAnyRole()
        +accessibleCourseIds()
        +canAccessCourse()
        +canAccessPr()
        +canAccessDocument()
        +canAccessVariant()
    }

    class Role {
        +id
        +name
    }

    class Course {
        +id
        +code
        +name
    }

    class PR {
        +id
        +course_id
        +number
        +fecha_limite
        +fase
    }

    class Document {
        +id
        +pr_id
        +plantilla_id
        +tema
        +type
        +short_title
        +canonical_name
        +supportsTema()
    }

    class DocumentVariant {
        +id
        +document_id
        +version
        +status_id
        +deadline_target
        +drive_link_url
        +created_by
    }

    class DocumentStatus {
        +id
        +name
    }

    class DocumentStatusHistory {
        +id
        +document_variant_id
        +status_id
    }

    class Plantilla {
        +id
        +tipo_documento
        +prefijo
    }

    class Notificacion {
        +id
        +user_id
        +titulo
        +mensaje
        +opened_at
    }

    User "*" -- "*" Role : tiene
    Course "1" --> "*" PR : contiene
    PR "1" --> "*" Document : agrupa
    PR "*" -- "*" User : docentes
    Document "*" -- "*" User : revisores
    Document "*" --> "1" Plantilla : usa
    Document "1" --> "*" DocumentVariant : versiona
    DocumentVariant "*" --> "1" DocumentStatus : estado_actual
    DocumentVariant "1" --> "*" DocumentStatusHistory : historico
    User "1" --> "*" Notificacion : recibe
```

## 3. Diagrama de secuencia: crear documento

```mermaid
sequenceDiagram
    actor Gestor
    participant Vista as Interfaz web
    participant Controller as PRDocumentController
    participant Action as CreateDocumentAction
    participant Plantillas as PlantillasAction
    participant Document as Document
    participant Variant as CreateVariantAction
    participant DB as Base de datos

    Gestor->>Vista: Solicita crear documento
    Vista->>Controller: POST /pr/{pr}/documentos/create
    Controller->>Controller: Validar type y tema
    Controller->>Action: createDocument(prId, type, userId, tema)
    Action->>Plantillas: latestByDocumentTypeOrFail(type)
    Plantillas-->>Action: plantilla
    Action->>DB: iniciar transaccion
    Action->>Document: crear documento
    Document->>DB: insertar documento
    Action->>Variant: create(document, userId)
    Variant->>DB: insertar primera variante
    Action->>DB: confirmar transaccion
    Action-->>Controller: documento creado
    Controller-->>Vista: JSON success + document_id
    Vista-->>Gestor: Confirmacion de alta
```

## 4. Diagrama E-R base

```mermaid
erDiagram
    USERS {
        int id PK
        string name
        string email
        string password
    }

    ROLES {
        int id PK
        string name
    }

    ROLE_USER {
        int user_id FK
        int role_id FK
    }

    COURSES {
        int id PK
        string code
        string name
    }

    PRS {
        int id PK
        int course_id FK
        int number
        date fecha_limite
        string fase
    }

    PR_TEACHERS {
        int pr_id FK
        int user_id FK
    }

    DOCUMENTS {
        int id PK
        int pr_id FK
        int plantilla_id FK
        int tema
        string type
        string short_title
        string canonical_name
    }

    DOCUMENT_REVIEWERS {
        int document_id FK
        int user_id FK
    }

    DOCUMENT_VARIANTS {
        int id PK
        int document_id FK
        int status_id FK
        int version
        date deadline_target
        string drive_link_url
        int created_by
    }

    DOCUMENT_STATUSES {
        int id PK
        string name
    }

    DOCUMENT_STATUS_HISTORIES {
        int id PK
        int document_variant_id FK
        int status_id FK
    }

    PLANTILLAS {
        int id PK
        string tipo_documento
        string prefijo
    }

    NOTIFICACIONS {
        int id PK
        int user_id FK
        string titulo
        string mensaje
        datetime opened_at
    }

    USERS ||--o{ ROLE_USER : tiene
    ROLES ||--o{ ROLE_USER : asigna
    COURSES ||--o{ PRS : contiene
    PRS ||--o{ PR_TEACHERS : relaciona
    USERS ||--o{ PR_TEACHERS : participa
    PRS ||--o{ DOCUMENTS : incluye
    PLANTILLAS ||--o{ DOCUMENTS : define
    DOCUMENTS ||--o{ DOCUMENT_REVIEWERS : asigna
    USERS ||--o{ DOCUMENT_REVIEWERS : revisa
    DOCUMENTS ||--o{ DOCUMENT_VARIANTS : genera
    DOCUMENT_STATUSES ||--o{ DOCUMENT_VARIANTS : clasifica
    DOCUMENT_VARIANTS ||--o{ DOCUMENT_STATUS_HISTORIES : registra
    DOCUMENT_STATUSES ||--o{ DOCUMENT_STATUS_HISTORIES : referencia
    USERS ||--o{ NOTIFICACIONS : recibe
```

## Recomendacion de uso en la memoria

- usa el diagrama de casos de uso en analisis de requisitos;
- usa el diagrama de clases en diseno estatico;
- usa el diagrama E-R en modelo de datos;
- usa el diagrama de secuencia en diseno estatico o codificacion.

## Siguiente paso recomendado

Convierte estos diagramas en imagenes limpias y anade una breve explicacion debajo de cada uno, indicando que representa, por que es relevante y como se relaciona con el funcionamiento del sistema.