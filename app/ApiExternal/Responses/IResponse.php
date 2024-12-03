<?php

namespace app\ApiExternal\Responses;

use app\ApiExternal\enums\Methods;
use App\Entity\Lead\CommonLead;
use app\ApiExternal\ALKRequest;
use App\Utils\Phone\PhoneModelRU;
use App\DI;

interface  IResponse
{

    public function isValid():bool;
    public function isSkip():bool;

    public function isOk():bool;

    public function getStatus():int;

    public function gerError():?string;



}