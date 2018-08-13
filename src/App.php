<?php

namespace JMCode\Cms;

use JMCode\Http\{Request, Response};

class App {

    private $_setting = [];
    private $_router;

    /**
     * @return Router
     */
    private function router($method = null, $args = []) {
        if(!$this->_router) {
            $this->_router = new Router();
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
    }

    /**
     * @param string $name
     * @param array $args
     * @return void
     */
    public function __call($method, $args)
    {
        if (count($args) === 0) {
            throw new \Exception('App::call - Аргументы не передана');
        }

        if($method === 'set' and count($args) === 2) {
            return $this->_setting[$args[0]] = $args[1];
        }

        if($method === 'get' and count($args) === 1) {
            return $this->_setting[$args[0]];
        }

        if (in_array($method, Router::ACCESS)) {
            $offset = 0;
            $call = $args[$offset];
            $path = '*';
            
            if(!is_callable($call)) {
                $ops = $call;

                while (is_array($ops) && count($ops) !== 0) {
                    $ops = $ops[0];
                }

                if (!is_callable($ops)) {
                    $offset = 1;
                    $path = $call;
                }
            }

            $args = array_slice($args, $offset);
            foreach($args AS $call) {
                $this->router($method, [$path, $call]);
            }
        }
    }

    /**
     * Run App
     * @return void
     */
    public function run() {
        echo '<pre>';
        print_r($this->router());
        echo '</pre>';
    }
}