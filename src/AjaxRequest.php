<?php

namespace Helpress;

class AjaxRequest

{

    private $action;
    private $function;
    private $data;
    private $scope;

    static function register($action, $function, $data, $scope = 'global')
    {
        new static($action, $function, $data, $scope);
    }

    private function __construct($action, $function, $data, $scope)
    {

        $this->action = $action;
        $this->function = $function;
        $this->data = $data;
        $this->scope = $scope;
        $this->registerHandle();
    }

    private function registerHandle()
    {
        add_action('wp_ajax_'.$this->action, [$this, 'handle']);

        if(!$this->isAdmin()) {
            add_action('wp_ajax_nopriv_'.$this->action, [$this, 'handle']);
        }
    }

    public function handle()

    {
        wp_send_json(is_callable($this->function) ? call_user_func($this->function, $this->data) : false);
    }

    private function isAdmin()

    {
        return 'admin' === $this->scope;
    }
}
