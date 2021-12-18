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

use Finvero\Domain\Finvero\Exception\CannotCreateFinveroException;
use Finvero\Entity\Finvero;

/**
 * Holds the abstraction for common actions for finvero commands.
 */
class AbstractFinverorHandler
{
    /**
     * Creates a finvero.
     *
     * @param $productId
     *
     * @return Finvero
     *
     * @throws CannotCreateFinveroException
     */
    protected function createFinvero($productId)
    {
        try {
            $finvero = new Finvero();
            $finvero->id_product = $productId;
            $finvero->is_finvero_product = 0;

            if (false === $finvero->save()) {
                throw new CannotCreateFinveroException(
                    sprintf(
                        'An error occurred when creating finvero product with product id "%s"',
                        $productId
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CannotCreateFinveroException(
                sprintf(
                    'An unexpected error occurred when creating finvero product with product id "%s"',
                    $productId
                ),
                0,
                $exception
            );
        }

        return $finvero;
    }
}
