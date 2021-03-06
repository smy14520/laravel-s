<?php

namespace Hhxsv5\LaravelS\Swoole;

use Illuminate\Http\Request as IlluminateRequest;
use Symfony\Component\HttpFoundation\HeaderBag;

class Request
{
    protected $swooleRequest;

    public function __construct(\swoole_http_request $request)
    {
        $this->swooleRequest = $request;
    }

    public function toIlluminateRequest()
    {
        $get = isset($this->swooleRequest->get) ? $this->swooleRequest->get : [];
        $post = isset($this->swooleRequest->post) ? $this->swooleRequest->post : [];
        $cookies = isset($this->swooleRequest->cookie) ? $this->swooleRequest->cookie : [];
        $server = isset($this->swooleRequest->server) ? $this->swooleRequest->server : [];
        $headers = isset($this->swooleRequest->header) ? $this->swooleRequest->header : [];
        $files = isset($this->swooleRequest->files) ? $this->swooleRequest->files : [];

        foreach ($headers as $key => $value) {
            $key = str_replace('-', '_', $key);
            $server['http_' . $key] = $value;
        }
        $server = array_change_key_case($server, CASE_UPPER);

        $content = $this->swooleRequest->rawContent() ?: null;
        $laravelRequest = new IlluminateRequest($get, $post, [], $cookies, $files, $server, $content);
        $laravelRequest->headers = new HeaderBag($headers);

        return $laravelRequest;
    }

}
