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

<h3 style="font-weight:bold;color:#2299ee" colspan=3>↪ Public routes</h3>

<table>
  <tr>
    <th>Details</th>
    <th>Method</th>
    <th>API End Points</th>
  </tr>
  <tr>
    <td>Registration</td>
    <td>POST</td>
    <td>/api/register</td>
  </tr>
  <tr>
    <td>Login</td>
    <td>POST</td>
    <td>/api/login</td>
  </tr>
  <tr>
    <td>Email verification </td>
    <td>GET</td>
    <td>/api/verify-email/{id}/{hash}</td>
  </tr>
  <tr>
    <td>Verify new email </td>
    <td>GET</td>
    <td>/api/verify-new-email</td>
  </tr>
  <tr>
    <td>Request password reset</td>
    <td>POST</td>
    <td>/api/password/forgot</td>
  </tr>
  <tr>
    <td>Reset password</td>
    <td>POST</td>
    <td>/api/password/reset/{token}</td>
  </tr>
</table>

<br>

<h3 style="font-weight:bold;color:#2299ee" colspan=3>↪ Authenticated routes</h3>

<table>
  <tr>
    <th>Details</th>
    <th>Method</th>
    <th>API End Points</th>
  </tr>
  <tr>
    <td>Resend email verification link</td>
    <td>POST</td>
    <td>/api/resend-verification-email</td>
  </tr>
  <tr>
    <td>Logout</td>
    <td>POST</td>
    <td>/api/logout</td>
  </tr>
  <tr>
    <td>Logout from all device</td>
    <td>POST</td>
    <td>/api/logout-all</td>
  </tr>
  <tr>
    <td>Dashboard</td>
    <td>GET</td>
    <td>/api/dashboard</td>
  </tr>
  <tr>
    <td>Active devices</td>
    <td>GET</td>
    <td>/api/active-device</td>
  </tr>
  <tr>
    <td style="font-weight:bold;color:#22bb99" colspan=3>↪ API Key Management</td>
  </tr>
  <tr>
    <td>List API keys</td>
    <td>GET</td>
    <td>/api/keys</td>
  </tr>
  <tr>
    <td>Create API key</td>
    <td>POST</td>
    <td>/api/keys</td>
  </tr>
  <tr>
    <td>Regenerate API key</td>
    <td>PATCH</td>
    <td>/api/keys/{id}</td>
  </tr>
  <tr>
    <td>Delete API key</td>
    <td>DELETE</td>
    <td>/api/keys/{id}</td>
  </tr>
  <tr>
    <td style="font-weight:bold;color:#22bb99" colspan=3>↪ Account Management</td>
  </tr>
  <tr>
    <td>Get profile</td>
    <td>GET</td>
    <td>/api/account</td>
  </tr>
  <tr>
    <td>Update email</td>
    <td>PATCH</td>
    <td>/api/account/email</td>
  </tr>
  <tr>
    <td>Update password</td>
    <td>POST</td>
    <td>/api/account/password</td>
  </tr>
  <tr>
    <td>Update account status</td>
    <td>PATCH</td>
    <td>/api/account/{status}</td>
  </tr>
</table>


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

### ↪ Public Routes
These endpoints are accessible without authentication and are subject to a strict rate limit.

#### 1. User Registration
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

**Response (201 Created)**
```js
{
  "success": true,
  "message": "Account created successfully! Please check your email to verify your account.",
	"data": {
		"user": {
			"id": 2,
			"name": "John Doe",
			"email": "user@example.com",
			"profile": null
		},
		"token": "1|lAdHhXEP5iQfh0v29DnEqVwbWzfolFGdU6dnP3rB52fe74a7"
	},
  "errors": []
}
```

<br>

#### 2. User Login
Authenticates a user and returns a Sanctum API token.

**Method**: `POST`

**Endpoint**: `/api/login`

**Request Body**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200 OK)**
```js
{
	"success": true,
	"message": "Login successful!",
	"data": {
		"user": {
			"id": 2,
			"name": "John Doe",
			"email": "user@example.com",
			"profile": {
				"profile_picture": "profiles/default.png",
				"mobile": "+8801712345678",
				"address": "House 123, Road 4, Dhaka, Bangladesh",
				"dob": "1990-01-01",
				"gender": "male",
				"bio": "This is a sample bio for user 1."
			}
		},
		"token": "1|lAdHhXEP5iQfh0v29DnEqVwbWzfolFGdU6dnP3rB52fe74a7"
	},
	"errors": []
}
```

<br>

#### 3. Verify Email Address
Verifies the user's email address using the ID and hash from the verification link.

**Method**: `GET`

**Endpoint**: `/api/verify-email/{id}/{hash}`

> URL structure: `/api/verify-email/2/<HASH>?expires=<TIMESTAMP>&signature=<ENCRYPTED>`

**Response (200 OK)**
```js
{
  "success": true,
  "message": "Your email has been successfully verified!",
  "data": null,
  "errors": []
}
```

<br>

#### 4. Verify New Email Address (After updating email)
This endpoint verifies user's new email address whenever user updates/change their user email using the ID and hash from the verification link.

**Method**: `GET`

**Endpoint**: `/api/verify-new-email`

> URL structure: `/api/verify-new-email?expires=<TIMESTAMP>&user=<USER ID>&signature=<ENCRYPTED>`

**Response (200 OK)**
```js
{
"success": true,
"message": "Email verified and updated successfully.",
"data": null,
"errors": []
}
```

<br>

#### 5. Request Password Reset
Sends a password reset link to the user's email address.

**Method**: `POST`

**Endpoint**: `/api/password/forgot`

**Request Body**
```json
{
  "email": "user@example.com"
}
```

**Response (200 OK)**
```json
{
	"success": true,
	"message": "A password reset link has been sent to your email address.",
	"data": null,
	"errors": []
}
```

<br>

#### 6. Reset Password
Sets a new password using the token from the password reset email.

**Method**: `POST`

**Endpoint**: `/api/password/reset/{token}`

**Request Body**
```json
{
	"email" : "test@example.com",
  "password": "password2",
  "password_confirmation": "password2",
	"token": "<TOKEN FROM THE URL>"
}
```

**Response (200 OK)**
```json
{
    "success": true,
    "message": "Password updated successfully! You can now log in with your new password.",
    "data": null,
    "errors": []

}
```

<br>
<br>

### ↪ Authenticated Routes
Below are the authenticated routes requires a valid Sanctum API token in the `Authorization` header.

```
Authorization: Bearer <SANCTUM_TOKEN>
```

<br>

#### 1. Resend Verification Email
Sends a new email verification link to the authenticated user.

**Method**: `POST`

**Endpoint**: `/api/resend-verification-email`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "An email verification link has been sent to your inbox. Please check your email and follow the instructions to complete the verification process.",
	"data": null,
	"errors": []
}
```

<br>

#### 2. Logout
Revokes the token that was used to authenticate the current request.

**Method**: `POST`

**Endpoint**: `/api/logout`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "Logged out successfully!",
	"data": null,
	"errors": []
}
```

<br>

#### 3. Logout From All Devices
Revokes all tokens associated with the authenticated user.

**Method**: `POST`

**Endpoint**: `/api/logout-all`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "Logged out successfully from all devices!",
	"data": null,
	"errors": []
}
```

<br>

#### 4. Get Active Devices
Lists all active sessions/tokens for the current user.

**Method**: `GET`

**Endpoint**: `/api/active-device`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "Active devices!",
	"data": [
		{
			"id": 1,
			"name": "Web API",
			"attributes": {
				"mac": "XX-XX-XX-XX-XX-XX   Media disconnected",
				"browser": false,
				"platform": false,
				"ip_address": "127.0.0.1",
				"device_name": "Desktop"
			},
			"last_used_at": "2025-10-08T06:00:22.000000Z",
			"created_at": "2025-10-08T05:34:44.000000Z"
		}
	],
	"errors": []
}
```

<br>
<br>

### ↪ Account Management
These endpoints require the user to be authenticated and email-verified.

#### 1. Get User Profile
Retrieves the profile information of the authenticated user.

**Method**: `GET`

**Endpoint**: `/api/account`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "User profile details!",
	"data": {
		"id": 1,
		"name": "Test User",
		"email": "test@example.com",
		"profile": {
			"profile_picture": "profiles/default.png",
			"mobile": "+8801712345678",
			"address": "House 123, Road 4, Dhaka, Bangladesh",
			"dob": "1990-01-01",
			"gender": "male",
			"bio": "This is a sample bio for user 1.",
      "other_profile_info": "data.."
		}
	},
	"errors": []
}
```

<br>

#### 2. Update Email Address
Updates the user's email address. A new verification link will be sent to the new email.

**Method**: `PATCH`

**Endpoint**: `/api/account/email`

**Request Body**
```json
{
  "email": "new.email@example.com",
  "password": "password123"
}
```

**Response (200 OK)**
```json
{
	"success": true,
	"message": "Email updated. Please check your new inbox to verify the address.",
	"data": null,
	"errors": []
}
```

<br>

#### 3. Update Password
Updates the user's password.

**Method**: `POST`

**Endpoint**: `/api/account/password`

**Request Body**
```json
{
    "current_password": "password",
    "new_password": "newStrongPassword456",
    "new_password_confirmation": "newStrongPassword456"
}
```

**Response (200 OK)**
```json
{
	"success": true,
	"message": "Your password has been updated successfully.",
	"data": null,
	"errors": []
}
```

<br>

#### 4. Inactive/Reactivate/Suspend Account
Changes the user's account status.

**Method**: `PATCH`

**Endpoint**: `/api/account/{status}`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "Account inactivated successfully!",
	"data": null,
	"errors": []
}
```
> *Message will be displayed based on the status*

<br>
<br>

### ↪ API Key Management
Endpoints for managing user-generated API keys.

#### 1. Create API Key
Creates a new API key.
> Important: The `plain_text_token` is only returned once upon creation. Store it securely.

**Method**: `POST`

**Endpoint**: `/api/keys`

**Request**
```json
{
  "name": "Website API"
}
```

**Response (201 Created)**
```json
{
	"success": true,
	"message": "API key created successfully. Store this token securely as it will not be shown again.",
	"data": {
		"id": 1,
		"name": "Website API",
		"key": "9QYu1EUfFDhRHUG2B8Ac3FtvxqXHAsDr",
		"secret": "hPNjZskLfkqGt1uvute2mV9Td1ymjRhsmwYze3zvZGY3xm5t8f50q7X3nsEKSWQc",
		"expires_at": "2026-01-09T21:24:48.000000Z"
	},
	"errors": []
}
```

<br>

#### 2. List API Keys
Retrieves all API keys belonging to the user.

**Method**: `GET`

**Endpoint**: `/api/keys`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "All API keys of the user!",
	"data": [
		{
			"id": 1,
			"name": "Website API",
			"key": "aBUaIu0g6vYSk8SKU96v3nCmSioLEVLb",
			"expires_at": "2026-01-10T05:43:31.000000Z",
			"created_at": "2025-10-12T05:43:31.000000Z"
		},
		{
			"id": 2,
			"name": "Mobile APP",
			"key": "SKUnCmSiu96oLEVLbaBUaIv30g6vYSk8",
			"expires_at": "2026-01-10T05:43:42.000000Z",
			"created_at": "2025-10-12T05:43:42.000000Z"
		}
	],
	"errors": []
}
```

<br>

#### 3. Regenerate API Key
Generates a new token for an existing API key.

**Method**: `PATCH`

**Endpoint**: `/api/keys/{id}`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "API key regenerated successfully!",
	"data": {
		"key": "hRHUG2B8AsDc3F9QYutvxqXHAr1EUfFD",
		"secret": "gqLskXhXjcpBT3aIPu8y6GbmlKsuJiONNoW03SlU6ByIbP489VOFzbIogGe3WUm7",
		"abilities": null
	},
	"errors": []
}
```

<br>

#### 4. Delete API Key
Deletes an API key.

**Method**: `DELETE`

**Endpoint**: `/api/keys/{id}`

**Response (200 OK)**
```json
{
	"success": true,
	"message": "API key revoked successfully!",
	"data": null,
	"errors": []
}
```

<br>

## Copyright and license

Code and documentation copyright 2022 the [M B Parvez](https://www.mbparvez.me) and [Gosoft](https://www.gosoft.io). Code released under the MIT License.
