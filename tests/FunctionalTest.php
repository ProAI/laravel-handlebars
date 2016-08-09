<?php

namespace ProAI\Handlebars;

use LightnCandy\LightnCandy;
use Orchestra\Testbench\TestCase;

class FunctionalTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['ProAI\Handlebars\HandlebarsServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [__DIR__]);
        $app['config']->set('handlebars.flags', LightnCandy::FLAG_HANDLEBARSJS_FULL);
    }

    public function testHandlebars()
    {
        $actual = \View::make('views.handlebars', [
            'title' => 'All about <p> Tags',
            'body' => '<p>This is a post about &lt;p&gt; tags</p>',
            'author' => [
                'firstName' => 'Manuel',
                'lastName' => 'Wieser'
            ],
            'people' => [
                'Taylor Otwell',
                'Jeffrey Way'
            ],
            'comments' => [
                [
                    'author' => [
                        'firstName' => 'Markus',
                        'lastName' => 'Wetzel'
                    ],
                    'body' => 'I ❤ Handlebars!'
                ]
            ],
        ]);
        $expected = file_get_contents(__DIR__ . '/expected/handlebars.html', FILE_TEXT);
        $this->assertEquals((string)$actual, $expected);
    }

    public function testBlade()
    {
        $actual = \View::make('views.blade');
        $expected = file_get_contents(__DIR__ . '/expected/blade.html', FILE_TEXT);
        $this->assertEquals((string)$actual, $expected);
    }

//    public function testLanguageHelpers()
//    {
//        $actual = \View::make('views.languageHelpers');
//        $expected = file_get_contents(__DIR__ . '/expected/languageHelpers.html', FILE_TEXT);
//        $this->assertEquals((string)$actual, $expected);
//    }

//    public function testOptionalRawOutput()
//    {
//        $actual = \View::make('views.optionalRawOutput');
//        $expected = file_get_contents(__DIR__ . '/expected/optionalRawOutput.html', FILE_TEXT);
//        $this->assertEquals((string)$actual, $expected);
//    }

//    public function testTranslatedRawOutput()
//    {
//        $actual = \View::make('views.translatedRawOutput');
//        $expected = file_get_contents(__DIR__ . '/expected/translatedRawOutput.html', FILE_TEXT);
//        $this->assertEquals((string)$actual, $expected);
//    }
}
