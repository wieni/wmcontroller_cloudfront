<?php

namespace Drupal\wmcontroller_cloudfront;

use Drupal\wmcontroller\Entity\Cache;
use Drupal\wmcontroller\Service\Cache\Storage\StorageInterface;

class CloudFront implements StorageInterface
{
    /** @var \Drupal\wmcontroller_cloudfront\CloudFrontInvalidator */
    protected $invalidator;
    /** @var \Drupal\wmcontroller\Service\Cache\Storage\StorageInterface */
    protected $storage;

    protected $concurrent = 50;
    protected $flushing = false;

    public function __construct(
        CloudFrontInvalidator $invalidator,
        StorageInterface $storage
    ) {
        $this->invalidator = $invalidator;
        $this->storage = $storage;
    }

    public function remove(array $ids)
    {
        $this->invalidate($ids);
        $this->storage->remove($ids);
    }

    public function flush()
    {
        // Flag that we are flushing. It's possible that the backend storage
        // calls ::remove() a bunch of times to clear. We don't want to be
        // invalidating those calls. When all is well and done we'll do a
        // mass-invalidation of all pages with "/*"
        $this->flushing = true;

        $this->storage->flush();

        $this->flushing = false;

        $this->invalidator->invalidate(['/*']);
    }

    protected function invalidate(array $ids)
    {
        // If we are flushing, don't invalidate. Once we're done flushing we'll
        // do a single mass-invalidation. See the ::flush() method.
        if ($this->flushing) {
            return;
        }

        // Invalidate in a foreach loop so we can leverage generators and
        // play nice with our memory when invalidating a whole bunch of items.
        //
        // Even though CloudFront does max 3000 concurrent invalidations..
        $paths = [];
        foreach ($this->storage->loadMultiple($ids, false) as $item) {
            $paths[] = parse_url($item->getUri(), PHP_URL_PATH);

            if (count($paths) === $this->concurrent) {
                $this->invalidator->invalidate($paths);
                $paths = [];
            }
        }

        if ($paths) {
            $this->invalidator->invalidate($paths);
        }
    }

    public function getExpired($amount)
    {
        return $this->storage->getExpired($amount);
    }

    public function load($id, $includeBody = true)
    {
        return $this->storage->load($id, $includeBody);
    }

    public function loadMultiple(array $ids, $includeBody): \Iterator
    {
        return $this->storage->loadMultiple($ids, $includeBody);
    }

    public function set(Cache $item, array $tags)
    {
        return $this->storage->set($item, $tags);
    }

    public function getByTags(array $tags)
    {
        return $this->storage->getByTags($tags);
    }
}
