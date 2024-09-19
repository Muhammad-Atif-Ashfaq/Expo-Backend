<?php
namespace App\Helpers;

class ExceptionHandlerHelper extends Helper
{

    public static function tryCatch($callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return self::sendSeverError('Something went wrong! Please try again later',
                $e->getMessage()
            );
        }
    }

}

