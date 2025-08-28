# Unified Project Structure

```
taskflow-pro/
├── .github/                    # CI/CD workflows
│   └── workflows/
│       ├── ci.yaml             # Test & lint pipeline
│       └── deploy.yaml         # Deployment pipeline
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Middleware/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   └── Policies/
│   ├── database/
│   │   ├── migrations/
│   │   ├── factories/
│   │   └── seeders/
│   ├── routes/
│   │   ├── api.php
│   │   └── external.php
│   ├── tests/
│   │   ├── Feature/
│   │   └── Unit/
│   ├── docker-compose.yml     # Laravel Sail config
│   ├── .env.example
│   └── phpunit.xml
├── frontend/                   # Next.js SPA
│   ├── app/                    # App Router
│   │   ├── (auth)/
│   │   ├── (dashboard)/
│   │   ├── api/
│   │   └── layout.tsx
│   ├── components/
│   │   ├── ui/                # Shadcn components
│   │   ├── features/
│   │   └── layouts/
│   ├── lib/
│   │   ├── api/               # API client
│   │   ├── hooks/             # Custom hooks
│   │   ├── auth/              # NextAuth config
│   │   └── utils/
│   ├── public/
│   ├── styles/
│   ├── tests/
│   │   ├── unit/
│   │   └── e2e/
│   ├── .env.local.example
│   ├── next.config.js
│   └── tsconfig.json
├── docs/                       # Documentation
│   ├── prd.md
│   ├── architecture.md        # This document
│   ├── api/
│   └── deployment/
├── scripts/                    # Build/deploy scripts
│   ├── setup.sh               # Initial setup
│   ├── test.sh                # Run all tests
│   └── deploy.sh              # Deployment script
├── .env.example               # Root environment template
├── package.json               # Root package.json for scripts
├── docker-compose.yml         # Development orchestration
├── Makefile                   # Common commands
└── README.md                  # Project documentation
```

---
