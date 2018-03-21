<?php

namespace App\Business\Product\Attribute;

use App\Model\Entity\Product;
use App\Model\Entity\ProductAttribute;
use App\Model\Entity\Repository\ProductAttributeRepository;

/**
 * ProductAttributeManager
 */
class ProductAttributeManager
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * ProductAttributeRepository
     *
     * @access protected
     * @var $productAttributeRepository
     */
    protected $productAttributeRepository;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Object cosntructor
     *
     * @access public
     * @param ProductAttributeRepository $productAttributeRepository
     */
    public function __construct(ProductAttributeRepository $productAttributeRepository)
    {
        $this->productAttributeRepository = $productAttributeRepository;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set attribute
     *
     * @access public
     * @param Product $product
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setAttribute(Product $product, string $name, string $value = null) : void
    {
        $attribute = $this->productAttributeRepository->findWhere([
            'product_id' => $product->getId(),
            'name' => $name
        ])->first();

        if ($attribute === null) {
            $attribute = new ProductAttribute();
            $attribute->product()->associate($product);
            $attribute->setName($name);
        }

        $attribute->setValue($value);
        $attribute->save();
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
