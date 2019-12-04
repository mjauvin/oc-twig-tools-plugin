<?php namespace StudioAzura\TwigTools;

use App;
use Backend;
use Event;
use File;
use Yaml;

use System\Classes\PluginBase;

/**
 * TwigTools Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Twig Filters & Functions',
            'description' => 'Useful Twig Filters & Functions',
            'author'      => 'StudioAzura',
            'icon'        => 'icon-cogs',
        ];
    }

    public function boot()
    {
        /*
         * Register CMS Twig environment
         */
        Event::listen('cms.page.init', function ($controller) {
            App::singleton('cms.twig.environment', function ($app) use ($controller) {
                return $controller->getTwig();
            }); 
        });
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'krsort' => function($array) {
                    if ($array)
                        krsort($array);
                    return $array;
                },
                'ksort' => function($array) {
                    if ($array)
                        ksort($array);
                    return $array;
                },
                'media_file' => function($path) {
                    $file = new \System\Models\File;
                    if ($path[0] !== '/' || !File::exists($path)) {
                        if (strpos($path, 'app/media/') === false) {
                            $path = 'app/media/' . $path;
                        }
                        $path = storage_path($path);
                    }
                    if (!File::exists($path)) {
                        $path = storage_path('app/media/noimg.jpg');
                    }
                    $file->disk_name = md5($path);
                    return $file->fromFile($path);
                },
                'get_lines' => function($text) {
                    if (!trim($text)) {
                        return [];
                    }
                    return explode("\n", $text);
                },
                'twig' => function ($content, $vars=[]) {
                    $env = App::make('cms.twig.environment');
                    return $env->createTemplate($content)->render($vars);
                },
                'br2nl' => function ($content) {
                    return str_replace("<br>", "\r\n", $content);
                },
                'yaml2array' => function($yamlString) {
                    return Yaml::parse($yamlString);
                },
            ],
        ];
    }
}
