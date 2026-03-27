<?php
$im = imagecreatetruecolor(100, 40);
$bg = imagecolorallocate($im, 255,255,255);
imagefilledrectangle($im, 0,0,100,40,$bg);
imagepng($im, __DIR__ . '/assets/captcha/test.png');
imagedestroy($im);
echo "OK";
