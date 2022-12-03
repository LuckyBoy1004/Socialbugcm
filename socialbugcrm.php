<?php
/**
 * @author Socialbug Team <support@mlm-socialbug.com>
 * @copyright 2020 Km Innovations Inc DBA SocialBug
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require 'vendor/autoload.php';
use PrestaShop\PsAccountsInstaller\Installer\Exception\ModuleVersionException;
use PrestaShop\PsAccountsInstaller\Installer\Exception\ModuleNotInstalledException;

class Socialbugcrm extends Module
{
    const DEFAULT_SALT = '$alt';

    private $emailSupport;

    /**
     * @var ServiceContainer
     */
    private $container;

    public function __construct()
    {
        $this->name = 'socialbugcrm';
        $this->tab = 'others';
        $this->version = '1.0.4';
        $this->author = 'SocialBug Team';
        $this->emailSupport = 'support@prestashop.com';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7.8.9');
        $this->bootstrap = true;
        $this->module_key = '4a59708648875ef40ba62e900aafd024';

        parent::__construct();

        $this->displayName = $this->l('SocialBugCRM');
        $this->description = $this->l('Integration module to connect with SocialBugCRM platform.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->uri_path = Tools::substr($this->context->link->getBaseLink(null, null, true), 0, -1);
        $this->images_dir = $this->uri_path . $this->getPathUri() . 'views/img/';
        $this->template_dir = $this->getLocalPath() . 'views/templates/admin/';

    }

    /**
     * Retrieve service
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        if ($this->container === null) {
            $this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }
        return $this->container->getService($serviceName);
    }

    public function install()

    {

        if (Shop::isFeatureActive()) {

                Shop::setContext(Shop::CONTEXT_ALL);

        }

 

        if (!parent::install()

            || !$this->registerHook('moduleRoutes')

            || !$this->registerHook('displayHeader')

            || !Configuration::updateValue('SOCIALBUGCRM_ApiKey', '')

            || !Configuration::updateValue('SOCIALBUGCRM_Salt', '')

            || !Configuration::updateValue('SOCIALBUGCRM_CreatedOnUtc', '')

            || !Configuration::updateValue('SOCIALBUGCRM_UserId', '')

            || !Configuration::updateValue('SOCIALBUGCRM_AppendHtml', '')

            || !$this->getService('ps_accounts.installer')->install()

        ) {

                return false;

        }

 

        return true;

    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('SOCIALBUGCRM_ApiKey')
            || !Configuration::deleteByName('SOCIALBUGCRM_Salt')
            || !Configuration::deleteByName('SOCIALBUGCRM_CreatedOnUtc')
            || !Configuration::deleteByName('SOCIALBUGCRM_UserId')
            || !Configuration::deleteByName('SOCIALBUGCRM_AppendHtml')
        ) {
                return false;
        }

        return true;
    }

    /**
     * Get the Tos URL from the context language, if null, send default link value
     *
     * @return string
     */
    public function getTosLink($iso_lang)
    {
        switch ($iso_lang) {
            case 'fr':
                $url = 'https://socialbug.io/conditions-of-use';
                break;
            default:
                $url = 'https://socialbug.io/conditions-of-use';
                break;
        }

        return $url;
    }

    /**
     * Get the Tos URL from the context language, if null, send default link value
     *
     * @return string
     */
    public function getPrivacyLink($iso_lang)
    {
        switch ($iso_lang) {
            case 'fr':
                $url = 'https://socialbug.io/privacy-notice';
                break;
            default:
                $url = 'https://socialbug.io/privacy-notice';
                break;
        }

        return $url;
    }

    private $origin;

    public function hookDisplayHeader($params)
    {
        $customerId = "";
        $appendHtml = Configuration::get('SOCIALBUGCRM_AppendHtml');

        if ($this->context->customer->isLogged(true)) {
            $salt = Configuration::get('SOCIALBUGCRM_Salt');
            $userId = $this->context->customer->id;
    
            $str = $userId.$salt;
            $customerId = $userId.'~'.md5($str);
        }
    
        $appendHtml = str_replace("%customerId%", $customerId, $appendHtml);
    
        if (isset($appendHtml) && $appendHtml != false) {
            $this->context->controller->registerJavascript(
                sha1($appendHtml),
                $appendHtml,
                array('position' => 'bottom', 'priority' => 100, 'server' => 'remote')
            );
        }
    }

    public function hookModuleRoutes()
    {
        return array(
            'module-socialbugcrm-api' => array(
                'controller' => 'api',
                'rule' => 'socialbugcrm/api{/:module_action}{/:id}',
                'keywords' => array(
                    'id' => array(
                        'regexp' => '[\d]+',
                        'param' => 'id'
                    ),
                    'module_action' => array(
                        'regexp' => '[\w]+',
                        'param' => 'module_action'
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'socialbugcrm',
                    'controller' => 'api'
                )
            )
        );
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $apiKey = Configuration::get('SOCIALBUGCRM_ApiKey');
            $salt = Configuration::get('SOCIALBUGCRM_Salt');
            $userId = Configuration::get('SOCIALBUGCRM_UserId');

            if ($salt == null) {
                $userId = $this->context->employee->id;
                $salt = $this->generateRandomString();
                
                $r1 = mt_rand(0, 65535);
                $r2 = mt_rand(0, 65535);
                $r3 = mt_rand(0, 65535);
                $r4 = mt_rand(16384, 20479);
                $r5 = mt_rand(32768, 49151);
                $r6 = mt_rand(0, 65535);
                $r7 = mt_rand(0, 65535);
                $r8 = mt_rand(0, 65535);

                $apiKey = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', $r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8);
                $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
                Configuration::updateValue('SOCIALBUGCRM_CreatedOnUtc', $date_utc->format("Y-m-d H:i:s e"));
            }

            Configuration::updateValue('SOCIALBUGCRM_ApiKey', $apiKey);
            Configuration::updateValue('SOCIALBUGCRM_Salt', $salt);
            Configuration::updateValue('SOCIALBUGCRM_UserId', $userId);
            $back = 'https://socialbugcrm.sb-affiliate.com';
            $path = '/api/gateway/stores/public/installation/prestashop/Install/11?authorization_code=';

            Tools::redirectLink($back.$path.$apiKey.'&site='._PS_BASE_URL_);
        }

        $accountsInstaller = $this->getService('ps_accounts.installer');
        $accountsInstaller->install();

            $output = '';

            try {
                    // retrieve the value set by the user
                $configValue = (string) Tools::getValue('MYMODULE_CONFIG');
        
                // check that the value is valid
                if (empty($configValue) || !Validate::isGenericName($configValue)) {
                    // invalid value, show an error
                    $output = $this->displayError($this->l('Invalid Configuration value'));
                } else {
                    // value is ok, update it and display a confirmation message
                    Configuration::updateValue('MYMODULE_CONFIG', $configValue);
                    $output = $this->displayConfirmation($this->l('Settings updated'));
                }

                $accountsFacade = $this->getService('ps_accounts.facade');
                $accountsService = $accountsFacade->getPsAccountsService();
                Media::addJsDef([
                    'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                        ->present($this->name),
                ]);

                // Retrieve Account CDN
                $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());

                $billingFacade = $this->getService('ps_billings.facade');
                $partnerLogo = $this->getLocalPath() . 'views/img/partnerLogo.png';

                // Billing
                Media::addJsDef($billingFacade->present([
                    'sandbox' => true,
                    'billingEnv' => 'preprod',
                    'logo' => $partnerLogo,
                    'tosLink' => $this->getTosLink($this->context->language->iso_code),
                    'privacyLink' => $this->getPrivacyLink($this->context->language->iso_code),
                    'emailSupport' => $this->emailSupport,
                ]));

                Media::addJsDef([
                    'storePsSocialbugcrm' => [
                        'context' => array_merge($billingFacade->present([
                            'versionPs' => _PS_VERSION_,
                            'versionModule' => $this->version,
                            'moduleName' => $this->name,
                            'emailSupport' => $this->emailSupport,
                            'ipAddress' => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '',
                            'sandbox' => true,
                            'billingEnv' => 'preprod',
                            'logo' => $partnerLogo,
                            'tosLink' => $this->getTosLink($this->context->language->iso_code),
                            'privacyLink' => $this->getPrivacyLink($this->context->language->iso_code),
                        ]),
                        [
                            // 'quantity' => 15000,
                            //'isSandbox' => true,
                            // 'planIdSelected' => null,
                            // 'byPassSelection' => true,
                        ]
                        )
                    ]
                ]);

                $this->context->smarty->assign('pathVendor', $this->getPathUri() . 'views/js/chunk-vendors-socialbugcrm.' . $this->version . '.js');
                $this->context->smarty->assign('pathApp', $this->getPathUri() . 'views/js/app-socialbugcrm.' . $this->version . '.js');
    
                // Load service for PsBilling
                $billingService = $this->getService('ps_billings.service');

                // Retrieve the customer
                $customer = $billingService->getCurrentCustomer();

                // Retrieve the subscritpion for this module
                $subscription = $billingService->getCurrentSubscription();

                // Retrieve the list and description for module plans
                $plans = $billingService->getModulePlans();
                $this->context->smarty->assign('customer', $customer);
                $this->context->smarty->assign('subscription', $subscription);
                $this->context->smarty->assign('plans', $plans);

            } catch (Exception $e) {
                $this->context->controller->errors[] = $e->getMessage();

                return '';
            }
        return $this->context->smarty->fetch($this->template_dir . 'socialbugcrm.tpl');
    }

    public function displayForm()
    {
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'submit' => array(
                'title' => $this->l('Launch App'),
                'class' => 'btn btn-default pull-left',
                'icon' => 'process-icon-refresh'
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->submit_action = 'submit'.$this->name;

        return $helper->generateForm($fields_form);
    }
}
