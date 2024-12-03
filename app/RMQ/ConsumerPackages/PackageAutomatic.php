<?php

namespace app\rmq\ConsumerPackages;

use app\base\models\ActiveRecordModel;
use app\helpers\Log\Log;
use PhpAmqpLib\Message\AMQPMessage;
use tuyakhov\jsonapi\JsonApiParser;
use tuyakhov\jsonapi\ResourceInterface;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Пакет содержит процесс парсинга/обновления/обогащения.
 * Должен вернуть соответсвующие сущности/данные для дальнейшей бизнес логики.
 * В большинстве случаев будет достаточно ключевой сущности.
 * */
class PackageAutomatic extends APackage
{
    private array $config;
    private array $configModel;
    private array $jsonApiPayload;
    private JsonApiParser $jsonApiParser;
    private array $currentDataItem = [];

    function __construct(AMQPMessage $AMQPMessage)
    {
        $this->jsonApiParser = new JsonApiParser();
        $this->config = require \Yii::getAlias('@app/config/jsonapi/models.php');
        parent::__construct($AMQPMessage);
    }

    protected function decodeBody(): void
    {
        $this->payload = json_decode($this->body, 1);
        $this->jsonApiPayload = $this->jsonApiParser->parse($this->body, 'application/vnd.api+json');
    }

    protected function validate(): void
    {
    }

    protected function setData(): void
    {
        if ($this->getByPath('data.type')) {
            $this->currentDataItem = $this->getByPath('data');
            $this->processingCurrentDataItem();
        } elseif ($this->getByPath('data.0.type')) {
            foreach ($this->getByPath('data') as $item) {
                $this->currentDataItem = $item;
                $this->processingCurrentDataItem();
            }
        }
    }

    private function processingCurrentDataItem(): void
    {
        $transaction = null;
        try {
            if (!$model = $this->getModel($this->getByPathInCurrentDataItem('type'))) {
                throw new \Exception('class model not found');
            }
            $modelDb = $model::getDb();
            if ($this->getByPathInCurrentDataItem('relationships') && $modelDb) {
                $transaction = $modelDb->beginTransaction();
            }
            $this->setAutomaticData($model);
            if ($transaction) {
                $transaction->commit();
            }
        } catch (\Throwable $e) {
            if ($transaction) {
                $transaction->rollBack();
            }
            Log::error('setAutomaticData error', $this, $e, $this->getBody());
            throw new \Exception('setAutomaticData error');
        }
    }

    private function setAutomaticData(ActiveRecordModel $model): void
    {
        $model->load($this->convertFields($this->getFieldsByData($this->currentDataItem), $model),'');
        $model = $model->createOrUpdate();
        if (!$model->validate()) {
            Log::error(
                [
                    'msg' => 'validation error ',
                    'validation error' => json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ], $this, null, $this->getBody()
            );
            throw new \Exception('validation error');
        }
        $model->save();


        foreach ($this->getIncludedWithRelationship() as $item) {
            if (!$modelTMP = $this->getModel(ArrayHelper::getValue($item, 'type'))) {
                continue;
            }
            $modelTMP->load($this->convertFields($this->getFieldsByData($item), $modelTMP),'');
            $modelTMP = $modelTMP->createOrUpdate();
            if (!$modelTMP->validate()) {
                Log::error(
                    [
                        'msg' => 'validation error ',
                        'validation error' => json_encode($modelTMP->getErrors(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ], $this, null, $this->getBody()
                );
                throw new \Exception('validation error');
            }
            $modelTMP->save();
        }

        if ($this->getByPathInCurrentDataItem('relationships')) {
            $this->linkRelationships($model);
        }
    }

    private function getModel(string $dataType = ''): ActiveRecordModel|null
    {
        if(!$class=$this->getModelClass($dataType)) return null;

        /** @var ActiveRecordModel $model */
        $model = new $class();
        if (!($model instanceof ActiveRecordModel)) {
            return null;
        }
        return $model;
    }

    private function getModelClass(string $dataType = ''): string|null
    {
        if (!$dataType) {
            return null;
        }
        if (!isset($this->config[$dataType]['model'])) {
            return null;
        }
        return $this->config[$dataType]['model'];
    }

    private function getConfigByModel(ActiveRecordModel $model): array
    {
        foreach ($this->config as $cfg) {
            if ($cfg['model'] == $model::class) {
                return $cfg;
            }
        }
        return [];
    }


    private function linkRelationships($model)
    {
        if (!$model instanceof ResourceInterface) {
            return;
        }

        foreach ($this->getRelationships() as $name => $models) {
            if (! $model->getRelation($name, false)) {
                continue;
            }

            foreach ($models as $class => $ids) {
                $records = $class::find()->andWhere(['in', $class::primaryKey(), $ids])->all();
                $model->setResourceRelationship($name, $records);
            }

            //            if ($related->multiple && !$this->allowFullReplacement) {
            //                continue;
            //            }

            //            /** @see ResourceTrait::$allowDeletingResources */
            //            if (method_exists($model, 'setAllowDeletingResources')) {
            //                $model->setAllowDeletingResources($this->enableResourceDeleting);
            //            }
        }
    }

    private function convertFields(array $data, ActiveRecordModel $model): array
    {
        $cfgArr = $this->getConfigByModel($model);
        $cfgArrFields = $cfgArr['fields'];
        foreach ($data as  $name => $value) {
                if (isset($cfgArrFields[$name])) {
                    $data[$cfgArrFields[$name]] = $value;
                    unset($data[$name]);
                }
        }
        return $data;
    }

    private function getFieldsByData (array $data)
    {
        $field=['id'=>ArrayHelper::getValue($data, 'id')];
        if(ArrayHelper::getValue($data, 'attributes')){
            $field=array_merge($field,ArrayHelper::getValue($data, 'attributes'));
        }
        return $field;
    }

    private function getByPathInCurrentDataItem($key): mixed
    {
        return ArrayHelper::getValue($this->currentDataItem, $key);
    }

    private function getRelationships(): array
    {
        $relationships=[];
        foreach ($this->getByPathInCurrentDataItem('relationships') as $relationName=>$relation){
            $dataItems=[];
            if(ArrayHelper::getValue($relation, 'data.0')){
                foreach (ArrayHelper::getValue($relation, 'data') as $data){
                    $dataItems[$this->getModelClass(ArrayHelper::getValue($relation, 'type'))][]=ArrayHelper::getValue($relation, 'id');
                }
            }else{
                $dataItems[$this->getModelClass(ArrayHelper::getValue($relation, 'data.type'))][]=ArrayHelper::getValue($relation, 'data.id');
            }
            $relationships[$relationName]=$dataItems;
        }

        return $relationships;
    }

    private function getIncludedWithRelationship(): array
    {
        if(!$this->getByPathInCurrentDataItem('relationships')) return [];
        $returnArray = [];
        foreach ($this->getByPathInCurrentDataItem('relationships') as $relationship) {
            if ($dataType = ArrayHelper::getValue($relationship, 'data.type')) {
                $returnArray = ArrayHelper::merge($returnArray, $this->getIncludedByDataType($dataType));
                continue;
            }
            if ($data = ArrayHelper::getValue($relationship, 'data'))
                foreach ($data as $dataItem) {
                    if ($dataType = ArrayHelper::getValue($dataItem, 'type')) {
                        $returnArray = ArrayHelper::merge($returnArray, $this->getIncludedByDataType($dataType));
                    }
                }
        }
        return $returnArray;
    }

    private function getIncludedByDataType(string $dataType): array
    {
        $returnArray = [];
        foreach ((array)$this->getByPath('included') as $item) {
            if (ArrayHelper::getValue($item, 'type') == $dataType)
                $returnArray[] = $item;
        }
        return $returnArray;
    }
}