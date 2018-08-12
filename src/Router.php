<?php

namespace JMCode\Cms;

class Router {

    const ACCESS = ['get', 'post', 'use', 'all'];

    private $_stack = [];

    public function __call($method, $args) {
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
            foreach ($args AS $call) {
                if(is_string($call) and file_exists($call)) {
                    $call = require($call);
                }

                if($call instanceof self) {
                    foreach($call->_stack AS $s) {
                        $this->_stack[] = [
                            $s[0],
                            $path . $s[1],
                            $s[2]
                        ];
                    }
                }
                
                if(is_callable($call)) {
                    $this->_stack[] = [
                        $method,
                        $path,
                        $call
                    ];
                }
            }
        }
    }
}