<?php
namespace Solveda\GraphqlCache\Helper;

use Magento\Framework\App\CacheInterface;

class CacheHandler
{
    const CACHE_TAG = 'GRAPHQL_CACHE';

    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function saveCache($key, $data, $lifetime = 3600)
    {
        $cacheKey = $this->generateCacheKey($key);
        $this->cache->save(serialize($data), $cacheKey, [self::CACHE_TAG], $lifetime);
    }

    public function loadCache($key)
    {
        $cacheKey = $this->generateCacheKey($key);
        $cachedData = $this->cache->load($cacheKey);
        return $cachedData ? unserialize($cachedData) : null;
    }

    private function generateCacheKey($key)
    {
        return 'graphql_' . md5($key);
    }
}
