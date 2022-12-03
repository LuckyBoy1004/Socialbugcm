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
use PrestaShopCorp\Billing\Clients\Handler\HttpResponseHandler;

/**
 * Construct the client used to make call to maasland.
 */
abstract class GenericClient
{
    /**
     * If set to false, you will not be able to catch the error
     * guzzle will show a different error message.
     *
     * @var bool
     */
    protected $catchExceptions = false;

    /**
     * Guzzle Client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Api route.
     *
     * @var string
     */
    protected $route;

    /**
     * Set how long guzzle will wait a response before end it up.
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * Version of the API
     *
     * @var string
     */
    protected $apiVersion;

    /**
     * GenericClient constructor.
     */
    public function __construct()
    {
    }

    /**
     * Wrapper of method post from guzzle client.
     *
     * @param array $options payload
     *
     * @return array return response or false if no response
     */
    protected function get(array $options = [])
    {
        $response = $this->getClient()->get($this->concatApiVersionAndRoute(), $options);
        $responseHandler = new HttpResponseHandler();

        return $responseHandler->handleResponse($response);
    }

    /**
     * Setter for client.
     *
     * @return void
     */
    protected function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Setter for exceptions mode.
     *
     * @param bool $bool
     *
     * @return void
     */
    protected function setCatchExceptions($bool)
    {
        $this->catchExceptions = $bool;
    }

    /**
     * Setter for route.
     *
     * @param string $route
     *
     * @return void
     */
    protected function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Setter for timeout.
     *
     * @param int $timeout
     *
     * @return void
     */
    protected function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Setter for apiVersion.
     *
     * @param string $apiVersion
     *
     * @return void
     */
    protected function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * Getter for exceptions mode.
     *
     * @return bool
     */
    protected function isCatchExceptions()
    {
        return $this->catchExceptions;
    }

    /**
     * Getter for client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Getter for route.
     *
     * @return string
     */
    protected function getRoute()
    {
        return $this->route;
    }

    /**
     * Getter for timeout.
     *
     * @return int
     */
    protected function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Getter for apiVersion.
     *
     * @return string
     */
    protected function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Add api version at the beginning of the route if set
     *
     * @return string
     */
    private function concatApiVersionAndRoute()
    {
        if ($this->getApiVersion()) {
            return $this->getApiVersion() . $this->getRoute();
        }

        return $this->getRoute();
    }
}
