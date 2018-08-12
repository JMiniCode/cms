<?php

namespace JMCode\Cms;

use JMCode\Http\{Request, Response};

class App {

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
     * Run App
     *
     * @return void
     */
    public function run() {

    }
}