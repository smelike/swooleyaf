<?php
/**
 * 模块注册刷新
 * User: jw
 * Date: 17-9-2
 * Time: 下午8:53
 */
include_once __DIR__ . '/syLibs/autoload.php';
ini_set('display_errors', 'On');
error_reporting(E_ALL);
date_default_timezone_set('PRC');
define('SY_ROOT', __DIR__);
define('SY_ENV', 'dev');

$modulePrefix = \DesignPatterns\Singletons\Etcd3Singleton::getInstance()->getPrefixModules();
$registryList = \DesignPatterns\Singletons\Etcd3Singleton::getInstance()->getList($modulePrefix);
if ($registryList === false) {
    exit();
}

$syPack = new \Tool\SyPack();
foreach ($registryList['data'] as $eRegistry) {
    $serverData = \Tool\Tool::jsonDecode($eRegistry['value']);
    if ($serverData['module'] == \Constant\Server::MODULE_NAME_API) {
        $url = 'http://' . $serverData['host'] . ':' . $serverData['port'];
        $syPack->setCommandAndData(\Tool\SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
            'task_module' => $serverData['module'],
            'task_command' => \Constant\Server::TASK_TYPE_REFRESH_SERVER_REGISTRY,
            'task_params' => [],
        ]);
        $packStr = $syPack->packData();
        $syPack->init();
        \Tool\Tool::sendSyHttpTaskReq($url, $packStr);
    } else {
        $syPack->setCommandAndData(\Tool\SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ, [
            'task_command' => \Constant\Server::TASK_TYPE_REFRESH_SERVER_REGISTRY,
            'task_params' => [],
        ]);
        $packStr = $syPack->packData();
        $syPack->init();
        \Tool\Tool::sendSyRpcReq($serverData['host'], (int)$serverData['port'], $packStr);
    }
}
