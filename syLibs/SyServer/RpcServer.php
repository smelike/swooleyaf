<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/8/19 0019
 * Time: 8:33
 */
namespace SyServer;

use Constant\ErrorCode;
use Constant\Server;
use Exception\Validator\ValidatorException;
use Log\Log;
use Request\RequestSign;
use Response\Result;
use Tool\SyPack;
use Tool\Tool;
use Yaf\Registry;
use Yaf\Request\Http;

class RpcServer extends BaseServer {
    /**
     * @var \Tool\SyPack
     */
    private $_receivePack = null;

    public function __construct(int $port,int $weight) {
        parent::__construct($port, $weight);
        define('SY_API', false);
        $this->_configs['swoole']['open_length_check'] = true;
        $this->_configs['swoole']['package_max_length'] = Server::SERVER_PACKAGE_MAX_LENGTH;
        $this->_configs['swoole']['package_length_type'] = 'L';
        $this->_configs['swoole']['package_length_offset'] = 4;
        $this->_configs['swoole']['package_body_offset'] = 0;
        $this->_receivePack = new SyPack();
    }

    private function __clone() {
    }

    /**
     * 初始化请求数据
     * @param array $data
     */
    private function init(array $data) {
        $_GET = [];
        $_POST = $data;
        $_COOKIE = [];
        $_FILES = [];
        $_SESSION = [];
        unset($_POST[RequestSign::KEY_SIGN]);

        Registry::del(Server::REGISTRY_NAME_SERVICE_ERROR);
    }

    /**
     * 清理
     */
    private function clear() {
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_FILES = [];
        $_SESSION = [];

        Registry::del(Server::REGISTRY_NAME_SERVICE_ERROR);
    }

    public function start() {
        $this->initStartBase();
        //初始化swoole服务
        $this->_server = new \swoole_server($this->_host, $this->_port);
        $this->_server->set($this->_configs['swoole']);
        //注册方法
        $this->_server->on('start', [$this, 'onStart']);
        $this->_server->on('managerStart', [$this, 'onManagerStart']);
        $this->_server->on('workerStart', [$this, 'onWorkerStart']);
        $this->_server->on('workerStop', [$this, 'onWorkerStop']);
        $this->_server->on('workerError', [$this, 'onWorkerError']);
        $this->_server->on('shutdown', [$this, 'onShutdown']);
        $this->_server->on('receive', [$this, 'onReceive']);
        $this->_server->on('task', [$this, 'onTask']);
        $this->_server->on('finish', [$this, 'onFinish']);
        $this->_server->on('close', [$this, 'onClose']);

        echo "\e[1;36m start " . SY_MODULE . ":\e[0m \e[1;32m \t[success] \e[0m" . PHP_EOL;

        //启动服务
        $this->_server->start();
    }

    private function handleTaskClient(array $data) : bool {
        $result = true;
        $taskCommand = Tool::getArrayVal($data, 'task_command', '');
        switch ($taskCommand) {
            case Server::TASK_TYPE_REFRESH_SERVER_REGISTRY:
                $this->refreshRegisterServices();
                break;
            case Server::TASK_TYPE_REFRESH_LOCAL_CACHE:
                $taskData = Tool::getArrayVal($data, 'task_params', []);
                $this->setProjectCache($taskData['key'], $taskData['value']);
                break;
            case Server::TASK_TYPE_CLEAR_LOCAL_USER_CACHE:
                $this->clearLocalUsers();
                break;
            default:
                $result = false;
                break;
        }

        return $result;
    }

    private function handleApiReceive(array $data) {
        self::$_reqStartTime = microtime(true);
        $healthTag = $this->sendReqHealthCheckTask($data['api_uri']);
        $this->init($data['api_params']);
        try {
            $result = $this->_app->bootstrap()->getDispatcher()->dispatch(new Http($data['api_uri']))->getBody();
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());
            if (!($e instanceof ValidatorException)) {
                Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());
            }

            $result = new Result();
            if (is_numeric($e->getCode())) {
                $result->setCodeMsg((int)$e->getCode(), $e->getMessage());
            } else {
                $result->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, '服务出错');
            }
        } finally {
            $this->clear();
            $this->reportLongTimeReq($data['api_uri'], $data['api_params']);
        }
        self::$_syHealths->del($healthTag);

        return is_string($result) ? $result : $result->getJson();
    }

    private function handleTaskReceive(\swoole_server $server,string $data) {
        $server->task($data, mt_rand(1, $this->_taskMaxId));
        $result = new Result();
        $result->setData([
            'msg' => 'task received',
        ]);

        return $result->getJson();
    }

    private function handleReceive(\swoole_server $server,string $data) {
        if(!$this->_receivePack->unpackData($data)){
            $result = new Result();
            $result->setCodeMsg(ErrorCode::COMMON_PARAM_ERROR, '请求数据格式错误');
            return $result->getJson();
        }

        $command = $this->_receivePack->getCommand();
        $commandData = $this->_receivePack->getData();
        switch ($command) {
            case SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_API_REQ:
                $result = $this->handleApiReceive($commandData);
                break;
            case SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ:
                $result = $this->handleTaskReceive($server, $data);
                break;
            default:
                $result = new Result();
                $result->setCodeMsg(ErrorCode::COMMON_PARAM_ERROR, '请求命令不支持');
                break;
        }
        Registry::del(Server::REGISTRY_NAME_SERVICE_ERROR);

        return is_string($result) ? $result : $result->getJson();
    }

    public function onWorkerStart(\swoole_server $server, $workerId){
        $this->basicWorkStart($server, $workerId);
    }

    public function onWorkerStop(\swoole_server $server, int $workerId){
        $this->basicWorkStop($server, $workerId);
    }

    public function onWorkerError(\swoole_server $server, $workId, $workPid, $exitCode){
        $this->basicWorkError($server, $workId, $workPid, $exitCode);
    }

    public function onTask(\swoole_server $server, int $taskId, int $fromId, string $data){
        $handleRes = $this->handleTaskBase($server, $taskId, $fromId, $data);
        if(is_string($handleRes)){
            return $handleRes;
        }

        $result = new Result();
        if(($handleRes['command'] == SyPack::COMMAND_TYPE_RPC_CLIENT_SEND_TASK_REQ) && $this->handleTaskClient($handleRes['params'])){
            $result->setData([
                'result' => 'success',
            ]);
        } else {
            $result->setData([
                'result' => 'fail',
            ]);
        }

        return $result->getJson();
    }

    /**
     * 处理请求
     * @param \swoole_server $server
     * @param int $fd TCP客户端连接的唯一标识符
     * @param int $reactor_id Reactor线程ID
     * @param string $data 收到的数据内容
     */
    public function onReceive(\swoole_server $server,int $fd,int $reactor_id,string $data) {
        $this->createReqId();
        self::$_syServer->incr(self::$_serverToken, 'request_times', 1);
        $result = $this->handleReceive($server, $data);
        $this->_receivePack->setCommandAndData(SyPack::COMMAND_TYPE_RPC_SERVER_SEND_RSP, [
            'rsp_data' => $result,
        ]);
        $rspData = $this->_receivePack->packData();
        $this->_receivePack->init();

        $server->send($fd, $rspData);
        self::$_reqId = '';
    }
}