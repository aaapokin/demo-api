<?php

namespace app\rmq\ConsumerPackages;


class ESkip extends \Exception
{
    public static function skip(){
        return new self();
    }

}