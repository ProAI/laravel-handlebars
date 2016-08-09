<?php namespace ProAI\Handlebars\Support;

use LightnCandy\LightnCandy as BaseLightnCandy;

class LightnCandy extends BaseLightnCandy
{

    protected static $compileHelpersOnly;

    /**
     * Compile handlebars template into PHP code.
     *
     * @param string $template handlebars template string
     * @param array<string,array|string|integer> $options LightnCandy compile time and run time options, default is array('flags' => LightnCandy::FLAG_BESTPERFORMANCE)
     *
     * @return string|false Compiled PHP code when successed. If error happened and compile failed, return false.
     */
    public static function compile($template, $options = array('flags' => self::FLAG_BESTPERFORMANCE))
    {
        self::$compileHelpersOnly = (isset($options['compile_helpers_only']) && $options['compile_helpers_only'] == true);

        return parent::compile($template, $options);
    }

    /**
     * Setup token delimiter by default or provided string
     *
     * @param array<string,array|string|integer> $context Current context
     * @param string $left left string of a token
     * @param string $right right string of a token
     */
    protected static function setupToken(&$context, $left = '{{', $right = '}}')
    {
        parent::setupToken($context, $left, $right);

        if (self::$compileHelpersOnly) {
            $helperTokens = array();
            foreach ($context['helpers'] as $helper => $value) {
                $helperTokens[] = $helper . '.*?';
            }
            $helperTokens = implode('|', $helperTokens);

            $context['tokens']['search'] = "/^(.*?)(\\s*)($left)(~?)([\\^#\\/!&>]?)(" . $helperTokens . ")(~?)($right)(\\s*)(.*)\$/s";
        }
    }

}
