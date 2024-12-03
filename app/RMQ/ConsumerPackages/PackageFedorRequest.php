<?php

namespace app\rmq\ConsumerPackages;

use app\utils\CrmRequest\Number;
use app\models\Request;
use app\models\RequestStatus;
use app\models\ProductType;
use Carbon\Carbon;
use Str\Str;
use yii\helpers\ArrayHelper;

//https://wiki.carmoney.ru/pages/viewpage.action?pageId=32539900
class PackageFedorRequest extends APackage
{
    /**
     * @var Request[]
     */
    private array $crmRequests = [];
    private ?Request $crmRequest = null;
    private ?ProductType $productType = null;
    private ?RequestStatus $crmRequestStatus = null;

    protected function validate(): void
    {
    }

    protected function setData(): void
    {
        $flagSkip = false;
        foreach ($this->getDataItems('request') as $request) {
            $requestNumber = Str::make((string) ($request['number'] ?? null));
            //пропускаем номера заявок из 1с
            if (!Number::validate($requestNumber->getString())) {
                $flagSkip = true;
                continue;
            }
            $this->setCrmRequestStatus(ArrayHelper::getValue($request, 'status.guid'));
            $this->setProductType(ArrayHelper::getValue($request, 'productType.guid'));
            $this->setCrmRequest($request);
            $this->crmRequests[]=$this->crmRequest;
        }
        if (!$this->crmRequests && !$flagSkip) {
            EValidationError::notFound('request');
        }
    }

    private function setCrmRequest(array $request): void
    {
        $guid = $request['guid'];
        if (!$this->crmRequest = Request::findById($guid)) {
            $this->crmRequest = new Request();
            $this->crmRequest->setId($guid);
        }
        $this->crmRequest->number = $request["number"] ?? null;
        $this->crmRequest->lead_id = ArrayHelper::getValue($request, 'lead.guid');
        $this->crmRequest->requested_sum = $request["requestedSum"] ?? null;
        $this->crmRequest->approved_sum = $request["approvedSum"] ?? null;
        $this->crmRequest->sum_contract = $request["sumContract"] ?? null;
        $this->crmRequest->client_sum = $request["clientSum"] ?? null;
        $this->crmRequest->recomended_sum = $request["recomendedSum"] ?? null;
        if ($dateReturn = $request["dateReturn"] ?? null) {
            $this->crmRequest->date_return = Carbon::parse($dateReturn);
        }
        $this->crmRequest->client_days = $request["clientDays"] ?? null;
        $this->crmRequest->days = $request["days"] ?? null;
        $this->crmRequest->save();
        if($this->productType) $this->crmRequest->link('productType',$this->productType);
        if($this->crmRequestStatus) $this->crmRequest->link('requestStatus',$this->crmRequestStatus);
    }

    private function setCrmRequestStatus(?string $guid): void
    {
        if (!$guid || !($status = $this->getDataItem('requestStatus', $guid))) {
            return;
        }
        if (!$this->crmRequestStatus = RequestStatus::findById($guid)) {
            $this->crmRequestStatus = new RequestStatus();
            $this->crmRequestStatus->setId($guid);
        }
        $this->crmRequestStatus->code = $status['code'] ?? '';
        $this->crmRequestStatus->description = $status['description'] ?? '';
        $this->crmRequestStatus->save();
    }

    private function setProductType(?string $guid): void
    {
        if (!$guid || !($productType = $this->getDataItem('productTypes', $guid))) {
            return;
        }
        if (!$this->productType = ProductType::findById($guid)) {
            $this->productType = new ProductType();
            $this->productType->setId($guid);
            $this->productType->name = $productType['name'] ?? '';
            $this->productType->code = $productType['code'] ?? '';
            $this->productType->is_need_pts = $productType['isNeedPts'] ?? null;
            $this->productType->min_sum = $productType['minSum'] ?? null;
            $this->productType->max_sum = $productType['maxSum'] ?? null;
            $this->productType->save();
        }
    }

    /**
     * @return \app\models\Request[]
     */
    public function CrmRequests(): array
    {
        return $this->crmRequests;
    }

    private function getDataItems(string $dataType): array
    {
        $returnArr = [];
        foreach ($this->getByPath('data') as $item) {
            if (ArrayHelper::getValue($item, 'type') == $dataType
                && ($tmp = ArrayHelper::getValue($item, 'data'))) {
                $returnArr[] = $tmp;
            }
        }
        return $returnArr;
    }

    private function getDataItem(string $dataType, string $guid): array
    {
        foreach ($this->getByPath('data') as $item) {
            if (ArrayHelper::getValue($item, 'type') == $dataType
                && ArrayHelper::getValue($item, 'data.guid') == $guid) {
                return ArrayHelper::getValue($item, 'data');
            }
        }
        return [];
    }
}