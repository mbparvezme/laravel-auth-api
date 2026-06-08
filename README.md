# Laravel Auth API

A modular Laravel 13 authentication API starter kit. Drop it into any project and remove the parts you don't need.

## Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
  - [Option A — Local (PHP installed)](#option-a--local-php-installed)
  - [Option B — Docker for development](#option-b--docker-for-development)
  - [Option C — Pull from Docker Hub](#option-c--pull-from-docker-hub)
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
| Email Verification | FRONTEND_URL pattern — backend signs, frontend redirects |
| Password Reset | Same FRONTEND_URL pattern |
| Profile Management | View and update profile fields |
| Email Change | Verify new address before updating (prevents lockout) |
| Password Change | Requires current password confirmation |
| Account Status | Users can set themselves active / inactive |
| Activity Logs | Paginated, user-visible log of account events |
| Multi-language | English and Bengali out of the box |
| User Status Guard | active, inactive, suspended, banned — enforced on every request |

### Optional (each independently removable)

| Feature | Folder | What it adds |
|---|---|---|
| Social Auth | `app/Features/SocialAuth/` | Google + Facebook OAuth |
| API Keys | `app/Features/ApiKeys/` | Create / list / revoke long-lived API keys with `X-API-Key` middleware |
| Multi-Device | `app/Features/MultiDevice/` | Track sessions per device, revoke individual sessions |

---

## Tech Stack

- **PHP** 8.2+ · **Laravel** 13 · **Laravel Sanctum**
- **Laravel Socialite** (Social Auth feature)
- **MySQL** 8.0 (PostgreSQL also works — change `DB_CONNECTION`)
- **Redis** (optional — for cache/queue)
- **Pest** for testing

---

## Getting Started

Choose the option that matches your situation:

| | Option A | Option B | Option C |
|---|---|---|---|
| **PHP installed locally** | Required | Not needed | Not needed |
| **MySQL installed locally** | Required | Not needed | Not needed |
| **Source code needed** | Yes | Yes | No |
| **Best for** | Local dev | Local dev (no PHP setup) | Deploying a published image |

> **About the `vendor` folder and Docker**
> The `vendor` folder is never included in the Docker image. The `Dockerfile` runs `composer install` during `docker build`, which creates `vendor` inside the image layer. Including `vendor` in the image would bloat it by 100–200 MB and break Docker's layer cache.

---

### Option A — Local (PHP installed)

**Requirements:** PHP 8.2+, Composer, MySQL

```bash
git clone https://github.com/your-username/laravel-auth-api.git
cd laravel-auth-api

composer install

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials, then:

```bash
php artisan migrate
php artisan serve
```

The API is now running at `http://localhost:8000`.

---

### Option B — Docker for development

**Requirements:** Docker Desktop (or Docker Engine + Compose plugin)

Docker replaces the need to install PHP and MySQL on your machine. You still clone the repository because you are **customizing the starter kit** — the source code is bind-mounted into the container so edits are reflected immediately without rebuilding.

```bash
git clone https://github.com/your-username/laravel-auth-api.git
cd laravel-auth-api

cp .env.example .env
```

Open `.env` and set at minimum:

```env
APP_KEY=          # generate with: php artisan key:generate --show
DB_DATABASE=laravel_auth
DB_USERNAME=laravel
DB_PASSWORD=secret
```

Start the full stack:

```bash
docker compose up --build
```

The entrypoint waits for MySQL to become healthy, runs all migrations, then starts PHP-FPM. The API is available at `http://localhost:8080`.

**Useful commands:**

```bash
# Run artisan commands inside the container
docker compose exec app php artisan tinker

# View application logs
docker compose logs -f app

# Stop everything
docker compose down

# Stop and delete the database volume
docker compose down -v
```

**Build and publish your image:**

Once you have customized the starter kit, build and push your own image:

```bash
docker build -t your-org/laravel-auth-api:latest .
docker push your-org/laravel-auth-api:latest
```

Set `APP_ENV=production` at runtime — the entrypoint automatically caches config, routes, views, and events on startup.

---

### Option C — Pull from Docker Hub

**Requirements:** Docker Desktop (or Docker Engine + Compose plugin)

Use this when you want to **run a published image** without cloning the repository — for example on a production server or in a CI/CD pipeline. You only need two files from the repository: the Hub compose file and the Nginx config.

**Step 1 — Download the two required files**

```bash
# Compose file (references the image, not the source)
curl -O https://raw.githubusercontent.com/your-username/laravel-auth-api/main/docker-compose.hub.yml

# Nginx config (referenced by the compose file)
mkdir -p docker/nginx
curl -o docker/nginx/default.conf \
  https://raw.githubusercontent.com/your-username/laravel-auth-api/main/docker/nginx/default.conf
```

**Step 2 — Create your `.env` file**

```bash
curl -O https://raw.githubusercontent.com/your-username/laravel-auth-api/main/.env.example
mv .env.example .env
```

Edit `.env` and set at minimum:

```env
APP_KEY=          # no artisan available — generate online or on another machine
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=laravel_auth
DB_USERNAME=laravel
DB_PASSWORD=strongpassword
DB_ROOT_PASSWORD=strongrootpassword
FRONTEND_URL=https://your-frontend.com
```

**Step 3 — Start the stack**

```bash
docker compose -f docker-compose.hub.yml up -d
```

The entrypoint pulls the image, waits for MySQL, runs migrations, caches config/routes (because `APP_ENV=production`), and starts serving.

**Update to a newer image version:**

```bash
docker compose -f docker-compose.hub.yml pull
docker compose -f docker-compose.hub.yml up -d
```

---

## Environment Variables

### Application

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | `Laravel` | Application name used in emails |
| `APP_ENV` | `local` | `local`, `staging`, or `production` |
| `APP_KEY` | *(empty)* | Encryption key — **required**. Generate with `php artisan key:generate` |
| `APP_DEBUG` | `true` | Set to `false` in production to hide stack traces from responses |
| `APP_URL` | `http://localhost` | Full URL of this API server |
| `APP_LOCALE` | `en` | Default response language (`en` or `bn`) |

### Frontend

| Variable | Default | Description |
|---|---|---|
| `FRONTEND_URL` | `http://localhost:3000` | Base URL of your SPA. Email verification and password reset links are built using this URL — the backend signs the URL, extracts the parameters, and embeds them into a frontend link so the user lands on your UI, not the raw API. |

### Database

| Variable | Default | Description |
|---|---|---|
| `DB_CONNECTION` | `mysql` | Driver: `mysql`, `pgsql`, or `sqlite` |
| `DB_HOST` | `127.0.0.1` | Database host. When using Docker this is overridden to `mysql` automatically |
| `DB_PORT` | `3306` | Database port |
| `DB_DATABASE` | `laravel_auth` | Database name |
| `DB_USERNAME` | `laravel` | Database user |
| `DB_PASSWORD` | *(empty)* | Database password |

### Mail

| Variable | Description |
|---|---|
| `MAIL_MAILER` | Driver: `smtp`, `ses`, `mailgun`, `resend`, `log`. Use `log` in development — emails are written to `storage/logs/laravel.log` |
| `MAIL_HOST` | SMTP host |
| `MAIL_PORT` | SMTP port |
| `MAIL_USERNAME` | SMTP username |
| `MAIL_PASSWORD` | SMTP password |
| `MAIL_FROM_ADDRESS` | Sender address on all outgoing emails |
| `MAIL_FROM_NAME` | Sender display name (defaults to `APP_NAME`) |

### Auth

| Variable | Default | Description |
|---|---|---|
| `SANCTUM_TOKEN_EXPIRY` | `43200` | Sanctum token lifetime in **minutes**. `43200` = 30 days. Set to `0` for no expiry. |

### Social Auth *(Feature: SocialAuth)*

| Variable | Description |
|---|---|
| `GOOGLE_CLIENT_ID` | Google OAuth client ID from Google Cloud Console |
| `GOOGLE_CLIENT_SECRET` | Google OAuth client secret |
| `GOOGLE_REDIRECT_URI` | Callback URL registered in Google Cloud Console. Example: `https://api.example.com/api/v1/auth/google/callback` |
| `FACEBOOK_APP_ID` | Facebook App ID from Facebook Developers |
| `FACEBOOK_APP_SECRET` | Facebook App secret |
| `FACEBOOK_REDIRECT_URI` | Callback URL registered in Facebook Developers |

### Docker

Only relevant when running via `docker-compose.yml`.

| Variable | Default | Description |
|---|---|---|
| `NGINX_PORT` | `8080` | Host port that Nginx listens on |
| `DB_FORWARD_PORT` | `3306` | Host port forwarded to the MySQL container |
| `DB_ROOT_PASSWORD` | `rootsecret` | MySQL root password (used by the healthcheck) |
| `REDIS_FORWARD_PORT` | `6379` | Host port forwarded to the Redis container |

---

## API Reference

### Response Format

All responses use the same envelope structure.

**Success**
```json
{
  "success": true,
  "message": "Human-readable message.",
  "data": { }
}
```

**Error**
```json
{
  "success": false,
  "message": "Human-readable message."
}
```

**Validation error** `422`
```json
{
  "success": false,
  "message": "Human-readable message.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

`data` is omitted on errors. `errors` is only present on validation failures.

---

### Authentication

Protected routes require a Bearer token in the `Authorization` header:

```
Authorization: Bearer 1|abc123...
```

Routes that also require **verified email** return `403` if the email has not been verified yet.

Routes marked **[signed]** are called via a signed URL sent by email — they are not called manually.

**Rate limits** apply to public endpoints: `5 requests/minute` on auth routes, `60 requests/minute` on authenticated routes.

---

### Core — Auth

#### Register

```
POST /api/v1/register
```

**Rate limit:** 5 / minute

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
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "status": "active",
      "profile": {
        "profile_picture": null,
        "mobile": null,
        "address": null,
        "dob": null,
        "gender": null,
        "bio": null
      }
    },
    "token": "1|abc123..."
  }
}
```

A verification email is sent automatically. The token is immediately usable but routes that require a verified email return `403` until verification is complete.

---

#### Login

```
POST /api/v1/login
```

**Rate limit:** 5 / minute

**Request**
```json
{
  "email": "john@example.com",
  "password": "Secret123!"
}
```

**Response** `200`
```json
{
  "success": true,
  "message": "Login successful!",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "status": "active",
      "profile": { }
    },
    "token": "2|xyz789..."
  }
}
```

| Status | Reason |
|---|---|
| `401` | Invalid email or password |
| `403` | Account is banned or suspended |

---

#### Logout — Current Device

```
POST /api/v1/logout
```

**Auth required.** Deletes the token used for this request.

**Response** `200`
```json
{ "success": true, "message": "Logged out successfully!" }
```

---

#### Logout — All Devices

```
POST /api/v1/logout-all
```

**Auth required.** Deletes every token belonging to this user.

**Response** `200`
```json
{ "success": true, "message": "Logged out from all devices successfully!" }
```

---

#### Verify Email

```
GET /api/v1/verify-email/{id}/{hash}?expires=...&signature=...
```

**[signed]** This URL is generated by the backend and embedded in a `FRONTEND_URL`-based link sent by email. The frontend extracts the four query parameters and calls this endpoint.

**Response** `200`
```json
{ "success": true, "message": "Your email has been verified successfully!" }
```

Returns `200` even if the email is already verified — safe to call multiple times.

| Status | Reason |
|---|---|
| `403` | Invalid or expired signature |

---

#### Resend Verification Email

```
POST /api/v1/resend-verification
```

**Auth required.**

**Response** `200`
```json
{ "success": true, "message": "A verification link has been sent to your email." }
```

---

#### Forgot Password

```
POST /api/v1/password/forgot
```

**Rate limit:** 5 / minute

**Request**
```json
{ "email": "john@example.com" }
```

**Response** `200`

Always returns `200` — including for unknown emails — to prevent email enumeration.

```json
{ "success": true, "message": "A password reset link has been sent to your email." }
```

The reset link is built using `FRONTEND_URL`. The user lands on your frontend at:
```
{FRONTEND_URL}/reset-password?token=...&email=...
```

---

#### Reset Password

```
POST /api/v1/password/reset
```

**Rate limit:** 5 / minute

**Request**
```json
{
  "token": "...",
  "email": "john@example.com",
  "password": "NewSecret123!",
  "password_confirmation": "NewSecret123!"
}
```

**Response** `200`
```json
{ "success": true, "message": "Password reset successfully! You can now log in with your new password." }
```

All existing tokens are revoked on success.

| Status | Reason |
|---|---|
| `422` | Invalid or expired reset token |

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
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active",
    "profile": {
      "profile_picture": null,
      "mobile": "+8801712345678",
      "address": "Dhaka, Bangladesh",
      "dob": "1995-06-15",
      "gender": "male",
      "bio": "Software developer."
    }
  }
}
```

---

#### Update Profile

```
PATCH /api/v1/profile
```

All fields are optional.

**Request**
```json
{
  "profile_picture": "https://cdn.example.com/avatar.jpg",
  "mobile": "+8801712345678",
  "address": "Dhaka, Bangladesh",
  "dob": "1995-06-15",
  "gender": "male",
  "bio": "Software developer."
}
```

| Field | Validation |
|---|---|
| `profile_picture` | nullable string |
| `mobile` | nullable, max 20 chars |
| `address` | nullable, max 500 chars |
| `dob` | nullable date, must be before today |
| `gender` | nullable, one of: `male` `female` `other` |
| `bio` | nullable, max 1000 chars |

**Response** `200`
```json
{ "success": true, "message": "Profile updated successfully.", "data": { } }
```

---

#### Request Email Change

```
PATCH /api/v1/account/email
```

Does **not** update the email immediately. Sends a verification link to the **new** address. The existing email remains active until the new one is verified.

**Request**
```json
{
  "email": "newemail@example.com",
  "password": "CurrentSecret123!"
}
```

**Response** `200`
```json
{ "success": true, "message": "Verification link sent to your new email address." }
```

| Status | Reason |
|---|---|
| `403` | Password is incorrect |
| `422` | Email already taken or invalid format |

---

#### Verify New Email

```
GET /api/v1/account/email/verify?id=...&email=...&expires=...&signature=...
```

**[signed]** Link is sent to the **new** email address. Clicking it updates `email` and `email_verified_at` atomically.

**Response** `200`
```json
{ "success": true, "message": "Email address updated successfully." }
```

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

**Response** `200`
```json
{ "success": true, "message": "Password updated successfully." }
```

| Status | Reason |
|---|---|
| `403` | Current password is incorrect |

---

#### Update Account Status

```
PATCH /api/v1/account/status/{status}
```

Users may only set `active` or `inactive` on themselves. `suspended` and `banned` are admin-only states.

| Value | Effect |
|---|---|
| `active` | Normal account access |
| `inactive` | Account deactivated by user (can still log in and reactivate) |

**Response** `200`
```json
{ "success": true, "message": "Account status updated successfully." }
```

| Status | Reason |
|---|---|
| `422` | Invalid value or attempting to set an admin-only status |

---

### Core — Activity Logs

#### List Logs

```
GET /api/v1/logs?page=1
```

**Auth + verified required.** Returns paginated logs marked as `user_visible` by the system. Only the authenticated user's own logs are returned.

**Response** `200`
```json
{
  "success": true,
  "message": "Activity logs retrieved successfully.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 12,
        "action": "LOGIN_OK",
        "data": {
          "ip_address": "102.89.1.1",
          "platform": "Windows",
          "device": "Desktop"
        },
        "created_at": "2026-06-09T10:30:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

**User-visible actions:**

`REGISTER` · `LOGIN_OK` · `LOGOUT` · `LOGOUT_ALL` · `EMAIL_VERIFIED` · `PASSWORD_RESET_OK` · `PASSWORD_UPDATED` · `PROFILE_UPDATED` · `EMAIL_UPDATE_REQUESTED` · `EMAIL_UPDATED` · `STATUS_UPDATED` · `SOCIAL_LOGIN` · `SOCIAL_REGISTER` · `API_KEY_CREATED` · `API_KEY_REVOKED`

All other internal actions are logged but hidden from users. To make a new action user-visible, add it to `USER_VISIBLE_ACTIONS` in `app/Services/AppLogService.php`.

---

### Feature — Social Auth

> **Removable.** See [Removing a Feature](#removing-a-feature).

Supported providers: `google`, `facebook`

**Full flow:**
1. Frontend calls the `redirect` endpoint to get the OAuth URL.
2. Frontend redirects the user to that URL.
3. After OAuth approval, the provider redirects back to the `callback` URL on this API.
4. The API resolves the user, returns a Sanctum token.
5. Frontend stores the token and uses it for all subsequent requests.

#### Get OAuth Redirect URL

```
GET /api/v1/auth/{provider}/redirect
```

**Rate limit:** 5 / minute

**Response** `200`
```json
{
  "success": true,
  "message": "OAuth redirect URL generated.",
  "data": {
    "url": "https://accounts.google.com/o/oauth2/auth?client_id=...&redirect_uri=...&scope=..."
  }
}
```

| Status | Reason |
|---|---|
| `422` | Unsupported provider |

---

#### OAuth Callback

```
GET /api/v1/auth/{provider}/callback?code=...&state=...
```

**Rate limit:** 5 / minute. Called by the OAuth provider after user approval.

**Resolution logic:**

| Scenario | Result |
|---|---|
| Social account already linked | Log in |
| Email matches a **verified** account | Link social account and log in |
| Email matches an **unverified** account | `403` — ask user to verify email or log in with password |
| No account with this email | Create new account (email pre-verified), log in |

**Response** `200`
```json
{
  "success": true,
  "message": "Login successful!",
  "data": {
    "user": {
      "id": 3,
      "name": "Jane Smith",
      "email": "jane@gmail.com",
      "status": "active",
      "profile": { }
    },
    "token": "5|def456..."
  }
}
```

| Status | Reason |
|---|---|
| `403` | Email matches an unverified local account |
| `403` | Account is banned or suspended |
| `422` | Unsupported provider |
| `422` | OAuth handshake failed (bad code, expired state) |

---

### Feature — API Keys

> **Removable.** See [Removing a Feature](#removing-a-feature).

Long-lived keys stored as SHA-256 hashes. The plaintext key is shown **once** at creation and cannot be retrieved again. Use the `api.key.auth` middleware to accept API key authentication on any route.

All management endpoints require **auth + verified email**.

#### List API Keys

```
GET /api/v1/api-keys
```

**Response** `200`
```json
{
  "success": true,
  "message": "API keys retrieved successfully.",
  "data": [
    {
      "id": 1,
      "name": "CI Pipeline",
      "prefix": "a1b2c3d4",
      "last_used_at": "2026-06-09T08:00:00.000000Z",
      "created_at": "2026-06-01T00:00:00.000000Z"
    }
  ]
}
```

Only the prefix (first 8 characters) is shown — never the full key.

---

#### Create API Key

```
POST /api/v1/api-keys
```

**Request**
```json
{ "name": "CI Pipeline" }
```

**Response** `201`
```json
{
  "success": true,
  "message": "API key created. Store it securely — it will not be shown again.",
  "data": {
    "id": 1,
    "name": "CI Pipeline",
    "prefix": "a1b2c3d4",
    "key": "a1b2c3d4XyZrandomstring...",
    "created_at": "2026-06-09T10:00:00.000000Z"
  }
}
```

Save the `key` value immediately — it is not stored and cannot be retrieved later.

---

#### Revoke API Key

```
DELETE /api/v1/api-keys/{id}
```

**Response** `200`
```json
{ "success": true, "message": "API key revoked successfully." }
```

| Status | Reason |
|---|---|
| `404` | Key not found or belongs to a different user |

---

#### Using an API Key on a Route

Apply the `api.key.auth` middleware to any route that should accept key-based authentication:

```php
Route::middleware('api.key.auth')->get('/your-endpoint', YourController::class);
```

The client sends the key in the `X-API-Key` header:

```
X-API-Key: a1b2c3d4XyZrandomstring...
```

---

### Feature — Multi-Device

> **Removable.** See [Removing a Feature](#removing-a-feature).

Automatically tracks device information (IP address, platform, device type) for every Sanctum token by intercepting authenticated requests in a global middleware — no changes to your controllers are needed.

All endpoints require **auth + verified email**.

#### List Sessions

```
GET /api/v1/devices
```

**Response** `200`
```json
{
  "success": true,
  "message": "Active sessions retrieved successfully.",
  "data": [
    {
      "id": 7,
      "name": "auth",
      "ip_address": "102.89.1.1",
      "platform": "Windows",
      "device": "Desktop",
      "last_used_at": "2026-06-09T11:00:00.000000Z",
      "created_at": "2026-06-09T09:00:00.000000Z",
      "is_current": true
    },
    {
      "id": 5,
      "name": "auth",
      "ip_address": "197.210.5.3",
      "platform": "Android",
      "device": "Mobile",
      "last_used_at": "2026-06-08T20:00:00.000000Z",
      "created_at": "2026-06-08T18:00:00.000000Z",
      "is_current": false
    }
  ]
}
```

`is_current: true` marks the session belonging to the token used for this request.

**Detected platforms:** `Windows`, `macOS`, `Android`, `iOS`, `Linux`, `Unknown`  
**Detected device types:** `Mobile`, `Tablet`, `Desktop`

---

#### Revoke a Session

```
DELETE /api/v1/devices/{tokenId}
```

**Response** `200`
```json
{ "success": true, "message": "Session revoked successfully." }
```

| Status | Reason |
|---|---|
| `404` | Session not found or belongs to a different user |

---

#### Revoke All Other Sessions

```
DELETE /api/v1/devices/logout-others
```

Keeps the session making this request active and revokes all others.

**Response** `200`
```json
{ "success": true, "message": "All other sessions have been revoked." }
```

---

## Removing a Feature

Each optional feature is fully self-contained. Removing one takes two steps and leaves no orphaned code.

### 1. Delete the feature folder

```bash
# Remove Social Auth
rm -rf app/Features/SocialAuth/

# Remove API Keys
rm -rf app/Features/ApiKeys/

# Remove Multi-Device
rm -rf app/Features/MultiDevice/
```

### 2. Unregister the service provider

Open `bootstrap/providers.php` and remove the corresponding line:

```php
return [
    AppServiceProvider::class,
    SocialAuthServiceProvider::class,   // ← delete this line for Social Auth
    ApiKeyServiceProvider::class,        // ← delete this line for API Keys
    MultiDeviceServiceProvider::class,   // ← delete this line for Multi-Device
];
```

### 3. Roll back the feature's migrations (optional)

If you want to drop the feature's tables from the database:

```bash
php artisan migrate:rollback --path=app/Features/SocialAuth/migrations
php artisan migrate:rollback --path=app/Features/ApiKeys/migrations
php artisan migrate:rollback --path=app/Features/MultiDevice/migrations
```

No other files in the codebase reference any optional feature.

---

## Adding a New Language

Language strings live in `lang/{locale}/app.php`.

**Step 1 — Create the file**

```bash
cp lang/en/app.php lang/fr/app.php
```

**Step 2 — Translate the values**

Open `lang/fr/app.php` and translate each string. The **keys must not change** — only the values.

```php
return [
    'ACCOUNT_CREATED' => 'Compte créé avec succès! Veuillez vérifier votre email.',
    'USER_LOGIN'      => 'Connexion réussie!',
    // ...
];
```

**Step 3 — Register the locale**

Open `app/Http/Middleware/SetLocale.php` and add the new locale to the supported list:

```php
private const SUPPORTED = ['en', 'bn', 'fr'];
```

**Step 4 — Use it**

Clients request a locale via the `Accept-Language` header:

```
Accept-Language: fr
```

If the locale is not in the supported list the middleware falls back to `APP_LOCALE` (default: `en`).

---

## Customization Notes

### Password Rules

The default Laravel password rules are applied (`Password::defaults()`). To customize them globally, add this to `AppServiceProvider::boot()`:

```php
use Illuminate\Validation\Rules\Password;

Password::defaults(fn () =>
    Password::min(8)->mixedCase()->numbers()->symbols()
);
```

### Token Expiry

Control token lifetime with `SANCTUM_TOKEN_EXPIRY` (minutes). To automatically clean up expired tokens, schedule the prune command in `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('sanctum:prune-expired --hours=24')->daily();
```

### Admin-Controlled User Status

The `status` column uses the `UserStatus` enum: `active`, `inactive`, `suspended`, `banned`. Users can only set `active` or `inactive` themselves via the API. To set `suspended` or `banned` from admin code:

```php
$user->update(['status' => \App\Enums\UserStatus::Banned]);
// or
$user->update(['status' => \App\Enums\UserStatus::Suspended]);
```

Banned and suspended users receive a `403` on every authenticated request.

### FRONTEND_URL Pattern

This project uses a decoupled pattern for all email-based flows. The backend generates a signed URL, extracts its parameters, and embeds them into a link that points at your frontend. This lets users land on your UI instead of hitting the raw API from their email client.

| Email type | Frontend URL format |
|---|---|
| Email verification | `{FRONTEND_URL}/verify-email?id=&hash=&expires=&signature=` |
| Password reset | `{FRONTEND_URL}/reset-password?token=&email=` |
| New email verification | `{FRONTEND_URL}/verify-new-email?id=&email=&expires=&signature=` |

The frontend reads these query parameters and passes them as-is to the corresponding API endpoint. Signatures remain valid because the backend receives the exact parameters it originally signed.

### Adding a New Optional Feature

Follow the same structure as the existing features:

```
app/Features/YourFeature/
├── YourFeatureServiceProvider.php
├── migrations/
├── Models/
├── Controllers/
└── routes.php
```

Minimal `ServiceProvider`:

```php
namespace App\Features\YourFeature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class YourFeatureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->group(__DIR__ . '/routes.php');
    }
}
```

Register in `bootstrap/providers.php`. To remove later: delete the folder, remove the line.

### Running Tests

Tests use SQLite in-memory — no extra database configuration needed.

```bash
# Full suite
php artisan test

# Specific feature
php artisan test --filter=Registration

# With coverage (requires Xdebug or PCOV)
php artisan test --coverage
```
