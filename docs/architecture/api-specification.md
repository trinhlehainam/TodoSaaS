# API Specification

## REST API Specification

```yaml
openapi: 3.0.0
info:
  title: TaskFlow Pro API
  version: 1.0.0
  description: Multi-tenant SaaS task management API
servers:
  - url: http://localhost:8000/api
    description: Development server
  - url: https://api.taskflow.pro/api
    description: Production server

paths:
  /v1/auth/register:
    post:
      summary: Register new user
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                name:
                  type: string
                email:
                  type: string
                  format: email
                password:
                  type: string
                  minLength: 8
                password_confirmation:
                  type: string
      responses:
        201:
          description: User created successfully
          
  /v1/auth/login:
    post:
      summary: User login
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                email:
                  type: string
                password:
                  type: string
                remember:
                  type: boolean
      responses:
        200:
          description: Login successful
          
  /v1/tasks:
    get:
      summary: List tasks
      parameters:
        - name: status
          in: query
          schema:
            type: string
            enum: [pending, in-progress, completed]
        - name: priority
          in: query
          schema:
            type: string
            enum: [low, medium, high]
        - name: page
          in: query
          schema:
            type: integer
      responses:
        200:
          description: Task list
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/Task'
                  meta:
                    $ref: '#/components/schemas/PaginationMeta'
                    
    post:
      summary: Create task
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/TaskInput'
      responses:
        201:
          description: Task created

components:
  schemas:
    Task:
      type: object
      properties:
        id:
          type: integer
        title:
          type: string
        description:
          type: string
        status:
          type: string
        priority:
          type: string
        due_date:
          type: string
          format: date
          
    TaskInput:
      type: object
      required:
        - title
      properties:
        title:
          type: string
        description:
          type: string
        priority:
          type: string
        due_date:
          type: string
          
    PaginationMeta:
      type: object
      properties:
        current_page:
          type: integer
        per_page:
          type: integer
        total:
          type: integer
        last_page:
          type: integer
```

---
