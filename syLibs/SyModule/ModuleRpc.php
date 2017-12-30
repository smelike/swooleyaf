<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-22
 * Time: 下午9:24
 */
namespace SyModule;

use Log\Log;
use Request\SyRequestRpc;
use Tool\Tool;
use Traits\SimpleTrait;

abstract class ModuleRpc extends ModuleBase {
    use SimpleTrait;

    /**
     * @var \Request\SyRequestRpc
     */
    private $syRequest = null;

    protected function init() {
        parent::init();
        $this->syRequest = new SyRequestRpc();
    }

    /**
     * 发送api请求
     * @param string $uri 请求uri
     * @param array $params 请求参数数组
     * @param bool $async 是否异步 true:异步 false:同步
     * @param callable $callback 回调函数
     * @return bool|string
     */
    public function sendApiReq(string $uri,array $params,bool $async=false,callable $callback=null) {
        $this->syRequest->init('rpc');
        $this->syRequest->setAsync($async);
        $serverInfo = $this->getRpcServerInfo();
        $this->syRequest->setHost($serverInfo['host']);
        $this->syRequest->setPort($serverInfo['port']);
        $this->syRequest->setTimeout(2000);
        $content = $this->syRequest->sendApiReq($uri, $params, $callback);
        if($content === false){
            Log::error('send api req fail: uri=' . $uri . '; params=' . Tool::jsonEncode($params));
        }

        return $content;
    }

    /**
     * 发送TASK请求
     * @param string $command task任务命令
     * @param array $params 请求参数数组
     * @param callable $callback 回调函数
     * @return bool|string
     */
    public function sendTaskReq(string $command,array $params,callable $callback=null) {
        $this->syRequest->init('rpc');
        $this->syRequest->setAsync(true);
        $serverInfo = $this->getRpcServerInfo();
        $this->syRequest->setHost($serverInfo['host']);
        $this->syRequest->setPort($serverInfo['port']);
        $this->syRequest->setTimeout(2000);
        $content = $this->syRequest->sendTaskReq($command, $params, $callback);
        if($content === false){
            Log::error('send task req fail: command=' . $command . ' params=' . Tool::jsonEncode($params, JSON_UNESCAPED_UNICODE));
        }

        return $content;
    }
}