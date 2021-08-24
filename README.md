wmcontroller_cloudfront
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmcontroller_cloudfront/v/stable)](https://packagist.org/packages/wieni/wmcontroller_cloudfront)
[![Total Downloads](https://poser.pugx.org/wieni/wmcontroller_cloudfront/downloads)](https://packagist.org/packages/wieni/wmcontroller_cloudfront)
[![License](https://poser.pugx.org/wieni/wmcontroller_cloudfront/license)](https://packagist.org/packages/wieni/wmcontroller_cloudfront)

> An [Amazon CloudFront](https://aws.amazon.com/cloudfront) cache invalidator for [wieni/wmcontroller](https://github.com/wieni/wmcontroller)

## Installation

This package requires PHP 8.0 and Drupal 9.1 or higher. It can be
installed using Composer:

```bash
 composer require wieni/wmcontroller_cloudfront
```

To enable this cache invalidator, change the `wmcontroller.cache.purger` container parameter:
```yaml
parameters:
    wmcontroller.cache.cloudfront:
        distributionId: ''
        accessKey: ''
        secret: ''
    
    wmcontroller.cache.storage: wmcontroller.cache.storage.cloudfront
    
    # This storage only invalidates at CloudFront. It does not store anything
    # and requires another storage to function. By default it uses the database storage.
    wmcontroller.cache.cloudfront.backend.storage: wmcontroller.cache.storage.mysql
```

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE) file
for more information.
