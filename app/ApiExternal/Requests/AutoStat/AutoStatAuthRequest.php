<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\enums\Methods;
use app\ApiExternal\Requests\ARequest;
use app\ApiExternal\Responses\AutoStat\AuthResponse;
use app\Env;

class AutoStatAuthRequest extends ARequest
{
    protected Methods $method = Methods::POST;
    protected string $responseClass = AuthResponse::class;

    protected string $host='https://auth.autostat.ru';
    protected string $url='';

    protected function setPayload(): void
    {
        $this->addPayloadValue(
            'query',
            sprintf(
                'mutation{generateToken(User:{username: "%s", password: "%s"}){value}}',
                Env::apiAutoStatLogin(),
                Env::apiAutoStatPassword()
            )
        );
    }
}