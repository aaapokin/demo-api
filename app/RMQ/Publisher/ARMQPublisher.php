<?php

namespace app\rmq\Publisher;

use app\jobs\SendRmqPackageJob;

abstract class ARMQPublisher implements IRMQPublisher
{
    public function send(IPublisherPackage $IPublisherPackage): void
    {
        $producer = \Yii::$app->rabbitmq->getProducer('www.producer');
        $msg = json_encode($IPublisherPackage->getPayload());
        $producer->publish($msg, $IPublisherPackage->getExchange(), $IPublisherPackage->getRoutingKey());
    }

    public function sendViaQUEUE(IPublisherPackage $IPublisherPackage): void
    {
        \Yii::$app->queue->push(new SendRmqPackageJob(['package' => $IPublisherPackage]));
    }
}