<?php

namespace app\ApiExternal\Requests\AutoStat;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\AutoStat\PriceResponse;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;

class GetPriceRequest extends AAutoStatRequest
{
    protected string $url = '/priceCalc/measuresValues';
    protected string $contract = '***';
    protected string $jsonAPI = '***';
    protected string $responseClass = PriceResponse::class;

    public function __construct(
        readonly private string $model,
        readonly private string $mark,
        readonly private string $year,
        readonly private string $generation,
        readonly private string $modification,
        readonly private string $carBodyType,
        readonly private string $equippingLevel
    ) {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        $map = [
            'базовый' => 'Цена базовый уо',
            'максимальный' => 'Цена максимальный уо',
            'средний' => 'Цена средний уо',
        ];

        $this->addPayloadValue('params.0.sourceType', 'measure')
            ->addPayloadValue('params.0.description', $map[$this->equippingLevel] ?? 'Цена базовый уо');

        $this->addPayloadValue('params.1.sourceType', 'param')
            ->addPayloadValue('params.1.description.name', 'Марка-Модель')
            ->addPayloadValue('params.1.description.type', 'folder')
            ->addPayloadValue('params.1.description.inner.name', 'Марка')
            ->addPayloadValue('params.1.description.inner.type', 'paramFolder')
            ->addPayloadValue('params.1.description.inner.value', $this->mark)
            ->addPayloadValue('params.1.description.inner.inner.name', 'Модель')
            ->addPayloadValue('params.1.description.inner.inner.type', 'paramFolder')
            ->addPayloadValue('params.1.description.inner.inner.value', $this->model)
            ->addPayloadValue('params.1.description.inner.inner.inner.name', 'Поколение')
            ->addPayloadValue('params.1.description.inner.inner.inner.type', 'paramFolder')
            ->addPayloadValue('params.1.description.inner.inner.inner.value', $this->generation)
            ->addPayloadValue('params.1.description.inner.inner.inner.inner.name', 'Модификация')
            ->addPayloadValue('params.1.description.inner.inner.inner.inner.type', 'param')
            ->addPayloadValue('params.1.description.inner.inner.inner.inner.value', $this->modification);

        $this->addPayloadValue('params.2.sourceType', 'param')
            ->addPayloadValue('params.2.description.name', 'Год выпуска')
            ->addPayloadValue('params.2.description.type', 'folder')
            ->addPayloadValue('params.2.description.inner.name', 'Годы выпуска')
            ->addPayloadValue('params.2.description.inner.type', 'param')
            ->addPayloadValue('params.2.description.inner.value', $this->year);

        $this->addPayloadValue('params.3.sourceType', 'param')
            ->addPayloadValue('params.3.description.name', 'Уровень оснащения')
            ->addPayloadValue('params.3.description.type', 'folder')
            ->addPayloadValue('params.3.description.inner.name', 'Имя уровня')
            ->addPayloadValue('params.3.description.inner.type', 'param')
            ->addPayloadValue('params.3.description.inner.value', $this->equippingLevel);

        $this->addPayloadValue('params.4.sourceType', 'param')
            ->addPayloadValue('params.4.description.name', 'Тип кузова')
            ->addPayloadValue('params.4.description.type', 'folder')
            ->addPayloadValue('params.4.description.inner.name', 'Типы кузова')
            ->addPayloadValue('params.4.description.inner.type', 'param')
            ->addPayloadValue('params.4.description.inner.value', $this->carBodyType);

    }
}