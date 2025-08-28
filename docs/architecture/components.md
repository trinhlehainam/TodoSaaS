# Components

## Authentication Service
**Responsibility:** Handle all authentication flows including email/password, OAuth, 2FA, and API keys

**Key Interfaces:**
- POST /auth/register - User registration
- POST /auth/login - Session authentication
- POST /auth/oauth/{provider} - OAuth flow
- POST /auth/2fa/verify - 2FA verification

**Dependencies:** Laravel Fortify, NextAuth.js, Laravel Socialite

**Technology Stack:** PHP 8.3, Laravel Fortify for backend, NextAuth.js for frontend OAuth

## Task Management Service
**Responsibility:** Core CRUD operations for tasks with team context and permissions

**Key Interfaces:**
- TaskRepository - Data access layer
- TaskPolicy - Authorization rules
- TaskResource - API response formatting
- TaskController - HTTP endpoint handling

**Dependencies:** Team Service, User Service, Cache Service

**Technology Stack:** Laravel Eloquent ORM, PostgreSQL, Valkey cache

## Subscription Service
**Responsibility:** Handle billing, subscriptions, and feature gating

**Key Interfaces:**
- SubscriptionController - Plan management endpoints
- BillingService - Stripe integration logic
- FeatureGate - Feature availability checks

**Dependencies:** Laravel Cashier, Stripe API

**Technology Stack:** Laravel Cashier, Stripe SDK, Webhook handlers

## API Key Service
**Responsibility:** Generate, validate, and track API key usage

**Key Interfaces:**
- ApiKeyController - Key management endpoints
- ApiKeyMiddleware - Authentication middleware
- UsageTracker - Request logging service

**Dependencies:** Team Service, Rate Limiter

**Technology Stack:** Laravel middleware, PostgreSQL JSONB for scopes

## Frontend API Client
**Responsibility:** Centralized API communication layer for Next.js

**Key Interfaces:**
- ApiClient - Axios instance with interceptors
- useAuth - Authentication hook
- useQuery - Data fetching with React Query

**Dependencies:** Axios, React Query, NextAuth.js

**Technology Stack:** TypeScript, Axios, TanStack Query v5

## Component Diagram

```mermaid
graph TB
    subgraph "Frontend Layer"
        NEXT[Next.js App]
        AUTH_UI[Auth Components]
        TASK_UI[Task Components]
        BILLING_UI[Billing Components]
    end
    
    subgraph "API Gateway"
        MIDDLEWARE[Laravel Middleware Stack]
        RATE_LIMIT[Rate Limiter]
        AUTH_MW[Auth Middleware]
    end
    
    subgraph "Service Layer"
        AUTH_SVC[Auth Service]
        TASK_SVC[Task Service]
        BILLING_SVC[Billing Service]
        API_SVC[API Key Service]
    end
    
    subgraph "Data Layer"
        REPO[Repositories]
        CACHE[Valkey Cache]
        DB[PostgreSQL]
    end
    
    NEXT --> MIDDLEWARE
    MIDDLEWARE --> AUTH_MW
    AUTH_MW --> RATE_LIMIT
    RATE_LIMIT --> AUTH_SVC
    RATE_LIMIT --> TASK_SVC
    RATE_LIMIT --> BILLING_SVC
    RATE_LIMIT --> API_SVC
    
    AUTH_SVC --> REPO
    TASK_SVC --> REPO
    BILLING_SVC --> REPO
    API_SVC --> REPO
    
    REPO --> CACHE
    REPO --> DB
```

---
