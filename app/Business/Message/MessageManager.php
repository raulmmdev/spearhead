<?php
namespace App\Business\Message;

use \App\Business\Api\Request\ApiRequest;

class MessageManager
{
    const QUEUES = [
       ApiRequest::MSG_CREATE_SITE => 'site',
    ];

	/**
     * Create a new message on the message queue.
     *
     * @return bool
     */
    public function produceJobMessage(string $type, string $body): bool
    {
        try {
            \Amqp::publish(
                '',
                $body,
                ['queue' => self::QUEUES[$type]]
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTrace());

            return false;
        }

        return true;
    }
}