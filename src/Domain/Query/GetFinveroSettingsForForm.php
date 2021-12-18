<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Domain\Finvero\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Gets finvero settings data ready for form display.
 *
 * @see \Finvero\Domain\Finvero\QueryHandler\GetFinveroSettingsForFormHandler how the data is retrieved.
 */
class GetFinveroSettingsForForm
{
    /**
     * @var ProductId|null
     */
    private $productId;

    /**
     * @param int|null $productId
     */
    public function __construct($productId)
    {
        $this->productId = null !== $productId ? new ProductId((int) $productId) : null;
    }

    /**
     * @return ProductId|null
     */
    public function getProductId()
    {
        return $this->productId;
    }
}
