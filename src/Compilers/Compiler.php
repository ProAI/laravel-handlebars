<?php namespace Wetzel\Handlebars\Compilers;

use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;

class Compiler extends BladeCompiler implements CompilerInterface {

	/**
	 * All of the available compiler functions.
	 *
	 * @var array
	 */
	protected $compilers = array(
		'Statements',
	);

	/**
	 * Compile @lang and @choice Blade statements.
	 *
	 * @param  string  $value
	 * @return mixed
	 */
	protected function compileStatements($value)
	{
		$callback = function($match)
		{
			if ($match[1] == 'lang' || $match[1] == 'choice')
			{
				$method = 'compile'.ucfirst($match[1]);
				$match[0] = $this->$method(array_get($match, 3));
			}

			return isset($match[3]) ? $match[0] : $match[0].$match[2];
		};

		return preg_replace_callback('/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value);
	}

}