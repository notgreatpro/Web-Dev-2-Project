<?php


function generateCaptchaString($length = 5) {
    $chars = 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[random_int(0, strlen($chars)-1)];
    }
    $_SESSION['captcha'] = $str;
    return $str;
}

function checkCaptcha($input) {
    return isset($_SESSION['captcha']) && strtolower($input) === strtolower($_SESSION['captcha']);
}
?>