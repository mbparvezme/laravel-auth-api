<?php

return [
    // Auth
    'ACCOUNT_CREATED'           => 'Account created successfully! Please check your email to verify your account.',
    'USER_LOGIN'                => 'Login successful!',
    'LOGGED_OUT'                => 'Logged out successfully!',
    'LOGGED_OUT_ALL'            => 'Logged out from all devices successfully!',
    'INVALID_LOGIN'             => 'Invalid email or password.',
    'USER_BANNED'               => 'Your account has been permanently banned.',
    'USER_SUSPENDED'            => 'Your account has been suspended. Please contact support.',

    // Email verification
    'EMAIL_VERIFIED'            => 'Your email has been verified successfully!',
    'ALREADY_VERIFIED'          => 'Your email is already verified.',
    'INVALID_VERIFICATION_LINK' => 'This verification link is invalid or has expired.',
    'VERIFICATION_EMAIL_SENT'   => 'A verification link has been sent to your email.',

    // Password
    'PASSWORD_RESET_SENT'       => 'A password reset link has been sent to your email.',
    'PASSWORD_RESET_OK'         => 'Password reset successfully! You can now log in with your new password.',
    'PASSWORD_RESET_FAILED'     => 'This password reset link is invalid or has expired.',
    'PASSWORD_UPDATED'          => 'Password updated successfully.',
    'INVALID_CURRENT_PASSWORD'  => 'The current password is incorrect.',
    'PASSWORD_RESET_SUBJECT'    => 'Reset Your Password',
    'PASSWORD_RESET_LINE'       => 'You are receiving this email because we received a password reset request for your account.',
    'PASSWORD_RESET_ACTION'     => 'Reset Password',
    'PASSWORD_RESET_EXPIRY'     => 'This password reset link will expire in 60 minutes.',

    // Profile
    'PROFILE_FETCHED'           => 'Profile retrieved successfully.',
    'PROFILE_UPDATED'           => 'Profile updated successfully.',
    'EMAIL_UPDATE_REQUESTED'    => 'Verification link sent to your new email address.',
    'EMAIL_UPDATED'             => 'Email address updated successfully.',
    'VERIFY_NEW_EMAIL_SUBJECT'  => 'Verify Your New Email Address',
    'VERIFY_EMAIL_ACTION'       => 'Verify Email',
    'STATUS_UPDATED'            => 'Account status updated successfully.',
    'INVALID_STATUS'            => 'Invalid account status provided.',

    // Multi-device
    'DEVICES_FETCHED'           => 'Active sessions retrieved successfully.',
    'DEVICE_REVOKED'            => 'Session revoked successfully.',
    'DEVICE_NOT_FOUND'          => 'Session not found.',
    'DEVICES_OTHERS_REVOKED'    => 'All other sessions have been revoked.',

    // API keys
    'API_KEYS_FETCHED'          => 'API keys retrieved successfully.',
    'API_KEY_CREATED'           => 'API key created. Store it securely — it will not be shown again.',
    'API_KEY_REVOKED'           => 'API key revoked successfully.',
    'API_KEY_NOT_FOUND'         => 'API key not found.',

    // Social auth
    'SOCIAL_REDIRECT'           => 'OAuth redirect URL generated.',
    'SOCIAL_AUTH_FAILED'        => 'Social authentication failed. Please try again.',
    'SOCIAL_INVALID_PROVIDER'   => 'The requested authentication provider is not supported.',
    'SOCIAL_EMAIL_UNVERIFIED'   => 'An account with this email exists but has not been verified. Please verify your email or log in with your password.',

    // Logs
    'LOGS_FETCHED'              => 'Activity logs retrieved successfully.',

    // General
    'ROUTE_NOT_FOUND'           => 'The requested route does not exist.',
    'SERVER_ERROR'              => 'An unexpected error occurred. Please try again later.',
    'UNAUTHENTICATED'           => 'You must be logged in to access this resource.',
    'UNVERIFIED'                => 'Please verify your email address before accessing this resource.',
];
