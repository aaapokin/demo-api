<?php

namespace app\ApiExternal\Responses;

use app\ApiExternal\IApiExternalResponse;
use app\helpers\Log\Log;
use yii\helpers\ArrayHelper;

abstract class AResponse implements IResponse
{
    protected bool $isValid = true;
    protected bool $isSkip = false;

    final function __construct(readonly protected IApiExternalResponse $response)
    {
        try {
            $this->validate();
            $this->setData();
        } catch (EResponseSkip $e) {
            $this->isSkip = true;
        } catch (EResponseValidate $e) {
            Log::error('validate response error', $this, $e, $this->response->getPayload());
            $this->isValid = false;
        }

    }

    protected function validate(): void
    {

    }

    protected function setData(): void
    {

    }

    protected function getByPath(string $key): mixed
    {
        return ArrayHelper::getValue($this->response->getPayload(), $key);
    }

    protected function existByPath(string $key): bool
    {
        if ($this->getByPath($key) !== null) return true;
        return false;
    }

    protected function validateExistByPath(array $arrKey): void
    {
        foreach ($arrKey as $key) {
            if (!$this->existByPath($key))
                throw EResponseValidate::notFound($key);
        }

    }

    protected function getByIncludeTypeByPath(string $type, string $key): mixed
    {
        $payload = $this->response->getPayload();
        foreach ((array)ArrayHelper::getValue($payload, 'included') as $item)
            if (ArrayHelper::getValue($item, 'type') == $type)
                return ArrayHelper::getValue($item, $key);

        return null;
    }

    protected function existByIncludeTypeByPath(string $type, string $key): bool
    {
        $payload = $this->response->getPayload();
        foreach ((array)ArrayHelper::getValue($payload, 'included') as $item)
            if (ArrayHelper::getValue($item, 'type') == $type)
                return (ArrayHelper::getValue($item, $key) !== null) ? true : false;

        return false;
    }

    /** @example [['type'=>'***'],'key'=>'**.***'] */
    protected function validateExistByIncludeTypeByPath(array $arrKey): void
    {
        foreach ($arrKey as $item) {
            if (!$this->existByIncludeTypeByPath($item['type'], $item['key']))
                throw EResponseValidate::notFound($item['type'] . ':' . $item['key']);
        }

    }

    protected function generateResponseForChild(array $payload): ApiExternalResponse
    {
        $response = clone $this->response;
        $response->setPayload($payload);
        return $response;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isSkip(): bool
    {
        return $this->isSkip;
    }

    public function isOk(): bool
    {
        if (!$this->response->getStatus()===200){
            return true;
        }
        return false;
    }

    public function gerError(): string
    {
        return $this->response->getErrorResponse();
    }

    public function getStatus(): int
    {
        return $this->response->getStatus();
    }



}