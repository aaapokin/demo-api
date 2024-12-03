<?php

namespace app\rmq\Publisher\Packages\Lk;

use app\helpers\IP;
use app\rmq\ConsumerPackages\EValidationError;
use app\rmq\Publisher\APublisherPackage;
use app\models\FormLead;
use app\models\SignatureDocument;
use app\models\SmsVerify;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use yii\helpers\ArrayHelper;

class AuthLinkPackage extends APublisherPackage
{
    protected string $exchange = "WWW.LeadLKK.1.1";
    protected string $routingKey = "WWW.Lead.Data.1.1";

    public function __construct(private FormLead $formLeadMerged)
    {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        $this->addPayloadValue('meta.guid', Uuid::uuid4()->toString())
            ->addPayloadValue('meta.time.publish', \time())
            ->addPayloadValue('meta.publisher.code', 'site');
        $this->addPayloadValue('meta.links.documentation.contract', 'https://wiki.carmoney.ru/x/ErYuB');
        $this->addPayloadValue('meta.links.documentation.jsonAPI', 'https://wiki.carmoney.ru/x/tUo6Ag');

        $this->addPayloadValue('data.type', 'clientLeadLKK')
            ->addPayloadValue('data.id', null)
            ->addPayloadValue('data.attributes.sum', (int)$this->formLeadMerged->required_sum)
            ->addPayloadValue('data.attributes.term', (int)$this->formLeadMerged->required_month_count)
            ->addPayloadValue('data.attributes.days', (int)$this->formLeadMerged->days)
            ->addPayloadValue('data.attributes.phone', $this->formLeadMerged->phone)
            ->addPayloadValue('data.attributes.leadId', $this->formLeadMerged->lead_id)
            ->addPayloadValue('data.attributes.leadsFlowClientId', $this->formLeadMerged->client_id)
            ->addPayloadValue('data.attributes.leadsFlowVisitId', $this->formLeadMerged->visit_id)
            ->addPayloadValue('data.attributes.productType', $this->formLeadMerged->product);
    }

}