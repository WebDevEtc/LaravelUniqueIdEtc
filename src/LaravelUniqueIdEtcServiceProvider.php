<?php

namespace WebDevEtc\LaravelUniqueIdEtc;

use Illuminate\Support\ServiceProvider;
use WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator\UniqueGenerator;
use WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator\UniqueGeneratorInterface;

/**
 * Class ContactEtcMainServiceProvider
 * @package WebDevEtc\ContactEtc
 */
class LaravelUniqueIdEtcServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishesFiles();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->makeBindings();
    }

    protected function publishesFiles()
    {
        $tag = 'blogetc_config';
        $this->publishes([
            __DIR__ . '/Config/uniqueid.php' => config_path('uniqueid.php'),
        ], $tag);
    }

    /**
     * make bindings
     */
    protected function makeBindings()
    {
        $this->app->bind(UniqueGeneratorInterface::class, function () {
            return new UniqueGenerator();
        });
    }
}


