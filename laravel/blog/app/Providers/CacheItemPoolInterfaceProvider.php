<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Repository;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItemPool;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItem;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolInterfaceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CacheItemPoolInterface::class, function () {
            $repository = $this->app->make(Repository::class);
            return new CacheItemPool($repository);
        });
        $this->app->bind(CacheItemInterface::class, function () {
            return new CacheItem('_');
        });
    }
}
