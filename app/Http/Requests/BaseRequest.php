<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
	public function resolveIfValid()
	{
		$validator = \Validator::make($this->all(), $this->rules());

		if ($validator->fails()) {
			die('ko, we change this later');
		}

		$this->resolve();
	}

	abstract protected function resolve();
}