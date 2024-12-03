<?php

namespace app\ApiExternal\Requests;

use app\ApiExternal\ALKRequest;
use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Listeners\IListener;
use app\ApiExternal\Responses\IApiExternalResponse;
use app\ApiExternal\Responses\IResponse;
use App\DI;
use App\Entity\Lead\CommonLead;
use App\Utils\Phone\PhoneModelRU;

interface  IRequest
{
    public function getMethod(): Methods;

    public function getPayload(): array;

    public function getHeaders(): array;

    public function getHost(): string;

    public function getUrl(): string;

    public function isValid():bool;
    public function isSkip():bool;

    public function getTimeout():int;
    public function getConnectTimeout():int;
    public function getReadTimeout():int;

    public function getResponse(IApiExternalResponse $IApiExternalResponse): IResponse;

    public function getListener(IResponse $IResponse): ?IListener;

    public function getSHA1(): string;

    public function getFormat():Formats;
}