<?php namespace StudioAzura\TwigTools;

use App;
use Backend;
use Event;
use File;
use Storage;
use Yaml;

use Cms\Classes\Content;
use Cms\Classes\Theme;
use October\Rain\Argon\Argon;
use System\Classes\PluginBase;
use System\Classes\PluginManager;

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
        Event::listen('cms.page.init', function ($controller, $page) {
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
                'elide' => function($text, $locale) {
                    if ($locale == 'fr') {
                        return preg_replace('/d[ae]\s([aeéèiou])/i', "d'$1", $text);
                    } else {
                        return $text;
                    }
                },
                'get_lines' => function ($text) {
                    if (!trim($text)) {
                        return [];
                    }
                    return explode("\n", $text);
                },
                'json_encode' => function ($data=[]) {
                    return json_encode($data);
                },
                'json_decode' => function ($data=[], $associative = null) {
                    return json_decode($data, $associative);
                },
                'krsort' => function ($array) {
                    $array = (array)$array;
                    if ($array)
                        krsort($array);
                    return $array;
                },
                'ksort' => function ($array) {
                    $array = (array)$array;
                    if ($array)
                        ksort($array);
                    return $array;
                },
                'asset_file' => function ($path) {
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
                'media_file' => function ($filename) {
                    if (!File::exists($filename)) {
                        $path = '/media/' . $filename;
                        if (Storage::exists($path)) {
                            $filename = Storage::path($path);
                        }
                    }
                    if (!File::exists($filename)) {
                        $filename = $this->plugin_path('assets/images/noimg.jpg');
                    }
                    $file = new \System\Models\File;
                    $file->disk_name = md5($filename);
                    return $file->fromFile($filename);
                },
                'media_exists' => function ($filename) {
                    if (!$filename || !Storage::exists('/media/' . $filename)) {
                        return false;
                    }
                    return true;
                },
                'twig' => function ($content, $vars=[]) {
                    $env = App::make('cms.twig.environment');
                    return $env->createTemplate($content)->render($vars);
                },
                'yaml2array' => function ($yamlString) {
                    return Yaml::parse($yamlString);
                },
                'trim' => function ($str) {
                    return trim($str);
                },
                'truncate' => function ($str, $length, $phrases=2) {
                    if (strlen($str) > $length) {
                        $sentences = explode('.', $str);
                        if (count($sentences) >= $phrases) {
                            $tstr = implode(". ", array_slice($sentences, 0, $phrases));
                            return $tstr . '.';
                        } else {
                            return substr($str, 0, $length) . ' ...';
                        }
                    } else {
                        return $str;
                    }
                },
            ],
            'functions' => [
                'lipsum' => function ($n=1, $type="s", $seperator=null) {
                    return lipsum($n, $type, $seperator);
                },
                'get_class' => function ($object) {
                    return get_class($object);
                },
                'isInstanceOf' => function ($object, $class) {
                    return $object instanceof $class;
                },
            ],
        ];
    }
    protected function plugin_path($path)
    {
        return plugins_path('studioazura/twigtools/' . $path);
    }
}
