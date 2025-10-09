<h1 align="center">Laravel API Endpoints</h1>
<h3 align="center">Ready APIs for Auth, verification, password reset, API key management and more!</h1>

<br>

## About This Package

**Laravel API Endpoints** is a powerful starter kit built on [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum), providing ready-to-use authentication, account management, and API key features for any Laravel-based application.
It’s designed to integrate seamlessly with the [sveltekit-dashboard-starter](https://github.com/theui-dev/sveltekit-dashboard-starter), offering a smooth full-stack experience.


<br>

## Features
This package encompasses a range of features, including but not limited to:

- User registration via email and password.
- Email verification (including re-verification for new email updates).
- Secure login.
- Password reset using email-based token.
- Multi-device authentication support.
- Generate, regenerate, and revoke API keys.
- Profile management (update email, change password, manage account status).
- Active device tracking and logout from all devices.
- RESTful responses with localization support.
- Throttle protection and API key middleware for security.

<br>

## API Endpoints
It includes the following endpoints:

| Details                         | Method | API End Points                       |
| ------------------------------- | ------ | ------------------------------------ |
| Registration (public)           | POST   | [/api/register](#)                   |
| Login (public)                  | POST   | [/api/login](#)                      |
| Email verification  (public)    | GET    | [/api/verify-email/{id}/{hash}](#)   |
| Verify new email  (public)      | GET    | [/api/verify-new-email](#)           |
| Request password reset (public) | POST   | [/api/password/forgot](#)            |
| Reset password (public)         | POST   | [/api/password/reset/{token}](#)     |
| Resend email verification link  | POST   | [/api/resend-verification-email](#)  |
| Logout                          | POST   | [/api/logout](#)                     |
| Logout from all device          | POST   | [/api/logout-all](#)                 |
| Dashboard                       | GET    | [/api/dashboard](#)                  |
| Active devices                  | GET    | [/api/active-device](#)              |
| all API keys                    | GET    | [/api/keys](#)                       |
| Generate API key                | POST   | [/api/keys](#)                       |
| Regenerate API key              | PATCH  | [/api/keys/{id}](#)                  |
| Delete API key                  | DELETE | [/api/keys/{id}](#)                  |
| Get profile                     | GET    | [/api/account](#)                    |
| Update email                    | PATCH  | [/api/account/email](#)              |
| Update password                 | POST   | [/api/account/password](#)           |
| Update account status           | PATCH  | [/api/account/{status}](#)           |

<br>

## API Documentation

- All endpoints are prefixed with `/api`.
- All endpoints requires the following request headers:
    ```json
    {
      "Content-Type": "application/json",
      "Accept": "application/json"
    }
    ```
- All authenticated endpoints require a valid Sanctum API token in the `Authorization` header.

    ```sh
    Authorization: Bearer <SANCTUM_TOKEN>
    ```
<br>

### Public Routes
These endpoints are accessible without authentication and are subject to a strict rate limit.


### 1. User Registration
Creates a new user account and sends an email verification link.

**Method**: `POST`

**Endpoint**: `/api/register`

**Request Body**
```json
{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response**
```js
{
  "success": true,
  "message": "Account created successfully! Please check your email to verify your account.",
	"data": {
		"user": {
			"id": 2,
			"name": "John Doe",
			"email": "email3@domain.com",
			"profile": null
		},
		"token": "1|lAdHJXEP5iwfh0v29bnEtVwbWzfolFGdU6dnP3rB52fe74a1"
	},
  "errors": []
}
```

<br>

## Copyright and license

Code and documentation copyright 2022 the [M B Parvez](https://www.mbparvez.me) and [Gosoft](https://www.gosoft.io). Code released under the MIT License.
