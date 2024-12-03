<?php

namespace app\rmq\ConsumerPackages;

use app\helpers\Log\Log;
use PhpAmqpLib\Message\AMQPMessage;
use yii\helpers\ArrayHelper;

/**
 * Пакет содержит процесс парсинга/обновления/обогащения.
 * Должен вернуть соответсвующие сущности/данные для дальнейшей бизнес логики.
 * В большинстве случаев будет достаточно ключевой сущности.
 * */
abstract class APackage implements IPackage
{
    private bool $isValid = true;
    private bool $isSkip = false;

    protected array $payload = [];
    private string $exchange;
    private string $routingKey;

    protected string $body;


    function __construct(private readonly AMQPMessage $AMQPMessage)
    {
        try {
            $this->exchange = $this->AMQPMessage->getExchange();
            $this->routingKey = $this->AMQPMessage->getRoutingKey();
            $this->body = $this->AMQPMessage->getBody();
            try {
                $this->decodeBody();
            } catch (\Throwable $e) {
                throw EValidationError::errorByPackage($this, 'json_decode error '.$e->getMessage());
            }
            $this->validate();
            $this->setData();
        } catch (EValidationError $e) {
            Log::error('valid error',$this,$e,$AMQPMessage->getBody());
            $this->isValid = false;
        } catch (ESkip $e) {
            $this->isSkip = true;
        } catch (\Throwable $e){
            Log::error('valid error',$this,$e,$AMQPMessage->getBody());
            throw new \Exception($e->getMessage());
        }
    }

    protected function decodeBody()
    {
        $this->payload = json_decode($this->body, 1);
    }

    abstract protected function validate(): void;

    abstract protected function setData(): void;

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isSkip(): bool
    {
        return $this->isSkip;
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }


    protected function getByPath($key): mixed
    {
        return ArrayHelper::getValue($this->payload, $key);
    }


}