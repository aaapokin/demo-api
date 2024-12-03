<?php

namespace app\ApiExternal;

use app\ApiExternal\IApiExternalResponse;
use app\ApiExternal\Requests\IRequest;
use app\ApiExternal\Responses\IResponse;

class ApiExternalResponse implements IApiExternalResponse
{
    public function __construct(
        readonly private int    $status,
        readonly private array  $headers,
        private array           $payload,
        readonly private array  $headersRequest,
        readonly private string $urlRequest,
        readonly private array  $payloadRequest,
        readonly private string  $errorResponse = "",

    )
    {

    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function getHeadersRequest(): array
    {
        return $this->headersRequest;
    }

    public function getUrlRequest(): string
    {
        return $this->urlRequest;
    }

    public function getPayloadRequest(): array
    {
        return $this->payloadRequest;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrorResponse(): string
    {
        return $this->errorResponse;
    }
}
