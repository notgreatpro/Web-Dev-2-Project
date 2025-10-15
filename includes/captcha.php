<?php
session_start();

// Only generate code if missing
if (empty($_SESSION['captcha_code'])) {
    $code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
    $_SESSION['captcha_code'] = $code;
} else {
    $code = $_SESSION['captcha_code'];
}

$width = 180;
$height = 60;
$image = imagecreatetruecolor($width, $height);

// Background color
$bgcolor = imagecolorallocate($image, rand(220,255), rand(220,255), rand(220,255));
imagefilledrectangle($image, 0, 0, $width, $height, $bgcolor);

// Draw noisy lines
for ($i = 0; $i < 10; $i++) {
    $linecol = imagecolorallocate($image, rand(120,220), rand(120,220), rand(120,220));
    imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $linecol);
}

// Draw noisy dots
for ($i = 0; $i < 200; $i++) {
    $dotcol = imagecolorallocate($image, rand(100,255), rand(100,255), rand(100,255));
    imagesetpixel($image, rand(0,$width), rand(0,$height), $dotcol);
}

// Load font (use your own TTF font file, e.g. arial.ttf, at public/fonts/arial.ttf)
$font_file = __DIR__ . '/fonts/DINPro-Bold_13934.woff';

// Draw each character with random angle and color
for ($i = 0; $i < strlen($code); $i++) {
    $font_size = rand(26, 32);
    $angle = rand(-30, 30);
    $x = 20 + $i * 25 + rand(-5,5);
    $y = rand(38, 48);
    $fontcolor = imagecolorallocate($image, rand(0,150), rand(0,150), rand(0,150));
    if (file_exists($font_file)) {
        imagettftext($image, $font_size, $angle, $x, $y, $fontcolor, $font_file, $code[$i]);
    } else {
        // fallback if font missing
        imagestring($image, 5, $x, $y-30, $code[$i], $fontcolor);
    }
}

// Optionally: Add a wavy line across the code
$wave_color = imagecolorallocate($image, rand(50,200), rand(50,200), rand(50,200));
$amplitude = rand(5,12);
$period = rand(80,120);
for ($px = 0; $px < $width; $px += 2) {
    $py = round($height/2 + sin($px/$period * 2 * M_PI) * $amplitude);
    imagesetpixel($image, $px, $py, $wave_color);
}

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>