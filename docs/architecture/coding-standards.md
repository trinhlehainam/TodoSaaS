# Coding Standards

## Critical Fullstack Rules

- **Type Sharing:** Always define types in backend API Resources and generate TypeScript interfaces
- **API Calls:** Never make direct HTTP calls - use the service layer with React Query
- **Environment Variables:** Access only through config objects, never process.env directly
- **Error Handling:** All API routes must use the standard Laravel exception handler
- **State Updates:** Never mutate state directly - use proper immutable patterns
- **Database Queries:** Always use Eloquent ORM, never raw SQL except for reports
- **Authentication:** Check auth at route level, not in components
- **Caching:** Invalidate cache on writes, use cache tags for granular control

## Naming Conventions

| Element | Frontend | Backend | Example |
|---------|----------|---------|---------|
| Components | PascalCase | - | `TaskCard.tsx` |
| Hooks | camelCase with 'use' | - | `useAuth.ts` |
| API Routes | - | kebab-case | `/api/task-lists` |
| Database Tables | - | snake_case plural | `team_members` |
| Models | - | PascalCase singular | `TeamMember.php` |
| Services | PascalCase | PascalCase | `TaskService` |
| Repositories | - | PascalCase | `TaskRepository` |

---
