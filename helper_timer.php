<?php
/**
 * 定时任务执行脚本
 * User: 姜伟
 * Date: 2017-3-19
 * Time: 16:57
 */
include_once __DIR__ . '/syLibs/autoload.php';
ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');

function handleSyTask() {
    global $timer;
    $timer->handleTask();
}

\Tool\Timer\TimerTool::refreshTimer();
////异步定时任务
//swoole_process::signal(SIGALRM, 'handleSyTask');
//swoole_process::alarm(1000000);

//同步定时任务
$apiDomain = trim(\Tool\Tool::getClientOption('-api'));
$timer = new \Tool\Timer\SyTimer($apiDomain);
echo 'start timer at ' . date('Y-m-d H:i:s') . PHP_EOL;

pcntl_signal(SIGALRM, 'handleSyTask');

while (true) {
    pcntl_alarm(1);
    pcntl_signal_dispatch();
    sleep(1);
}