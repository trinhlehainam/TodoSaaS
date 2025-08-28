# Security and Performance

## Security Requirements

**Frontend Security:**
- CSP Headers: `default-src 'self'; script-src 'self' 'unsafe-inline' https://js.stripe.com`
- XSS Prevention: React auto-escaping, DOMPurify for user content
- Secure Storage: httpOnly cookies for sessions, no sensitive data in localStorage

**Backend Security:**
- Input Validation: Laravel Form Requests with validation rules
- Rate Limiting: 60 requests/minute for authenticated, 10 for guests
- CORS Policy: Allow only frontend origin, credentials included

**Authentication Security:**
- Token Storage: httpOnly secure cookies
- Session Management: 2 hour timeout, sliding expiration
- Password Policy: Minimum 8 characters, complexity requirements

## Performance Optimization

**Frontend Performance:**
- Bundle Size Target: <200KB initial JS
- Loading Strategy: Code splitting, lazy loading, prefetching
- Caching Strategy: React Query 5 minute cache, stale-while-revalidate

**Backend Performance:**
- Response Time Target: <200ms p95
- Database Optimization: Indexes on foreign keys, query optimization
- Caching Strategy: 5 minute cache for listings, invalidate on write

---
