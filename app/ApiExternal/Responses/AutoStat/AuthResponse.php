<?php

namespace app\ApiExternal\Responses\AutoStat;

use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\EResponseValidate;

class AuthResponse extends AResponse
{
    private string $token;

    protected function validate(): void
    {
        if (!$token = $this->getByPath('data.generateToken.value')) {
            throw EResponseValidate::notFound('token data.generateToken.value');
        }
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}