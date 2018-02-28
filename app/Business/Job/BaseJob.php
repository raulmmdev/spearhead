<?php

namespace App\Business\Job;

/**
 * BaseJob
 */
abstract class BaseJob
{
	/**
	 * Errors container
	 *
	 * @access private
	 * @var array
	 */
	private $errors = [];

	/**
	 * Errors getter
	 *
	 * @access public
	 * @return array
	 */
	public function getErrors() : array
	{
		return $this->errors;
	}

	/**
	 * Errors setter
	 *
	 * @access public
	 * @param array $errors
	 * @return void
	 */
	public function setErrors(array $errors)
	{
		$this->errors = $errors;
	}

	/**
	 * Abstract method thats needs to be implemented
	 *
	 * @abstract
	 * @access protected
	 */
	abstract protected function resolve();
}