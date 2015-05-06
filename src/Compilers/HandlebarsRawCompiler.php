<?php namespace Wetzel\Handlebars\Compilers;

use Wetzel\Handlebars\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;

class HandlebarsRawCompiler extends Compiler implements CompilerInterface {

    /**
     * Create a new compiler instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $cachePath
     * @return void
     */
    public function __construct(Filesystem $files, $cachePath)
    {
        $app = app();

        $this->files = $files;
        $this->cachePath = $cachePath;

        $this->options = $app['config']->get('handlebars');

        // set basedir from laravel view config
        if ( ! isset($this->options['blade_lang_directives'])) $this->options['blade_lang_directives'] = false;
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @param  bool  $rendered
     * @return void
     */
    public function compile($path, $render = true)
    {
        $contents = $this->files->get($path);

        // compile language variables
        if($this->options['blade_lang_directives']) {
            $contents = $this->compileString($contents);
        }

        if ( ! is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string  $path
     * @return string
     */
    public function getCompiledPath($path)
    {
        return $this->cachePath.'/'.md5($path.'-raw');
    }

}