<?php

namespace SGT;

use Illuminate\Support\ServiceProvider;
use SGT\HTTP\SGTHtml;

class SGTServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishConfigFile();
        $this->publishViewFiles();

        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {

        return ['sgt_html', SGTHtml::class];
    }

    /**
     * Publish captcha.php file.
     *
     * @return void
     */
    public function publishConfigFile()
    {

        if (method_exists($this, 'publishes'))
        {
            $this->publishes([
                                 __DIR__ . '/../defaults/config/sgtform.php' => config_path('sgtform.php')
                             ]);
        }
    }

    public function publishViewFiles()
    {

        $view_path = __DIR__ . '/../defaults/views';

        $this->loadViewsFrom($view_path, 'sgtform');

        $this->publishes([$view_path => resource_path('views/vendor/sgtform')]);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('sgt_html', function($app)
        {

            return new SGTHtml();
        });

        $this->app->alias('sgt_html', SGTHtml::class);
    }

}
