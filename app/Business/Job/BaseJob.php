<?php

namespace App\Business\Job;

abstract class BaseJob
{
	private $errors = [];

	public function getErrors()
	{
		return $this->errors;
	}

	public function setErrors(array $errors)
	{
		$this->errors = $errors;
	}

	abstract protected function resolve();
}