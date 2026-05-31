<?php

namespace Botble\Pesapal\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PesapalServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        Helper::autoload(__DIR__ . '/../../helpers');
        
        // Load OAuth library
        require_once __DIR__ . '/../Libraries/OAuth.php';
    }

    public function boot(): void
    {
        $this->setNamespace('plugins/pesapal')
            ->loadAndPublishConfigurations(['config'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->publishAssets()
            ->loadRoutes();

        $this->app->register(HookServiceProvider::class);
    }
}

