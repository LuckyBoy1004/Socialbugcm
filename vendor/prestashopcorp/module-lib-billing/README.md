# prestashop-rbm-installer

Utility package to present data from psx configuration page.

## Installation

This package is available on [Packagist](https://packagist.org/packages/prestashopcorp/module-lib-billing),
you can install it via [Composer](https://getcomposer.org).

```shell script
composer require prestashopcorp/module-lib-billing
```

## Register as a service in your PSx container

Beforehand, you must have defined [PS Account services](https://github.com/PrestaShopCorp/prestashop-accounts-installer#register-as-a-service-in-your-psx-container-recommended)

Example :

```yaml
services:
  #####################
  # PS Billing

  ps_billings.facade:
    class: 'PrestaShopCorp\Billing\Presenter\BillingPresenter'
    arguments:
      - '@ps_accounts.facade'
      - '@rbm_example.module'
      - '@rbm_example.context'
```

## How to use it

### Presenter

For example in your main module's class `getContent` method.

```php
    Media::addJsDef($this->getService('ps_billings.facade')
        ->present([
            'sandbox' => true,
            'billingEnv' => 'preprod',
            'logo' => '//yoururl.com/logo.svg',
            'tosLink' => 'https://yoururl.com/tos',
            'emailSupport' => 'youremail@yourdomain.ltd',
        ])
    );
```

## Contribute

### Code style

```
php vendor/bin/php-cs-fixer fix
```

### Automatic tests

#### Install

Please follow theses steps to launch unit tests

```
# Needs to have wget, for OS without wget pleae see the official website (or just visit this link)
wget -O phpunit https://phar.phpunit.de/phpunit-5.phar

chmod +x phpunit

# Should display the version
./phpunit --version
```

#### Run

```
./phpunit tests
```
