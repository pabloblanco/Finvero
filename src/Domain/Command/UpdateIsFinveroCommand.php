<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Domain\Finvero\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * used to update finvero products status.
 *
 * @see \Finvero\Domain\Finvero\CommandHandler\UpdateIsFinveroProductHandler how the data is handled.
 */
class UpdateIsFinveroProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var bool
     */
    private $isFinveroProduct;

    /**
     * @param int $productId
     * @param bool $isFinveroProduct
     *
     * @throws ProductException
     */
    public function __construct($productId, $isFinveroProduct)
    {
        $this->productId = new ProductId($productId);
        $this->isFinveroProduct = $isFinveroProduct;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return bool
     */
    public function isFinveroProduct()
    {
        return $this->isFinveroProduct;
    }
}
