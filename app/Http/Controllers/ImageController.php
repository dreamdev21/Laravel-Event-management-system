<?php

namespace App\Http\Controllers;

class ImageController extends Controller
{
    public function generateThumbnail($image_src, $width = false, $height = false, $quality = 90)
    {
        $img = Image::make('public/foo.jpg');

        $img->resize(320, 240);

        $img->insert('public/watermark.png');

        $img->save('public/bar.jpg');
    }
}
