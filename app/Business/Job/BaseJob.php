<?php

namespace App\Business\Job;

use Illuminate\Database\Eloquent\Model;

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
     * Data container
     *
     * @access public
     * @var array
     */
    public $data = [];

    /**
     * Object container
     *
     * @access private
     * @var object
     */
    private $object = null;

    /**
     * Errors helper
     *
     * @access public
     * @return boolean
     */
    public function hasErrors() : bool
    {
        return count($this->errors) > 0;
    }

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
     * Object getter
     *
     * @access public
     * @return object
     */
    public function getObject() : object
    {
        return $this->object;
    }

    /**
     * Object setter
     *
     * @access public
     * @param Model $object
     * @return void
     */
    public function setObject(Model $object)
    {
        $this->object = $object;
    }

    /**
     * Abstract method thats needs to be implemented
     *
     * @abstract
     * @access protected
     */
    abstract protected function resolve();
}
