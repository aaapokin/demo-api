<?php

namespace app\rmq\Publisher\Packages\Comc;

use app\dto\PhoneAndNameDTO;
use Ramsey\Uuid\Uuid;

class ComcBringFriendPublisherPackage extends ACOMCPublisherPackage
{
    protected string $routingKey = "sms";

    public function __construct(
        readonly private PhoneAndNameDTO $phoneAndNameDTO,
    ) {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        parent::setPayload();
        $this->addPayloadValue('data.template', 'pay_for_friend')
            ->addPayloadValue('data.params.0.communicationId', Uuid::uuid4())
            ->addPayloadValue('data.params.0.name', $this->phoneAndNameDTO->name)
            ->addPayloadValue('data.params.0.key', $this->phoneAndNameDTO->phoneRU->toDataBaseFormat())
            ->addPayloadValue(
                'data.params.0.linkToShort773',
                'https://carmoney.ru/dengi-za-chas-pod-zalog-pts/?utm_medium=mgm&utm_source=202010_mvp&utm_campaign='
            );
    }
}