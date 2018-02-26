<?php 

namespace App\Business\Api\Request;

use \Illuminate\Http\Request;
use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Message\MessageManager;

class ApiRequest implements ResolvableInterface
{

	const MSG_CREATE_SITE = 'CREATE_SITE';

	/**
     * contains the request json.
     *
     * @var $body
     */
	private $body;

	/**
     * The Message manager.
     *
     * @var $messageManager
     */
	private $messageManager;


	public function __construct(Request $request, MessageManager $messageManager)
	{
		$this->body = $request->getContent();
		$this->messageManager = $messageManager;
	}

	public function getBody() : string 
	{
		return $this->body;
	}

	public function setBody(string $body) 
	{
		$this->body = $body;
	}

	public function resolve(string $messageType): bool
	{
		return $this->messageManager->produceJobMessage($messageType, $this->getBody());
	}
}