<?php

namespace Tado\Lyrc;

use Illuminate\Support\ServiceProvider;

class LyrcServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ __DIR__ . '/config/lyrc.php' => config_path('lyrc.php'), ]);

        $this->mergeConfigFrom(__DIR__ . '/config/lyrc.php', 'lyrc');

        if ($this->app->runningInConsole()) {
            $this->commands(['Tado\Lyrc\Console\Commands\CompileRoutesCommand',]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([ __DIR__ . '/../src/config/lyrc.php' => config_path('lyrc.php'), ]);
    }
}
