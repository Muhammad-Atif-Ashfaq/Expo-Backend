<?php
namespace App\Helpers;
use Illuminate\Http\Request;

class UploadFiles extends Helper
{

    public static function upload($image, string $fieldName, string $folder)
    {

        if ($image && $image->isValid()) {
            $image = $image;
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->storeAs($folder, $imageName, 'public');
            $baseUrl = url('/');
            $imageUrl =$baseUrl . '/storage/' . $folder . '/' . $imageName;
            return $imageUrl;
        }
        return null;
    }

}