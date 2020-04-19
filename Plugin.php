<?php namespace StudioAzura\TwigTools;

use App;
use Backend;
use Event;
use Cms\Classes\Theme;
use File;
use Yaml;

use System\Classes\PluginBase;

/**
 * TwigTools Plugin Information File
 */
class Plugin extends PluginBase
{
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
            App::instance('cms.twig.environment', $controller->getTwig());
        });
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'br2nl' => function ($content) {
                    return str_replace("<br>", "\r\n", $content);
                },
                'get_lines' => function($text) {
                    if (!trim($text)) {
                        return [];
                    }
                    return explode("\n", $text);
                },
                'json_decode' => function ($data=[]) {
                    return json_decode($data);
                },
                'krsort' => function($array) {
                    $array = (array)$array;
                    if ($array)
                        krsort($array);
                    return $array;
                },
                'ksort' => function($array) {
                    $array = (array)$array;
                    if ($array)
                        ksort($array);
                    return $array;
                },
                'ldate' => function($date, $format = '%A, %e %B %Y %H:%M') {
                    $timezone = \Config::get('cms.backendTimezone', 'UTC');
                    date_default_timezone_set($timezone);

                    $locale = \App::getLocale();
                    setlocale(LC_TIME, $locale, $locale . '_CA');

                    if (is_string($date)) { 
                        $ts = strtotime($date);
                    } else {
                        $ts = $date->timestamp;
                    }
                    return utf8_encode(strftime($format, $ts));
                },
                'asset_file' => function($path) {
                    if (!starts_with($path, 'assets/')) {
                        $path = 'assets/' . $path;
                    }
                    $localPath = themes_path(sprintf('%s/%s', Theme::getActiveTheme()->getDirName(), $path));
                    if (!File::exists($localPath)) {
                        $localPath = $this->plugin_path('assets/images/noimg.jpg');
                    }
                    $file = new \System\Models\File;
                    $file->disk_name = md5($localPath);
                    return $file->fromFile($localPath);
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
                        $path = $this->plugin_path('assets/images/noimg.jpg');
                    }
                    $file->disk_name = md5($path);
                    return $file->fromFile($path);
                },
                'twig' => function ($content, $vars=[]) {
                    $env = App::make('cms.twig.environment');
                    return $env->createTemplate($content)->render($vars);
                },
                'yaml2array' => function($yamlString) {
                    return Yaml::parse($yamlString);
                },
            ],
            'functions' => [
                'lipsum' => function($n=1) {
                    $gen = new \Badcow\LoremIpsum\Generator();
                    return implode('<p>', $gen->getSentences($n));
                },
            ],
        ];
    }
    protected function plugin_path($path)
    {
        return plugins_path('studioazura/twigtools/' . $path);
    }
}
