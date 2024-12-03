<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\Listeners\IListener;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;
use app\ApiExternal\Responses\IApiExternalResponse;
use app\ApiExternal\Responses\IResponse;

class GetMarksRequest extends AAutoStatRequest
{
    protected string $url = '/priceCalc/param';
    protected string $contract = '***';
    protected string $jsonAPI = '***';

    protected function setPayload(): void
    {
        $this->addPayloadValue('paramHierarchy.name','Марка-Модель')
            ->addPayloadValue('paramHierarchy.type','folder')
            ->addPayloadValue('paramHierarchy.inner.name','Марка')
            ->addPayloadValue('paramHierarchy.inner.type','param');
    }

    public function getResponse(IApiExternalResponse $IApiExternalResponse): ValuesResponse
    {
        return new ValuesResponse($IApiExternalResponse);
    }

    public function getListener(IResponse $IResponse): ?IListener
    {
        return null;
    }
}