<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\ApiAutoStat;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Requests\ARequest;
use app\ApiExternal\Requests\ERequestValidate;

abstract class AAutoStatRequest extends ARequest
{
    protected Methods $method = Methods::POST;
    public function __construct()
    {
        $this->host = 'https://price.autostat.ru';

        if (!$token = (new ApiAutoStat())?->getTokenResponse()?->getToken()) {
            throw new ERequestValidate('не удалось получить токен');
        }
        // dd($token);
        $this->addHeader('Token', $token);
        // dd($this->headers);

        parent::__construct();
    }


}