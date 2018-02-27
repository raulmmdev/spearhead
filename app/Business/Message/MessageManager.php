<?php
namespace App\Business\Message;

use \App\Business\Api\Request\ApiRequest;
use \App\Business\BusinessLog\BusinessLogManager;
use \App\Business\FormRequest\FormRequestFactory;
use \App\Business\Site\SiteManager;
use \App\Http\Requests\Qwindo\SaveSite;
use \App\Http\Requests\SaveSite\Qwindo;
use \App\Model\Document\BusinessLog;

class MessageManager
{
	const QUEUES = [
		ApiRequest::MSG_CREATE_SITE => 'site',
	];

	const BUSINESS_LOG_ELEMENT_TYPES = [
		ApiRequest::MSG_CREATE_SITE => BusinessLog::ELEMENT_TYPE_SITE,
	];

	public function __construct(
		FormRequestFactory $formRequestFactory,
		SiteManager $siteManager,
		BusinessLogManager $businessLogManager
	) {
		$this->formRequestFactory = $formRequestFactory;
		$this->siteManager = $siteManager;
		$this->businessLogManager = $businessLogManager;
	}

	/**
	 * Create a new message on the message queue.
	 *
	 * @return bool
	 */
	public function produceJobMessage(string $type, string $body): bool
	{
		try {
			\Amqp::publish('', $body, [
				'queue' => self::QUEUES[$type]
			]);
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
			\Log::error($e->getTraceAsString());

			return false;
		}

		return true;
	}

	/**
	 * consume a message from the message queue.
	 *
	 * @return
	 */
	public function consumeJobMessage(string $type, bool $asDaemon)
	{
		$console = new \Symfony\Component\Console\Output\ConsoleOutput();

		\Amqp::consume(self::QUEUES[$type], function ($message, $resolver) use ($type, $console, $asDaemon) {
			//unserialize
			$body = json_decode($message->body, true);

			//validate && resolve
			$request = $this->formRequestFactory->create($type, $body);
			$object = $request->resolveIfValid();

			if (count($request->getErrors())) {
				$this->businessLogManager->error(
					BusinessLog::USER_TYPE_MERCHANT,
					self::BUSINESS_LOG_ELEMENT_TYPES[$type],
					"Error processing a message [ {$type} ]",
					json_encode([
						'body' => $body,
						'errors' => $request->getErrors(),
					])
				);

				$console->writeln("<error>Error processing a message [ {$type} ]</error>");
			} else {
				$this->businessLogManager->info(
					BusinessLog::USER_TYPE_MERCHANT,
					self::BUSINESS_LOG_ELEMENT_TYPES[$type],
					"Successfully processed a message [ {$type} ]",
					json_encode([
						'body' => $body,
						'response' => $object,
					])
				);

				$console->writeln("<info>Successfully processed a message [ {$type} ]</info>");
			}

			//next message
			$resolver->acknowledge($message);

			//stop if no more messages to process
			if ($asDaemon === false) {
				$resolver->stopWhenProcessed();
			}
		});
	}
}