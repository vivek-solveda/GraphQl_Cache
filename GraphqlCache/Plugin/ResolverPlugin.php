<?php
namespace Solveda\GraphqlCache\Plugin;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Solveda\GraphqlCache\Helper\CacheHandler;

class ResolverPlugin
{
    protected $cacheHandler;

    public function __construct(CacheHandler $cacheHandler)
    {
        $this->cacheHandler = $cacheHandler;
    }

    public function aroundResolve(ResolverInterface $subject, callable $proceed, array $args, $context)
    {
        // Generate a unique key for the query and arguments
        $queryKey = json_encode($args);
        $cachedResponse = $this->cacheHandler->loadCache($queryKey);

        // Return cached response if available
        if ($cachedResponse) {
            return $cachedResponse;
        }

        // Execute the original resolver
        $result = $proceed($args, $context);

        // Cache the result for future use
        $this->cacheHandler->saveCache($queryKey, $result);

        return $result;
    }
}
