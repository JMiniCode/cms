<?php

namespace JMCode\Cms;

class Router
{

    const ACCESS = ['get', 'post', 'use', 'all', 'put', 'delete'];

    private $_stack = [];
    private $_app;

    /**
     * Create Router
     * @param App|null $app
     */
    public function __construct(App $app = null)
    {
        if (!is_null($app)) {
            $this->_app = $app;
        }
    }

    /**
     * @param string $name
     * @param array $args
     * @return void
     */
    public function __call($name, $args)
    {
        if (in_array($name, Router::ACCESS)) {
            $offset = 0;
            $call = $args[$offset];
            $path = '*';

            if (!is_callable($call)) {
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
            foreach ($args as $call) {
                if (is_string($call) and file_exists($call)) {
                    $call = require($call);
                }

                if ($call instanceof self) {
                    foreach ($call->_stack as $i) {
                        if (is_callable($i[2])) {
                            $i[2] = $i[2]->bindTo($this->_app);
                        }
                        $this->_stack[] = [
                            $i[0],
                            $path . $i[1],
                            $i[2]
                        ];
                    }
                }

                if (is_callable($call)) {
                    $call = $call->bindTo($this->_app);
                    $this->_stack[] = [
                        $name,
                        $path,
                        $call
                    ];
                }
            }
        }
    }

    /**
     * Run Router
     *
     * @return array
     */
    public function run()
    {
        $count_routes = count($this->_stack);
        $count_handlers = 0;

        while (true) {
            $handle = (1 + $count_handlers);
            if (is_callable($this->_stack[$count_routes - $handle][2])) {
                if (count((new \ReflectionFunction($this->_stack[$count_routes - $handle][2]))->getParameters()) != 4) {
                    break;
                }

                $count_handlers++;
            }
        }

        $i = 0;
        $ignore_methods = ['use', 'all'];
        $args = [$this->_app->request(), $this->_app->response(), function ($err = null) {
            if ($err instanceof \Error) return $err;
            if (is_integer($err)) return $err;
            return 1;
        }];

        while ($i < $count_routes) {
            list($method, $url, $call) = $this->_stack[$i];

            if (!is_array($method, $ignore_methods) and !$request->isMethod($method)) {
                $i++;
                contine;
            }

            if ($i >= $count_routes - $count_handlers and count($args) === 3) {
                break;
            }

            if (is_callable($call)) {
                $next = call_user_func_array($call, $args);
                if ($next instanceof \Error and count($args) === 3) {
                    $args = array_merge([$next], $args);
                    $i = $count_routes - $count_handlers;
                } elseif (is_integer($next)) {
                    $i = $i + $next;
                } else {
                    $i++;
                }
            }
        }
    }
}