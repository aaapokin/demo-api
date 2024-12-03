<?php

namespace app\rmq\ConsumerPackages;

interface IPackage
{
    public function isValid(): bool;

    public function isSkip(): bool;

    public function getExchange(): string;

    public function getBody(): string;

    public function getRoutingKey(): string;

    public function getPayload(): array;
}