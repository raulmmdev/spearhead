<?php

namespace App\Business\Job;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\ProductManagerAwareInterface;
use App\Business\Product\ProductManager;

/**
 * DeleteProductJob
 */
class DeleteProductJob extends BaseJob implements ProductManagerAwareInterface
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * CategoryManager
     *
     * @access private
     * @var ProductManager
     */
    private $productManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * ProductManager setter
     *
     * @access public
     * @param ProductManager $productManager
     * @return void
     */
    public function setProductManager(ProductManager $productManager) : void
    {
        $this->productManager = $productManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * ProductManager getter
     *
     * @access public
     * @return ProductManager
     */
    public function getProductManager() : ProductManager
    {
        return $this->productManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Resolve current job
     *
     * @access public
     * @return DeleteSiteCategoryJob | null
     */
    public function resolve() : ?DeleteProductJob
    {
        return $this->productManager->deleteFromJob($this);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
