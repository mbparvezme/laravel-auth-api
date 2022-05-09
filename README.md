<h1 align="center">Laravel API Endpoints</h1>
<h3 align="center">Ready APIs for Auth and Password Reset With Mobile and Email OTP</h1>
<br>

## About This Package

**Laravel API Endpoints** is a complete authentication and account recovery package developed on the top of the [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum). This package is completely compatible with [sveltekit-admin-starter](https://github.com/theUIxyz/sveltekit-admin-starter). In other word, this Laravel package has been developed for [sveltekit-admin-starter](https://github.com/theUIxyz/sveltekit-admin-starter) but perfect for any thechnology that uses the REST API endpoints.

<br>

## Features
This package includes the following features:
- Secure login/logout
- Registratin
- Mobile number verification with OTP
- Email verification
- Easy to understand response and error messages

<br>

## API Endpoints
It includes the following endpoints:

| Details                 | Method | API End Points             |
| ----------------------- | ------ | -------------------------- |
| Registration            | POST   | [/register](#)             |
| Login                   | POST   | [/login](#)                |
| Password reset request  | POST   | [/password-reset](#)       |
| Password reset          | POST   | [/reset-password](#)       |
| Logout                  | GET    | [/logout](#)               |

<br>

### Registration Endpoint

> Request Method: **POST** <br> Endpoint: **/register**

Request Body
```js
{
  "name": "M B Parvez",
  "mobile": "01717000000",
  "email": "user1@email.com",
  "password": "password1",
  "password_confirmation": "password1",
  "country" : "bd",
  "country_code" : "88"
}
```
Request Header
```js
{
  "Content-Type": "application/json"
  "Accept": "application/json"
}
```
Response
```js
{
  "user": {
    "name": "M B Parvez",
    "email": "user1@email.com",
    "mobile": "01717000000",
    "country": "bd",
    "country_code": "88",
    "updated_at": "2022-05-08T13:09:53.000000Z",
    "created_at": "2022-05-08T13:09:53.000000Z",
    "id": 1
  },
  "token": "1|qaPeBQyYYYIFDZZ8JPS7OOFONWy1LlgHSg5EzsW8",
  "success": true,
  "message": "Account created successfully"
}
```

<br>

### Login Endpoint

> Request Method: **POST** <br> Endpoint: **/login** <br> User can use either Email or mobile number as `userID` for login

Request Body
```js
{
  "userID": "user1@email.com", // or 01717000000
  "password": "password1"
}
```
Request Header
```js
{
  "Content-Type": "application/json"
  "Accept": "application/json"
}
```
Response
```js
{
  "user": {
    "id": 1,
    "name": "M B Parvez",
    "email": "user1@email.com",
    "email_verified_at": null,
    "created_at": "2022-05-07T13:26:20.000000Z",
    "updated_at": "2022-05-07T13:26:20.000000Z"
  },
  "token": "2|MyddQxXZvBJ6spzKOI3Kubjte6q3QqS0IYZpZ0uw",
  "success": true,
  "error": false
}
```

<br>

### Password Reset Request Endpoint

> Request Method: **POST** <br> Endpoint: **/password-reset**

Request Body
```js
{
  "userID": "user1@email.com", // or 01717000000
}
```
> User can use either Email or mobile number as `userID`. User will receive the OTP either in the email or in their mobile depending on the `userID`

Request Header
```js
{
  "Content-Type": "application/json"
  "Accept": "application/json"
}
```
Response
```js
{
  "success": true,
  "message": "Please check your Email and use to code to reset password."
}
```

<br>

### Password Reset Endpoint

> Request Method: **POST** <br> Endpoint: **/reset-password**

Request Body
```js
{
  "otp": "290479",
  "password" : "password1", // New password
  "password_confirmation" : "password1", // New password confirmation
  "token": "3b1c32e4-8729-481a-8de2-27e3287f8ac3"
}
```
> Token will be generated while hitting the `password-reset` endpoints.

Request Header
```js
{
  "Content-Type": "application/json"
  "Accept": "application/json"
}
```
Response
```js
{
  "success": true,
  "message": "Password updated successfully! Login to your account"
}
```

<br>

### Logout Endpoint

> Request Method: **GET** <br> Endpoint: **/logout**

Request Header
```js
{
  "Content-Type": "application/json"
  "Accept": "application/json",
  'Authorization': "Bearer SANCTUM_AUTH_TOKEN"
}
```
> Replace **SANCTUM_AUTH_TOKEN** with real token

## Copyright and license

Code and documentation copyright 2022 the [M B Parvez](https://www.mbparvez.me) and [Gosoft](https://www.gosoft.io). Code released under the MIT License.