<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSource;

use Doctrine\Common\Cache\Cache;

class CachedDataSource implements DataSource
{
    /** @var DataSource */
    private $dataSource;

    /** @var Cache */
    private $cache;

    /** @var string */
    private $cacheKey;

    /** @var int */
    private $cacheLifetime;

    public function __construct(
        DataSource $dataSource,
        Cache $cache,
        string $cacheKey,
        int $cacheLifetime = 0
    ) {
        $this->dataSource    = $dataSource;
        $this->cache         = $cache;
        $this->cacheKey      = $cacheKey;
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * @return mixed[][]
     */
    public function getData() : array
    {
        if ($this->cache->contains($this->cacheKey)) {
            return $this->cache->fetch($this->cacheKey);
        }

        $data = $this->dataSource->getData();

        $this->cache->save($this->cacheKey, $data, $this->cacheLifetime);

        return $data;
    }
}
