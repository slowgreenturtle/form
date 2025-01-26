<?php

namespace SGT;

use Illuminate\Support\ServiceProvider;
use SGT\HTTP\Collective\FormBuilder;
use SGT\HTTP\Collective\HtmlBuilder;
use SGT\HTTP\SGTHtml;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\Str;

class SGTServiceProvider extends ServiceProvider
{

    /**
     * Supported Blade Directives
     *
     * @var array
     */
    protected $directives = [
        'entities',
        'decode',
        'script',
        'style',
        'image',
        'favicon',
        'link',
        'secureLink',
        'linkAsset',
        'linkSecureAsset',
        'linkRoute',
        'linkAction',
        'mailto',
        'email',
        'ol',
        'ul',
        'dl',
        'meta',
        'tag',
        'open',
        'model',
        'close',
        'token',
        'label',
        'input',
        'text',
        'password',
        'hidden',
        'email',
        'tel',
        'number',
        'date',
        'datetime',
        'datetimeLocal',
        'time',
        'url',
        'file',
        'textarea',
        'select',
        'selectRange',
        'selectYear',
        'selectMonth',
        'getSelectOption',
        'checkbox',
        'radio',
        'reset',
        'image',
        'color',
        'submit',
        'button',
        'old'
    ];

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

        return ['sgt_html', SGTHtml::class, 'html', HtmlBuilder::class, 'form', FormBuilder::class];
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

        $this->registerHtmlBuilder();

        $this->registerFormBuilder();

        $this->app->alias('html', HtmlBuilder::class);
        $this->app->alias('form', FormBuilder::class);

        $this->registerBladeDirectives();

    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {

        $this->app->singleton('html', function($app)
        {

            return new HtmlBuilder($app['url'], $app['view']);
        });
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {

        $this->app->singleton('form', function($app)
        {

            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);

            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Register Blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {

        $this->app->afterResolving('blade.compiler', function(BladeCompiler $bladeCompiler)
        {

            $namespaces = [
                'Html' => get_class_methods(HtmlBuilder::class),
                'Form' => get_class_methods(FormBuilder::class),
            ];

            foreach ($namespaces as $namespace => $methods)
            {
                foreach ($methods as $method)
                {
                    if (in_array($method, $this->directives))
                    {
                        $snakeMethod = Str::snake($method);
                        $directive   = strtolower($namespace) . '_' . $snakeMethod;

                        $bladeCompiler->directive($directive, function($expression) use ($namespace, $method)
                        {

                            return "<?php echo $namespace::$method($expression); ?>";
                        });
                    }
                }
            }
        });
    }

}
