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
    // PROPERTIES
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
        $attr = $this->productAttributeRepository->findWhere([
            'product_id' => $product->getId(),
            'name' => $name
        ])->first();

        if ($attr === null) {
            $attr = new ProductAttribute();
            $attr->product()->associate($product);
            $attr->setName($name);
        }

        $attr->setValue($value);
        $attr->save();
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
