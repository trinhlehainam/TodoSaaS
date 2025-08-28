# Testing Strategy

## Testing Pyramid
```
        E2E Tests (10%)
       /              \
    Integration Tests (30%)
    /                    \
Frontend Unit (30%)  Backend Unit (30%)
```

## Test Organization

### Frontend Tests
```
frontend/tests/
├── unit/
│   ├── components/
│   │   └── TaskCard.test.tsx
│   └── hooks/
│       └── useAuth.test.ts
├── integration/
│   └── api/
│       └── tasks.test.ts
└── e2e/
    └── user-flows/
        └── task-management.cy.ts
```

### Backend Tests
```
backend/tests/
├── Unit/
│   ├── Models/
│   │   └── TaskTest.php
│   └── Services/
│       └── TaskServiceTest.php
├── Feature/
│   ├── Auth/
│   │   └── LoginTest.php
│   └── Api/
│       └── TaskApiTest.php
└── Integration/
    └── Billing/
        └── StripeWebhookTest.php
```

## Test Examples

### Frontend Component Test
```typescript
// tests/unit/components/TaskCard.test.tsx
import { render, screen, fireEvent } from '@testing-library/react';
import { TaskCard } from '@/components/features/tasks/TaskCard';

describe('TaskCard', () => {
  const mockTask = {
    id: 1,
    title: 'Test Task',
    status: 'pending',
    priority: 'high',
  };

  it('renders task information', () => {
    render(<TaskCard task={mockTask} />);
    expect(screen.getByText('Test Task')).toBeInTheDocument();
    expect(screen.getByText('high')).toBeInTheDocument();
  });

  it('calls onUpdate when status clicked', () => {
    const onUpdate = jest.fn();
    render(<TaskCard task={mockTask} onUpdate={onUpdate} />);
    fireEvent.click(screen.getByRole('button', { name: /status/i }));
    expect(onUpdate).toHaveBeenCalled();
  });
});
```

### Backend API Test
```php
<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Team;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $user->id]);
        
        $this->actingAs($user)
            ->postJson('/api/v1/tasks', [
                'title' => 'New Task',
                'priority' => 'high',
            ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New Task',
                    'priority' => 'high',
                ],
            ]);
            
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'team_id' => $team->id,
        ]);
    }
}
```

### E2E Test
```typescript
// tests/e2e/task-management.cy.ts
describe('Task Management Flow', () => {
  beforeEach(() => {
    cy.login('user@example.com', 'password');
  });

  it('creates and completes a task', () => {
    cy.visit('/tasks');
    cy.get('[data-testid="create-task-btn"]').click();
    
    cy.get('input[name="title"]').type('E2E Test Task');
    cy.get('select[name="priority"]').select('high');
    cy.get('button[type="submit"]').click();
    
    cy.contains('E2E Test Task').should('be.visible');
    cy.get('[data-testid="task-status"]').click();
    cy.contains('completed').click();
    
    cy.get('[data-testid="task-badge"]')
      .should('contain', 'completed');
  });
});
```

---
