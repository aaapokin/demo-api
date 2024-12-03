<?php

namespace app\rmq\Publisher\Packages\Comc;

use app\rmq\Publisher\APublisherPackage;
use app\models\SignatureDocument;
use app\models\SmsVerify;
use Ramsey\Uuid\Uuid;

class ComcSignaturePublisherPackage extends ACOMCPublisherPackage
{
    protected string $routingKey = "sms";


    /** @param SignatureDocument[] $documents */
    public function __construct(
        readonly private SmsVerify $phoneSmsVerify,
        readonly private array     $documents
    )
    {
        parent::__construct();
    }


    protected function setPayload(): void
    {
        parent::setPayload();
        $links = [];
        foreach ($this->documents as $document) $links[$document->link] = $document->link;

        $this->addPayloadValue('data.template', 'SMS_ID_SIGN_CONSENT_SITE_2688')
            ->addPayloadValue('data.params.0.communicationId', $this->phoneSmsVerify->id)
            ->addPayloadValue('data.params.0.name', $this->phoneSmsVerify->phone)
            ->addPayloadValue('data.params.0.key', $this->phoneSmsVerify->phone)
            ->addPayloadValue('data.params.0.code', $this->phoneSmsVerify->code);

        foreach ($links as $link) {
            $this->addPayloadValue('data.params.0.docLinks', [$link]);
        }
    }
}