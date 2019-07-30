<?php

namespace SGT;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use SGT\HTTP\SGTHtml;

class HtmlServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('html', function ($app)
        {

            return new SGTHtml();
        });

        $this->app->alias('html', SGTHtml::class);
    }

    public function provides()
    {

        return ['html', SGTHtml::class];
    }
}
