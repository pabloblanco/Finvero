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
 * Used for toggling the product if is findero product.
 *
 * @see \Finvero\Domain\Finvero\CommandHandler\ToggleIsFinveroProductHandler how the data is handled.
 */
class ToggleIsFinveroProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param int $productId
     *
     * @throws ProductException
     */
    public function __construct($productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }
}
