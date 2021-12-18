<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Domain\Finvero\CommandHandler;

use Finvero\Domain\Finvero\Command\ToggleIsFinveroProductCommand;
use Finvero\Domain\Finvero\Exception\CannotCreateFinveroException;
use Finvero\Domain\Finvero\Exception\CannotToggleFinveroProductStatusException;
use Finvero\Entity\Finvero;
use Finvero\Repository\FinveroRepository;
use PrestaShopException;

/**
 * Used for toggling the finvero product if is allowed to pay with finvero credit.
 */
class ToggleIsFinveroProductHandler extends AbstractFinveroHandler
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

    /**
     * @param ToggleIsFinveroProductCommand $command
     *
     * @throws CannotCreateFinveroException
     * @throws CannotToggleFinveroProductStatusException
     */
    public function handle(ToggleIsFinveroProductCommand $command)
    {
        $finveroId = $this->finveroRepository->findIdByProduct($command->getProductId()->getValue());

        $finvero = new Finvero($finveroId);

        if (0 >= $finvero->id) {
            $finvero = $this->createFinvero($command->getProductId()->getValue());
        }

        $finvero->is_finvero_product = (bool) !$finvero->is_finvero_product;

        try {
            if (false === $finvero->update()) {
                throw new CannotToggleFinveroProductStatusException(
                    sprintf('Failed to change status for finvero product with id "%s"', $finvero->id)
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CannotToggleFinveroProductStatusException(
                'An unexpected error occurred when updating finvero product status'
            );
        }
    }
}
