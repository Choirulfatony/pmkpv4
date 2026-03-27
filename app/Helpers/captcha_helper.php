<?php

// function create_captcha(array $config = [])
// {
//     // ================= CHARSET =================
//     $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
//     $chars .= 'abcdefghjkmnpqrstuvwxyz';
//     $chars .= '23456789';
//     // $chars .= '@#$%&*?';

//     $length = $config['length'] ?? 5;

//     $word = '';
//     for ($i = 0; $i < $length; $i++) {
//         $word .= $chars[random_int(0, strlen($chars) - 1)];
//     }

//     // ================= IMAGE SIZE =================
//     $imgWidth  = $config['img_width']  ?? 160;
//     $imgHeight = $config['img_height'] ?? 50;
//     $fontSize  = $config['font_size']  ?? 5;

//     $imgPath = FCPATH . 'assets/captcha/';
//     $imgUrl  = base_url('assets/captcha');

//     if (!is_dir($imgPath)) {
//         mkdir($imgPath, 0777, true);
//     }

//     // bersihkan captcha lama
//     foreach (glob($imgPath . 'captcha_*.png') as $old) {
//         if (@filemtime($old) < time() - 300) {
//             @unlink($old);
//         }
//     }

//     // ================= DRAW IMAGE =================
//     $image = imagecreate($imgWidth, $imgHeight);
//     $bg    = imagecolorallocate($image, 255, 255, 255);
//     $text  = imagecolorallocate($image, 20, 40, 100);

//     // posisi tengah
//     $textX = ($imgWidth  - imagefontwidth($fontSize) * strlen($word)) / 2;
//     $textY = ($imgHeight - imagefontheight($fontSize)) / 2;

//     imagestring($image, $fontSize, $textX, $textY, $word, $text);

//     // noise
//     for ($i = 0; $i < 30; $i++) {
//         $noise = imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
//         imagesetpixel($image, rand(0, $imgWidth), rand(0, $imgHeight), $noise);
//     }

//     $filename = 'captcha_' . time() . '_' . rand(100, 999) . '.png';
//     imagepng($image, $imgPath . $filename);
//     imagedestroy($image);

//     return [
//         'word'  => $word,
//         'image' => '<img src="' . $imgUrl . '/' . $filename . '?v=' . time() . '" alt="captcha">'
//     ];
// }

function create_captcha(array $config = [])
{
    // ================= CONFIG DEFAULT =================
    $length     = $config['length']      ?? 5;
    $imgWidth   = $config['img_width']   ?? 160;
    $imgHeight  = $config['img_height']  ?? 50;
    $ttl        = $config['ttl']         ?? 300; // 5 menit
    $maxFiles   = $config['max_files']   ?? 50;

    // ================= CHARSET =================
    $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $chars .= 'abcdefghjkmnpqrstuvwxyz';
    $chars .= '23456789';

    // ================= GENERATE WORD =================
    $word = '';
    for ($i = 0; $i < $length; $i++) {
        $word .= $chars[random_int(0, strlen($chars) - 1)];
    }

    // ================= PATH =================
    $imgPath = FCPATH . 'assets/captcha/';
    $imgUrl  = base_url('assets/captcha');

    if (!is_dir($imgPath)) {
        mkdir($imgPath, 0777, true);
    }

    // ================= CLEANUP CAPTCHA =================
    $files = glob($imgPath . 'captcha_*.png') ?: [];

    // Hapus berdasarkan umur (TTL)
    foreach ($files as $file) {
        if (filemtime($file) < time() - $ttl) {
            @unlink($file);
        }
    }

    // Batasi jumlah file
    $files = glob($imgPath . 'captcha_*.png') ?: [];
    if (count($files) > $maxFiles) {
        usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));
        $excess = count($files) - $maxFiles;
        for ($i = 0; $i < $excess; $i++) {
            @unlink($files[$i]);
        }
    }

    // ================= DRAW IMAGE =================
    $image = imagecreate($imgWidth, $imgHeight);
    $bg    = imagecolorallocate($image, 255, 255, 255);
    $text  = imagecolorallocate($image, rand(20, 60), rand(20, 60), rand(100, 150));

    // Noise garis
    for ($i = 0; $i < 3; $i++) {
        $lineColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
        imageline(
            $image,
            rand(0, $imgWidth),
            rand(0, $imgHeight),
            rand(0, $imgWidth),
            rand(0, $imgHeight),
            $lineColor
        );
    }

    // Noise titik
    for ($i = 0; $i < 40; $i++) {
        $noise = imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
        imagesetpixel($image, rand(0, $imgWidth), rand(0, $imgHeight), $noise);
    }

    // ================= DRAW TEXT =================
    $fontSize = rand(4, 5);
    $textX = ($imgWidth - imagefontwidth($fontSize) * strlen($word)) / 2;
    $textY = ($imgHeight - imagefontheight($fontSize)) / 2;

    imagestring($image, $fontSize, (int)$textX, (int)$textY, $word, $text);

    // ================= SAVE IMAGE =================
    $filename = 'captcha_' . bin2hex(random_bytes(6)) . '.png';
    imagepng($image, $imgPath . $filename);
    imagedestroy($image);

    return [
        'word'  => strtoupper($word),
        'image' => '<img src="' . $imgUrl . '/' . $filename . '?v=' . time() . '" alt="captcha">'
    ];
}

