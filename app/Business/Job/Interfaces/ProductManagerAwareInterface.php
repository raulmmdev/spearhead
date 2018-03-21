<?php

namespace App\Business\Job\Interfaces;

use App\Business\Product\ProductManager;

/**
 * ProductManagerAwareInterface
 */
interface ProductManagerAwareInterface
{
    public function setProductManager(ProductManager $productManager);
    public function getProductManager() : ProductManager;
}
