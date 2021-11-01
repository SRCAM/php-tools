<?php


namespace saber\PhpTools\Http\traits;


use Psr\Http\Message\ResponseInterface;
use saber\PhpTools\Exceptions\HttpInvalidArgumentException;
use saber\PhpTools\Http\contract\Arrayable;
use saber\PhpTools\Http\Response;

trait ResponseCallable
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string|null $type
     * @return mixed|array |object|string
     * @throws \Exception
     */
    protected function castResponseToType(ResponseInterface $response, string $type = null)
    {
        $response = Response::buildFromPsrResponse($response);
        $response->getBody()->rewind();

        switch ($type ?? 'array') {
            case 'array':
                return $response->toArray();
            case 'object':
                return $response->toObject();
            case 'raw':
                return $response;
            default:
                if (!is_subclass_of($type, Arrayable::class)) {
                    throw new \Exception(sprintf('Config key "response_type" classname must be an instanceof %s', Arrayable::class));
                }

                return new $type($response);
        }
    }

    /**
     * @param mixed $response
     * @param string|null $type
     * @return array|mixed|object|string
     * @throws \Exception
     */
    protected function detectAndCastResponseToType($response, string $type = null)
    {

        switch (true) {
            case $response instanceof ResponseInterface:
                $response = Response::buildFromPsrResponse($response);

                break;
            case $response instanceof Arrayable:
                $response = new Response(200, [], json_encode($response->toArray()));
                break;
            case is_scalar($response):
                $response = new Response(200, [], (string)$response);
                break;
            default:
                throw new HttpInvalidArgumentException(sprintf('Unsupported response type "%s"', gettype($response)));
        }

        return $this->castResponseToType($response, $type);
    }
}