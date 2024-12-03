<?php

namespace app\rmq\ConsumerPackages;


use app\helpers\Log\Log;

class EValidationError extends \Exception
{

    public static function errorByPackage(APackage|PackageAutomatic $package, $msg): self
    {
        Log::error($msg,$package,$e =new self());
        return $e;
    }

    public static function notFound($name){
        return new self($name.' not found');
    }

}