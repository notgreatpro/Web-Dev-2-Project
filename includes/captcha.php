<?php
// Simple text-based CAPTCHA for demo (not an image/distorted, but matches your screenshot style)
function generateCaptchaString($length = 6) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $chars[rand(0, strlen($chars)-1)];
    }
    return $captcha;
}

function checkCaptcha($input) {
    return isset($_SESSION['captcha_code']) && strtolower(trim($input)) === strtolower($_SESSION['captcha_code']);
}