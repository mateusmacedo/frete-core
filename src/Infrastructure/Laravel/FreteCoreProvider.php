<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;

class FreteCoreProvider extends ServiceProvider
{
    public function register()
    {
        // @phpstan-ignore-next-line
        $this->app->register(\Ecotone\Laravel\EcotoneProvider::class);
    }

    public function boot()
    {
        // @phpstan-ignore-next-line
        $this->publishes([
            // @phpstan-ignore-next-line
            __DIR__ . '/Config/ecotone.php' => config_path('ecotone.php')
        ], 'ecotone-frete-core-config');
    }
}
