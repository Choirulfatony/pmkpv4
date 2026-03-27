<?php

if (!function_exists('waktu_lalu')) {

    function waktu_lalu($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) {
            return "Baru saja";
        }

        if ($diff < 3600) {
            return floor($diff / 60) . " menit lalu";
        }

        if ($diff < 86400) {
            return floor($diff / 3600) . " jam lalu";
        }

        if ($diff < 172800) {
            return "Kemarin";
        }

        return floor($diff / 86400) . " hari lalu";
    }
}
