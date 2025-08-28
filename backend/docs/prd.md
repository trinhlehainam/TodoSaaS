# TaskFlow Pro Backend Requirements

## Overview
Laravel 12.x backend implementation for a production-ready multi-tenant SaaS task management platform with comprehensive authentication, subscription billing, and external API access.

## Technical Stack
- **Framework**: Laravel 12.x (Modular Monolith Architecture)
- **Database**: PostgreSQL 17
- **Cache**: Valkey 7.x
- **Queue**: Laravel Queues (Database driver, upgradeable to Redis/SQS)
- **Authentication**: Laravel Fortify + Passport
- **Payments**: Laravel Cashier + Stripe
- **Development**: Laravel Sail (Docker)
- **Testing**: Pest for unit/integration tests

## Core Requirements

### Authentication & Security
- **Email/Password Auth**: Registration, verification, login with bcrypt hashing
- **OAuth Integration**: Laravel Socialite for Google/GitHub providers
- **2FA Support**: TOTP implementation with recovery codes
- **Session Management**: 2-hour timeout with sliding expiration, httpOnly secure cookies
- **API Authentication**: Laravel Passport for API key generation and management
- **Rate Limiting**: 60 req/min authenticated, 10 req/min guest
- **Security Headers**: CSP, XSS protection enforcement

### Database Schema

#### Core Tables
- **users**: id, name, email, password, email_verified_at, two_factor_secret, two_factor_recovery_codes, current_team_id
- **teams**: id, name, owner_id, timestamps
- **team_members**: user_id, team_id, role (owner/admin/member)
- **team_invitations**: id, team_id, email, token, expires_at
- **tasks**: id, team_id, user_id, title, description, status, priority, due_date, completed_at
- **api_keys**: id, team_id, name, key_hash, scopes (JSONB), last_used_at, expires_at
- **subscriptions**: Stripe subscription data via Laravel Cashier

### API Endpoints

#### Authentication
- POST `/api/auth/register` - User registration
- POST `/api/auth/login` - Email/password login
- POST `/api/auth/logout` - Session termination
- POST `/api/auth/oauth/{provider}` - OAuth initiation
- GET `/api/auth/oauth/{provider}/callback` - OAuth callback
- POST `/api/auth/2fa/enable` - Enable 2FA
- POST `/api/auth/2fa/verify` - Verify 2FA code

#### Teams
- GET `/api/teams` - List user's teams
- POST `/api/teams` - Create team
- PUT `/api/teams/{id}` - Update team
- DELETE `/api/teams/{id}` - Delete team (soft)
- POST `/api/teams/{id}/invite` - Send invitation
- POST `/api/teams/invitations/{token}/accept` - Accept invitation
- DELETE `/api/teams/{id}/members/{userId}` - Remove member

#### Tasks
- GET `/api/tasks` - List tasks (paginated, filterable)
- POST `/api/tasks` - Create task
- GET `/api/tasks/{id}` - Get task details
- PUT `/api/tasks/{id}` - Update task
- DELETE `/api/tasks/{id}` - Delete task (soft)
- PATCH `/api/tasks/{id}/assign` - Assign task
- PATCH `/api/tasks/{id}/status` - Update status

#### Billing
- GET `/api/billing/plans` - Available subscription plans
- POST `/api/billing/checkout` - Create Stripe checkout session
- POST `/api/billing/portal` - Generate billing portal link
- POST `/api/billing/webhooks/stripe` - Stripe webhook handler

#### API Keys
- GET `/api/keys` - List team's API keys
- POST `/api/keys` - Generate new key
- DELETE `/api/keys/{id}` - Revoke key
- GET `/api/keys/{id}/usage` - Usage statistics

### Service Layer Architecture

#### Modules
- **Auth Module**: User management, authentication, 2FA
- **Team Module**: Multi-tenancy, invitations, permissions
- **Task Module**: CRUD operations, assignment logic
- **Billing Module**: Stripe integration, subscription management
- **API Module**: Key generation, rate limiting, usage tracking

#### Key Services
- `AuthService`: Registration, login, OAuth handling
- `TeamService`: Team creation, member management
- `TaskService`: Task operations, filtering, assignment
- `BillingService`: Stripe operations, webhook processing
- `ApiKeyService`: Key generation, validation, tracking

### Background Jobs
- Email verification sending
- Team invitation emails
- Stripe webhook processing
- API usage aggregation
- Task reminder notifications (future)

### Caching Strategy
- Query result caching (5 minutes with stale-while-revalidate)
- User session caching
- Team membership caching
- API rate limit counters in cache

### Performance Requirements
- Response time <200ms (95th percentile)
- Database query optimization with proper indexing
- N+1 query prevention with eager loading
- Connection pooling for PostgreSQL
- Queue processing for non-critical operations

## Epic Implementation

### Epic 1: Foundation & Core Authentication

#### Story 1.1: Project Infrastructure (Backend)
- Initialize Laravel 12.x application
- Configure Laravel Sail with PostgreSQL, Valkey, Mailpit
- Setup environment variables and configuration
- Create health check endpoint `/api/health`
- Configure Laravel Telescope for development

#### Story 1.2: Database Schema
- Create all user/auth related migrations
- Add proper indexes on foreign keys
- Create database seeders for testing
- Setup migration rollback testing

#### Story 1.3: Registration API
- Implement registration endpoint with validation
- Password hashing with bcrypt
- Email verification token generation
- Rate limiting middleware
- Return appropriate error responses

#### Story 1.4: Login & Sessions
- Login endpoint with credential validation
- Session cookie generation with security flags
- Session management with Laravel's session driver
- Logout endpoint implementation

#### Story 1.5: OAuth Backend
- Configure Laravel Socialite
- Provider configuration for Google/GitHub
- OAuth callback handling
- User account creation/linking from OAuth

### Epic 2: Team Management

#### Story 2.1: Team Models & Migrations
- Create teams and team_members migrations
- Define Eloquent relationships
- Setup team factories for testing

#### Story 2.2: Team CRUD API
- Team creation with owner assignment
- Update/delete with authorization
- Team switching functionality
- List teams endpoint

#### Story 2.3: Invitation System
- Invitation token generation
- Email sending via queues
- Invitation acceptance/rejection logic
- Pending invitation management

#### Story 2.4: Authorization Policies
- Laravel policies for team resources
- Role-based permission checks
- Middleware for team context
- 403 responses for unauthorized access

### Epic 3: Task Management

#### Story 3.1: Task Model & Repository
- Task migration with all fields
- Eloquent model with relationships
- Task repository pattern
- Enum classes for status/priority

#### Story 3.2: Task API Endpoints
- CRUD operations with validation
- Team association logic
- Authorization checks
- Resource transformers

#### Story 3.3: Task Filtering Service
- Query builder for filters
- Pagination implementation
- Sorting logic
- Full-text search capability

#### Story 3.4: Task Business Logic
- Assignment validation
- Status transition rules
- Bulk operations support
- Activity logging

### Epic 4: Subscription Billing

#### Story 4.1: Stripe Setup
- Laravel Cashier installation
- Webhook endpoint registration
- Stripe customer creation
- Product/price configuration

#### Story 4.2: Checkout API
- Checkout session creation
- Success/cancel URL handling
- Subscription status tracking

#### Story 4.3: Webhook Processing
- Event handler implementations
- Subscription synchronization
- Payment failure handling
- Webhook logging

#### Story 4.4: Feature Gating
- Subscription tier checking
- Feature flag implementation
- Grace period logic
- Billing portal API

### Epic 5: External API

#### Story 5.1: API Key System
- Key generation with secure tokens
- Hashed storage implementation
- Scope management system
- Expiration handling

#### Story 5.2: Key Management API
- CRUD endpoints for keys
- Usage statistics queries
- Key regeneration logic

#### Story 5.3: API Authentication Middleware
- Header validation
- Key verification
- Scope checking
- Rate limiting per key

#### Story 5.4: Usage Tracking
- Request logging system
- Usage aggregation jobs
- Analytics queries
- Export functionality

## Testing Requirements

### Unit Tests (Pest)
- Model tests with factories
- Service layer testing
- Repository pattern tests
- Helper function tests

### Integration Tests
- API endpoint testing
- Database transaction tests
- Queue job testing
- Cache operation tests

### Test Coverage
- Minimum 80% code coverage
- Critical path 100% coverage
- Mutation testing for business logic

## Deployment & DevOps
- Docker containerization
- Environment-based configuration
- Database migration strategy
- Queue worker configuration
- Log aggregation setup
- Health check endpoints
- Graceful shutdown handling