<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Helper
{
    // TODO: This method constructs and sends a JSON response indicating a server error.
    // TODO: It takes an error message, an optional array of additional error messages, and an HTTP status code.
    // TODO: It returns a JSON response with the provided error message(s) and status code.
    public static function sendSeverError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['payload'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}

if (!function_exists('deleteImageFromSpecificPath')) {
    function deleteImageFromSpecificPath($imagePath, $name)
    {
        if (isset($name) && !empty($name)) {
            if (Storage::disk('public')->exists($imagePath . $name)) {
                Storage::disk('public')->delete($imagePath . $name);
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('getImagePath')) {
    function imagePathWithDefault($imagePath, $imageName, $defaultPath, $defaultName)
    {
        if (!empty($imageName)) {
            if (filter_var($imageName, FILTER_VALIDATE_URL)) {
                $headers = get_headers($imageName);
                if ($headers && strpos($headers[0], '200')) {
                    return $imageName;
                }
            }
            if (Storage::disk('public')->exists($imagePath . $imageName)) {
                return asset('storage/' . $imagePath . $imageName);
            }
        }
        return $defaultPath . $defaultName;
    }
}
