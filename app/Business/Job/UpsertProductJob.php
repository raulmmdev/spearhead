<?php

namespace App\Business\Job;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\ProductManagerAwareInterface;
use App\Business\Product\ProductManager;

/**
 * UpsertProductJob
 */
class UpsertProductJob extends BaseJob implements ProductManagerAwareInterface
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * ProductManager
     *
     * @access protected
     * @var ProductManager
     */
    protected $productManager;

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
     * @return UpsertProductJob | null
     */
    public function resolve() : ?UpsertProductJob
    {
        return $this->productManager->upsertFromJob($this);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
