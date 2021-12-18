<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Domain\Finvero\QueryHandler;

use Finvero\Domain\Finvero\Query\GetFinveroSettingsForForm;
use Finvero\Domain\Finvero\QueryResult\FinveroSettingsForForm;
use Finvero\Repository\FinveroRepository;

/**
 * Gets finvero settings data ready for form display.
 */
class GetFinveroSettingsForFormHandler
{
    /**
     * @var FinveroRepository
     */
    private $finveroRepository;

    /**
     * @param FinveroRepository $finveroRepository
     */
    public function __construct(FinveroRepository $finveroRepository)
    {
        $this->finveroRepository = $finveroRepository;
    }

    public function handle(GetFinveroSettingsForForm $query)
    {
        if (null === $query->getProductId()) {
            return new FinveroSettingsForForm(false);
        }

        return new FinveroSettingsForForm(
            $this->finveroRepository->getIsFinveroProductStatus($query->getProductId()->getValue())
        );
    }
}
