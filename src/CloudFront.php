<?php

namespace Drupal\wmcontroller_cloudfront;

use Aws\Credentials\Credentials;
use Aws\Sdk;
use Drupal\wmcontroller\Entity\Cache;
use Drupal\wmcontroller\Service\Cache\Purger\PurgerInterface;

class CloudFront implements PurgerInterface
{
    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function purge(array $items)
    {
        $this->invalidate($items);
        return true;
    }

    public function flush()
    {
        $this->purgeCDN(['/', '/*']);
    }

    private function invalidate(array $items)
    {
        $paths = array_map([$this, 'getPath'], $items);

        if (!$paths) {
            return;
        }

        $this->purgeCDN($paths);
    }

    private function getPath(Cache $item)
    {
        return parse_url($item->getUri(), PHP_URL_PATH);
    }

    private function purgeCDN(array $paths)
    {
        $distributionId = $this->config['distributionId'];
        $accessKey = $this->config['accessKey'];
        $secret = $this->config['secret'];

        $client = (new Sdk([
            'region' => 'us-east-1',
            'version' => '2017-03-25',
            'credentials' => new Credentials($accessKey, $secret)
        ]))->createCloudFront();

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