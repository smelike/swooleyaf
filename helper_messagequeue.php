<?php
/**
 * 消息队列消费
 * User: 姜伟
 * Date: 2017/8/24 0024
 * Time: 17:02
 */
include_once __DIR__ . '/syLibs/autoload.php';
ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');

function syMessageQueueHelp(){
    print_r('帮助信息' . PHP_EOL);
    print_r('-t 消息队列类型: redis kafka' . PHP_EOL);
}

function startRedisConsumer() {
    global $consumer;
    $consumer->start();
}

$type = \Tool\Tool::getClientOption('-t');
if($type == 'redis'){
    $consumer = new \MessageQueue\Consumer\RedisConsumer();
    pcntl_signal(SIGALRM, 'startRedisConsumer');

    while (true) {
        pcntl_alarm(1);
        pcntl_signal_dispatch();
        sleep(1);
    }
} else if($type == 'kafka'){
    \MessageQueue\Consumer\KafkaConsumer::getInstance();
    while (true) {
        \MessageQueue\Consumer\KafkaConsumer::getInstance()->consumeMsg();
    }
} else {
    syMessageQueueHelp();
}