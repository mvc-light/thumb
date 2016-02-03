<?php

namespace Thumbnail;

class Thumb {

    public static function run($url, $w_, $h_) {
        $src = self::trueUrl($url);
        $w = $w_;
        $h = $h_;
        $mtp_name = md5($src) . "w$w" . "h$h" . '.tmb';
        if (!is_dir('cache')) {
            @mkdir('cache');
        }
        if (!file_exists('cache/' . $mtp_name)) {
            $info = getimagesize($src);
            $src_w = $info[0];
            $src_h = $info[1];
            $new_w = $w;
            $new_h = $new_w * $src_h / $src_w;
            $left = 0;
            $top = 0;
            if ($h > $new_h) {
                $new_h = $h;
                $new_w = $new_h * $src_w / $src_h;
                $left = ($new_w - $w) / 2;
            }
            if ($src_w / $src_h <= $w / $h) {
                $top = (($w * $src_h / $src_w) - $h) / 2;
            }
            $createthumb = imagecreatetruecolor($w, $h);
            $source = imagecreatefromjpeg($src);
            imagecopyresized($createthumb, $source, -$left, -$top, 0, 0, $new_w, $new_h, $src_w, $src_h);
            imagejpeg($createthumb, dirname($_SERVER['SCRIPT_FILENAME']) . '/cache/' . $mtp_name, 90);
        }
        header('Content-Type: image/jpeg');
        $image = imagecreatefromjpeg(self::trueUrl('/cache/' . $mtp_name));
        exit(imagejpeg($image));
    }

    private static function trueUrl($link = '') {
        $array_info = parse_url($link);
        if ($array_info['path'] === '') {
            return FALSE;
        }
        $scheme = (isset($array_info['scheme'])) ? $array_info['scheme'] : $_SERVER['REQUEST_SCHEME'];
        $host = (isset($array_info['host'])) ? $array_info['host'] : $_SERVER['HTTP_HOST'];
        $path = $array_info['path'];
        return "$scheme://$host$path";
    }

}
