<?php
session_start();

// Generate a new code ONLY if not already set.
if (empty($_SESSION['captcha_code'])) {
    $code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    $_SESSION['captcha_code'] = $code;
} else {
    $code = $_SESSION['captcha_code'];
}

$width = 200;
$height = 60;
$image = imagecreatetruecolor($width, $height);

// Fill background with light random color
$bgcolor = imagecolorallocate($image, rand(200,255), rand(200,255), rand(200,255));
imagefilledrectangle($image, 0, 0, $width, $height, $bgcolor);

// Draw many random lines for noise
for ($i = 0; $i < 15; $i++) {
    $linecol = imagecolorallocate($image, rand(100,220), rand(100,220), rand(100,220));
    imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $linecol);
}

// Draw random dots for more noise
for ($i = 0; $i < 350; $i++) {
    $dotcol = imagecolorallocate($image, rand(120,255), rand(120,255), rand(120,255));
    imagesetpixel($image, rand(0,$width), rand(0,$height), $dotcol);
}

// Draw a wavy line over the code
$wave_color = imagecolorallocate($image, rand(50,200), rand(50,200), rand(50,200));
$amplitude = rand(5,12);
$period = rand(80,120);
for ($px = 0; $px < $width; $px += 2) {
    $py = round($height/2 + sin($px/$period * 2 * M_PI) * $amplitude);
    imagesetpixel($image, $px, $py, $wave_color);
}

// Draw each character with random angle/position/color
$font_file = __DIR__ . '/fonts/arial.ttf'; // Place a .ttf font here

for ($i = 0; $i < strlen($code); $i++) {
    $font_size = rand(26,34);
    $angle = rand(-35,35);
    $x = 20 + $i * 28 + rand(-4,4);
    $y = rand(38, 54);
    $fontcolor = imagecolorallocate($image, rand(0,120), rand(0,120), rand(0,120));
    if (file_exists($font_file)) {
        imagettftext($image, $font_size, $angle, $x, $y, $fontcolor, $font_file, $code[$i]);
    } else {
        imagestring($image, 5, $x, $y-30, $code[$i], $fontcolor);
    }
}

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>