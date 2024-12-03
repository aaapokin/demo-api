<?php

namespace app\ApiExternal\Requests;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Responses\AResponse;
use app\helpers\Log\Log;
use yii\helpers\ArrayHelper;

abstract class  ARequest implements IRequest
{
    protected string $contract = 'https://wiki.carmoney.ru/x/wZeDAw';
    protected string $jsonAPI = 'https://wiki.carmoney.ru/x/tUo6Ag';

    protected array $headers = [];
    protected Methods $method = Methods::GET;
    protected Formats $format = Formats::json;
    protected string $host = 'https://***.carmoney.ru';
    protected string $url = '/test';
    protected array $payload = [];
    protected int $timeout = 60;
    protected int $connectionTimeout = 60;
    protected int $readyTimeout = 60;
    private bool $isValid = true;
    private bool $isSkip = false;

    public function __construct()
    {
        try {
            $this->validate();
            $this->setAuth();
            $this->setPayload();
        } catch (ERequestSkip $e) {
            $this->isSkip = true;
        } catch (ERequestValidate $e) {
            Log::error('request validate error', $this, $e);
            $this->isValid = false;
        }

    }

    protected function validate(): void
    {

    }

    abstract protected function setPayload(): void;

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getMethod(): Methods
    {
        return $this->method;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeaders(): array
    {
        if (!isset($this->headers['content-type']) && $this->getFormat() == Formats::json)
            $this->addHeader('content-type', 'application/json');
        if (!isset($this->headers['content-type']) && $this->getFormat() == Formats::form)
            $this->addHeader('content-type', 'application/x-www-form-urlencoded');
        return $this->headers;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isSkip(): bool
    {
        return $this->isSkip;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectionTimeout;
    }

    public function getReadTimeout(): int
    {
        return $this->readyTimeout;
    }

    public function getFormat(): Formats
    {
        return $this->format;
    }

    protected function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    protected function addPayloadValue(string $key, mixed $value): self
    {
        ArrayHelper::setValue($this->payload, $key, $value);
        return $this;
    }

    protected function setAuth(): void
    {

    }

    public function getSHA1():string
    {
        return sha1(json_encode($this->getPayload()));
    }


}