<?php

namespace app\ApiExternal\Listeners;

use app\ApiExternal\Responses\IResponse;

abstract class AListener implements IListener
{
    public function __construct(
        readonly protected IResponse $response
    )
    {

    }
}