<?php namespace Wetzel\Handlebars\Engines;

use Illuminate\View\Engines\EngineInterface;
use Illuminate\View\Engines\CompilerEngine;
use Wetzel\Handlebars\Compilers\HandlebarsCompiler;
use Wetzel\Handlebars\Compilers\HandlebarsRawCompiler;

class HandlebarsEngine extends CompilerEngine implements EngineInterface {

    /**
     * The Handlebars compiler instance.
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    protected $compiler;

    /**
     * The Handlebars compiler instance for raw output.
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    protected $rawCompiler;

    /**
     * Template output is in raw format.
     *
     * @var bool
     */
    protected $raw = false;

    /**
     * Create a new Blade view engine instance.
     *
     * @param  \Wetzel\Handlebars\Compilers\HandlebarsCompiler  $compiler
     * @param  \Wetzel\Handlebars\Compilers\HandlebarsRawCompiler  $rawCompiler
     * @return void
     */
    public function __construct(HandlebarsCompiler $compiler, HandlebarsRawCompiler $rawCompiler)
    {
        $app = app();

        $this->compiler = $compiler;
        $this->rawCompiler = $rawCompiler;

        $this->options = $app['config']->get('handlebars');
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        $this->raw = ($this->options['optional_raw_output'] && isset($data['raw']) && $data['raw']) ? true : false;

        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.
        if ($this->compiler->isExpired($path))
        {
            $this->rawCompiler->compile($path);
            $this->compiler->compile($path);
        }

        if ($this->raw) {
            $compiled = $this->rawCompiler->getCompiledPath($path);
        } else {
            $compiled = $this->compiler->getCompiledPath($path);
        }

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        if ($this->raw) {
            $results = $this->evaluateRawPath($compiled, $data);
        } else {
            $results = $this->evaluatePath($compiled, $data);
        }

        array_pop($this->lastCompiled);

        return $results;
    }

    /**
     * Get the evaluated contents of the view at the given raw output path.
     *
     * @param  string  $__path
     * @param  array   $__data
     * @return string
     */
    protected function evaluateRawPath($__path, $__data)
    {
        return parent::evaluatePath($__path, $__data);
    }

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param  string  $__path
     * @param  array   $__data
     * @return string
     */
    protected function evaluatePath($__path, $__data)
    {
        $obLevel = ob_get_level();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try
        {
            $renderer = include($__path);

            $results = $renderer($__data);
        }
        catch (Exception $e)
        {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim($results);
    }

}