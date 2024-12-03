<?php

namespace app\ApiExternal;

use app\ApiExternal\Requests\IRequest;
use app\ApiExternal\Responses\IResponse;

interface IApiExternalResponse
{
    public function getHeaders(): array;

    public function getPayload(): array;

    public function setPayload(array $payload): self;

    public function getStatus(): int;

    public function getHeadersRequest(): array;

    public function getUrlRequest(): string;

    public function getPayloadRequest(): array;

    public function isOk(): bool;
    public function getErrorResponse(): string;

}