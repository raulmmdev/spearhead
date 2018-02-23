<?php

namespace App\Business\Api\Interfaces;

use App\Business\Api\ApiRequest;

interface ResolvableInterface {
	public function resolve(string $messageType);
}