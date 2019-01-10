# Introduction

[![Latest Version](https://img.shields.io/packagist/v/lemonphp/webhook-shield.svg)](https://packagist.org/packages/lemonphp/webhook-shield)
[![Software License](https://img.shields.io/github/license/lemonphp/webhook-shield.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/lemonphp/webhook-shield/master.svg)](https://travis-ci.org/lemonphp/webhook-shield)
[![Coverage Status](https://img.shields.io/coveralls/github/lemonphp/webhook-shield/master.svg)](https://coveralls.io/github/lemonphp/webhook-shield?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/lemonphp/webhook-shield.svg)](https://packagist.org/packages/lemonphp/webhook-shield)
[![Requires PHP](https://img.shields.io/travis/php-v/lemonphp/webhook-shield.svg)](https://travis-ci.org/lemonphp/webhook-shield)

Protects against unverified webhooks from 3rd party services.

## Features

* Compatible with [PSR-7], [PSR-15] and [PSR-17]
* No dependencies
* Supported drivers
   - [x] Bitbucket
   - [x] Facebook
   - [x] Github
   - [x] Gitlab
   - [x] Mailgun
   - [x] Shopify
   - [x] Trello
* Coding style follow [PSR-2], [PSR-4]

## Requirements

* php >=7.1

## Installation

Begin by pulling in the package through Composer.

```bash
$ composer require lemonphp/webhook-shield
```

## Usage

### LemonPHP Framework

### Zend Expressive

```php
// TODO
```

### Slim 3

```php
// TODO
```

### Lumen

```php
// TODO
```

## Changelog

See all change logs in [CHANGELOG](CHANGELOG.md)

## Testing

```bash
$ git clone git@github.com/lemonphp/webhook-shield.git /path
$ cd /path
$ composer install
$ composer phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email to [Oanh Nguyen](mailto:oanhnn.bk@gmail.com) instead of 
using the issue tracker.

## Credits

- [Oanh Nguyen](https://github.com/oanhnn)
- [All Contributors](../../contributors)

## License

This project is released under the MIT License.   
Copyright © 2019 LemonPHP Team.


[PSR-2]:  https://www.php-fig.org/psr/psr-2
[PSR-4]:  https://www.php-fig.org/psr/psr-4
[PSR-7]:  https://www.php-fig.org/psr/psr-7
[PSR-15]: https://www.php-fig.org/psr/psr-15
[PSR-17]: https://www.php-fig.org/psr/psr-17
