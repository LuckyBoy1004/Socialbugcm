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

namespace PrestaShopCorp\Billing\Presenter;

use Module;
use PrestaShopCorp\Billing\Builder\EnvBuilder;
use PrestaShopCorp\Billing\Builder\UrlBuilder;
use PrestaShopCorp\Billing\Exception\BillingContextException;
use PrestaShopCorp\Billing\Wrappers\BillingContextWrapper;

class BillingPresenter
{
    /**
     * @var EnvBuilder
     */
    private $envBuilder;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var BillingContextWrapper
     */
    private $billingContextWrapper;

    /**
     * @var \Module
     */
    private $module;

    /**
     * Presenter constructor.
     *
     * @param \Module $module
     * @param PsAccounts $accountFacade
     * @param \Context|null $context
     */
    public function __construct(
        BillingContextWrapper $billingContextWrapper = null,
        Module $module
    ) {
        $this->setModule($module);

        $this->setEnvBuilder(new EnvBuilder());
        $this->setUrlBuilder(new UrlBuilder());

        $this->setBillingContextWrapper($billingContextWrapper);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function present(array $params)
    {
        $this->validateContextArgs($params);

        $getEnv = $this->getBillingContextWrapper()->getBillingEnv() ?: '';
        $billingEnv = $this->getEnvBuilder()->buildBillingEnv($getEnv);

        return [
            'psBillingContext' => [
                'context' => [
                    'billingEnv' => $billingEnv,
                    'billingUIUrl' => $this->getUrlBuilder()->buildUIUrl($billingEnv),
                    'isSandbox' => $this->getBillingContextWrapper()->isSandbox()
                        ? $this->getBillingContextWrapper()->isSandbox()
                        : false,

                    'versionPs' => _PS_VERSION_,
                    'versionModule' => $this->getModule()->version,
                    'moduleName' => $this->getModule()->name,
                    'displayName' => $this->getModule()->displayName,

                    'i18n' => [
                        'isoCode' => $this->getBillingContextWrapper()->getLanguageIsoCode(),
                    ],

                    'refreshToken' => $this->getBillingContextWrapper()->getRefreshToken(),
                    'shop' => [
                        'uuid' => $this->getBillingContextWrapper()->getShopUuid(),
                    ],
                    'user' => [
                        'createdFromIp' => \Tools::getRemoteAddr(),
                        'email' => $this->getBillingContextWrapper()->getEmail(),
                    ],

                    'moduleLogo' => $this->encodeImage($this->getModuleLogo()),
                    'partnerLogo' => !empty($params['logo']) ? $this->encodeImage($params['logo']) : '',
                    'moduleTosUrl' => !empty($params['tosLink']) ? $params['tosLink'] : '',
                    'modulePrivacyUrl' => !empty($params['privacyLink']) ? $params['privacyLink'] : '',
                    'emailSupport' => !empty($params['emailSupport']) ? $params['emailSupport'] : '',
                ],
            ],
        ];
    }

    /**
     * Validate the args pass to the method "present" above
     *
     * @param mixed $params
     *
     * @return void
     *
     * @throws BillingContextException when some data are missing
     */
    private function validateContextArgs(array $params)
    {
        if (empty($params['emailSupport'])) {
            throw new BillingContextException('"emailSupport" must be provided (value=' . $params['emailSupport'] . ')');
        }
        if (!\Validate::isEmail($params['emailSupport'])) {
            throw new BillingContextException('"emailSupport" must be a valid email (value=' . $params['emailSupport'] . ')');
        }
        if (empty($params['tosLink'])) {
            throw new BillingContextException('"tosLink" must be provided (value=' . $params['tosLink'] . ')');
        }
        if (!\Validate::isAbsoluteUrl($params['tosLink'])) {
            throw new BillingContextException('"tosLink" must be a valid url (value=' . $params['tosLink'] . ')');
        }
        if (empty($params['privacyLink'])) {
            throw new BillingContextException('"privacyLink" must be provided (value=' . $params['privacyLink'] . ')');
        }
        if (!\Validate::isAbsoluteUrl($params['privacyLink'])) {
            throw new BillingContextException('"privacyLink" must be a valid url (value=' . $params['privacyLink'] . ')');
        }
    }

    /**
     * @return string
     */
    private function encodeImage(string $image_path)
    {
        $mime_type = $this->getMimeTypeByExtension($image_path);
        if ($mime_type === null) {
            return $mime_type;
        }

        $image_content = \Tools::file_get_contents($image_path);

        return 'data:' . $mime_type . ';base64,' . base64_encode($image_content);
    }

    /**
     * Return the mime type by the file extension.
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getMimeTypeByExtension($fileName)
    {
        $types = [
            'image/gif' => ['gif'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/webp' => ['webp'],
            'image/svg+xml' => ['svg'],
        ];
        $extension = substr($fileName, strrpos($fileName, '.') + 1);

        $mimeType = null;
        foreach ($types as $mime => $exts) {
            if (in_array($extension, $exts)) {
                $mimeType = $mime;
                break;
            }
        }

        return $mimeType;
    }

    /**
     * @return string
     */
    private function getModuleLogo()
    {
        if (@filemtime($this->getModule()->getLocalPath() . 'logo.png')) {
            return $this->getModule()->getLocalPath() . 'logo.png';
        }

        return $this->getModule()->getLocalPath() . 'logo.gif';
    }

    /**
     * setEnvBuilder
     *
     * @param EnvBuilder $envBuilder
     *
     * @return void
     */
    private function setEnvBuilder($envBuilder)
    {
        $this->envBuilder = $envBuilder;
    }

    /**
     * getEnvBuilder
     *
     * @return EnvBuilder
     */
    private function getEnvBuilder()
    {
        return $this->envBuilder;
    }

    /**
     * setUrlBuilder
     *
     * @param UrlBuilder $urlBuilder
     *
     * @return void
     */
    private function setUrlBuilder($urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * getUrlBuilder
     *
     * @return UrlBuilder
     */
    private function getUrlBuilder()
    {
        return $this->urlBuilder;
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

    /**
     * setModule
     *
     * @param \Module $module
     *
     * @return void
     */
    private function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * getModule
     *
     * @return \Module
     */
    private function getModule()
    {
        return $this->module;
    }
}
