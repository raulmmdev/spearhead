<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
	public function resolveIfValid()
	{
		$validator = \Validator::make($this->all(), $this->rules());

		if ($validator->passes()) {
			$this->resolve();
			//@TODO: we have to log the successful update/insert
		} else {
			die('ko, we change this later');
		}
	}

	abstract protected function resolve();
}