<?php

return [
    'failed'   => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // OTP / 2FA
    'otp_subject'           => 'Your Login Verification Code',
    'otp_greeting'          => 'Hello :name,',
    'otp_line1'             => 'Your one-time verification code is:',
    'otp_line2'             => 'This code expires in 10 minutes.',
    'otp_line3'             => 'If you did not attempt to log in, please ignore this email.',
    'otp_title'             => 'Two-Factor Verification',
    'otp_code'              => 'Verification Code',
    'otp_verify_button'     => 'Verify',
    'otp_resend_link'       => 'Resend code',
    'otp_resent'            => 'A new code has been sent to your email.',
    'otp_resend_limit'      => 'Too many resend attempts. Please wait a moment.',
    'otp_invalid'           => 'Invalid or expired code. Please try again.',
    'otp_too_many_attempts' => 'Too many failed attempts. Please try again in :seconds seconds.',
    'otp_hint'              => 'A 6-digit code was sent to your registered email address.',
];
