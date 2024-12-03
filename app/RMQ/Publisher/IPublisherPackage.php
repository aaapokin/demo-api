<?php

namespace app\rmq\Publisher;

interface IPublisherPackage
{
    public function getExchange(): string;

    public function getRoutingKey(): string;

    public function getPayload(): array;

    public function isValid(): bool;
    public function getValidErrorText(): string;

    public function isSkip(): bool;
}