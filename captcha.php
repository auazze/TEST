<?php
declare(strict_types=1);
session_start();
header('Content-Type: image/png');

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$code  = '';
for ($i = 0; $i < 4; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
}
$_SESSION['captcha_code'] = $code;

$w = 120; $h = 40;
$img = imagecreatetruecolor($w, $h);
$white = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, $w, $h, $white);

for ($i = 0; $i < 5; $i++) {
    $c = imagecolorallocate($img, random_int(150,255), random_int(150,255), random_int(150,255));
    imageline($img, random_int(0,$w), random_int(0,$h), random_int(0,$w), random_int(0,$h), $c);
}

$dark = imagecolorallocate($img, 30, 30, 30);
imagestring($img, 5, 22, 12, $code, $dark);

imagepng($img);
imagedestroy($img);
