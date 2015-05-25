# Laravel Handlebars

This package allows you to use Handlebars (and Mustache) templates with Laravel. You can integrate Handlebars templates into Blade templates and you can even use the Blade language directives `@lang` and `@choice` in Handlebars templates.

It's the perfect choice, if you want to use the same templates in different languages (i. e. PHP and JavaScript) and/or server- and clientside. The compiling and rendering is veeery fast, because this package wraps the super fast template engine [LightnCandy](https://github.com/zordius/lightncandy).

## Installation

Laravel Handlebars is distributed as a composer package. So you first have to add the package to your `composer.json` file:

```
"markusjwetzel/laravel-handlebars": "~1.0@dev"
```

Then you have to run `composer update` to install the package. Once this is completed, you have to add the service provider to the providers array in `config/app.php`:

```
'Wetzel\Handlebars\HandlebarsServiceProvider'
```

You can publish the package configuration with the following command:

```
php artisan vendor:publish --provider="Wetzel\Handlebars\HandlebarsServiceProvider"
```

## Usage

### Configuration

Most of the options in `config/handlebars.php` are also used by [LightnCandy](https://github.com/zordius/lightncandy). So please have a look at the LightnCandy readme for more information.

Only the basedir option can't be set in this config file. Instead the package uses the `paths` option in `config/view.php` to define base directories and also the `compiled` option in the same file to define the directory for the compiled templates (i. e. the cache directory).

In addition to the LightnCandy options there are the options `language_helpers`, `optional_raw_output` and `translate_raw_output`. These options are described below.

### Basics

You can use Handlebars templates the same way you use Blade templates. You can return them with `View::make('articles', ['name' => 'Taylor'])` or include them with the Blade `@include` directive, i. e. `@include('articles', ['name' => 'Taylor'])`.

By default all views which have a `.hbs` or `.handlebars` file extension are automatically detected as Handlebars templates. You can add more file extensions that should be treated as Handlebars templates in the `fileext` array in `config/handlebars.php`.

### Language Helpers

If you wish, you can use the Blade language directives `@lang` and `@choice` in Handlebars templates, too. You have to set `$language_helpers = true` in order to use them. Here is an example:

```
// Blade syntax:
@lang('message', ['firstname' => 'John', 'lastname' => $lastname])
@choice('comment_count', 2, ['item' => 'Article'])
```
```
// Handlebars syntax:
{{lang 'message' firstname='John' lastname=lastname }}
{{choice 'comment_count' 2 item='Article' }}
```

### Raw Output

If you want to output the raw code of a template (maybe because you want to use the unrendered template clientside), you can set `$optional_raw_output = true` in the configuration. Then you can pass a variable `$raw = true` to the template or more comfortable you can use the `@raw` Blade directive.

```
// Passing the $raw variable to the view:
View::make('articles', ['raw' => true])
@include('articles', ['raw' => true])
```
```
// Blade @raw directive:
@raw('articles')
```

If you want to output a raw template with compiled and rendered language variables, you can set `$translate_raw_output = true`.

### Partials

This package automatically adds the directory of the current template to the basedir of LightnCandy. By that it is possible to easily include other Handlebars templates in the same directory. Just write `{{> comment}}` to include `comment.hbs` from the same directory.

### Example Template

```
{{#each array_variable }}
	{{#if this }}
		{{ output_some_variable }} {{> include_templatename }}
	{{else}}
		{{lang 'language_variable' }}
	{{/if}}
{{/each}}
```

For more information about the Handlebars syntax see the [Handlebars documentation](http://handlebarsjs.com). It does not matter that the examples are for JavaScript, because Handlebars templates are the same for JavaScript and PHP.

## Support

Bugs and feature requests are tracked on [GitHub](https://github.com/markusjwetzel/laravel-data-mapper/issues).

## License

This package is released under the [MIT License](LICENSE).
