<?php
include_once __DIR__ . '/syLibs/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');

\Tool\SyXhprof::start();
$data3 = new \Tool\Timer\TimerData();
$data3->setExecTime(2, '0 * * * * *');
$data3->setUri('/Index/image/index');
$data3->setParams([
    'callback' => 'jwtest123',
]);

$res = \Tool\SyXhprof::run('xhprof');

echo 'http://xhprof-ui-address/index.php?run=' . $res['run_id'] . '&source=' . $res['source'] . PHP_EOL;