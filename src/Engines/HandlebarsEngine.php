<?php namespace ProAI\Handlebars\Engines;

use Illuminate\Contracts\View\Engine;
use Illuminate\View\Engines\CompilerEngine;

class HandlebarsEngine extends CompilerEngine implements Engine
{

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        $raw = (isset($data['raw']) && $data['raw']) ? true : false;
        $compiled = $this->compiler->getCompiledPath($path, $raw);

        // convert objects to arrays
        $data = array_map('self::convertObjectToArray', $data);

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        $results = $this->evaluatePath($compiled, $data);

        array_pop($this->lastCompiled);

        return $results;
    }

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param  string $__path
     * @param  array  $__data
     * @return string
     */
    protected function evaluatePath($__path, $__data)
    {
        $obLevel = ob_get_level();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        $renderer = include($__path);

        $results = $renderer($__data);

        return ltrim($results);
    }

    protected function convertObjectToArray($item)
    {
        if (is_object($item) && method_exists($item, 'toArray') && is_callable([$item, 'toArray'])) {
            $item = $item->toArray();
        }

        return $item;
    }

}
