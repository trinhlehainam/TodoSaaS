# Deployment Architecture

## Deployment Strategy

**Frontend Deployment:**
- **Platform:** Vercel
- **Build Command:** `npm run build`
- **Output Directory:** `.next`
- **CDN/Edge:** Vercel Edge Network

**Backend Deployment:**
- **Platform:** DigitalOcean App Platform / AWS ECS
- **Build Command:** `composer install --no-dev && php artisan config:cache`
- **Deployment Method:** Docker containers via registry

## CI/CD Pipeline
```yaml
# .github/workflows/deploy.yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Backend Tests
        run: |
          cd backend
          composer install
          php artisan test --coverage
          
      - name: Frontend Tests
        run: |
          cd frontend
          npm ci
          npm run test:ci
          npm run build

  deploy-backend:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Production
        run: |
          docker build -t taskflow-api ./backend
          docker push registry.digitalocean.com/taskflow/api
          doctl apps create-deployment $APP_ID

  deploy-frontend:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Vercel
        run: |
          npm i -g vercel
          cd frontend
          vercel --prod --token=$VERCEL_TOKEN
```

## Environments

| Environment | Frontend URL | Backend URL | Purpose |
|------------|--------------|-------------|---------|
| Development | http://localhost:3000 | http://localhost:8000 | Local development |
| Staging | https://staging.taskflow.app | https://api-staging.taskflow.app | Pre-production testing |
| Production | https://taskflow.app | https://api.taskflow.app | Live environment |

---
