<?php namespace Wetzel\Handlebars\Compilers;

use Closure;
use Wetzel\Handlebars\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use LightnCandy;

class HandlebarsCompiler extends Compiler implements CompilerInterface {

    /**
     * LightnCandy instance.
     *
     * @var \LightnCandy
     */
    protected $lightncandy;

    /**
     * Create a new compiler instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \LightnCandy  $lightncandy
     * @param  string  $cachePath
     * @return void
     */
    public function __construct(Filesystem $files, LightnCandy $lightncandy, $cachePath)
    {
        $app = app();

        $this->files = $files;
        $this->lightncandy = $lightncandy;
        $this->cachePath = $cachePath;

        $this->options = $app['config']->get('handlebars');

        // set basedir from laravel view config
        $this->options['basedir'] = $app['config']->get('view.paths');
        if ( ! isset($this->options['blade_lang_directives'])) $this->options['blade_lang_directives'] = false;

        // set language variables helper function
        if($this->options['blade_lang_directives'])
        {
            if ( ! isset($this->options['helpers'])) $this->options['helpers'] = [];

            $helpers = [
                'lang' => function($args, $named) {
                    // todo: pass $named array
                    return \Illuminate\Support\Facades\Lang::get($args[0]);
                },
                'choice' => function($args, $named) {
                    // todo: pass $named array
                    return \Illuminate\Support\Facades\Lang::choice($args[0], $args[1]);
                }
            ];

            $this->options['helpers'] = array_merge($helpers, $this->options['helpers']);
        }
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

        // prepare language variables
        if($this->options['blade_lang_directives']) {
            $contents = $this->compileString($contents);
        }

        // compile with Handlebars compiler
        $contents = $this->lightncandy->compile($contents, $this->options);

        if ( ! is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

    /**
     * Compile the lang statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileLang($expression)
    {
        $expression = str_replace("array(","",$expression);
        $expression = trim($expression, "(),[]");
        return "{{lang $expression }}";
    }

    /**
     * Compile the choice statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileChoice($expression)
    {
        $expression = str_replace("array(","",$expression);
        $expression = trim($expression, "(),[]");
        return "{{choice $expression }}";
    }

}