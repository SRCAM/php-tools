<?php


namespace saber\PhpTools\Think\Yaml;


use Arrayy\Arrayy;
use Exception;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use think\App;

class Yaml implements \ArrayAccess
{


    /**
     * env 配置
     * @var Arrayy
     */
    protected $data;


    public function __construct()
    {
        $this->data = new  Arrayy($_ENV);
    }


    /**
     * 读取环境变量定义文件
     * @access public
     * @param string $file 环境变量定义文件
     * @return void
     */
    public function load(string $file): void
    {
        $env = is_file($file) ? SymfonyYaml::parseFile($file, true) : [];
        $this->set($env);
    }


    /**
     * @param $env
     * @param null $value
     */
    public function set($env, $value = null)
    {
        if (is_array($env)) {
            $env = array_change_key_case($env, CASE_UPPER);
            $this->data->push($env);
        } else {
            $this->data->set($env, $value);
        }
    }


    /**
     * 获取环境变量
     * @param string $name
     * @param null $default
     * @return array|Arrayy|bool|mixed|mixed[]
     */
    public function get(string $name, $default = null)
    {
        if (empty($name)) {
            return $this->data;
        }

        if ($this->data->has($name)) {
            return $this->data->get($name);
        }

        return $this->getEnv($name, $default);
    }


    /**
     * 获取环境变量值
     * @access public
     * @param string $name 环境变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    protected function getEnv(string $name, $default = null)
    {
        $result = getenv('PHP_' . $name);

        if (false === $result) {
            return $default;
        }

        if ('false' === $result) {
            $result = false;
        } elseif ('true' === $result) {
            $result = true;
        }

        if (!isset($this->data[$name])) {
            $this->data[$name] = $result;
        }

        return $result;
    }

    /**
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        return $this->data->has($name);
    }


    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     * @param mixed $offset
     * @return array|Arrayy|bool|mixed|mixed[]
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($name)
    {
        throw new Exception('not support: unset');
    }


    /**
     * 兼容think-php
     * 初始化
     */
    public function initialization(App $app, string $name = null)
    {
        $file = empty($name) ? 'env.yaml' : $name . '.yaml';
        $this->load($app->getRootPath() . $file);
    }


}