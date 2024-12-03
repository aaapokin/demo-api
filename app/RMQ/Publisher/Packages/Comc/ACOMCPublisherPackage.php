<?php

namespace app\rmq\Publisher\Packages\Comc;

use app\rmq\Publisher\APublisherPackage;
use app\models\SmsVerify;
use Ramsey\Uuid\Uuid;


abstract class ACOMCPublisherPackage extends APublisherPackage
{
    protected string $exchange = "COMC.Message.1.1";
    protected function setPayload(): void
    {
        $this->addPayloadValue('publisher', 'WWW')
            ->addPayloadValue('publishTime', time())
            ->addPayloadValue('guid', Uuid::uuid4()->toString())
            ->addPayloadValue('docUrl', 'https://wiki.carmoney.ru/x/pQo6Ag');
    }
}