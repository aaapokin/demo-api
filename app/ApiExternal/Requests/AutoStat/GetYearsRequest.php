<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;

class GetYearsRequest extends AAutoStatRequest
{
    protected string $url = '/priceCalc/param';
    protected string $contract = '***';
    protected string $jsonAPI = '***';
    protected string $responseClass = ValuesResponse::class;

    public function __construct(
        readonly private string $model,
        readonly private string $mark
    ) {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        $this->addPayloadValue('paramHierarchy.name', 'Год выпуска')
            ->addPayloadValue('paramHierarchy.type', 'folder')

            ->addPayloadValue('paramHierarchy.inner.name', 'Годы выпуска')
            ->addPayloadValue('paramHierarchy.inner.type', 'param');



        $this->addPayloadValue('selectionParams.0.name', 'Марка-Модель')
            ->addPayloadValue('selectionParams.0.type', 'folder')

            ->addPayloadValue('selectionParams.0.inner.name', 'Марка')
            ->addPayloadValue('selectionParams.0.inner.type', 'paramFolder')
            ->addPayloadValue('selectionParams.0.inner.value', $this->mark)

            ->addPayloadValue('selectionParams.0.inner.inner.name', 'Модель')
            ->addPayloadValue('selectionParams.0.inner.inner.type', 'param')
            ->addPayloadValue('selectionParams.0.inner.inner.value', $this->model);
    }
}