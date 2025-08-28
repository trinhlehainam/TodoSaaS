# Tech Stack

This is the **DEFINITIVE technology selection** for the entire TaskFlow Pro project. All development must use these exact versions and technologies as specified in the PRD.

## Technology Stack Table

| Category | Technology | Version | Purpose | Rationale |
|----------|------------|---------|---------|-----------|
| Frontend Language | TypeScript | 5.3+ | Type-safe JavaScript for React | Type safety reduces runtime errors and improves IDE support |
| Frontend Framework | Next.js | 15.x | React framework with SSR/SSG | Latest App Router for optimal performance and developer experience |
| UI Component Library | Shadcn/ui | Latest | Accessible React components | Copy-paste components with full customization control |
| State Management | TanStack Query | v5 | Server state management | Powerful caching and synchronization for API data |
| Backend Language | PHP | 8.3 | Laravel runtime | Latest stable with performance improvements and typed properties |
| Backend Framework | Laravel | 12.x | API development framework | Comprehensive ecosystem with built-in auth, billing, and testing |
| API Style | REST | - | JSON API standard | Simple, well-understood, perfect for CRUD operations |
| Database | PostgreSQL | 17 | Primary data storage | JSONB support, performance, and advanced indexing |
| Cache | Valkey | 7.x | Session & data caching | Redis-compatible, actively maintained fork with better licensing |
| File Storage | Local/S3 | - | User uploads & assets | Local for dev, S3-compatible for production |
| Authentication | Laravel Fortify + NextAuth.js | Latest | Multi-strategy auth | Fortify for API, NextAuth for OAuth SSO |
| Frontend Testing | Jest | 29+ | Unit & component tests | Fast, comprehensive testing with React Testing Library |
| Backend Testing | Pest | 2.x | PHP unit & feature tests | Elegant syntax built on PHPUnit, Laravel-optimized |
| E2E Testing | Cypress | 13+ | End-to-end testing | Reliable browser automation with great debugging |
| Build Tool | Vite (Laravel) / Turbopack (Next.js) | Latest | Asset compilation | Fast HMR and optimized production builds |
| Bundler | Webpack (via Next.js) | 5.x | JavaScript bundling | Integrated with Next.js build pipeline |
| IaC Tool | Docker Compose | Latest | Infrastructure as Code | Laravel Sail provides Docker orchestration |
| CI/CD | GitHub Actions | - | Automated testing/deployment | Native GitHub integration with marketplace actions |
| Monitoring | Laravel Telescope (dev) | Latest | Request monitoring | Built-in Laravel debugging and profiling |
| Logging | Monolog (Laravel) | 3.x | Structured logging | PSR-3 compliant with multiple handlers |
| CSS Framework | TailwindCSS | 3.4+ | Utility-first CSS | Rapid styling with excellent Next.js integration |

## Additional Development Tools

| Category | Technology | Version | Purpose | Rationale |
|----------|------------|---------|---------|-----------|
| Email Testing | Mailpit | Latest | Local email capture | Included with Laravel Sail, perfect for dev |
| PHP Formatter | Laravel Pint | Latest | Code style enforcement | Opinionated formatter following Laravel conventions |
| JS/TS Formatter | Biome | Latest | Fast linting & formatting | All-in-one replacement for ESLint + Prettier |
| Container Platform | Docker | Latest | Development environment | Consistent environment via Laravel Sail |
| Payment Processing | Stripe | Latest SDK | Subscription billing | Industry standard with Laravel Cashier integration |
| Package Manager (PHP) | Composer | 2.x | PHP dependency management | Standard PHP package manager |
| Package Manager (JS) | npm/yarn | Latest | JavaScript dependencies | npm for standard, yarn for performance |

---
