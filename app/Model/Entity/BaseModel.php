<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * Adds __set() __get() magic methods to desired classes or models
 */
class BaseModel extends Model
{
    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Overload each Model entity to dynamicaly create setters and getters
     * using __call() magic method
     *
     * @param string $method
     * @param mixed $parameters
     * @return void
     * @throws \Exception In case of method/property not found
     */
    public function __call($method, $parameters)
    {
        $isSetter = starts_with($method, 'set');
        $isGetter = starts_with($method, 'get');
        $studlyCasedAttribute = substr($method, strlen('get'));

        $attributesMap = [];
        if (count($this->fillable)) {
            foreach ($this->fillable as $attr) {
                $attributesMap[studly_case($attr)] = $attr;
            }
        }

        if (isset($attributesMap[$studlyCasedAttribute])) {
            $attribute = $attributesMap[$studlyCasedAttribute];

            if ($isSetter) {
                $this->$attribute = array_first($parameters);
            }

            return $this->$attribute;
        }

        return parent::__call($method, $parameters);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
