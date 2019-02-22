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

# This storage only invalidates at CloudFront. It does not store anything
# and requires another storage to function. By default it uses the database storage.
wmcontroller.cache.cloudfront.backend.storage: wmcontroller.cache.storage.mysql
```
