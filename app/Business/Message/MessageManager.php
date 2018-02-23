<?php
namespace App\Business\Message;
  
class MessageManager
{
	/**
     * Create a new message on the message queue.
     *
     * @return null
     */
    public function produceJobMessage(string $type, string $body)
    {
    	return null;
    }
}