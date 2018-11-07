<?php

namespace Helpress;

abstract class HttpRequest
{
    protected $base_url;

    protected $body = [];

    protected $headers = [];

    public function post($method, $body = [])
    {

        $args = [
            'timeout' => 45,
            'headers' => $this->headers,
            'body' => json_encode(array_merge($this->body, $body)),
        ];

        return $this->response(wp_remote_post($this->base_url . $method, $args));
    }

    public function get($method)
    {
        $args = [
            'headers' => $this->headers,
            'timeout' => 45,
        ];

        return $this->response(wp_remote_get($this->base_url . $method, $args));
    }

    public function delete()
    {
        $args = [
            'method' => 'DELETE',
            'timeout' => 45,
            'headers' => $this->headers,
            'body' => json_encode($this->body),
        ];

        return wp_remote_request($this->url, $args);
    }

    public function postFile($url, $params = [], $headers = [], $file = [])
    {

        // initialise the curl request
        $request = curl_init($url);

        $headers = array_merge(['Content-Type' => 'multipart/form-data'], $headers);

        $file = [
            'Arquivo' => '@' . realpath($file['tmp_name']) . ';filename=' . $file['name']
        ];

        $params = array_merge($file, $params);

        // send a file
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $params);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);

        // output the response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($request);

        $code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        // close the session
        curl_close($request);

        $success = [200, 201];
        if (!in_array($code, $success)) {
            throw new \Exception($code);
        }

        return [
            'body' => json_decode($response, true),
        ];
    }

    public function put($url, $body = [], $headers = [])
    {

        $headers = array_merge([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ], $headers);

        $args = [
            'method' => 'PUT',
            'timeout' => 45,
            'headers' => $headers,
            'body' => json_encode($body),
        ];

        $response = wp_remote_request($url, $args);
        $code = wp_remote_retrieve_response_code($response);
        $success = [200, 201];

        if (!in_array($code, $success)) {
            throw new \Exception($code . ' - ' . \wp_remote_retrieve_response_message($response));
        }

        return [
            'body' => json_decode(wp_remote_retrieve_body($response), true),
            'headers' => wp_remote_retrieve_headers($response)
        ];
    }

    public function response($response)
    {
        if (is_wp_error($response)) {
            return $response->get_error_message();
        } else {
            return isset($response['body']) ? json_decode($response['body'], true) : null;
        }
    }
}
