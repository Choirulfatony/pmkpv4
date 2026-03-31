<?php

namespace App\Libraries;

class Captcha
{
    private string $imgPath;
    private string $imgUrl;
    private int $ttl = 300;

    public function __construct()
    {
        $this->imgPath = FCPATH . 'assets/captcha/';
        $this->imgUrl  = base_url('assets/captcha');
        
        if (!is_dir($this->imgPath)) {
            mkdir($this->imgPath, 0755, true);
        }
    }

    public function generate(array $config = []): array
    {
        $length     = $config['length'] ?? 5;
        $imgWidth   = $config['img_width'] ?? 150;
        $imgHeight  = $config['img_height'] ?? 40;
        $ttl        = $config['ttl'] ?? $this->ttl;

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $word = '';
        for ($i = 0; $i < $length; $i++) {
            $word .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $this->cleanup($ttl);

        $filename = 'captcha_' . bin2hex(random_bytes(6)) . '.png';
        $this->createImage($filename, $word, $imgWidth, $imgHeight);

        return [
            'word'  => strtoupper($word),
            'image' => '<img src="' . $this->imgUrl . '/' . $filename . '?v=' . time() . '" alt="CAPTCHA" class="rounded" id="captcha-img">',
            'filename' => $filename
        ];
    }

    private function createImage(string $filename, string $word, int $width, int $height): void
    {
        $image = imagecreate($width, $height);
        $bg    = imagecolorallocate($image, 255, 255, 255);
        $text  = imagecolorallocate($image, rand(30, 80), rand(30, 80), rand(80, 150));

        for ($i = 0; $i < 3; $i++) {
            $line = imagecolorallocate($image, rand(150, 220), rand(150, 220), rand(150, 220));
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line);
        }

        for ($i = 0; $i < 50; $i++) {
            $pixel = imagecolorallocate($image, rand(180, 250), rand(180, 250), rand(180, 250));
            imagesetpixel($image, rand(0, $width), rand(0, $height), $pixel);
        }

        $fontSize = 5;
        $textX = ($width - imagefontwidth($fontSize) * strlen($word)) / 2;
        $textY = ($height - imagefontheight($fontSize)) / 2;
        imagestring($image, $fontSize, (int)$textX, (int)$textY, $word, $text);

        imagepng($image, $this->imgPath . $filename);
        imagedestroy($image);
    }

    private function cleanup(int $ttl): void
    {
        $files = glob($this->imgPath . 'captcha_*.png') ?: [];
        
        foreach ($files as $file) {
            if (filemtime($file) < time() - $ttl) {
                @unlink($file);
            }
        }

        if (count($files) > 50) {
            usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));
            $excess = count($files) - 50;
            for ($i = 0; $i < $excess; $i++) {
                @unlink($files[$i]);
            }
        }
    }

    public static function validate(string $input, string $sessionWord): bool
    {
        return strtoupper($input) === strtoupper($sessionWord);
    }
}
