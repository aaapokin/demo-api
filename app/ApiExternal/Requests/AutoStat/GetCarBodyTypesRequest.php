<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;

class GetCarBodyTypesRequest extends AAutoStatRequest
{
    protected string $url = '/priceCalc/param';
    protected string $contract = '***';
    protected string $jsonAPI = '***';
    protected string $responseClass = ValuesResponse::class;

    public function __construct(
        readonly private string $model,
        readonly private string $mark,
        readonly private string $year,
        readonly private string $generation,
        readonly private string $modification
    ) {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        $this->addPayloadValue('paramHierarchy.name', 'Тип кузова')
            ->addPayloadValue('paramHierarchy.type', 'folder')
            ->addPayloadValue('paramHierarchy.inner.name', 'Типы кузова')
            ->addPayloadValue('paramHierarchy.inner.type', 'param');



        $this->addPayloadValue('selectionParams.0.name', 'Марка-Модель')
            ->addPayloadValue('selectionParams.0.type', 'folder')

            ->addPayloadValue('selectionParams.0.inner.name', 'Марка')
            ->addPayloadValue('selectionParams.0.inner.type', 'paramFolder')
            ->addPayloadValue('selectionParams.0.inner.value', $this->mark)

            ->addPayloadValue('selectionParams.0.inner.inner.name', 'Модель')
            ->addPayloadValue('selectionParams.0.inner.inner.type', 'paramFolder')
            ->addPayloadValue('selectionParams.0.inner.inner.value', $this->model)

            ->addPayloadValue('selectionParams.0.inner.inner.inner.name', 'Поколение')
            ->addPayloadValue('selectionParams.0.inner.inner.inner.type', 'paramFolder')
            ->addPayloadValue('selectionParams.0.inner.inner.inner.value', $this->generation)

            ->addPayloadValue('selectionParams.0.inner.inner.inner.inner.name', 'Модификация')
            ->addPayloadValue('selectionParams.0.inner.inner.inner.inner.type', 'param')
            ->addPayloadValue('selectionParams.0.inner.inner.inner.inner.value', $this->modification);



        $this->addPayloadValue('selectionParams.1.name', 'Год выпуска')
            ->addPayloadValue('selectionParams.1.type', 'folder')
            ->addPayloadValue('selectionParams.1.inner.name', 'Годы выпуска')
            ->addPayloadValue('selectionParams.1.inner.type', 'param')
            ->addPayloadValue('selectionParams.1.inner.value', $this->year);
    }
}