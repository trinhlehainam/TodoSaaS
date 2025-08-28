# Development Workflow

## Local Development Setup

### Prerequisites
```bash
# Required software
- PHP 8.3+
- Composer 2.x
- Node.js 18+
- Docker Desktop
- Git
```

### Initial Setup
```bash
# Clone repository
git clone https://github.com/yourteam/taskflow-pro.git
cd taskflow-pro

# Backend setup
cd backend
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed

# Frontend setup
cd ../frontend
cp .env.local.example .env.local
npm install
npm run dev
```

### Development Commands
```bash
# Start all services
make dev

# Start frontend only
cd frontend && npm run dev

# Start backend only
cd backend && ./vendor/bin/sail up

# Run tests
make test

# Format code
make format

# Database operations
./vendor/bin/sail artisan migrate:fresh --seed
```

## Environment Configuration

### Required Environment Variables
```bash
# Frontend (.env.local)
NEXT_PUBLIC_API_URL=http://localhost:8000/api
NEXTAUTH_URL=http://localhost:3000
NEXTAUTH_SECRET=your-secret-key
GOOGLE_CLIENT_ID=your-google-id
GOOGLE_CLIENT_SECRET=your-google-secret
GITHUB_CLIENT_ID=your-github-id
GITHUB_CLIENT_SECRET=your-github-secret

# Backend (.env)
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=taskflow
DB_USERNAME=sail
DB_PASSWORD=password
REDIS_HOST=valkey
REDIS_PASSWORD=null
REDIS_PORT=6379
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Shared
APP_ENV=local
APP_DEBUG=true
```

---
