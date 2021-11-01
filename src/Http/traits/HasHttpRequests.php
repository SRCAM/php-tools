<?php


namespace saber\PhpTools\Http\traits;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Utils;
use saber\PhpTools\Json\Json;

trait HasHttpRequests
{
    use ResponseCallable;


    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;


    /**
     * @var string|callable  请求处理
     */
    protected $guzzleHandler;


    /**
     * @var array 请求中间件
     */
    protected $middlewares =[];


    /**
     * @var  \GuzzleHttp\HandlerStack  请求中间件
     */
    protected $handlerStack;



    /**
     * @var array
     */
    protected  $defaultsOption = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];



    /**
     * 添加请求中间件
     *
     * @param callable $middleware
     * @param string $name
     *
     * @return HasHttpRequests
     */
    public function pushMiddleware(callable $middleware, string $name = null): HasHttpRequests
    {
        if (!is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            array_push($this->middlewares, $middleware);
        }

        return $this;
    }


    /**
     * @param $url
     * @param string $method
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($url, string $method = 'GET', array $options = []): \Psr\Http\Message\ResponseInterface
    {
        $options = $this->fixJsonIssue($options);

        if (property_exists($this, 'baseUrl') && !is_null($this->baseUrl)) {
            $options['base_uri'] = $this->baseUrl;
        }

        $method       = strtoupper($method);
        $options      = array_merge($this->defaultsOption, $options, ['handler' => $this->getHandlerStack()]);
        $response     = $this->getHttpClient()->request($method, $url, $options);
        $response->getBody()->rewind();

        return $response;
    }


    /**
     * 设置处理堆栈
     * @param \GuzzleHttp\HandlerStack $handlerStack
     * @return HasHttpRequests
     */
    public function setHandlerStack(HandlerStack $handlerStack): HasHttpRequests
    {
        $this->handlerStack = $handlerStack;
        return $this;
    }

    /**
     * Build a handler stack.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    protected function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }
        $this->handlerStack = HandlerStack::create($this->getGuzzleHandler());
        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->push($middleware, $name);
        }

        return $this->handlerStack;
    }



    /**
     * 获取HttpClient 客户端
     */
    protected function getHttpClient():ClientInterface
    {

        if (!($this->httpClient instanceof ClientInterface)){
            $this->httpClient = new Client(['handler'=>HandlerStack::create($this->getGuzzleHandler())]);
        }
        return $this->httpClient;
    }


    /**
     * Get guzzle handler.
     * @return callable
     */
    protected function getGuzzleHandler()
    {

        if (property_exists($this, 'baseUri') && !empty($this->guzzleHandler)) {

            return is_string($this->guzzleHandler)
                ? new $this->guzzleHandler()
                : $this->guzzleHandler;
        }

        return Utils::chooseHandler();
    }


    /**
     * @param array $options
     *
     * @return array
     */
    protected function fixJsonIssue(array $options): array
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

            if (empty($options['json'])) {
                $options['body'] = Json::jsonEncode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = Json::jsonEncode($options['json'], JSON_UNESCAPED_UNICODE);
            }
            unset($options['json']);
        }
        return $options;
    }
}