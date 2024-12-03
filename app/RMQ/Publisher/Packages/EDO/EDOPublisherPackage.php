<?php

namespace app\rmq\Publisher\Packages\EDO;

use app\helpers\IP;
use app\rmq\ConsumerPackages\EValidationError;
use app\rmq\Publisher\APublisherPackage;
use app\models\FormLead;
use app\models\Request;
use app\models\SignatureDocument;
use app\models\SmsVerify;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use yii\helpers\ArrayHelper;

class EDOPublisherPackage extends APublisherPackage
{
    protected string $exchange = "WWW.RequestConfirmations";
    protected string $routingKey = "RequestConfirmations.Data.1.1";

    /**
     * @param SignatureDocument[]        $documents
     * @param \app\models\FormLead       $formLeadMerged
     * @param \app\models\SmsVerify|null $smsVerify
     */
    public function __construct(private array $documents, private FormLead $formLeadMerged, private ?SmsVerify $smsVerify = null)
    {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        if(!$request=Request::find()->where(['lead_id' => $this->formLeadMerged->lead_id])->one()){
            throw new EValidationError(
                'RMQPublisher EDOPublisherPackage. Not found REQUEST '
            );
        }

        $this->addPayloadValue('meta.guid', Uuid::uuid4()->toString())
            ->addPayloadValue('meta.time.publish', \time())
            ->addPayloadValue('meta.publisher.code', 'WWW');
        $this->addPayloadValue('meta.links.documentation.contract', 'https://wiki.carmoney.ru/x/V4EDB');
        $this->addPayloadValue('meta.links.documentation.jsonAPI', 'https://wiki.carmoney.ru/x/tUo6Ag');

        $this->addPayloadValue('data.type', 'printForms')
            ->addPayloadValue('data.id', null)
            ->addPayloadValue('data.attributes.smsCode', $this->smsVerify?->code)
            ->addPayloadValue('data.attributes.communicationId', $this->smsVerify?->id)
            ->addPayloadValue('data.attributes.phone', $this->smsVerify?->phone)
            ->addPayloadValue('data.attributes.userAgent', '')
            ->addPayloadValue('data.attributes.os', '')
            ->addPayloadValue('data.attributes.clientId', '')
            ->addPayloadValue('data.attributes.requestId', '')
            ->addPayloadValue('data.attributes.packageDoc', '')
            ->addPayloadValue('data.attributes.subPackage', '')
            ->addPayloadValue('data.attributes.login', $this->smsVerify?->phone)
            ->addPayloadValue('data.attributes.IP', IP::getRealIP());

        foreach ($this->documents as $document) {
            $this->mergePayload('data.attributes.forms', [
                [
                    "form_guid_type" => $document->form_id,
                    "form_name" => $document->title,
                ],
            ]);
        }

        $this->addPayloadValue('data.attributes.lastName', $this->formLeadMerged->last_name)
            ->addPayloadValue('data.attributes.firstName', $this->formLeadMerged->first_name)
            ->addPayloadValue('data.attributes.secondName', $this->formLeadMerged->middle_name)
            ->addPayloadValue('data.attributes.passportSerialNumber', $this->formLeadMerged->getSeriya())
            ->addPayloadValue('data.attributes.passportNumber', $this->formLeadMerged->getNumber())
            ->addPayloadValue('data.attributes.dateOfIssue', Carbon::parse($this->formLeadMerged->date_of_issue)->format('Y-m-d'))
            ->addPayloadValue('data.attributes.departmentCode', $this->formLeadMerged->department_code)
            ->addPayloadValue('data.attributes.placeOfIssue', $this->formLeadMerged->place_of_issue)
            ->addPayloadValue('data.attributes.residentialAddress', $this->formLeadMerged->residential_address)
            ->addPayloadValue('data.attributes.dateOfBirth', Carbon::parse($this->formLeadMerged->birth_date)->format('Y-m-d'))
            ->addPayloadValue('data.attributes.placeOfBirth', $this->formLeadMerged->place_of_birth)
            ->addPayloadValue('data.attributes.requestGuid', $request->id);

        $this->customValidate();
    }

    private function customValidate():void
    {
        $payload = $this->getPayload();
        foreach (
            [
                'data.attributes.lastName',
                'data.attributes.firstName',
                //                     'data.attributes.secondName',
                'data.attributes.passportSerialNumber',
                'data.attributes.passportNumber',
                'data.attributes.dateOfIssue',
                'data.attributes.departmentCode',
                'data.attributes.placeOfIssue',
                'data.attributes.residentialAddress',
                'data.attributes.dateOfBirth',
                'data.attributes.placeOfBirth',
                'data.attributes.requestGuid',
                'data.attributes.forms',
            ] as $key
        ) {
            if (!ArrayHelper::getValue($payload, $key)) {
                throw new EValidationError(
                    'RMQPublisher EDOPublisherPackage. Not found '.$key.'. SignatureVerification:'.$this->formLeadMerged->form_session_id
                );
            }
        }
    }
}