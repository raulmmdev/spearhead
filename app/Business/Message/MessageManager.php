<?php
namespace App\Business\Message;

use \App\Business\Api\Request\ApiRequest;
use \App\Business\BusinessLog\BusinessLogManager;
use \App\Business\FormRequest\FormRequestFactory;
use \App\Business\Site\SiteManager;
use \App\Http\Requests\BaseRequest;
use \App\Http\Requests\Qwindo\SaveSite;
use \App\Http\Requests\SaveSite\Qwindo;
use \App\Model\Document\BusinessLog;

class MessageManager
{
	const QUEUES = [
		ApiRequest::MSG_CREATE_SITE => 'site',
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
		\Amqp::consume(self::QUEUES[$type], function ($message, $resolver) use ($type, $asDaemon) {
			//unserialize
			$body = json_decode($message->body, true);

			//validate
			$request = $this->formRequestFactory->create($type, $body);

			//no errors? resolve
			$object = null;
			if (empty($request->getErrors())) {
				$object = $request->resolveIfValid();
			}

			//log accordingly
			$this->reportToBusinessLogger($request, $type, $body, $object);

			//next message
			$resolver->acknowledge($message);

			//stop if no more messages to process
			if ($asDaemon === false) {
				$resolver->stopWhenProcessed();
			}
		});
	}

	private function reportToBusinessLogger(BaseRequest $request, string $type, array $body, $object = null)
	{
		$console = new \Symfony\Component\Console\Output\ConsoleOutput();

		if (empty($request->getErrors())) {
			$businessLogType = BusinessLog::LEVEL_TYPE_INFO;
			$error_message = "Successfully processed a message [ {$type} ]";

			$error_description = json_encode([
				'body' => $body,
				'response' => $object,
			]);

			$console->writeln("<info>Successfully processed a message [ {$type} ]</info>");
		} else {
			$businessLogType = BusinessLog::LEVEL_TYPE_ERROR;
			$error_message = "Error processing a message [ {$type} ]";

			$error_description = json_encode([
				'body' => $body,
				'errors' => $request->getErrors(),
			]);

			$console->writeln("<error>Error processing a message [ {$type} ]</error>");
		}

		$this->businessLogManager->log(
			$businessLogType,
			BusinessLog::USER_TYPE_MERCHANT,
			BusinessLog::BUSINESS_LOG_ELEMENT_TYPES[$type],
			$error_message,
			$error_description
		);
	}
}