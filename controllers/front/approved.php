<?php
/**
* 2021 Eos Software S.A. de C.V.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Pablo Ariel Blanco: https://www.linkedin.com/in/pablo-blanco-8728b388/
*  @copyright 2021 Eos Software S.A. de C.V. https://eossoftware.mx/
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* -------------------------------------------------------------------------------------
*        Methods availables for FinveroModuleApprovedModuleFrontController Class
* -------------------------------------------------------------------------------------
*  public function initContent()
*/

/********************************************************************************************************
*********************************************************************************************************
**                                       Used classes section                                          **
*********************************************************************************************************
********************************************************************************************************/

use Tools;

class FinveroModuleApprovedModuleFrontController extends ModuleFrontController
{

/********************************************************************************************************
*********************************************************************************************************
**                        	Methods availables for de Approved Controller                              **
*********************************************************************************************************
********************************************************************************************************/

    public function initContent()
    {
        //$customer = new Customer($cart->id_cuscomer);
        //print_r('Hello from approval nro '.$cart->id_cuscomer);
        parent::initContent();
        $this->context->smarty->assign([
            "data" => $response
        ]);
        $this->setTemplate("module:finveromodule/views/templates/front/approved.tpl");

        //Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart-id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }  
}