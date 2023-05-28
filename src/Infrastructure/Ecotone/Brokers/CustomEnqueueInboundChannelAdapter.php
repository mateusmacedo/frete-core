<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers;

use Ecotone\Messaging\Endpoint\PollingConsumer\ConnectionException;
use Ecotone\Messaging\Message;
use Exception;
use Interop\Queue\Message as EnqueueMessage;
use Ecotone\Enqueue\EnqueueInboundChannelAdapter;

abstract class CustomEnqueueInboundChannelAdapter extends EnqueueInboundChannelAdapter
{
    private bool $initialized = false;

    protected function getMessageParamsConsume()
    {
        return true;
    }

    public function receiveMessage(int $timeout = 0): ?Message
    {
        try {

            if ($this->declareOnStartup && $this->initialized === false) {
                $this->initialize();

                $this->initialized = true;
            }

            $consumer = $this->connectionFactory->getConsumer(
                $this->connectionFactory->createContext()->createQueue($this->queueName)
            );

            /** @var bool|array $consumableParamsMessage*/
            $consumableParamsMessage = $this->getMessageParamsConsume();
            if (!$consumableParamsMessage) {
                return null;
            }

            if (is_array($consumableParamsMessage)) {
                $consumer->getQueue()->setPartition($consumableParamsMessage['partition']);
                $consumer->setOffset($consumableParamsMessage['offset']);
            }

            /** @var EnqueueMessage $message */
            $message = $consumer->receive($timeout ?: $this->receiveTimeoutInMilliseconds);
            if (!$message) {
                return null;
            }

            $convertedMessage = $this->inboundMessageConverter->toMessage($message, $consumer);
            $convertedMessage = $this->enrichMessage($message, $convertedMessage);

            return $convertedMessage->build();
        } catch (Exception $exception) {
            if ($this->isConnectionException($exception) || ($exception->getPrevious() && $this->isConnectionException($exception->getPrevious()))) {
                throw new ConnectionException('There was a problem while polling message channel', 0, $exception);
            }

            throw $exception;
        }
    }

    private function isConnectionException(Exception $exception): bool
    {
        return is_subclass_of($exception, $this->connectionException()) || $exception::class === $this->connectionException();
    }
}