<?php namespace ProAI\Handlebars\Compilers;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler as BaseBladeCompiler;
use Illuminate\View\Compilers\CompilerInterface;

class BladeCompiler extends BaseBladeCompiler implements CompilerInterface
{

    /**
     * Compile the include statements into valid PHP.
     *
     * @param  string $expression
     * @return string
     */
    protected function compileRaw($expression)
    {
        if (Str::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        return "<?php echo \$__env->make($expression, ['raw' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
    }

}
