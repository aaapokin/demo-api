<?php

namespace app\rmq\Publisher;

interface  IRMQPublisher
{
    public function send(IPublisherPackage $IPublisherPackage):void;
    public function sendViaQUEUE(IPublisherPackage $IPublisherPackage):void;
}