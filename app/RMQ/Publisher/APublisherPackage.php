<?php

namespace app\rmq\Publisher;

use app\helpers\Log\Log;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use yii\helpers\ArrayHelper;

abstract class APublisherPackage implements IPublisherPackage
{
    protected string $exchange = "***.***";
    protected string $routingKey = "***.***";
    protected array $payload = [];
    private bool $isSkip = false;
    private bool $isValid = true;
    private string $validErrorText = '';

    public function __construct()
    {
        try {
            $this->validate();
            $this->setPayload();
        } catch (ESkip $e) {
            $this->isSkip = true;
        } catch (EValidationError $e) {
            Log::error('error valid', $this, $e, $this->payload);
            $this->validErrorText = $e->getMessage();
            $this->isValid = false;
        }
    }

    protected function validate(): void
    {
    }

    protected function setPayload(): void
    {
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function isSkip(): bool
    {
        return $this->isSkip;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getValidErrorText(): string
    {
        return $this->validErrorText;
    }

    protected function addPayloadValue(string $key, mixed $value): self
    {
        ArrayHelper::setValue($this->payload, $key, $value);
        return $this;
    }

    protected function mergePayload(string $path, array $value = []): self
    {
        if (!$old = ArrayHelper::getValue($this->payload, $path)) {
            $old = [];
        }
        ArrayHelper::setValue($this->payload, $path, array_merge($old, $value));
        return $this;
    }

    protected function checkNullByKeys(array $keys): bool|string
    {
        foreach ($keys as $key) {
            if (is_null(ArrayHelper::getValue($this->payload, $key))) {
                return $key;
            }
        }
        return false;
    }
}