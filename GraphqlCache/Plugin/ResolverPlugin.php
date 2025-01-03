<?php
namespace Solveda\GraphqlCache\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Solveda\GraphqlCache\Helper\CacheHandler;

class ResolverPlugin
{
    protected $cacheHandler;

    public function __construct(CacheHandler $cacheHandler)
    {
        $this->cacheHandler = $cacheHandler;
    }

    public function aroundResolve(
        ResolverInterface $subject,
        callable $proceed,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $queryKey = md5(json_encode(['field' => $field->getName(), 'args' => $args]));

        $cachedResponse = $this->cacheHandler->loadCache($queryKey);
        if ($cachedResponse) {
            return $cachedResponse;
        }

        $result = $proceed($field, $context, $info, $value, $args);

        $this->cacheHandler->saveCache($queryKey, $result);

        return $result;
    }
}
