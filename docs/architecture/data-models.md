# Data Models

## User Model
**Purpose:** Core authentication entity managing user accounts and preferences

**Key Attributes:**
- id: bigserial - Primary key
- name: string - User display name
- email: string - Unique email address
- email_verified_at: timestamp - Email verification status
- password: string - Hashed password
- two_factor_secret: text - 2FA secret key
- two_factor_recovery_codes: text - Backup codes
- current_team_id: bigint - Active team context
- stripe_id: string - Stripe customer ID
- created_at/updated_at: timestamps - Audit timestamps

**TypeScript Interface:**
```typescript
interface User {
  id: number;
  name: string;
  email: string;
  emailVerifiedAt: string | null;
  currentTeamId: number | null;
  stripeId: string | null;
  twoFactorEnabled: boolean;
  createdAt: string;
  updatedAt: string;
}
```

**Relationships:**
- Has many teams (owner)
- Belongs to many teams (member)
- Has many tasks
- Has many social accounts

## Team Model
**Purpose:** Multi-tenancy workspace for collaborative task management

**Key Attributes:**
- id: bigserial - Primary key  
- name: string - Team display name
- owner_id: bigint - Team owner user ID
- stripe_id: string - Stripe customer ID for billing
- created_at/updated_at: timestamps - Audit timestamps

**TypeScript Interface:**
```typescript
interface Team {
  id: number;
  name: string;
  ownerId: number;
  stripeId: string | null;
  subscription?: Subscription;
  memberCount?: number;
  createdAt: string;
  updatedAt: string;
}
```

**Relationships:**
- Belongs to user (owner)
- Has many team members
- Has many tasks
- Has one subscription
- Has many API keys

## Task Model
**Purpose:** Core business entity representing work items

**Key Attributes:**
- id: bigserial - Primary key
- team_id: bigint - Team context
- user_id: bigint - Task assignee
- title: string - Task title
- description: text - Task details
- status: enum - pending/in-progress/completed
- priority: enum - low/medium/high
- due_date: date - Due date
- completed_at: timestamp - Completion timestamp

**TypeScript Interface:**
```typescript
interface Task {
  id: number;
  teamId: number;
  userId: number;
  title: string;
  description: string | null;
  status: 'pending' | 'in-progress' | 'completed';
  priority: 'low' | 'medium' | 'high';
  dueDate: string | null;
  completedAt: string | null;
  createdAt: string;
  updatedAt: string;
}
```

**Relationships:**
- Belongs to team
- Belongs to user
- Has many tags (future)

## ApiKey Model
**Purpose:** External API authentication and usage tracking

**Key Attributes:**
- id: bigserial - Primary key
- team_id: bigint - Team owner
- name: string - Key description
- key: string - Unique API key (shown once)
- secret_hash: string - Hashed secret
- scopes: jsonb - Permission scopes
- last_used_at: timestamp - Last usage
- expires_at: timestamp - Expiration date

**TypeScript Interface:**
```typescript
interface ApiKey {
  id: number;
  teamId: number;
  name: string;
  key: string; // Only shown on creation
  scopes: string[];
  lastUsedAt: string | null;
  expiresAt: string | null;
  createdAt: string;
}
```

**Relationships:**
- Belongs to team
- Has many usage records

---
