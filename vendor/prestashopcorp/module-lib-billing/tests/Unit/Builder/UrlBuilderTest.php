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

namespace PrestaShopCorp\Billing\Tests\Unit\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShopCorp\Billing\Builder\UrlBuilder;

class UrlBuilderTest extends TestCase
{
    public function testBuildUIUrl()
    {
        $builder = new UrlBuilder();
        $this->assertEquals($builder->buildUIUrl('development'), null);
        $this->assertEquals($builder->buildUIUrl('integration'), 'https://billing.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle1'), 'https://billing-prestabulle1.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle2'), 'https://billing-prestabulle2.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle3'), 'https://billing-prestabulle3.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle4'), 'https://billing-prestabulle4.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle5'), 'https://billing-prestabulle5.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle6'), 'https://billing-prestabulle6.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle7'), 'https://billing-prestabulle7.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle8'), 'https://billing-prestabulle8.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('prestabulle9'), 'https://billing-prestabulle9.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildUIUrl('preprod'), 'https://billing.distribution-preprod.prestashop.net');
        $this->assertEquals($builder->buildUIUrl(), 'https://billing.distribution.prestashop.net');
    }

    public function testBuildAPIUrl()
    {
        $builder = new UrlBuilder();
        $this->assertEquals($builder->buildAPIUrl('development'), null);
        $this->assertEquals($builder->buildAPIUrl('development', 'https://www.w3schoo��ls.co�m'), 'https://www.w3schools.com');
        $this->assertEquals($builder->buildAPIUrl('development', 'https://www.w3schools.com'), 'https://www.w3schools.com');
        $this->assertEquals($builder->buildAPIUrl('integration'), 'https://billing-api.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle1'), 'https://billing-api-psbulle1.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle2'), 'https://billing-api-psbulle2.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle3'), 'https://billing-api-psbulle3.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle4'), 'https://billing-api-psbulle4.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle5'), 'https://billing-api-psbulle5.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle6'), 'https://billing-api-psbulle6.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle7'), 'https://billing-api-psbulle7.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle8'), 'https://billing-api-psbulle8.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('prestabulle9'), 'https://billing-api-psbulle9.distribution-integration.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl('preprod'), 'https://billing-api.distribution-preprod.prestashop.net');
        $this->assertEquals($builder->buildAPIUrl(), 'https://billing-api.distribution.prestashop.net');
    }
}
