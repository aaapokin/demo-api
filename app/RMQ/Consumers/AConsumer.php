<?php

namespace app\rmq\Consumers;

use app\helpers\Log\Log;
use app\rmq\ConsumerPackages\APackage;
use app\Env;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        try {
            $package = $this->getPackage($msg);
            if ($package->isSkip()) throw new EConsumerSkip();
            if (!$package->isValid()) throw new EConsumerInvalidPackage();

            if(!Env::rmqConsumeActive()){
                sleep(10);
                throw new EConsumerRequeue();
            }

            $this->run($package);
            return ConsumerInterface::MSG_ACK;
        } catch (EConsumerAck $e) {
            return ConsumerInterface::MSG_ACK;
        } catch (EConsumerReject $e) {
            return ConsumerInterface::MSG_REJECT;
        } catch (EConsumerRequeue $e) {
            sleep(3);
            return ConsumerInterface::MSG_REQUEUE;
        } catch (EConsumerInvalidPackage $e) {
            Log::error('invalid package', $this, $e, $msg->getBody());
            return ConsumerInterface::MSG_ACK;
        } catch (EConsumerSkip $e) {
            return ConsumerInterface::MSG_ACK;
        } catch (\Throwable $e) {
            Log::error('', $this, $e, $msg->getBody());
            sleep(3);
            return ConsumerInterface::MSG_REQUEUE;
        }
    }

    /**
     * В пакете происходит подготовка/обновление/обогащение сущностей.
     * Полезная бизнес логика должна стартовать из этого метода, если есть.
     * Иначе оставить пустым.
     */
    abstract protected function run(APackage $package): void;

    abstract protected function getPackage(AMQPMessage $AMQPMessage): APackage;
}