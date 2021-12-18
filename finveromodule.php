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
* -------------------------------------------------
*               Constructor section
* -------------------------------------------------
*  public function __construct()
*
* -------------------------------------------------
*                 Install section
* -------------------------------------------------
*  public function install()
*  public function uninstall()
*
* -------------------------------------------------
*                  Hooks section
* -------------------------------------------------
*  public function hookDisplayHeader($params)
*  public function hookDisplayHome()
*  public function hookDisplayShoppingCart()
*  public function hookModuleRoutes($params)
*  public function hookPaymentOptions($params)
*  public function hookBackOfficeHeader()
*  public function hookActionProductGridDefinitionModifier(array $params)
*  public function hookActionProductGridQueryBuilderModifier(array $params)
*  public function hookActionProductFormBuilderModifier(array $params)
*  public function hookActionAfterUpdateProductFormHandler(array $params)
*  public function hookActionAfterCreateProductFormHandler(array $params)
*
* -------------------------------------------------
*                  Widget section
* -------------------------------------------------
*  public function renderWidget($hookName, array $configuration)
*
* -------------------------------------------------
*        Methods availables for Finvero Class
* -------------------------------------------------
*  public function isUsingNewTranslationSystem()
*  private function installTables()
*  private function uninstallTables()
*  public function hasAllFinveroProducts($cart)
*  public function checkCurrency($cart)
*  public function getContent()
*  public function getWarnings($getAll = true)
*  private function handleException(FinveroException $exception)
*  public function postProcess()
*  public function getForm()
*/

/********************************************************************************************************
*********************************************************************************************************
**                                Implemented classes section                                          **
*********************************************************************************************************
********************************************************************************************************/

use Finvero\Domain\Finvero\Command\UpdateIsFinveroProductCommand;
use Finvero\Domain\Finvero\Exception\CannotCreateFinveroException;
use Finvero\Domain\Finvero\Exception\CannotToggleFinveroProductStatusException;
use Finvero\Domain\Finvero\Exception\FinveroException;
use Finvero\Domain\Finvero\Query\GetFinveroSettingsForForm;
use Finvero\Domain\Finvero\QueryResult\FinveroSettingsForForm;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

if(!defined('_PS_VERSION_'))
exit;

class Finveromodule extends PaymentModule
{

/********************************************************************************************************
*********************************************************************************************************
**                                     	Constructor section                                            **
*********************************************************************************************************
********************************************************************************************************/

	public function __construct()
    {
        // Module name.
        $this->name = 'finveromodule';

        // Tab in which the module will be displayed in the backoffice.
        $this->tab = 'payments_gateways'; 
        $this->version = '1.0.0'; 
        $this->author ='Eos Software';

        // Need load any instance?.
        $this->need_instance = 0; 

        $this->limited_countries = ['MX'];
        $this->limited_currencies = ['MXN'];    

        // Minimum version required to ensure proper operation.
        $this->ps_versions_compliancy = [
        'min' => '1.7.6.0', 
        'max' => _PS_VERSION_
        ]; 

        // If it is true, the module will adapt with Bootstrap.
        $this->bootstrap = true; 

        // Call to father contructor.
        parent::__construct(); 

        // Send warnings during install process.
        $this->warning = $this->getWarnings(false);

        // Module name.
        $this->displayName = $this->getTranslator()->trans(
        'Finvero', 
        [],
        'Modules.Finveromodule.Admin'
        ); 
        // Description of functionality.
        $this->description = $this->getTranslator()->trans(
        'Módulo de prueba técnica para la empresa EOS.', 
        [],
        'Modules.Finveromodule.Admin'
        );     

        // Warning before uninstalling the module.    
        $this->confirmUninstall = $this->getTranslator()->trans(
            '¿Estás seguro de que quieres desinstalar el módulo?',
        [],
        'Modules.Finveromodule.Admin'
        );   

        // Point Of Sale Terminal settings
        $this->url_finvero_end_point = 'https://bemantic.com//FinveroEndPoint/servlets/TransactionStartBridge.php';
    }

/********************************************************************************************************
*********************************************************************************************************
**                                         	Install section                                            **
*********************************************************************************************************
********************************************************************************************************/

    /**
    * Install module and register hooks to allow the Finvero operation over this eCommerce.
    *
    * @see https://developers.finvero.com/integrations/prestashop/modules
    *
    * @return bool
    */
  	public function install()
  	{	
  		// Check Server from PHP CURL Activation and break the instalation process if this does not comply with the rule.
	    if (extension_loaded('curl') == false)
	    {
	      	$this->_errors[] = $this->getTranslator()->trans('You have to enable the cURL extension on your server to install this module',
                [],
                'Modules.Finvero.Admin'
            );
	      	return false;
	    }

	    // Check the default store localization and break the instalation process if it is not within the coverage area of the Finvero service.
    	$iso_code = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
    	if (in_array($iso_code, $this->limited_countries) == false)
	    {
	      	$this->_errors[] = $this->getTranslator()->trans('This module is not available in your country',
                [],
                'Modules.Finvero.Admin'
            );
	      	return false;
	    }

	    // If the shop is multistore then able the module for all the stores.
	    if (Shop::isFeatureActive()) {
	      	Shop::setContext(Shop::CONTEXT_ALL);
	    }

  		/********************************************************************************
  		*********************************************************************************
  		**                     	  Register hooks section                               **
  		*********************************************************************************
  		********************************************************************************/
	    return (parent::install()
        // Hooking in the Front End Views
  	    	&& $this->registerHook('displayHeader') 
  	    	&& $this->registerHook('displayHome')
  	    	&& $this->registerHook('displayShoppingCart')  
  	    	&& $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
	    	// Hooking in the Back End Views  		    	
	    	  && $this->registerHook('backOfficeHeader')
        // Hooking in the Back End Actions
          // Register hook to allow Product grid definition modifications.
          // Each grid's definition modification hook has it's own name. Hook name is built using
          // this structure: "action{grid_id}GridDefinitionModifier", in this case "grid_id" is "product"
          // this means we will be modifying "Sell > Catalog > Products" page grid.
          // You can check any definition factory service in PrestaShop\PrestaShop\Core\Grid\Definition\Factory
          // to see available grid ids. Grid id is returned by `getId()` method.
          && $this->registerHook('actionProductGridDefinitionModifier')
          // Register hook to allow Product grid query modifications which allows to add any sql condition.
          && $this->registerHook('actionProductGridQueryBuilderModifier')
          // Register hook to allow overriding product form
          // this structure: "action{block_prefix}FormBuilderModifier", in this case "block_prefix" is "product"
          // {block_prefix} is either retrieved automatically by its type. E.g "ManufacturerType" will be "manufacturer"
          // or it can be modified in form type by overriding "getBlockPrefix" function
          && $this->registerHook('actionProductFormBuilderModifier')
          && $this->registerHook('actionAfterCreateProductFormHandler')
          && $this->registerHook('actionAfterUpdateProductFormHandler')
          // Intall a Finvero link to module on SELL section in the menu of the Backend.
          && $this->installtab()
          // Install nesessary tables to correct function of the module and to building the model in such a way as to 
          // avoid overrides and take advantage of the features of SQLBus.
          && $this->installTables()
      );

      // Clean template cache.
      $this->emptyTemplatesCache();

      return (bool) $return;
    }

    /**
    * Uninstall module, unregister hooks and DROP all created tables.
    *
    * 
    *
    * @return bool
    */
    public function uninstall()
    {

        $this->_clearCache('*');
        // Uninstall all Hooks and tables from the Data Base.
        if(!parent::uninstall() 
            || !$this->unregisterHook('displayHeader')
            || !$this->unregisterHook('displayHome')
            || !$this->unregisterHook('displayShoppingCart')
            || !$this->unregisterHook('moduleRoutes')
            || !$this->unregisterHook('paymentOptions')
            || !$this->unregisterHook('backOfficeHeader')
            || !$this->unregisterHook('actionProductGridDefinitionModifier')
            || !$this->unregisterHook('actionProductGridQueryBuilderModifier')
            || !$this->unregisterHook('actionProductFormBuilderModifier')
            || !$this->unregisterHook('actionAfterCreateProductFormHandler')
            || !$this->unregisterHook('actionAfterUpdateProductFormHandler')
            || !$this->uninstalltab()
            || !$this->uninstallTables()
            )
        return false;

      return true;
    }

/********************************************************************************************************
*********************************************************************************************************
**                              	Hooks availables for Finvero Class                                 **
*********************************************************************************************************
********************************************************************************************************/

    /**
     * This hook load CSS & JavaScript files you need on the header position of the Front End master layout.
    *
    * @param $params
    */  
    public function hookDisplayHeader($params)
    {
        $this->context->controller->registerStylesheet('modules-finveromodule', 'modules/'.$this->name.'/views/css/finvero.css', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerJavascript('modules-finveromodule', 'modules/'.$this->name.'/views/js/finvero.js',[ 'position' => 'bottom','priority' => 150]);
    }

    /**
     * This hook show the Finvero Notice on the Front End Home Page.
    *
    */ 
    public function hookDisplayHome()
    {
        return $this->display(__FILE__, 'views/templates/hook/finvero.tpl');
    }

    /**
     * This hook show the Finvero Notice on the Front End Shopping Cart Page.
    *
    */ 
    public function hookDisplayShoppingCart()
    {
        return $this->display(__FILE__, 'views/templates/hook/finvero.tpl');
    }

    /**
     * This hook contain all the modern design routes.
    *
    * @param $params
    *
    * @return $option array
    */  
    public function hookModuleRoutes($params)
    {
        return [
            'credit' => [
                'controller' => 'credit',
                'rule' => 'fc-credit',
                'keywords' => [],
                'params' => [
                    'module' => $this->name,
                    'fc' => 'module',
                    'controller' => 'credit'
                ]
            ]
        ];
    }

    /**
     * This hook show the Finvero Payment option if all products in the cart are payables by Finvero credit.
    *
    * @param $params
    *
    * @return $option array
    */  
    public function hookPaymentOptions($params)
    {
        // Check if the module is active.
        if (!$this->active) {
            return;
        }
        // Check if are defined currencies.
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        // Check if all products are payables by Finvero credit.
        if (!$this->hasAllFinveroProducts($params['cart'])) {
            return;
        }
        // Makes a new payment option.
        $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        // Makes and show the Finvero payment option hooked in the Step 4 of sales process.
        $option->setCallToActionText($this->l('Pay on credit with Finvero.'))
            ->setAction($this->context->link->getModuleLink($this->name, 'credit', array(), true))
            ->setInputs([
                'token' => [
                    'name' =>'token',
                    'type' =>'hidden',
                    'value' =>'12345689',
                ],
            ])
            ->setAdditionalInformation($this->context->smarty->fetch('module:finveromodule/views/templates/front/payment_checkout_infos.tpl'))
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/logo_payment_options.png'));

        return [
            $option
        ];
    }

    /**
     * This hook show the Finvero Payment option if all products in the cart are payables by Finvero credit.
    *
    * @param $params
    *
    * @return $option array
    */  
    public function hookPaymentReturn($params)
    {
        if (!$this->active || !Configuration::get(self::FLAG_DISPLAY_PAYMENT_INVITE)) {
            return;
        }

        $state = $params['order']->getCurrentState();
        if (
            in_array(
                $state,
                array(
                    Configuration::get('PS_OS_BANKWIRE'),
                    Configuration::get('PS_OS_OUTOFSTOCK'),
                    Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'),
                )
        )) {
            $bankwireOwner = $this->owner;
            if (!$bankwireOwner) {
                $bankwireOwner = '___________';
            }

            $bankwireDetails = Tools::nl2br($this->details);
            if (!$bankwireDetails) {
                $bankwireDetails = '___________';
            }

            $bankwireAddress = Tools::nl2br($this->address);
            if (!$bankwireAddress) {
                $bankwireAddress = '___________';
            }

            $totalToPaid = $params['order']->getOrdersTotalPaid() - $params['order']->getTotalPaid();
            $this->smarty->assign(array(
                'shop_name' => $this->context->shop->name,
                'total' => Tools::displayPrice(
                    $totalToPaid,
                    new Currency($params['order']->id_currency),
                    false
                ),
                'bankwireDetails' => $bankwireDetails,
                'bankwireAddress' => $bankwireAddress,
                'bankwireOwner' => $bankwireOwner,
                'status' => 'ok',
                'reference' => $params['order']->reference,
                'contact_url' => $this->context->link->getPageLink('contact', true)
            ));
        } else {
            $this->smarty->assign(
                array(
                    'status' => 'failed',
                    'contact_url' => $this->context->link->getPageLink('contact', true),
                )
            );
        }

        return $this->fetch('module:finveromodule/views/templates/hook/payment_return.tpl');
    }

    /**
     * This hook load CSS & JavaScript files you need on the header position of the Back End master layout.
    *
    */  
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Hook allows to modify Products grid definition.
     * This hook is a right place to add/remove columns or actions (bulk, grid).
     *
     * @param array $params
     */
    public function hookActionProductGridDefinitionModifier(array $params)
    {
        /** @var GridDefinitionInterface $definition */
        $definition = $params['definition'];

        $translator = $this->getTranslator();

        $definition
            ->getColumns()
            ->addAfter(
                'active',
                (new ToggleColumn('is_finvero_product'))
                    ->setName($translator->trans('Allowed for credit pay', [], 'Modules.Finveromodule.Admin'))
                    ->setOptions([
                        'field' => 'is_finvero_product',
                        'primary_field' => 'id_product',
                        'route' => 'ps_finvero_toggle_is_finvero_product',
                        'route_param_name' => 'productId',
                    ])
            )
        ;

        $definition->getFilters()->add(
            (new Filter('is_finvero_product', YesAndNoChoiceType::class))
            ->setAssociatedColumn('is_finvero_product')
        );
    }

    /**
     * Hook allows to modify Products query builder and add custom sql statements.
     *
     * @param array $params
     */
    public function hookActionProductGridQueryBuilderModifier(array $params)
    {
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];

        /** @var ProductFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder->addSelect(
            'IF(fp.`is_finvero_product` IS NULL,0,fp.`is_finvero_product`) AS `is_finvero_product`'
        );

        $searchQueryBuilder->leftJoin(
            'p',
            '`' . pSQL(_DB_PREFIX_) . 'finvero_products`',
            'fp',
            'fp.`id_product` = p.`id_product`'
        );

        if ('is_finvero_product' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('fp.`is_finvero_product`', $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('is_finvero_product' === $filterName) {
                $searchQueryBuilder->andWhere('fp.`is_finvero_product` = :is_finvero_product');
                $searchQueryBuilder->setParameter('is_finvero_product', $filterValue);

                if (!$filterValue) {
                    $searchQueryBuilder->orWhere('fp.`is_finvero_product` IS NULL');
                }
            }
        }
    }

    /**
     * Hook allows to modify Product form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     */
    public function hookActionProductFormBuilderModifier(array $params)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $formBuilder->add('is_finvero_product', SwitchType::class, [
            'label' => $this->getTranslator()->trans('Allow credit pays with Finvero', [], 'Modules.Finveromodule.Admin'),
            'required' => false,
        ]);

        /**
         * @var CommandBusInterface
         */
        $queryBus = $this->get('prestashop.core.query_bus');

        /**
         * Is Used a Command Query Responsibility Segregation pattern query to perform read operation from Finvero entity.
         *
         * @var FinderoSettingsForForm
         */
        $finderoSettings = $queryBus->handle(new GetFinveroSettingsForForm($params['id']));

        $params['data']['is_finvero_product'] = $finveroSettings->isFinveroProduct();

        $formBuilder->setData($params['data']);
    }

    /**
     * Hook allows to modify Products form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     *
     * @throws ProductException
     */
    public function hookActionAfterUpdateProductFormHandler(array $params)
    {
      $this->updateProductFinveroStatus($params);
    }

    /**
     * Hook allows to modify Products form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     *
     * @throws ProductException
     */
    public function hookActionAfterCreateProductFormHandler(array $params)
    {
      $this->updateProductFinveroStatus($params);
    }
/********************************************************************************************************
*********************************************************************************************************
**                                       Widgets Section                                               **
*********************************************************************************************************
********************************************************************************************************/

/**
* This hook load like a widget, taking advantage from modern hooking technics.
*
* @param $hookName, array $configuration
*
* @return a TPL Widget
*/  
public function renderWidget($hookName, array $configuration)
{
    echo $this->context->link->getModuleLink($this->name);
    if ($hookName === 'displayNavFullWidth'){
        return "<br />Exception from displayNavFullWidth hook";
    }
    if (!$this->isCached($this->templateFile, $this->getCachedId($this->name))){
        $this->context->smarty->assing($this->getWidgetVariables($hookName, $comfiguration));
    }
    return $this->fetch("module:finveromodule/views/templates/hook/footer.tpl");
}

/********************************************************************************************************
*********************************************************************************************************
**                             	Methods availables for Finvero Class                                   **
*********************************************************************************************************
********************************************************************************************************/

	/**
     * This function is required in order to make module compatible with new translation system.
    *
    *
    *
    * @return bool
    */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

	/**
    * This function install a Finvero link to module on SELL section in the Backend.
    *
    * @return bool
    */
    public function installtab()
    {
        $tab = new Tab();
        $tab->class_name = 'ModuleLink';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('SELL');
        $tab->icon = 'settings_applications';
        $languages = Language::getLanguages();
        foreach ($languages as $lang){
            $tab->name[$lang['id_lang']] =$this->l('Finvero');
        }

        try {
            $tab->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }
    
	/**
     * This function uninstall Finvero link to module on SELL section in the Backend.
    *
    * @return bool
    */
    public function uninstalltab()
    {
        $idTab = (int)Tab::getIdFromClassName('AdminTest');

        if ($idTab) {
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    /**
     * Installs tables required for Finvero operation.
    *
	*
	*
    * @return bool
    */
    private function installTables()
    {
    	$success = true;
    	// Create this table to storage if de product of the store is available to be sold on credit with Finvero.
        $sql = '
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'finvero_products` (
                `id_finvero_product` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` INT(10) UNSIGNED NOT NULL,
                `is_finvero_product` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_finvero_product`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' COLLATE=utf8_unicode_ci;
        ';

        if (!Db::getInstance()->execute($sql)){
        	$success = false;
		}

        // Create this table to storage all transaction made with Finvero credit.
        $sql = '
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'finvero_transactions` (
                `id_finvero_transaction` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_order` INT(10) UNSIGNED NOT NULL,
                `id_finvero_customer` TINYINT(1) NOT NULL,
                `id_finvero_transaction_code` INT(10) NOT NULL,
                PRIMARY KEY (`id_finvero_transaction`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' COLLATE=utf8_unicode_ci;
        ';

        if (!Db::getInstance()->execute($sql)){
        	$success = false;
		}

        return $success;        
    }

    /**
    * Uninstalls tables created for Finvero operation.
    *
    *
    *
    * @return bool
    */
    private function uninstallTables()
    {
        $success = true;
        $sql = 'DROP TABLE IF EXISTS `' . pSQL(_DB_PREFIX_) . 'finvero_products`';

        if (!Db::getInstance()->execute($sql)){
        	$success = false;
		    }

        $sql = 'DROP TABLE IF EXISTS `' . pSQL(_DB_PREFIX_) . 'finvero_transactions`';

        if (!Db::getInstance()->execute($sql)){
        	$success = false;
		    }

        return $success;  
    }

    /**
    * Evaluates all the products in the cart and returns true if all these are available for sale on credit with the Finvero service.
    *
    * @param $cart
    *
    * @return bool
    */
    public function hasAllFinveroProducts($cart)
    {
      	$has_all_finvero_products = true;

	    // Build query
	    $sql = new DbQuery();

	    // Build SELECT
	    $sql->select('cp.`id_product`, fp.`is_finvero_product`');

	    // Build FROM
	    $sql->from('cart_product', 'cp');   

	    // Build JOIN
	    $sql->leftJoin('finvero_products', 'fp', 'fp.`id_product` = cp.`id_product`');

	    // Build WHERE clauses
	    $sql->where('cp.`id_cart` = ' . (int) $cart->id);

	    // Build ORDER BY
	    $sql->orderBy('cp.`date_add`, cp.`id_product` ASC');

        // Execute the QUERY
	    $result = Db::getInstance()->executeS($sql);

        // Check every product for is_finvero_product property.
      	foreach ($result as $row) {
        	if (!$row['is_finvero_product']){
          		$has_all_finvero_products = false;
        	}
      	}

      	return $has_all_finvero_products;
    }

    /**
    * Evaluates currency of shopping cart.
    *
    * @param $cart
    *
    * @return bool
    */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * - Send messages warning from Context of Server and PrestaShop configuration to Back End view.
    * - Update the form information from Data Base.
    * - Show TPL file with info about module.
    * - Save the form information to Data Base.
    *
    * @void
    *
    * @return $warning
    */
    public function getContent()
    {
        if ($warnings = $this->getWarnings()) {
            $this->html .= $this->displayError($warnings);
        }
        return  $this->display(__FILE__, 'infos.tpl') . $this->postProcess() . $this->getForm();
    }

    /**
    * Send messages warning to Back End view.
    *
    * @var $getAll bool
    *
    * @return $warning
    */
    public function getWarnings($getAll = true)
    {
        $warning = array();

        /* Check if SSL is enabled for Finvero's security compliance*/
        if (!Configuration::get('PS_SSL_ENABLED')) {
            $warning[] = $this->getTranslator()->trans('You must enable SSL on the store if you want to use this module in production.',
                [$this->name],
                'Modules.Finveromodule.Admin'
            );
        }

        if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE')) {
            $warning[] = $this->getTranslator()->trans('You have to enable non PrestaShop modules at ADVANCED PARAMETERS - PERFORMANCE',
                [],
                'Modules.Finveromodule.Admin'
            );
        }

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $warning[] = $this->getTranslator()->trans('Module is not enabled for any currency',
                [],
                'Modules.Finveromodule.Admin'
            );
        }

        if (count($warning) && !$getAll) {
            return $warning[0];
        }

        return $warning;
    }

    /**
    * Handles exceptions and displays message.
    *
    * @param FinveroException $exception
    *
    * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
    */
    private function handleException(FinveroException $exception)
    {
        $exceptionDictionary = [
            CannotCreateFinveroException::class => $this->getTranslator()->trans(
                'Failed to create a record for product',
                [],
                'Modules.Finveromodule.Admin'
            ),
            CannotToggleFinveroProductStatusException::class => $this->getTranslator()->trans(
                'Failed to toggle is finvero froduct status',
                [],
                'Modules.Finveromodule.Admin'
            ),
        ];

        $exceptionType = get_class($exception);

        if (isset($exceptionDictionary[$exceptionType])) {
            $message = $exceptionDictionary[$exceptionType];
        } else {
            $message = $this->getTranslator()->trans(
                'An unexpected error occurred. [%type% code %code%]',
                [
                    '%type%' => $exceptionType,
                    '%code%' => $exception->getCode(),
                ],
                'Admin.Notifications.Error'
            );
        }

        throw new \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException($message);
    }

    /**
    * Send message if update of information form is successfully.
    *
    * @var 
    *
    * @return $message
    */
    public function postProcess()
    {
        if (Tools::isSubmit('finvero')) {
            $api_key = Tools::getValue('api_key');
            $test_api_key = Tools::getValue('test_api_key');
            $life_mode = Tools::getValue('life_mode');
            Configuration::updateValue('FINVERO_API_KEY', $api_key);
            Configuration::updateValue('FINVERO_SANDBOX_API_KEY', $test_api_key);
            Configuration::updateValue('FINVERO_LIVE_MODE', $life_mode);
            $message = $this->displayConfirmation($this->getTranslator()->trans('Updated Successfully',
                [],
                'Modules.Finveromodule.Admin'
            ));
            return $message;
        }
    }

    /**
    * Create the configuration form in the Back End throught the HelperForm Class.
    *
    * @var 
    *
    * @return $form
    */
    public function getForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $this->context->controller->getLanguages();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $this->context->controller->default_form_language;
        $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
        $helper->title = $this->displayName;

        $helper->submit_action = 'finvero';
        $helper->fields_value['api_key'] = Configuration::get('FINVERO_API_KEY');
        $helper->fields_value['test_api_key'] = Configuration::get('FINVERO_SANDBOX_API_KEY');        
        $helper->fields_value['life_mode'] = Configuration::get('FINVERO_LIVE_MODE');
        
        $this->form[0] = array(
            'form' => array(

                'legend' => array(
                    'title' => $this->displayName,
                    'icon' => 'icon-envelope'
                 ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'life_mode',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode or sandbox'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Finvero Api Key'),
                        'desc' => $this->l('Put the API KEY proportionated by Finvero ').'<a href="https://developers.finvero.com/security/" target="_blank">https://developers.finvero.com/security/</a>',
                        'hint' => $this->l("If you haven't, signup in https://finvero.com/inscripcion/"),
                        'name' => 'api_key',
                        'lang' => false,
                     ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Finvero Test Api Key'),
                        'desc' => $this->l('Put the API KEY to the sandbox proportionated by Finvero ').'<a href="https://developers.finvero.com/security/" target="_blank">https://developers.finvero.com/security/</a>',
                        'hint' => $this->l("If you haven't, signup in https://finvero.com/inscripcion/"),
                        'name' => 'test_api_key',
                        'lang' => false,
                     ),                    
                 ),
                'submit' => array(
                    'title' => $this->l('Save')
                 )
             )
         );
        return $helper->generateForm($this->form);
    }
}

?>