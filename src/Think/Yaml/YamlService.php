<?php


namespace saber\PhpTools\Think\Yaml;

use think\Service;


class YamlService extends Service
{
    public function register(): void
    {
        $this->app->register('yaml',Yaml::class);
    }


    public function boot(): void
    {
        
    }
}