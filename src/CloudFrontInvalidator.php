<?php

namespace Drupal\wmcontroller_cloudfront;

use Aws\Credentials\Credentials;
use Aws\Sdk;

class CloudFrontInvalidator
{
    /** @var array */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function invalidate(array $paths)
    {
        if (!$paths) {
            return;
        }

        $this->purgeCDN($paths);
    }

    protected function purgeCDN(array $paths)
    {
        $distributionId = $this->config['distributionId'];
        $accessKey = $this->config['accessKey'];
        $secret = $this->config['secret'];

        if (empty($distributionId) || empty($accessKey) || empty($secret)) {
            // Todo: We should scream and shout exceptions
            // (But I don't want to deal with broken/incomplete/missing .env files)
            // Sue me
            return null;
        }

        $client = (new Sdk([
            'region' => 'us-east-1',
            'version' => '2017-03-25',
            'credentials' => new Credentials($accessKey, $secret)
        ]))->createCloudFront();

        // Todo: what if we are already invalidating >= 3000 paths and
        // cloudfront takes it no more.
        $result = $client->createInvalidation([
            'DistributionId' => $distributionId,
            'InvalidationBatch' => [
                'CallerReference' => sha1(uniqid('', true) . '-' . mt_rand(0, 10000000)),
                'Paths' => [
                    'Items' => $paths,
                    'Quantity' => count($paths),
                ],
            ],
        ])->toArray();

        return $result;
    }
}
