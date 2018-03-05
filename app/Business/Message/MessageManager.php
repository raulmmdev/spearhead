<?php

namespace App\Business\Message;

use \App\Business\BusinessLog\BusinessLogManager;
use \App\Model\Document\BusinessLog;
use \App\Business\Job\JobFactory;

/**
 * MessageManager
 */
class MessageManager
{
    /**
     * Object constructor
     *
     * @access public
     * @param JobFactory         $jobFactory
     * @param BusinessLogManager $businessLogManager
     */
    public function __construct(JobFactory $jobFactory, BusinessLogManager $businessLogManager)
    {
        $this->jobFactory = $jobFactory;
        $this->businessLogManager = $businessLogManager;
    }

    /**
     * Produces a messages into the message queue system
     *
     * @access public
     * @param  string $queue
     * @param  array  $values
     * @return bool
     */
    public function produceJobMessage(string $queue, array $values) : bool
    {
        try {
            //@TODO we need to wrap these AMQP calls into a QueueHandler
            //so we decouple the vendor from the source code
            \Amqp::publish('', json_encode($values), [
                'queue' => $queue
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            return false;
        }
    }

    /**
     * Consume messages from the message queue system
     *
     * @access public
     * @param  string $queue
     * @param  bool   $asDaemon
     * @return void
     */
    public function consumeJobMessage(string $queue, bool $asDaemon) : void
    {
        \Amqp::consume($queue, function ($message, $resolver) use ($queue, $asDaemon) {
            //unserialize
            $values = json_decode($message->body, true);

            $job = $this->jobFactory->create($queue, $values);

            $object = $job->resolve();

            //log accordingly
            $this->reportToBusinessLogger($queue, $values, $object);

            //next message
            $resolver->acknowledge($message);

            //stop if no more messages to process
            if ($asDaemon === false) {
                $resolver->stopWhenProcessed();
            }
        });
    }

    /**
     * Produces a BusinessLog message
     *
     * @access private
     * @param  string $queue
     * @param  array  $values
     * @param  object $object (Optional. Default: null)
     * @return void
     */
    private function reportToBusinessLogger(string $queue, array $values, $object = null) : void
    {
        $console = new \Symfony\Component\Console\Output\ConsoleOutput();

        $message = $values['name'];

        $strpadQueue = str_pad($queue, 8, ' ', STR_PAD_RIGHT);
        $strpadMessage = str_pad($message, 60, ' ', STR_PAD_RIGHT);

        if (!is_null($object)) {
            $businessLogType = BusinessLog::LEVEL_TYPE_INFO;

            $messageTitle = "Successfully processed the message [ {$message} ] from queue [ {$queue} ] ";
            $messageTitleConsole = "Successfully processed the message [ {$strpadMessage} ] from queue [ {$strpadQueue} ]";

            $messageBody = json_encode([
                'request' => $values,
                'response' => $object,
            ]);

            $console->writeln("<info>{$messageTitleConsole}</info>");
        } else {
            $businessLogType = BusinessLog::LEVEL_TYPE_ERROR;

            $messageTitle = "Error processing the message [ {$message} ] from queue [ {$queue} ]";
            $messageTitleConsole = "Error processing the message [ {$strpadMessage} ] from queue [ {$strpadQueue} ]";

            $messageBody = json_encode([
                'request' => $values,
                'errors' => $request->getErrors(),
            ]);

            $console->writeln("<error>{$messageTitleConsole}</error>");
        }

        $this->businessLogManager->log(
            $businessLogType,
            BusinessLog::USER_TYPE_MERCHANT,
            BusinessLog::BUSINESS_LOG_ELEMENT_TYPES[$queue],
            $messageTitle,
            $messageBody
        );
    }
}
