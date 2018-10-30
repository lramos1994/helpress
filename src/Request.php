<?php

namespace Helpress;

use Helpress\HttpRequest;

class Request extends HttpRequest
{
    public function __construct( $url, $body = [], $headers = [] ) {
        $this->url = $url;
        $this->body = array_merge( [
            'date'         => current_time('Y-m-d H:i:s'),
        ], $body );

        $this->headers = array_merge( [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ], $headers );
    }
}
