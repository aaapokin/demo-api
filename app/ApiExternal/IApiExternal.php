<?php

namespace app\ApiExternal;

use app\ApiExternal\Requests\IRequest;
use app\ApiExternal\Responses\IResponse;

interface IApiExternal
{
    public function request(IRequest $request):?IResponse;

    public function requestViaQUEUE(IRequest $request): bool;
}
