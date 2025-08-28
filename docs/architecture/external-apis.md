# External APIs

## Stripe API
- **Purpose:** Payment processing and subscription management
- **Documentation:** https://stripe.com/docs/api
- **Base URL(s):** https://api.stripe.com
- **Authentication:** Bearer token with secret key
- **Rate Limits:** 100 requests/second in test mode

**Key Endpoints Used:**
- `POST /v1/checkout/sessions` - Create checkout session
- `POST /v1/customers` - Create customer
- `POST /v1/subscriptions` - Manage subscriptions
- `POST /v1/payment_methods` - Update payment methods

**Integration Notes:** Use Laravel Cashier for abstraction, implement webhook handlers for events

## Google OAuth API
- **Purpose:** Social authentication provider
- **Documentation:** https://developers.google.com/identity/protocols/oauth2
- **Base URL(s):** https://accounts.google.com/o/oauth2/v2/auth
- **Authentication:** OAuth 2.0 flow
- **Rate Limits:** 10,000 requests per day

**Key Endpoints Used:**
- `GET /auth` - Authorization endpoint
- `POST /token` - Token exchange
- `GET /userinfo` - User profile data

**Integration Notes:** Use Laravel Socialite for backend, NextAuth.js for frontend

## GitHub OAuth API
- **Purpose:** Developer-focused social authentication
- **Documentation:** https://docs.github.com/en/developers/apps/building-oauth-apps
- **Base URL(s):** https://github.com/login/oauth
- **Authentication:** OAuth 2.0 flow
- **Rate Limits:** 5,000 requests per hour authenticated

**Key Endpoints Used:**
- `GET /authorize` - Authorization endpoint
- `POST /access_token` - Token exchange
- `GET /user` - User profile data

**Integration Notes:** Store access tokens for future API operations if needed

---
