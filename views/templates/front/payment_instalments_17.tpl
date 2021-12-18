{/**
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
*}

<form action="" method="POST" class="additional-information">
    <p style="font-weight: bold">{l s='Do you want to finance your purchase?' mod='finvero'}</p>
    <select id="instalment_months" name="instalment_months">
        <option value="0">{l s='No, thanks' mod='finvero'}</option>
        {foreach from=$instalments item=instalment}
            <option value="{$instalment|intval}">{l s='Yes, in %s months' mod='finvero' sprintf=[$instalment|intval]}</option>
        {/foreach}
    </select>
</form>