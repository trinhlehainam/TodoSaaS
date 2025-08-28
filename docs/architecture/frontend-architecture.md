# Frontend Architecture

## Component Architecture

### Component Organization
```
frontend/
├── app/
│   ├── (auth)/              # Auth group layout
│   │   ├── login/
│   │   ├── register/
│   │   └── layout.tsx
│   ├── (dashboard)/         # Protected routes
│   │   ├── tasks/
│   │   ├── teams/
│   │   ├── billing/
│   │   └── layout.tsx
│   └── layout.tsx           # Root layout
├── components/
│   ├── ui/                  # Shadcn components
│   │   ├── button.tsx
│   │   ├── card.tsx
│   │   └── dialog.tsx
│   ├── features/            # Feature components
│   │   ├── tasks/
│   │   │   ├── TaskList.tsx
│   │   │   ├── TaskCard.tsx
│   │   │   └── TaskForm.tsx
│   │   └── billing/
│   └── layouts/
└── lib/
    ├── api/                 # API layer
    ├── hooks/               # Custom hooks
    └── utils/               # Utilities
```

### Component Template
```typescript
// components/features/tasks/TaskCard.tsx
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Task } from '@/types';

interface TaskCardProps {
  task: Task;
  onUpdate: (task: Task) => void;
  onDelete: (id: number) => void;
}

export function TaskCard({ task, onUpdate, onDelete }: TaskCardProps) {
  return (
    <Card className="hover:shadow-md transition-shadow">
      <CardHeader>
        <CardTitle className="flex justify-between">
          {task.title}
          <Badge variant={task.priority === 'high' ? 'destructive' : 'default'}>
            {task.priority}
          </Badge>
        </CardTitle>
      </CardHeader>
      <CardContent>
        <p className="text-muted-foreground">{task.description}</p>
        {/* Additional task UI */}
      </CardContent>
    </Card>
  );
}
```

## State Management Architecture

### State Structure
```typescript
// lib/stores/types.ts
interface AppState {
  auth: {
    user: User | null;
    team: Team | null;
    isAuthenticated: boolean;
  };
  tasks: {
    items: Task[];
    filters: TaskFilters;
    isLoading: boolean;
  };
  billing: {
    subscription: Subscription | null;
    invoices: Invoice[];
  };
}
```

### State Management Patterns
- Server state managed by React Query
- Client state with React Context for UI state
- Optimistic updates for better UX
- Automatic cache invalidation on mutations
- Prefetching for anticipated navigation

## Routing Architecture

### Route Organization
```
app/
├── (auth)/
│   ├── login/page.tsx       # /login
│   ├── register/page.tsx    # /register
│   └── layout.tsx           # Auth layout wrapper
├── (dashboard)/
│   ├── page.tsx             # /dashboard
│   ├── tasks/
│   │   ├── page.tsx         # /tasks
│   │   └── [id]/page.tsx    # /tasks/:id
│   ├── teams/
│   │   └── page.tsx         # /teams
│   └── layout.tsx           # Protected layout
└── api/
    └── auth/[...nextauth]/route.ts
```

### Protected Route Pattern
```typescript
// app/(dashboard)/layout.tsx
import { redirect } from 'next/navigation';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/lib/auth';

export default async function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getServerSession(authOptions);
  
  if (!session) {
    redirect('/login');
  }
  
  return (
    <div className="flex h-screen">
      <Sidebar />
      <main className="flex-1 overflow-y-auto">
        {children}
      </main>
    </div>
  );
}
```

## Frontend Services Layer

### API Client Setup
```typescript
// lib/api/client.ts
import axios from 'axios';
import { getSession } from 'next-auth/react';

const apiClient = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  withCredentials: true,
});

apiClient.interceptors.request.use(async (config) => {
  const session = await getSession();
  if (session?.accessToken) {
    config.headers.Authorization = `Bearer ${session.accessToken}`;
  }
  return config;
});

export default apiClient;
```

### Service Example
```typescript
// lib/api/tasks.ts
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiClient from './client';
import { Task, TaskInput } from '@/types';

export const useTasks = (filters?: TaskFilters) => {
  return useQuery({
    queryKey: ['tasks', filters],
    queryFn: async () => {
      const { data } = await apiClient.get('/v1/tasks', { params: filters });
      return data;
    },
  });
};

export const useCreateTask = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: async (task: TaskInput) => {
      const { data } = await apiClient.post('/v1/tasks', task);
      return data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['tasks'] });
    },
  });
};
```

---
