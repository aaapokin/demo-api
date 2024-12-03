<?php

namespace app\rmq\Publisher\Packages\Comc;

use app\rmq\Publisher\APublisherPackage;
use app\models\SmsVerify;
use Ramsey\Uuid\Uuid;

class ComcSmsPublisherPackage extends ACOMCPublisherPackage
{
    protected string $routingKey = "sms";


    public function __construct(
        readonly private SmsVerify $phoneSmsVerify
    )
    {
        parent::__construct();
    }


    protected function setPayload(): void
    {
        parent::setPayload();
        $this->addPayloadValue('data.template', 'SMS_ID_CONFIRM_CODE_SITE_1731')
            ->addPayloadValue('data.params.0.communicationId', $this->phoneSmsVerify->id)
            ->addPayloadValue('data.params.0.name', $this->phoneSmsVerify->phone)
            ->addPayloadValue('data.params.0.key', $this->phoneSmsVerify->phone)
            ->addPayloadValue('data.params.0.code', $this->phoneSmsVerify->code);
    }
}