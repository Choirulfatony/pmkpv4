<?php

namespace App\Controllers;

class Test extends BaseController
{
   public function gd()
{
    $path = FCPATH . 'assets/captcha/test.png';

    $im = imagecreatetruecolor(120, 40);
    $bg = imagecolorallocate($im, 255,255,255);
    imagefilledrectangle($im, 0, 0, 120, 40, $bg);

    imagepng($im, $path);
    imagedestroy($im);

    return "
        FCPATH = " . FCPATH . "<br>
        ROOTPATH = " . ROOTPATH . "<br>
        File path = " . $path;
}

}
