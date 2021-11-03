<?php


namespace saber\PhpTools\Think\Yaml;

use think\Service;


class YamlService extends Service
{
    public function register(): void
    {
        $this->app->bind('yaml',Yaml::class);
    }


    public function boot(): void
    {
        $this->app->yaml->initialization($this->app);
    }
}