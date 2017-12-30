<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 13:32
 */
namespace SyTask;

use Constant\Server;
use MessageQueue\Consumer\Redis\AddJsLogService;
use MessageQueue\Consumer\Redis\AddMysqlLogService;
use Tool\SyPack;

class SyModuleApiTask extends SyModuleTaskBase implements SyModuleTaskInterface {
    public function __construct() {
        parent::__construct();
        $this->moduleTag = Server::MODULE_NAME_API;
    }

    private function __clone() {
    }

    public function handleTask(array $data) {
        if($data['wxcache_refresh']){
            $this->handleRefreshWxCache([
                'app_id' => $data['wxcache_appid'],
                'access_token' => $data['wxcache_accesstoken'],
                'js_ticket' => $data['wxcache_jsticket'],
                'projects' => $data['projects'],
            ], $this->moduleTag);
        }

//        //添加mysql日志任务
//        $mysqlLogService = new AddMysqlLogService();
//        $mysqlLogService->handleMessage([]);
//
//        //添加js日志任务
//        $jsLogService = new AddJsLogService();
//        $jsLogService->handleMessage([]);

        if($data['clear_apisign']){ //清理api签名
            $this->syPack->setCommandAndData(SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ, [
                'task_module' => $this->moduleTag,
                'task_command' => Server::TASK_TYPE_CLEAR_API_SIGN_CACHE,
                'task_params' => [],
            ]);
            $apiTaskStr = $this->syPack->packData();
            $this->syPack->init();
            foreach ($data['projects'] as $eProject) {
                $this->sendSyTaskReq($eProject['host'], $eProject['port'], $apiTaskStr, 'http');
            }
        }
        if($data['clear_localuser']){ //清除本地用户信息缓存
            $this->clearLocalUserCache([
                'projects' => $data['projects'],
            ], $this->moduleTag);
        }
    }
}