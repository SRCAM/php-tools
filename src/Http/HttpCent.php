<?php


namespace saber\PhpTools\Http;



use Psr\Http\Message\ResponseInterface;
use saber\PhpTools\Http\traits\HasHttpRequests;


class HttpCent
{
    use HasHttpRequests {
        request as baseRequest;
    }

    /**
     * @var array
     */
    protected $options = [];


    /**
     * @var object  返回类型
     */
    protected $responseType = 'array';


    /**
     * 访问地址
     * @var string
     */
    protected $baseUrl ='';


    /**
     *
     * GET 请求
     * @param string $url
     * @param array $query
     * @return array|mixed|object|ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    /**
     * post request.
     * @param string $url
     * @param array $data
     * @param array $query
     * @param bool $returnRaw
     * @return array|mixed|object|ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function httpPost(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data, 'query' => $query]);
    }

    /**
     * JSON request.
     * @param string $url
     * @param array $data
     * @param array $query
     * @param bool $returnRaw
     * @return array|mixed|object|ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @param bool $returnRaw
     * @return array|mixed|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    protected function request(string $url, string $method = 'GET', array $options = [], bool $returnRaw=false)
    {

        if (!empty($this->middlewares)){
            $this->registerHttpMiddleware();
        }
        $options = array_merge($this->options,$options);

        $options = $this->rewritingOptions($method ,$options);

        $response = $this->baseRequest($url, $method, $options);

        return $returnRaw ? $response : $this->detectAndCastResponseToType($response, $this->responseType);
    }
    
    /**
     *注册中间件
     */
    protected function registerHttpMiddleware():void
    {

    }


    /**
     * 请求事件处理
     * @param string $method
     * @param array $options
     * @return array
     */
    protected function rewritingOptions(string  $method , array  $options):array
    {
        return $options;
    }
}