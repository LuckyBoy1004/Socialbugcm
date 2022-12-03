<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShopCorp\Billing\Services;

use Module;
use PrestaShopCorp\Billing\Builder\UrlBuilder;
use PrestaShopCorp\Billing\Clients\BillingClient;
use PrestaShopCorp\Billing\Wrappers\BillingContextWrapper;

class BillingService
{
    /**
     * Created to make billing API request
     *
     * @var BillingClient
     */
    private $billingClient;

    /**
     * Created to make billing API request
     *
     * @var string
     */
    private $apiVersion;

    /**
     * @var BillingContextWrapper
     */
    private $billingContextWrapper;

    public function __construct(
        BillingContextWrapper $billingContextWrapper = null,
        Module $module,
        $apiVersion = BillingClient::DEFAULT_API_VERSION,
        $apiUrl = null
    ) {
        $this->setBillingContextWrapper($billingContextWrapper);

        $urlBuilder = new UrlBuilder();

        /*
         If you want to specify your own API URL you should edit the common.yml
         file with the following code

         ps_billings.context_wrapper:
           class: 'PrestaShopCorp\Billing\Wrappers\BillingContextWrapper'
           public: false
           arguments:
             - '@ps_accounts.facade'
             - '@rbm_example.context'
             - true # if true you are in sandbox mode, if false or empty not in sandbox
             - 'development'

         ps_billings.service:
           class: PrestaShopCorp\Billing\Services\BillingService
           public: true
           arguments:
             - '@ps_billings.context_wrapper'
             - '@rbm_example.module'
             - 'v1'
             - 'http://host.docker.internal:3000'
        */
        $this->setBillingClient(new BillingClient(
            $module->name,
            null,
            $urlBuilder->buildAPIUrl($this->getBillingContextWrapper()->getBillingEnv(), $apiUrl),
            $this->getBillingContextWrapper()->getAccessToken(),
            $this->getBillingContextWrapper()->isSandbox()
        ));
        $this->setApiVersion($apiVersion);
    }

    /**
     * Retrieve the Billing customer associated with the shop
     * on which your module is installed
     *
     * @return array
     */
    public function getCurrentCustomer()
    {
        return $this->getBillingClient()->retrieveCustomerById($this->getBillingContextWrapper()->getShopUuid(), $this->getApiVersion());
    }

    /**
     * Retrieve the Billing subscription associated with the shop
     * on which your module is installed
     *
     * @return array
     */
    public function getCurrentSubscription()
    {
        return $this->getBillingClient()->retrieveSubscriptionByCustomerId($this->getBillingContextWrapper()->getShopUuid(), $this->getApiVersion());
    }

    /**
     * Retrieve the plans associated to this module
     *
     * @return array
     */
    public function getModulePlans()
    {
        return $this->getBillingClient()->retrievePlans($this->getBillingContextWrapper()->getLanguageIsoCode(), $this->getApiVersion());
    }

    /**
     * setApiVersion
     *
     * @param string $apiVersion
     *
     * @return void
     */
    public function setApiVersion(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * getApiVersion
     *
     * @return string
     */
    private function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * setBillingClient
     *
     * @param BillingClient $billingClient
     *
     * @return void
     */
    public function setBillingClient(BillingClient $billingClient)
    {
        $this->billingClient = $billingClient;
    }

    /**
     * getBillingClient
     *
     * @return BillingClient
     */
    private function getBillingClient()
    {
        return $this->billingClient;
    }

    /**
     * setBillingContextWrapper
     *
     * @param BillingContextWrapper $billingContextWrapper
     *
     * @return void
     */
    private function setBillingContextWrapper($billingContextWrapper)
    {
        $this->billingContextWrapper = $billingContextWrapper;
    }

    /**
     * getBillingContextWrapper
     *
     * @return BillingContextWrapper
     */
    private function getBillingContextWrapper()
    {
        return $this->billingContextWrapper;
    }
}
