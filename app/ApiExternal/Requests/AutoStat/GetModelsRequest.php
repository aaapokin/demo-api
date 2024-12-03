<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;

class GetModelsRequest extends AAutoStatRequest
{
    protected string $url = '/priceCalc/param';
    protected string $contract = '***';
    protected string $jsonAPI = '***';


    protected string $responseClass = ValuesResponse::class;

    public function __construct(
        readonly private string $mark
    )
    {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        $this->addPayloadValue('paramHierarchy.name','Марка-Модель')
            ->addPayloadValue('paramHierarchy.type','folder')
            ->addPayloadValue('paramHierarchy.inner.name','Марка')
            ->addPayloadValue('paramHierarchy.inner.type','paramFolder')
            ->addPayloadValue('paramHierarchy.inner.value',$this->mark)

            ->addPayloadValue('paramHierarchy.inner.inner.name','Модель')
            ->addPayloadValue('paramHierarchy.inner.inner.type','param')
            ->addPayloadValue('paramHierarchy.inner.inner.value',$this->mark);
    }
}