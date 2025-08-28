# Monitoring and Observability

## Monitoring Stack

- **Frontend Monitoring:** Sentry for error tracking, Vercel Analytics for performance
- **Backend Monitoring:** Laravel Telescope (dev), New Relic APM (production)
- **Error Tracking:** Sentry unified for frontend and backend
- **Performance Monitoring:** Core Web Vitals, API response time tracking

## Key Metrics

**Frontend Metrics:**
- Core Web Vitals (LCP < 2.5s, FID < 100ms, CLS < 0.1)
- JavaScript error rate < 0.1%
- API response times p95 < 500ms
- User interaction success rate > 99%

**Backend Metrics:**
- Request rate (baseline: 1000 req/min)
- Error rate < 0.5%
- Response time p95 < 200ms
- Database query performance < 50ms
- Cache hit rate > 80%
- Queue processing time < 30s

---
