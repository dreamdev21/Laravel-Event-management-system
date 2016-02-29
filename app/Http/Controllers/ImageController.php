<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ImageController extends Controller {

    public function generateThumbnail($image_src, $width = FALSE, $height = false, $quality = 90) {
        $img = Image::make('public/foo.jpg');

        $img->resize(320, 240);

        $img->insert('public/watermark.png');

        $img->save('public/bar.jpg');
    }

}
