<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;

class GetModificationsRequest extends AAutoStatRequest
{
    protected string $url = '/priceCalc/param';
    protected string $contract = '***';
    protected string $jsonAPI = '***';
    protected string $responseClass = ValuesResponse::class;

    public function __construct(
        readonly private string $model,
        readonly private string $mark,
        readonly private string $year,
        readonly private string $generation
    ) {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        $this->addPayloadValue('paramHierarchy.name', 'Марка-Модель')
            ->addPayloadValue('paramHierarchy.type', 'folder')
            ->addPayloadValue('paramHierarchy.inner.name', 'Марка')
            ->addPayloadValue('paramHierarchy.inner.type', 'paramFolder')
            ->addPayloadValue('paramHierarchy.inner.value', $this->mark)

            ->addPayloadValue('paramHierarchy.inner.inner.name', 'Модель')
            ->addPayloadValue('paramHierarchy.inner.inner.type', 'paramFolder')
            ->addPayloadValue('paramHierarchy.inner.inner.value', $this->model)

            ->addPayloadValue('paramHierarchy.inner.inner.inner.name', 'Поколение')
            ->addPayloadValue('paramHierarchy.inner.inner.inner.type', 'paramFolder')
            ->addPayloadValue('paramHierarchy.inner.inner.inner.value', $this->generation)

            ->addPayloadValue('paramHierarchy.inner.inner.inner.inner.name', 'Модификация')
            ->addPayloadValue('paramHierarchy.inner.inner.inner.inner.type', 'param');

        $this->addPayloadValue('selectionParams.0.name', 'Год выпуска')
            ->addPayloadValue('selectionParams.0.type', 'folder')
            ->addPayloadValue('selectionParams.0.inner.name', 'Годы выпуска')
            ->addPayloadValue('selectionParams.0.inner.type', 'param')
            ->addPayloadValue('selectionParams.0.inner.value', $this->year);
    }
}