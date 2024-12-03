<?php

namespace app\rmq\Consumers;


use app\rmq\ConsumerPackages\APackage;
use app\rmq\ConsumerPackages\PackageAutomatic;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerAutomatic extends AConsumer
{
    protected function run(PackageAutomatic|APackage $packageTest): void
    {
        // TODO: Implement run() method.
    }

    protected function getPackage(AMQPMessage $AMQPMessage): PackageAutomatic
    {
        return new PackageAutomatic($AMQPMessage);
    }
}