<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // Auth related
    'ACCOUNT_CREATED'               => 'Account created successfully!',
    'INVALID_LOGIN'                 => 'Invalid login details! Please try again with valid credentials.',
    'USER_LOGIN'                    => 'Login successful!',
    'LOGGED_OUT'                    => 'Logged out successfully!',
    'LOGGED_OUT_ALL'                => 'Logged out successfully from all devices!',
    'ALL_LOGGED_DEVICES'            => 'Active devices!',
    'ALREADY_VERIFIED'              => 'This email address is already verified.',
    'VERIFICATION_EMAIL_SEND'       => 'An email verification link has been sent to your inbox. Please check your email and follow the instructions to complete the verification process.',
    'INVALID_VERIFICATION_LINK'     => 'Invalid verification link.',
    'EMAIL_VERIFIED'                => 'Email verified successfully.',

    // Password related
    'PASS_RESET_MSG'                => 'An email will be sent if there is an account with the email you provided!',
    'RESET_PASS_ERR'                => 'Password reset error!',
    'RESET_PASS_OK'                 => 'Password reset successfully!',
    'UNAUTH_PASS_UPDATE'            => 'Password update error!',
    'PASS_UPDATE_CURRENT_PASS_ERR'  => 'Current password doesn\'t matched!',
    'PASS_UPDATE'                   => 'Password updated successfully! Login to your account to continue.',

    // Profile related
    'USER_INFO'                     => 'User profile details!',
    'EMAIL_UPDATE_PASS_ERR'         => 'Password does not match!',
    'EMAIL_UPDATE_VERIFICATION_SENT'=> 'An email sent to your new email to verify. Please verify it.',
    'EMAIL_UPDATE_INVALID_TOKEN'    => 'Invalid or expired verification link!',
    'EMAIL_UPDATE_INVALID_USER'     => 'Invalid user for new email verification!',
    'EMAIL_UPDATE_NOT_PENDING'      => 'No pending email change found!',
    'NEW_EMAIL_VERIFIED'            => 'Email verified and updated successfully.',
    // User status update
    'ACTIVE'                    => 'Account has been activated!',
    'REACTIVE'                  => 'Account has been reactivated!',
    'INACTIVE'                  => 'Account inactivated successfully!',
    'DELETE'                    => 'Account deleted successfully!',
    'BANNED'                    => 'Account banded!',

    // API Key related
    'API_KEY_ALL'                   => 'All API keys of the user!',
    'API_KEY_CREATE'                => 'API key created successfully!',
    'API_KEY_REGENERATE'            => 'API key regenerated successfully!',
    'API_KEY_DELETE'                => 'API key revoked successfully!',
    'API_INVALID_CREDENTIAL'        => 'Invalid or expired API credentials.',

    // Common messages
    'THROTTLE'                  => 'You are making too many requests. Please try again in a minute.',
    'ERROR_COMMON'              => 'Something went wrong! Please try again.',
    'SUCCESS_COMMON'            => 'Operation successful!',
    'INVALID_REQUEST'           => 'Invalid request!',
    'ROUTE_FALLBACK'            => 'Operation successful!',
];