<?php namespace Wetzel\Handlebars;

use Illuminate\Support\ServiceProvider;
use Wetzel\Handlebars\Engines\HandlebarsEngine;
use Wetzel\Handlebars\Compilers\HandlebarsCompiler;
use Wetzel\Handlebars\Compilers\HandlebarsRawCompiler;
use LightnCandy;

class HandlebarsServiceProvider extends ServiceProvider {

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEngineResolverExtensions();

        $this->registerFileExtensions();

        $this->registerPartialBladeDirective();
    }

    /**
     * Register the view engine resolver extension.
     *
     * @return void
     */
    protected function registerEngineResolverExtensions()
    {
        $this->app->extend('view.engine.resolver', function($resolver, $app)
        {
            $this->registerHandlebarsEngine($resolver);

            return $resolver;
        });
    }

    /**
     * Register the mustache engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerHandlebarsEngine($resolver)
    {
        $app = $this->app;

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Handlebars compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $app->singleton('handlebars.lightncandy', function($app)
        {
            return new LightnCandy;
        });

        $app->singleton('handlebars.compiler', function($app)
        {
            $cache = $app['config']['view.compiled'];

            return new HandlebarsCompiler($app['files'], $app['handlebars.lightncandy'], $cache);
        });

        $app->singleton('handlebars.rawcompiler', function($app)
        {
            $cache = $app['config']['view.compiled'];

            return new HandlebarsRawCompiler($app['files'], $cache);
        });

        $resolver->register('handlebars', function() use ($app)
        {
            return new HandlebarsEngine($app['handlebars.compiler'], $app['handlebars.rawcompiler']);
        });
    }

    /**
     * Register the file extensions.
     *
     * @return void
     */
    protected function registerFileExtensions()
    {
        $this->app->extend('view', function($env, $app)
        {
            $fileexts = $app['config']['handlebars.fileext'];

            foreach($fileexts as $fileext) {
                $env->addExtension(trim($fileext, '.'), 'handlebars');
            }

            return $env;
        });
    }

    /**
     * Register @partial Blade directive.
     *
     * @return void
     */
    protected function registerPartialBladeDirective()
    {
        $this->app->extend('blade', function($view, $compiler)
        {
            $pattern = $compiler->createMatcher('partial');

            return preg_replace($pattern, '$1<?php echo $2; ?>', $view);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['handlebars'];
    }

}