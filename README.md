# middlewares/referrer-spam

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to block referrer spammers using [matomo/referrer-spam-blacklist](https://github.com/matomo-org/referrer-spam-blacklist). It returns a `403` response if the url host in the `Referer` header is in the blacklist.

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)
* `ext-intl` PHP extension or [true/punycode](https://github.com/true/php-punycode) as alternative

## Installation

This package is installable and autoloadable via Composer as [middlewares/referrer-spam](https://packagist.org/packages/middlewares/referrer-spam).

```sh
composer require middlewares/referrer-spam
```

## Usage

By default, use `matomo/referrer-spam-blacklist` as a list of spammers

```php
$spam = new Middlewares\ReferrerSpam();
```

But you can configure a custom spam list if you don't want to use the default:

```php
$spammers = [
    'http://www.0n-line.tv',
    'http://холодныйобзвон.рф',
];

$spam = new Middlewares\ReferrerSpam($spammers);
```

Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` as the second argument to create the error responses (`403`). If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$responseFactory = new MyOwnResponseFactory();

$spam = new Middlewares\ReferrerSpam($spammers, $responseFactory);
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/referrer-spam.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/referrer-spam/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/referrer-spam.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/referrer-spam
[link-downloads]: https://packagist.org/packages/middlewares/referrer-spam
