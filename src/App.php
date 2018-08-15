<?php

namespace JMCode\Cms;

use JMCode\Http\{
    Request, Response
};

class App
{

    private $_setting = [];
    private $_router;

    private function getClosure()
    {

    }

    /**
     * @return Router
     */
    private function router($method = null, $args = [])
    {
        if (!$this->_router) {
            $this->_router = new Router($this);
        }

        if (!is_null($method)) {
            call_user_func_array([$this->_router, $method], $args);
        }

        return $this->_router;
    }

    /**
     * Create App
     * @param array $ops
     */
    public function __construct(array $ops = [])
    {
        if (!isset($ops['request'])) {
            $ops['request'] = new Request();
        }
        if (!isset($ops['response'])) {
            $ops['response'] = new Response();
        }
        if (!isset($ops['template_name'])) {
            $ops['template_name'] = 'view';
        }
        if (!isset($ops['template_dir_path'])) {
            $ops['template_dir_path'] = APP_DIR_PATH . '\\templates';
        }

        foreach ($ops AS $name => $val) {
            $this->set($name, $val);
        }
    }

    /**
     * @param string $name
     * @param array $args
     * @return void
     */
    public function __call($method, $args)
    {
        if (count($args) === 1 and $method === 'get') {
            return $this->_setting[$args[0]];
        }

        if (count($args) === 2 and $method === 'set') {
            return $this->_setting[$args[0]] = $args[1];
        }

        if (in_array($method, Router::ACCESS)) {
            return $this->router($method, $args);
        }

        if (isset($this->_setting[$method]) and is_object($this->_setting[$method])) {
            if (is_callable($this->_setting[$method])) {
                return call_user_func_array($this->_setting[$method], $args);
            }

            return $this->_setting[$method];
        }
    }

    /**
     * Run App
     * @return void
     */
    public function run()
    {
        $this->_router->run();
    }
}