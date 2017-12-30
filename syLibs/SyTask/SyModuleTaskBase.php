<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-11-14
 * Time: 下午7:06
 */
namespace SyTask;

use Constant\Server;
use Tool\SyPack;
use Tool\Tool;

abstract class SyModuleTaskBase {
    /**
     * @var \Tool\SyPack
     */
    protected $syPack = null;
    /**
     * @var string
     */
    protected $moduleTag = '';

    public function __construct() {
        $this->syPack = new SyPack();
    }

    private function __clone() {
    }

    public function sendSyGetReq(string $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    public function sendSyTaskReq(string $host,int $port,string $taskStr,string $protocol) {
        if ($protocol == 'http') {
            $url = 'http://' . $host . ':' . $port;
            Tool::sendSyHttpTaskReq($url, $taskStr);
        } else {
            Tool::sendSyRpcReq($host, $port, $taskStr);
        }
    }

    protected function handleRefreshWxCache(array $data,string $moduleTag) {
        if(strlen($moduleTag) == 0){
            //刷新微信access token缓存
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ, [
                'task_command' => Server::TASK_TYPE_REFRESH_LOCAL_CACHE,
                'task_params' => [
                    'key' => 'wx01_' . $data['app_id'],
                    'value' => $data['access_token'],
                ],
            ]);
            $accessTokenStr = $this->syPack->packData();
            $this->syPack->init();

            //刷新微信js ticket缓存
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ, [
                'task_command' => Server::TASK_TYPE_REFRESH_LOCAL_CACHE,
                'task_params' => [
                    'key' => 'wx02_' . $data['app_id'],
                    'value' => $data['js_ticket'],
                ],
            ]);
            $jsTicketStr = $this->syPack->packData();
            $this->syPack->init();

            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $accessTokenStr, 'rpc');
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $jsTicketStr, 'rpc');
            }
        } else {
            //刷新微信access token缓存
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
                'task_module' => $moduleTag,
                'task_command' => Server::TASK_TYPE_REFRESH_LOCAL_CACHE,
                'task_params' => [
                    'key' => 'wx01_' . $data['app_id'],
                    'value' => $data['access_token'],
                ],
            ]);
            $accessTokenStr = $this->syPack->packData();
            $this->syPack->init();

            //刷新微信js ticket缓存
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
                'task_module' => $moduleTag,
                'task_command' => Server::TASK_TYPE_REFRESH_LOCAL_CACHE,
                'task_params' => [
                    'key' => 'wx02_' . $data['app_id'],
                    'value' => $data['js_ticket'],
                ],
            ]);
            $jsTicketStr = $this->syPack->packData();
            $this->syPack->init();

            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $accessTokenStr, 'http');
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $jsTicketStr, 'http');
            }
        }
    }

    protected function clearLocalUserCache(array $data,string $moduleTag) {
        if(strlen($moduleTag) == 0){
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ, [
                'task_command' => Server::TASK_TYPE_CLEAR_LOCAL_USER_CACHE,
                'task_params' => [],
            ]);
            $apiTaskStr = $this->syPack->packData();
            $this->syPack->init();
            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $apiTaskStr, 'rpc');
            }
        } else {
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
                'task_module' => $moduleTag,
                'task_command' => Server::TASK_TYPE_CLEAR_LOCAL_USER_CACHE,
                'task_params' => [],
            ]);
            $apiTaskStr = $this->syPack->packData();
            $this->syPack->init();
            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $apiTaskStr, 'http');
            }
        }
    }
}