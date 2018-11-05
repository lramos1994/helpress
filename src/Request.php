<?php

namespace Helpress;

use Helpress\HttpRequest;

class Request extends HttpRequest
{
    public function __construct($base_url, $headers = []) {
        $this->base_url = $base_url;

        $this->headers = array_merge( [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ], $headers );
    }

    public function setBody($body = [])
    {
        $this->body = array_merge( [
            'date' => current_time('Y-m-d H:i:s'),
        ], $body );
    }
}
