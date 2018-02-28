<?php
namespace App\Business\Message;

use \App\Business\BusinessLog\BusinessLogManager;
use \App\Model\Document\BusinessLog;
use \App\Business\Job\JobFactory;

class MessageManager
{
	public function __construct(
		JobFactory $jobFactory,
		BusinessLogManager $businessLogManager
	) {
		$this->jobFactory = $jobFactory;
		$this->businessLogManager = $businessLogManager;
	}

	/**
	 * Create a new message on the message queue.
	 *
	 * @return bool
	 */
	public function produceJobMessage(string $queue, array $values): bool
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
	 * consume a message from the message queue.
	 *
	 * @return
	 */
	public function consumeJobMessage(string $queue, bool $asDaemon)
	{
		\Amqp::consume($queue, function($message, $resolver) use ($queue, $asDaemon) {
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

	private function reportToBusinessLogger(string $queue, array $values, $object = null)
	{
		$console = new \Symfony\Component\Console\Output\ConsoleOutput();

		if (!is_null($object)) {
			$businessLogType = BusinessLog::LEVEL_TYPE_INFO;

			$messageTitle = "Successfully processed a message on queue [ {$queue} ]";

			$messageBody = json_encode([
				'request' => $values,
				'response' => $object,
			]);

			$console->writeln("<info>Successfully processed a message on queue [ {$queue} ]</info>");
		} else {
			$businessLogType = BusinessLog::LEVEL_TYPE_ERROR;

			$messageTitle = "Error processing a message [ {$queue} ]";

			$messageBody = json_encode([
				'request' => $values,
				'errors' => $request->getErrors(),
			]);

			$console->writeln("<error>Error processing a message [ {$queue} ]</error>");
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