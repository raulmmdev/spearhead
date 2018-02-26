<?php

namespace App\Business\Api\Interfaces;

interface ResolvableInterface {
	public function resolve(string $messageType);
}