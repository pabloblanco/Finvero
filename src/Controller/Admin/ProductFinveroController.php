<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Controller\Admin;

use Finvero\Domain\Finvero\Command\ToggleIsFinveroProductCommand;
use Finvero\Domain\Finvero\Exception\CannotCreateFinveroException;
use Finvero\Domain\Finvero\Exception\CannotToggleFinveroProductStatusException;
use Finvero\Domain\Finvero\Exception\FinveroException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * This controller holds all custom actions which are added by extending "Sell > Catalog > Products" page.
 *
 */
class ProductFinveroController extends FrameworkBundleAdminController
{
    /**
     * Catches the toggle action of finvero product.
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function toggleIsFinveroProductAction($productId)
    {
        try {
            /*
            * This part usage of CQRS pattern command strategy to perform write operation for Finvero entity.
            *
            */
            $this->getCommandBus()->handle(new ToggleIsFinveroProductCommand((int) $productId));

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (FinveroException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessageMapping()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Gets error message mappings which are later used to display friendly user error message instead of the
     * exception message.
     *
     * @return array
     */
    private function getErrorMessageMapping()
    {
        return [
            ProductException::class => $this->trans(
                'Something bad happened when trying to get product id',
                'Modules.Finvero.Productfinverocontroller'
            ),
            CannotCreateFinveroException::class => $this->trans(
                'Failed to create finvero',
                'Modules.Finvero.Productfinverocontroller'
            ),
            CannotToggleFinveroProductStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Modules.Finvero.Productfinverocontroller'
            ),
        ];
    }
}
