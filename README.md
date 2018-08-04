# middlewares/referrer-spam

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to block referrer spammers using [piwik/referrer-spam-blacklist](https://github.com/matomo-org/referrer-spam-blacklist). It returns a 403 response if the url host in the `Referer` header is in the blacklist.

## Requirements

* PHP >= 7.0
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)
* `ext-intl` PHP extension or [true/punycode](https://github.com/true/php-punycode) as alternative

## Installation

This package is installable and autoloadable via Composer as [middlewares/referrer-spam](https://packagist.org/packages/middlewares/referrer-spam).

```sh
composer require middlewares/referrer-spam
```

## Example

```php
$dispatcher = new Dispatcher([
    new Middlewares\ReferrerSpam()
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `__construct(array $blackList = null)`

Allow to configure a custom spam list if you don't want to use the piwik's one.

#### `responseFactory(Psr\Http\Message\ResponseFactoryInterface $responseFactory)`

A PSR-17 factory to create `403` responses.
---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/referrer-spam.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/referrer-spam/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/referrer-spam.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/referrer-spam.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/20172f03-763a-4367-9168-4a7f88dbb5a1.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/referrer-spam
[link-travis]: https://travis-ci.org/middlewares/referrer-spam
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/referrer-spam
[link-downloads]: https://packagist.org/packages/middlewares/referrer-spam
[link-sensiolabs]: https://insight.sensiolabs.com/projects/20172f03-763a-4367-9168-4a7f88dbb5a1
