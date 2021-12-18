<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Domain\Finvero\QueryResult;

/**
 * Holds data used in modified Products form.
 */
class FinveroSettingsForForm
{
    /**
     * @var bool
     */
    private $isFinveroProduct;

    /**
     * @param bool $isFinveroProduct
     */
    public function __construct($isFinveroProduct)
    {
        $this->isFinveroProduct = $isFinveroProduct;
    }

    /**
     * @return bool
     */
    public function isFinveroProduct()
    {
        return $this->isFinveroProduct;
    }
}
