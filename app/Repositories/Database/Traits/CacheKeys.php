<?php

declare(strict_types=1);

namespace App\Repositories\Database\Traits;

trait CacheKeys
{
    private function generateCacheKey(string $functionName): string
    {
        return md5(__CLASS__.'::'.$functionName);
    }
}
