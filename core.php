<?php

$vendorDir = dirname(dirname(__DIR__));
$baseDir = dirname($vendorDir);

if (!defined('APP_DIR_PATH')) {
    define('APP_DIR_PATH', $baseDir . '\\app');
}