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

namespace PrestaShopCorp\Billing\Builder;

class UrlBuilder
{
    /**
     * @return string
     */
    public function buildUIUrl($envName = null)
    {
        switch ($envName) {
            case 'development':
                // Handle by .env in Billing UI
                return null;
            case 'integration':
                return 'https://billing.distribution-' . $envName . '.prestashop.net';
                break;
            case 'prestabulle1':
            case 'prestabulle2':
            case 'prestabulle3':
            case 'prestabulle4':
            case 'prestabulle5':
            case 'prestabulle6':
            case 'prestabulle7':
            case 'prestabulle8':
            case 'prestabulle9':
                return 'https://billing-' . $envName . '.distribution-integration.prestashop.net';
                break;
            case 'preprod':
                return 'https://billing.distribution-' . $envName . '.prestashop.net';
                break;
            default:
                return 'https://billing.distribution.prestashop.net';
        }
    }

    /**
     * buildAPIUrl
     *
     * @param string $envName
     * @param string $apiUrl
     *
     * @return string
     */
    public function buildAPIUrl($envName = null, $apiUrl = null)
    {
        switch ($envName) {
            case 'development':
                return $apiUrl ? filter_var($apiUrl, FILTER_SANITIZE_URL) : null;
            case 'integration':
                return 'https://billing-api.distribution-' . $envName . '.prestashop.net';
                break;
            case 'prestabulle1':
            case 'prestabulle2':
            case 'prestabulle3':
            case 'prestabulle4':
            case 'prestabulle5':
            case 'prestabulle6':
            case 'prestabulle7':
            case 'prestabulle8':
            case 'prestabulle9':
                return 'https://billing-api-' . str_replace('prestabulle', 'psbulle', $envName) . '.distribution-integration.prestashop.net';
                break;
            case 'preprod':
                return 'https://billing-api.distribution-' . $envName . '.prestashop.net';
                break;
            default:
                return 'https://billing-api.distribution.prestashop.net';
        }
    }
}
