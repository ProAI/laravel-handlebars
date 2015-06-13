<?php namespace Wetzel\Handlebars\Compilers;

use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use Wetzel\Handlebars\Support\LightnCandy;

class HandlebarsCompiler extends Compiler implements CompilerInterface {

    /**
     * LightnCandy instance.
     *
     * @var \LightnCandy
     */
    protected $lightncandy;

    /**
     * Language helpers option.
     *
     * @var bool
     */
    protected $languageHelpers = true;

    /**
     * Optional raw output.
     *
     * @var bool
     */
    protected $optionalRawOutput = true;

    /**
     * Translate raw output.
     *
     * @var bool
     */
    protected $translateRawOutput = true;

    /**
     * Create a new compiler instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Wetzel\Handlebars\Support\LightnCandy  $lightncandy
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

        // make sure helpers array is set
        if ( ! isset($this->options['helpers'])) $this->options['helpers'] = [];

        // set language helpers option
        $this->languageHelpers = (isset($this->options['language_helpers']))
            ? $this->options['language_helpers']
            : $this->languageHelpers;

        // set translate raw output option
        $this->translateRawOutput = (isset($this->options['translate_raw_output']))
            ? $this->options['translate_raw_output']
            : $this->translateRawOutput;

        // set translate raw output option
        $this->optionalRawOutput = (isset($this->options['optional_raw_output']))
            ? $this->options['optional_raw_output']
            : $this->optionalRawOutput;
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path)
    {
        // compile with Handlebars compiler (raw output)
        if ($this->optionalRawOutput) {
            $this->compileString($path, true);
        }

        // compile with Handlebars compiler
        $this->compileString($path);
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @param  array  $options
     * @param  bool  $raw
     * @return void
     */
    public function compileString($path, $raw = false)
    {
        $options = $this->options;
        
        // set partials directory
        if ( ! $raw) {
            $options['basedir'][] = dirname($path);
        }

        // set raw option
        array_set($options, 'compile_helpers_only', $raw);

        // set language helper functions
        if($this->languageHelpers)
        {
            if ( ! $raw) {
                $helpers = array_merge($this->getHelpers(), $options['helpers']);
            } elseif ($this->translateRawOutput) {
                $helpers = $this->getLanguageHelpers();
            } else {
                $helpers = [];
            }

            array_set($options, 'helpers', $helpers);
        }

        // compile with Handlebars compiler
        $contents = $this->lightncandy->compile($this->files->get($path), $options);

        if ( ! is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($path, $raw), $contents);
        }
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string  $path
     * @param  bool  $raw
     * @return string
     */
    public function getCompiledPath($path, $raw = false)
    {
        if ($raw) $path .= '-raw';

        return $this->cachePath.'/'.md5($path.'-raw');
    }

    /**
     *
     * Get all helpers included that come included in this package
     *
     * @return array
     */
    protected function getHelpers()
    {
        return array_merge($this->getFormHelpers(), $this->getLanguageHelpers());
    }

    /**
     * Get form helper functions.
     *
     * @return array
     */
    protected function getFormHelpers()
    {
        return [
            'form_token' => function() {
                return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
            }
        ];
    }

    /**
     * Get language helper functions.
     *
     * @return array
     */
    protected function getLanguageHelpers()
    {
        return [
            'lang' => function($args, $named) {
                return \Illuminate\Support\Facades\Lang::get($args[0], $named);
            },
            'choice' => function($args, $named) {
                return \Illuminate\Support\Facades\Lang::choice($args[0], $args[1], $named);
            }
        ];
    }
}