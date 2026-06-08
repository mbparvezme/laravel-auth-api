# Laravel Auth API

A modular Laravel 13 authentication API starter kit. Drop it in, keep what you need, delete what you don't.

## Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
  - [Option A — Git Clone](#option-a--git-clone)
  - [Option B — Docker](#option-b--docker)
- [Environment Variables](#environment-variables)
- [API Reference](#api-reference)
  - [Response Format](#response-format)
  - [Authentication](#authentication)
  - [Core — Auth](#core--auth)
  - [Core — Profile & Account](#core--profile--account)
  - [Core — Activity Logs](#core--activity-logs)
  - [Feature — Social Auth](#feature--social-auth)
  - [Feature — API Keys](#feature--api-keys)
  - [Feature — Multi-Device](#feature--multi-device)
  - [Feature — Magic Link](#feature--magic-link)
  - [Feature — IP Rules](#feature--ip-rules)
- [Removing a Feature](#removing-a-feature)
- [Adding a New Language](#adding-a-new-language)
- [Customization Notes](#customization-notes)

---

## Features

### Core (always present)

| Feature | Description |
|---|---|
| Registration | Email + password sign-up with automatic email verification |
| Login / Logout | Sanctum token-based auth, single-device or all-device logout |
| Email Verification | Backend signs URL, user lands on frontend |
| Password Reset | Same FRONTEND_URL pattern |
| Profile Management | View and update profile fields |
| Email Change | Verify new address before updating |
| Password Change | Requires current password |
| Account Status | Users can set themselves active / inactive |
| Activity Logs | Paginated user-visible log of account events |
| Multi-language | English and Bengali out of the box |
| User Status Guard | active, inactive, suspended, banned — enforced on every request |

### Optional (each independently removable)

| Feature | Folder | What it adds |
|---|---|---|
| Social Auth | `app/Features/SocialAuth/` | Google + Facebook OAuth |
| API Keys | `app/Features/ApiKeys/` | Long-lived API keys with `X-API-Key` middleware |
| Multi-Device | `app/Features/MultiDevice/` | Track and revoke sessions per device |
| Magic Link | `app/Features/MagicLink/` | Passwordless login via email link |
| IP Rules | `app/Features/IpRules/` | Global IP allowlist / blocklist |

---

## Tech Stack

- **PHP** 8.2+ · **Laravel** 13 · **Laravel Sanctum**
- **Laravel Socialite** (Social Auth feature)
- **MySQL** 8.0 (PostgreSQL also works — change `DB_CONNECTION`)
- **Redis** (optional — for cache/queue)
- **Pest** for testing

---

## Getting Started

| | Option A | Option B |
|---|---|---|
| **PHP installed locally** | Required | Not needed |
| **MySQL installed locally** | Required | Not needed |
| **Best for** | Local dev | Local dev (no PHP/MySQL setup) |

---

### Option A — Git Clone

**Requirements:** PHP 8.2+, Composer, MySQL

```bash
git clone https://github.com/your-username/laravel-auth-api.git
cd laravel-auth-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API available at `http://localhost:8000`.

---

### Option B — Docker

**Requirements:** Docker Desktop (or Docker Engine + Compose plugin)

Docker replaces PHP and MySQL. You still clone the repository because the source code is bind-mounted into the container — edits are reflected immediately without rebuilding.

```bash
git clone https://github.com/your-username/laravel-auth-api.git
cd laravel-auth-api
cp .env.example .env
```

Set at minimum in `.env`:

```env
APP_KEY=          # generate with: php artisan key:generate --show
DB_DATABASE=laravel_auth
DB_USERNAME=laravel
DB_PASSWORD=secret
```

```bash
docker compose up --build
```

The entrypoint waits for MySQL, runs all migrations, then starts PHP-FPM. API available at `http://localhost:8080`.

```bash
docker compose exec app php artisan tinker   # run artisan commands
docker compose logs -f app                   # view logs
docker compose down                          # stop
docker compose down -v                       # stop + delete database volume
```

**Build and publish your image:**

```bash
docker build -t your-org/laravel-auth-api:latest .
docker push your-org/laravel-auth-api:latest
```

Set `APP_ENV=production` at runtime — the entrypoint caches config, routes, views, and events automatically.

---

## Environment Variables

Only custom variables are listed here. All standard Laravel variables (`APP_*`, `DB_*`, `MAIL_*`, etc.) remain in `.env.example` with their defaults.

| Variable | Default | Description |
|---|---|---|
| `FRONTEND_URL` | `http://localhost:3000` | Base URL of your SPA. Email verification, password reset, and magic link emails link here. |
| `SANCTUM_TOKEN_EXPIRY` | `43200` | Sanctum token lifetime in minutes (`43200` = 30 days, `0` = no expiry) |

### Social Auth *(Feature: SocialAuth)*

| Variable | Description |
|---|---|
| `GOOGLE_CLIENT_ID` | Google OAuth client ID |
| `GOOGLE_CLIENT_SECRET` | Google OAuth client secret |
| `GOOGLE_REDIRECT_URI` | Callback URL, e.g. `https://api.example.com/api/v1/auth/google/callback` |
| `FACEBOOK_APP_ID` | Facebook App ID |
| `FACEBOOK_APP_SECRET` | Facebook App secret |
| `FACEBOOK_REDIRECT_URI` | Facebook callback URL |

### Docker *(Option B only)*

| Variable | Default | Description |
|---|---|---|
| `NGINX_PORT` | `8080` | Host port for Nginx |
| `DB_FORWARD_PORT` | `3306` | Host port forwarded to MySQL |
| `DB_ROOT_PASSWORD` | `rootsecret` | MySQL root password |
| `REDIS_FORWARD_PORT` | `6379` | Host port forwarded to Redis |

---

## API Reference

### Response Format

**Success**
```json
{ "success": true, "message": "...", "data": { } }
```

**Error**
```json
{ "success": false, "message": "..." }
```

**Validation error** `422`
```json
{ "success": false, "message": "...", "errors": { "field": ["message"] } }
```

`data` is omitted on errors. `errors` is only present on validation failures.

---

### Authentication

Protected routes require a Bearer token:

```
Authorization: Bearer 1|abc123...
```

Routes requiring **verified email** return `403` if the email has not been verified.

Routes marked **[signed]** use a signed URL sent by email — not called manually.

**Rate limits:** `5 req/min` on public auth routes · `60 req/min` on authenticated routes.

---

### Core — Auth

#### Register

```
POST /api/v1/register
```

**Request**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Secret123!",
  "password_confirmation": "Secret123!"
}
```

**Response** `201`
```json
{
  "success": true,
  "message": "Account created successfully! Please check your email to verify your account.",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "status": "active", "profile": { } },
    "token": "1|abc123..."
  }
}
```

A verification email is sent automatically. The token works immediately but verified-only routes return `403` until verification is complete.

---

#### Login

```
POST /api/v1/login
```

**Request**
```json
{ "email": "john@example.com", "password": "Secret123!" }
```

**Response** `200`
```json
{
  "success": true,
  "message": "Login successful!",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "status": "active", "profile": { } },
    "token": "2|xyz789..."
  }
}
```

| Status | Reason |
|---|---|
| `401` | Invalid email or password |
| `403` | Account banned or suspended |

---

#### Logout — Current Device

```
POST /api/v1/logout
```

**Auth required.** Deletes the token used for this request.

**Response** `200` — `{ "success": true, "message": "Logged out successfully!" }`

---

#### Logout — All Devices

```
POST /api/v1/logout-all
```

**Auth required.** Deletes every token for this user.

**Response** `200` — `{ "success": true, "message": "Logged out from all devices successfully!" }`

---

#### Verify Email

```
GET /api/v1/verify-email/{id}/{hash}?expires=...&signature=...
```

**[signed]** URL is generated by the backend and embedded in a `FRONTEND_URL` link sent by email. The frontend extracts the four query parameters and calls this endpoint.

**Response** `200` — safe to call multiple times (returns success if already verified).

| Status | Reason |
|---|---|
| `403` | Invalid or expired signature |

---

#### Resend Verification Email

```
POST /api/v1/resend-verification
```

**Auth required.**

**Response** `200` — `{ "success": true, "message": "A verification link has been sent to your email." }`

---

#### Forgot Password

```
POST /api/v1/password/forgot
```

**Request** — `{ "email": "john@example.com" }`

Always returns `200` (including for unknown emails) to prevent enumeration. The reset link points to:
```
{FRONTEND_URL}/reset-password?token=...&email=...
```

---

#### Reset Password

```
POST /api/v1/password/reset
```

**Request**
```json
{
  "token": "...",
  "email": "john@example.com",
  "password": "NewSecret123!",
  "password_confirmation": "NewSecret123!"
}
```

All existing tokens are revoked on success.

| Status | Reason |
|---|---|
| `422` | Invalid or expired token |

---

### Core — Profile & Account

All routes require **auth + verified email**.

#### Get Profile

```
GET /api/v1/profile
```

**Response** `200`
```json
{
  "success": true,
  "message": "Profile retrieved successfully.",
  "data": {
    "id": 1, "name": "John Doe", "email": "john@example.com", "status": "active",
    "profile": {
      "profile_picture": null, "mobile": "+8801712345678", "address": "Dhaka, Bangladesh",
      "dob": "1995-06-15", "gender": "male", "bio": "Software developer."
    }
  }
}
```

---

#### Update Profile

```
PATCH /api/v1/profile
```

All fields optional.

| Field | Validation |
|---|---|
| `profile_picture` | nullable string |
| `mobile` | nullable, max 20 chars |
| `address` | nullable, max 500 chars |
| `dob` | nullable date, must be before today |
| `gender` | nullable, one of: `male` `female` `other` |
| `bio` | nullable, max 1000 chars |

---

#### Request Email Change

```
PATCH /api/v1/account/email
```

Does **not** update the email immediately. Sends a verification link to the new address. Existing email stays active until the new one is verified.

**Request** — `{ "email": "new@example.com", "password": "CurrentSecret123!" }`

| Status | Reason |
|---|---|
| `403` | Password incorrect |
| `422` | Email already taken or invalid |

---

#### Verify New Email

```
GET /api/v1/account/email/verify?id=...&email=...&expires=...&signature=...
```

**[signed]** Updates `email` and `email_verified_at` atomically.

---

#### Change Password

```
POST /api/v1/account/password
```

**Request**
```json
{
  "current_password": "OldSecret123!",
  "password": "NewSecret456!",
  "password_confirmation": "NewSecret456!"
}
```

| Status | Reason |
|---|---|
| `403` | Current password incorrect |

---

#### Update Account Status

```
PATCH /api/v1/account/status/{status}
```

Users may only set `active` or `inactive`. `suspended` and `banned` are admin-only.

| Status | Reason |
|---|---|
| `422` | Invalid value or admin-only status |

---

### Core — Activity Logs

#### List Logs

```
GET /api/v1/logs?page=1
```

**Auth + verified required.** Returns paginated user-visible logs. Only the authenticated user's own logs are returned.

**Response** `200`
```json
{
  "success": true,
  "message": "Activity logs retrieved successfully.",
  "data": {
    "current_page": 1,
    "data": [
      { "id": 12, "action": "LOGIN_OK", "data": { "ip_address": "102.89.1.1", "platform": "Windows", "device": "Desktop" }, "created_at": "2026-06-09T10:30:00.000000Z" }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

**User-visible actions:**

`REGISTER` · `LOGIN_OK` · `LOGOUT` · `LOGOUT_ALL` · `EMAIL_VERIFIED` · `PASSWORD_RESET_OK` · `PASSWORD_UPDATED` · `PROFILE_UPDATED` · `EMAIL_UPDATE_REQUESTED` · `EMAIL_UPDATED` · `STATUS_UPDATED` · `SOCIAL_LOGIN` · `SOCIAL_REGISTER` · `API_KEY_CREATED` · `API_KEY_REVOKED` · `MAGIC_LINK_LOGIN`

To make a new action user-visible, add it to `USER_VISIBLE_ACTIONS` in `app/Services/AppLogService.php`.

---

### Feature — Social Auth

> **Removable.** See [Removing a Feature](#removing-a-feature).

Supported providers: `google`, `facebook`

**Flow:**
1. Frontend calls `redirect` to get the OAuth URL.
2. Frontend sends the user to that URL.
3. After OAuth approval, the provider calls `callback` on this API.
4. API resolves the user and returns a Sanctum token.

#### Get OAuth Redirect URL

```
GET /api/v1/auth/{provider}/redirect
```

**Response** `200`
```json
{ "success": true, "message": "OAuth redirect URL generated.", "data": { "url": "https://accounts.google.com/..." } }
```

| Status | Reason |
|---|---|
| `422` | Unsupported provider |

---

#### OAuth Callback

```
GET /api/v1/auth/{provider}/callback?code=...&state=...
```

| Scenario | Result |
|---|---|
| Social account already linked | Log in |
| Email matches a **verified** account | Link social account and log in |
| Email matches an **unverified** account | `403` |
| No account with this email | Create account (pre-verified) and log in |

**Response** `200`
```json
{ "success": true, "message": "Login successful!", "data": { "user": { }, "token": "5|def456..." } }
```

| Status | Reason |
|---|---|
| `403` | Email matches unverified account, or account is banned/suspended |
| `422` | Unsupported provider or OAuth handshake failed |

---

### Feature — API Keys

> **Removable.** See [Removing a Feature](#removing-a-feature).

Long-lived keys stored as SHA-256 hashes. The plaintext is shown **once** at creation. Apply the `api.key.auth` middleware to any route that should accept key-based auth.

All management endpoints require **auth + verified email**.

#### List API Keys

```
GET /api/v1/api-keys
```

**Response** `200`
```json
{
  "data": [{ "id": 1, "name": "CI Pipeline", "prefix": "a1b2c3d4", "last_used_at": "...", "created_at": "..." }]
}
```

Only the prefix (first 8 chars) is shown — never the full key.

---

#### Create API Key

```
POST /api/v1/api-keys
```

**Request** — `{ "name": "CI Pipeline" }`

**Response** `201`
```json
{ "data": { "id": 1, "name": "CI Pipeline", "prefix": "a1b2c3d4", "key": "a1b2c3d4...", "created_at": "..." } }
```

Save the `key` immediately — it cannot be retrieved later.

---

#### Revoke API Key

```
DELETE /api/v1/api-keys/{id}
```

| Status | Reason |
|---|---|
| `404` | Key not found or belongs to a different user |

---

#### Using an API Key

```php
Route::middleware('api.key.auth')->get('/your-endpoint', YourController::class);
```

```
X-API-Key: a1b2c3d4XyZrandomstring...
```

---

### Feature — Multi-Device

> **Removable.** See [Removing a Feature](#removing-a-feature).

Tracks device info (IP, platform, device type) for every Sanctum token via global middleware — no controller changes needed.

All endpoints require **auth + verified email**.

#### List Sessions

```
GET /api/v1/devices
```

**Response** `200`
```json
{
  "data": [
    { "id": 7, "name": "auth", "ip_address": "102.89.1.1", "platform": "Windows", "device": "Desktop", "last_used_at": "...", "created_at": "...", "is_current": true },
    { "id": 5, "name": "auth", "ip_address": "197.210.5.3", "platform": "Android", "device": "Mobile", "last_used_at": "...", "created_at": "...", "is_current": false }
  ]
}
```

`is_current: true` marks the session for the token used in this request.

**Detected platforms:** `Windows` · `macOS` · `Android` · `iOS` · `Linux` · `Unknown`  
**Detected device types:** `Mobile` · `Tablet` · `Desktop`

---

#### Revoke a Session

```
DELETE /api/v1/devices/{tokenId}
```

| Status | Reason |
|---|---|
| `404` | Session not found or belongs to a different user |

---

#### Revoke All Other Sessions

```
DELETE /api/v1/devices/logout-others
```

Keeps the current session active, revokes all others.

---

### Feature — Magic Link

> **Removable.** See [Removing a Feature](#removing-a-feature).

Passwordless login via a single-use, 15-minute email link. The link points to `FRONTEND_URL/magic-login?token=...&email=...`. The frontend extracts the params and calls the verify endpoint.

#### Request Magic Link

```
POST /api/v1/magic-link/request
```

**Rate limit:** 3 / minute

**Request** — `{ "email": "john@example.com" }`

**Response** `200`

Always returns `200` (including for unknown emails) to prevent enumeration. No link is sent for banned or suspended accounts.

---

#### Verify Magic Link

```
POST /api/v1/magic-link/verify
```

**Rate limit:** 10 / minute

**Request**
```json
{ "token": "...", "email": "john@example.com" }
```

**Response** `200`
```json
{
  "success": true,
  "message": "Login successful!",
  "data": { "user": { }, "token": "3|abc..." }
}
```

| Status | Reason |
|---|---|
| `422` | Token invalid, expired, already used, or email mismatch |
| `403` | Account banned or suspended |

---

### Feature — IP Rules

> **Removable.** See [Removing a Feature](#removing-a-feature).

Global IP allowlist / blocklist applied to all API requests before any auth or route middleware.

**Modes:**
- **Blocklist** (default) — all IPs allowed except those with `type: block`
- **Allowlist** — if any `type: allow` rule exists, only listed IPs are allowed; all others are blocked

Both single IPs (`1.2.3.4`) and CIDR ranges (`10.0.0.0/24`) are supported.

Blocked requests receive `403` regardless of authentication status.

Management endpoints require **auth + verified email**.

#### List IP Rules

```
GET /api/v1/ip-rules
```

**Response** `200`
```json
{ "data": [{ "id": 1, "ip_address": "1.2.3.4", "type": "block", "label": "Spammer", "created_at": "..." }] }
```

---

#### Add IP Rule

```
POST /api/v1/ip-rules
```

**Request**
```json
{ "ip_address": "10.0.0.0/24", "type": "block", "label": "Internal network" }
```

| Field | Validation |
|---|---|
| `ip_address` | Required. Valid IPv4 address or CIDR range (e.g. `192.168.0.0/16`) |
| `type` | Required. `allow` or `block` |
| `label` | Optional string, max 100 chars |

**Response** `201`

---

#### Remove IP Rule

```
DELETE /api/v1/ip-rules/{id}
```

| Status | Reason |
|---|---|
| `404` | Rule not found |

---

## Removing a Feature

Each optional feature is fully self-contained. Two steps, no orphaned code.

### 1. Delete the feature folder

```bash
rm -rf app/Features/SocialAuth/
rm -rf app/Features/ApiKeys/
rm -rf app/Features/MultiDevice/
rm -rf app/Features/MagicLink/
rm -rf app/Features/IpRules/
```

### 2. Unregister the service provider

Open `bootstrap/providers.php` and remove the corresponding line:

```php
return [
    AppServiceProvider::class,
    SocialAuthServiceProvider::class,    // ← Social Auth
    ApiKeyServiceProvider::class,        // ← API Keys
    MultiDeviceServiceProvider::class,   // ← Multi-Device
    IpRulesServiceProvider::class,       // ← IP Rules
    MagicLinkServiceProvider::class,     // ← Magic Link
];
```

### 3. Roll back the feature's migrations (optional)

```bash
php artisan migrate:rollback --path=app/Features/SocialAuth/migrations
php artisan migrate:rollback --path=app/Features/ApiKeys/migrations
php artisan migrate:rollback --path=app/Features/MultiDevice/migrations
php artisan migrate:rollback --path=app/Features/MagicLink/migrations
php artisan migrate:rollback --path=app/Features/IpRules/migrations
```

---

## Adding a New Language

**Step 1 — Copy the English file**

```bash
cp lang/en/app.php lang/fr/app.php
```

**Step 2 — Translate the values** (keys must not change)

```php
return [
    'ACCOUNT_CREATED' => 'Compte créé avec succès!',
    // ...
];
```

**Step 3 — Register the locale** in `app/Http/Middleware/SetLocale.php`:

```php
private const SUPPORTED = ['en', 'bn', 'fr'];
```

**Step 4 — Use it**

```
Accept-Language: fr
```

Falls back to `APP_LOCALE` if the locale is not in the supported list.

---

## Customization Notes

### Password Rules

Add to `AppServiceProvider::boot()`:

```php
use Illuminate\Validation\Rules\Password;

Password::defaults(fn () =>
    Password::min(8)->mixedCase()->numbers()->symbols()
);
```

### Token Expiry

Control with `SANCTUM_TOKEN_EXPIRY` (minutes). To prune expired tokens, schedule in `routes/console.php`:

```php
Schedule::command('sanctum:prune-expired --hours=24')->daily();
```

### Admin-Controlled User Status

```php
$user->update(['status' => \App\Enums\UserStatus::Banned]);
$user->update(['status' => \App\Enums\UserStatus::Suspended]);
```

Banned and suspended users receive `403` on every authenticated request.

### FRONTEND_URL Pattern

The backend generates a signed URL, extracts the parameters, and embeds them into a `FRONTEND_URL` link. The frontend passes those parameters as-is to the corresponding API endpoint.

| Email type | Frontend URL format |
|---|---|
| Email verification | `{FRONTEND_URL}/verify-email?id=&hash=&expires=&signature=` |
| Password reset | `{FRONTEND_URL}/reset-password?token=&email=` |
| New email verification | `{FRONTEND_URL}/verify-new-email?id=&email=&expires=&signature=` |
| Magic link login | `{FRONTEND_URL}/magic-login?token=&email=` |

### Adding a New Optional Feature

```
app/Features/YourFeature/
├── YourFeatureServiceProvider.php
├── migrations/
├── Models/
├── Controllers/
└── routes.php
```

```php
class YourFeatureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api/v1')->group(__DIR__ . '/routes.php');
    }
}
```

Register in `bootstrap/providers.php`. To remove: delete the folder, remove the line.

### Running Tests

Tests use SQLite in-memory — no extra database setup needed.

```bash
php artisan test                        # full suite
php artisan test --filter=MagicLink    # specific feature
php artisan test --coverage            # with coverage (requires Xdebug or PCOV)
```
