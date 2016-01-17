<?php

use ProAI\Handlebars\Support\LightnCandy;

return [

    /*
    |--------------------------------------------------------------------------
    | Flags
    |--------------------------------------------------------------------------
    |
    | Set Lightncandy flags here. See https://github.com/zordius/lightncandy
    | for more information.
    |
    */

    'flags' => LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_ERROR_EXCEPTION,

    /*
    |--------------------------------------------------------------------------
    | File Extensions
    |--------------------------------------------------------------------------
    |
    | All file extensions that should be compiled with the Handlebars
    | template engine.
    |
    */
    
    'fileext' => [
        '.handlebars',
        '.hbs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Partials
    |--------------------------------------------------------------------------
    |
    | LightnCandy supports partial when compile time. You can provide partials
    | by partials option when compile()
    |
    */

    'partials' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Helpers
    |--------------------------------------------------------------------------
    |
    | Custom helper can help you deal with common template tasks, for example:
    | provide URL and text then generate a link. To know more about custom
    | helper, you can read original handlebars.js document here:
    | http://handlebarsjs.com/expressions.html . 
    |
    */

    'helpers' => [],

    /*
    |--------------------------------------------------------------------------
    | Block Custom Helpers
    |--------------------------------------------------------------------------
    |
    | Block custom helper must be used as a section, the section is started
    | with {{#helper_name ...}} and ended with {{/helper_name}}.
    |
    */

    'blockhelpers' => [],

    /*
    |--------------------------------------------------------------------------
    | Handlebars.js' Custom Helpers
    |--------------------------------------------------------------------------
    |
    | You can implement helpers more like Handlebars.js way with hbhelpers
    | option, all matched single custom helper and block custom helper will be
    | handled. In Handlebars.js, a block custom helper can rendener child block
    | by executing options.fn; or change context by send new context as first
    | parameter.
    |
    */

    'hbhelpers' => [],

    /*
    |--------------------------------------------------------------------------
    | Language Helpers
    |--------------------------------------------------------------------------
    |
    | Use this option, if you want to use the language helpers in a template.
    | You can use a {{lang ...}} and {{choice ...}} helper. Both have the same
    | behaviour like the @lang and @choice Blade directives.
    |
    */

    'language_helpers' => true,

    /*
    |--------------------------------------------------------------------------
    | Optional Raw Output
    |--------------------------------------------------------------------------
    |
    | If this option is set to true, you can pass a $raw variable to the data
    | array. If $raw is true, then the template will be returned without
    | rendering in raw format. This is helpful if you want to use a Handlebars
    | template clientside with javascript.
    |
    */

    'optional_raw_output' => true,

    /*
    |--------------------------------------------------------------------------
    | Translate Raw Output
    |--------------------------------------------------------------------------
    |
    | If language_helpers and optional_raw_output are set to true, this option
    | can also set to true. If so, the translation helpers will also be
    | rendered for the raw output.
    |
    */

    'translate_raw_output' => true,

];
