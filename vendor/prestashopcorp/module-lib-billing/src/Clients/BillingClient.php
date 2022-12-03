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

namespace PrestaShopCorp\Billing\Clients;

use GuzzleHttp\Client;

/**
 * BillingClient low level client to access to billing API routes
 */
class BillingClient extends GenericClient
{
    const DEFAULT_API_VERSION = 'v1';

    /**
     * Constructor with parameters
     *
     * @return void
     */
    public function __construct(
        $moduleName,
        $client = null,
        $apiUrl = null,
        $token = null,
        $isSandbox = null
    ) {
        parent::__construct();

        // Client can be provided for tests or some specific use case
        if (!isset($client) || null === $client) {
            $clientParams = [
                'base_url' => $apiUrl,
                'defaults' => [
                    'timeout' => $this->timeout,
                    'exceptions' => $this->catchExceptions,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $token,
                        'User-Agent' => 'module-lib-billing (' . $moduleName . ')',
                    ],
                ],
            ];
            if (true === $isSandbox) {
                $clientParams['defaults']['headers']['Sandbox'] = 'true';
            }
            $client = new Client($clientParams);
        }
        $this->setClient($client);
        $this->setModuleName($moduleName);
    }

    /**
     * retrieveCustomerById
     *
     * @param string $customerId the shop id
     * @param string $apiVersion version of API to use (default: "v1")
     *
     * @return array with success (bool), httpStatus (int), body (array) extract from the response
     */
    public function retrieveCustomerById(string $customerId, $apiVersion = self::DEFAULT_API_VERSION)
    {
        $this->setApiVersion($apiVersion);
        $this->setRoute('/customers/' . $customerId);

        return $this->get();
    }

    /**
     * Retrieve the subscription of the customer for your module
     *
     * @param string $customerId the shop id
     * @param string $apiVersion version of API to use (default: "v1")
     *
     * @return array with success (bool), httpStatus (int), body (array) extract from the response
     */
    public function retrieveSubscriptionByCustomerId(string $customerId, $apiVersion = self::DEFAULT_API_VERSION)
    {
        $this->setApiVersion($apiVersion);
        $this->setRoute('/customers/' . $customerId . '/subscriptions/' . $this->getModuleName());

        return $this->get();
    }

    /**
     * Retrieve plans associated with the module
     *
     * @param string $lang the lang of the user
     * @param string $apiVersion version of API to use (default: "v1")
     * @param string $status whether you want to get only "active" plan, or the "archived", or both when set to null  (default: "active")
     * @param string $limit number of plan to return (default: "10")
     * @param string $offset pagination start (default: null)
     *
     * @return array with success (bool), httpStatus (int), body (array) extracted from the response
     */
    public function retrievePlans(string $lang, $apiVersion = self::DEFAULT_API_VERSION, $status = 'active', $limit = '10', $offset = null)
    {
        $this->setApiVersion($apiVersion);
        $this->setRoute('/products/' . $this->getModuleName() . '/plans?status=' . $status . '&lang_iso_code=' . $lang . '&limit=' . $limit . ($offset ? '&offset=' . $offset : ''));

        return $this->get();
    }

    /**
     * Technical name of the module
     *
     * @var string
     */
    private $moduleName;

    /**
     * Getter for moduleName
     *
     * @return string
     */
    private function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Setter for moduleName
     *
     * @param string $moduleName
     *
     * @return void
     */
    private function setModuleName(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }
}
