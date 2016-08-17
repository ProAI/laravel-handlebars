<?php namespace ProAI\Handlebars\Compilers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use ProAI\Handlebars\Support\LightnCandy;

class HandlebarsCompiler extends Compiler implements CompilerInterface
{

    /** @var LightnCandy */
    protected $lightncandy;

    /** @var bool */
    protected $developmentEnvironment = false;

    /** @var bool */
    protected $languageHelpers = true;

    /** @var bool */
    protected $optionalRawOutput = true;

    /** @var bool */
    protected $translateRawOutput = true;

    /**
     * Create a new compiler instance.
     *
     * @param Filesystem  $files
     * @param LightnCandy $lightncandy
     * @param string      $cachePath
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
        if (!isset($this->options['helpers'])) {
            $this->options['helpers'] = [];
        }

        // set development environment option
        $this->developmentEnvironment = (isset($this->options['development_environment']))
            ? App::environment($this->options['development_environment'])
            : $this->developmentEnvironment;

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
     * @param  string $path
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
     * @param  string $path
     * @param  bool   $raw
     */
    public function compileString($path, $raw = false)
    {
        $options = $this->options;

        // set partials directory
        if (!$raw) {
            $options['basedir'][] = dirname($path);
        }

        // set raw option
        array_set($options, 'compile_helpers_only', $raw);

        // set language helper functions
        if ($this->languageHelpers) {
            if (!$raw) {
                $helpers = array_merge($this->getLanguageHelpers(), $options['helpers']);
            } elseif ($this->translateRawOutput) {
                $helpers = $this->getLanguageHelpers();
            } else {
                $helpers = [];
            }

            array_set($options, 'helpers', $helpers);
        }

        // As of LightnCandy v0.91 resolving via `basedir` and `fileext` options has been stripped from LightnCandy.
        if (!$options['partialresolver']) {
            $options['partialresolver'] = function ($context, $name) use ($options) {
                foreach ($options['basedir'] as $dir) {
                    foreach ($options['fileext'] as $ext) {
                        $path = sprintf('%s/%s.%s', rtrim($dir, DIRECTORY_SEPARATOR), $name, ltrim($ext, '.'));
                        if (file_exists($path)) {
                            return file_get_contents($path);
                        }
                    }
                }
                return "[Partial $path not found]";
            };
        }

        $contents = $this->lightncandy->compile($this->files->get($path), $options);

        if (!is_null($this->cachePath)) {
            // As of LightnCandy v0.90 generated PHP code will not includes `<?php`.
            $this->files->put($this->getCompiledPath($path, $raw), "<?php $contents");
        }
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string $path
     * @param  bool   $raw
     * @return string
     */
    public function getCompiledPath($path, $raw = false)
    {
        if ($raw) {
            $path .= '-raw';
        }

        return $this->cachePath . '/' . md5($path . '-raw');
    }

    /**
     * Get language helper functions.
     *
     * @return array
     */
    protected function getLanguageHelpers()
    {
        return [
            'lang' => function ($args, $named) {
                return Lang::get($args[0], $named);
            },
            'choice' => function ($args, $named) {
                return Lang::choice($args[0], $args[1], $named);
            }
        ];
    }

    /**
     * Make sure that developing locally is fun.
     *
     * @param string $path
     * @return bool
     */
    public function isExpired($path)
    {
        if ($this->developmentEnvironment) {
            return true;
        }

        return parent::isExpired($path);
    }

}
