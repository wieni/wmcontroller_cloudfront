# CloudFront Invalidator

This is a CloudFront invalidator for [wieni/wmcontroller](https://github.com/wieni/wmcontroller)

## Installation

```bash
composer require wieni/wmcontroller_cloudfront
cd public
drush en wmcontroller_cloudfront
```

```yaml
// services.yml
wmcontroller.cache.cloudfront:
    distributionId: ''
    accessKey: ''
    secret: ''

wmcontroller.cache.purger: wmcontroller.cache.purger.cloudfront
```