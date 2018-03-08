<?php

namespace App\Business\Message;

use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Job\JobFactory;
use App\Model\Document\BusinessLog;
use Symfony\Component\Console\Output\ConsoleOutput;

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
    public function produceJobMessage(string $queue, array $values) :? string
    {
        try {
            // @TODO we need to wrap these AMQP calls into a QueueHandler
            // so we decouple the vendor from the source code
            $values['uuid'] = uniqid('', $moreEntropy = true);

            \Amqp::publish('', json_encode($values), [
                'queue' => $queue
            ]);

            return $values['uuid'];
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
            // unserialize
            $values = json_decode($message->body, true);

            $job = $this->jobFactory->create($queue, $values);
            $job = $job->resolve();

            // log accordingly
            $this->reportToBusinessLogger($queue, $values, $job);

            // print console output
            $this->printConsoleOutput($queue, $values, $job);

            // next message
            $resolver->acknowledge($message);

            // stop if no more messages to process
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
     * @param  object $job (Optional. Default: null)
     * @return void
     */
    private function reportToBusinessLogger(string $queue, array $values, $job = null) : void
    {
        $uuid = $values['uuid'];

        if ($job->hasErrors()) {
            $businessLogType = BusinessLog::LEVEL_TYPE_ERROR;
            $messageTitle = "Error processing the message [ {$uuid} ] from queue [ {$queue} ]";
            $messageBody = json_encode([
                'request' => $values,
                'errors' => $job->getErrors(),
            ]);
        } else {
            $businessLogType = BusinessLog::LEVEL_TYPE_INFO;
            $messageTitle = "Successfully processed the message [ {$uuid} ] from queue [ {$queue} ] ";
            $messageBody = json_encode([
                'request' => $values,
                'response' => $job->getObject(),
            ]);
        }

        $this->businessLogManager->log(
            $businessLogType,
            BusinessLog::USER_TYPE_MERCHANT,
            BusinessLog::BUSINESS_LOG_ELEMENT_TYPES[$queue],
            $messageTitle,
            $messageBody
        );
    }

    /**
     * print output to console
     *
     * @access private
     * @param  string $queue
     * @param  array  $values
     * @param  object $job (Optional. Default: null)
     * @return void
     */
    private function printConsoleOutput(string $queue, array $values, $job = null) : void
    {
        $console = new ConsoleOutput();

        $uuid = $values['uuid'];

        $strpadQueue = str_pad($queue, 8, ' ', STR_PAD_RIGHT);
        $elapsedTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

        if ($job->hasErrors()) {
            $messageTitleConsole = "Error processing the message [ {$uuid} ] from queue [ {$strpadQueue} ] ({$elapsedTime} seconds)";
            $console->writeln("<error>{$messageTitleConsole}</error>");
        } else {
            $messageTitleConsole = "Successfully processed the message [ {$uuid} ] from queue [ {$strpadQueue} ] ({$elapsedTime} seconds)";
            $console->writeln("<info>{$messageTitleConsole}</info>");
        }
    }
}
