<?php
namespace App\Business\Message;

use \App\Business\Api\Request\ApiRequest;
use \App\Business\FormRequest\FormRequestFactory;
use \App\Business\Site\SiteManager;
use \App\Http\Requests\Qwindo\SaveSite;
use \App\Http\Requests\SaveSite\Qwindo;

class MessageManager
{
	const QUEUES = [
		ApiRequest::MSG_CREATE_SITE => 'site',
	];

	public function __construct(
		FormRequestFactory $formRequestFactory,
		SiteManager $siteManager
	) {
		$this->formRequestFactory = $formRequestFactory;
		$this->siteManager = $siteManager;
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
			\Log::error($e->getTrace());

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

			//validate && resolve
			$request = $this->formRequestFactory->create($type, $body);
			$request->resolveIfValid();

			//next message
			$resolver->acknowledge($message);

			//stop if no more messages to process
			if ($asDaemon === false) {
				$resolver->stopWhenProcessed();
			}
		});
	}
}