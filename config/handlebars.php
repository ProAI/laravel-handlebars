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
    | https://github.com/zordius/lightncandy#partial-support
    |
    */

    'partials' => [],
    'partialresolver' => false,

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    |
    | https://github.com/zordius/lightncandy#custom-helper
    |
    */

    'helpers' => [],
    'helperresolver' => false,

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
