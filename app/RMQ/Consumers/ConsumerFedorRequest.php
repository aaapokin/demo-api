<?php

namespace app\rmq\Consumers;


use app\rmq\ConsumerPackages\APackage;
use app\rmq\ConsumerPackages\PackageFedorRequest;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerFedorRequest extends AConsumer
{
    protected function run(PackageFedorRequest|APackage $package): void
    {
        // TODO: Implement run() method.
    }

    protected function getPackage(AMQPMessage $AMQPMessage): PackageFedorRequest
    {
        return new PackageFedorRequest($AMQPMessage);
    }
}