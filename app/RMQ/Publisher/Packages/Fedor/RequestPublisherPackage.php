<?php

namespace app\rmq\Publisher\Packages\Fedor;

use app\rmq\Publisher\APublisherPackage;
use app\rmq\Publisher\EValidationError;
use app\models\FormLead;
use app\models\Request;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class RequestPublisherPackage extends APublisherPackage
{
    protected string $exchange = "WWW";
    protected string $routingKey = "WWW.requests.outgoing";
    private string $MOBILE_APPLICATION_POINT_ID_FOR_FEDOR = 'DB98FE88-1F5B-4711-8135-21A3DABA1B00';
    private string $REFINANCING_POINT_ID_FOR_FEDOR = 'c7d61a32-84b0-11ea-a2ee-00505683924b';

    public function __construct(
        readonly private FormLead $formLead
    ) {
        parent::__construct();
    }

    protected function setPayload(): void
    {
        if(!$request=Request::find()->where(['lead_id' => $this->formLead->lead_id])->one()){
            $request=new Request();
            $request->lead_id=$this->formLead->lead_id;
            $request->save();
        }


        $this->addPayloadValue('meta.guid', $request->id)
            ->addPayloadValue('meta.time.publish', \time())
            ->addPayloadValue('meta.publisher.code', 'WWW');
        $this->addPayloadValue('meta.links.documentation.contract', 'https://wiki.carmoney.ru/x/AaUuB');
        $this->addPayloadValue('meta.links.documentation.jsonAPI', 'https://wiki.carmoney.ru/x/tUo6Ag');

        $this->addPayloadValue('data.type', 'request')
            ->addPayloadValue('data.id', $this->formLead->form_session_id)
            ->addPayloadValue('data.attributes.phone', $this->formLead->phone)
            ->addPayloadValue('data.attributes.required_sum', (int)$this->formLead->required_sum)
            ->addPayloadValue('data.attributes.required_month_count', (int)$this->formLead->required_month_count)
            ->addPayloadValue('data.attributes.days', (int)$this->formLead->days)
            ->addPayloadValue('data.attributes.point_id', $this->getPointId())
            ->addPayloadValue('data.attributes.car_brand_id', $this->formLead->car_brand)
            ->addPayloadValue('data.attributes.car_model_id', $this->formLead->car_model)
            ->addPayloadValue('data.attributes.car_issue_year', (int)$this->formLead->car_issue_year)
            ->addPayloadValue('data.attributes.car_cost', (int)$this->formLead->car_cost)
            ->addPayloadValue('data.attributes.car_reg_number', null)
            ->addPayloadValue('data.attributes.clt_name_first', $this->formLead->first_name)
            ->addPayloadValue('data.attributes.clt_name_last', $this->formLead->last_name)
            ->addPayloadValue('data.attributes.clt_name_middle', $this->formLead->middle_name)
            ->addPayloadValue('data.attributes.clt_email', $this->formLead->email)
            ->addPayloadValue('data.attributes.clt_passport', $this->formLead->passport_id)
            ->addPayloadValue('data.attributes.clt_birthdate', Carbon::parse($this->formLead->birth_date)->format('Y-m-d'))
            ->addPayloadValue('data.attributes.clt_avg_income', (int)$this->formLead->avg_income)
            ->addPayloadValue('data.attributes.region_registration_id', $this->formLead->registration_region)
            ->addPayloadValue('data.attributes.external_id', $this->formLead->lead_id)//???
            ->addPayloadValue('data.attributes.region_fact_id', $this->formLead->region)
            ->addPayloadValue('data.attributes.is_new_process', true)
            ->addPayloadValue('data.attributes.source', 'WWW')
            ->addPayloadValue('data.attributes.passport_issue_date', Carbon::parse($this->formLead->date_of_issue)->format('Y-m-d'))
            ->addPayloadValue('data.attributes.passport_issuer_code', $this->formLead->department_code)
            ->addPayloadValue('data.attributes.passport_issuer', $this->formLead->place_of_issue)
            ->addPayloadValue('data.attributes.address_registration', $this->formLead->residential_address)
            ->addPayloadValue('data.attributes.place_of_birth', $this->formLead->place_of_birth);

        $this->addPayloadValue('data.relationships.lead.data.type', 'lead')
            ->addPayloadValue('data.relationships.lead.data.id', $this->formLead->lead_id);


        if($res=$this->checkNullByKeys([
            'data.attributes.point_id',
            'data.attributes.required_sum',
            'data.attributes.required_month_count',
            'data.attributes.days',
            'data.attributes.car_brand_id',
            'data.attributes.car_model_id',
            'data.attributes.car_issue_year',
            'data.attributes.clt_name_first',
            'data.attributes.clt_name_last',
            'data.attributes.clt_passport',
            'data.attributes.passport_issue_date',
            'data.attributes.passport_issuer_code',
            'data.attributes.passport_issuer',
            'data.attributes.address_registration',
            'data.attributes.clt_birthdate',
            'data.attributes.place_of_birth',
            'data.attributes.region_registration_id',
            'data.attributes.clt_avg_income',
            'data.attributes.region_fact_id',
            'data.relationships.lead.data.id',
        ])){
            throw new EValidationError('Fedor '.$res . ' is required');
        }
    }


    private function getPointId():string
    {
        if($this->formLead->lead_type==='site3_refinance'){
            return $this->REFINANCING_POINT_ID_FOR_FEDOR;
        }
        return $this->MOBILE_APPLICATION_POINT_ID_FOR_FEDOR;
    }
}