<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if (!$image) {
            return null;
        }

        $fileName = time() . '.png';
        // save image

        \Storage::disk($path)->put($fileName, base64_decode($image));

        // return the path
        // URL is the base url exp: localhost:8000
        return URL::to('/') . '/storage/' . $path . '/' . $fileName;
    }
}
