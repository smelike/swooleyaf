<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-5
 * Time: 16:42
 */
namespace SyServer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Singletons\Etcd3Singleton;
use Exception\Swoole\HttpServerException;
use Exception\Validator\ValidatorException;
use Log\Log;
use Request\RequestSign;
use Response\Result;
use Response\SyResponseHttp;
use SyModule\SyModuleApi;
use SyModule\SyModuleOrder;
use SyModule\SyModuleService;
use SyModule\SyModuleUser;
use Tool\SyPack;
use Tool\Tool;
use Yaf\Registry;
use Yaf\Request\Http;

class HttpServer extends BaseServer {
    const RESPONSE_RESULT_TYPE_FORBIDDEN = 0; //响应结果类型-拒绝请求
    const RESPONSE_RESULT_TYPE_ACCEPT = 1; //响应结果类型-允许请求执行业务
    const RESPONSE_RESULT_TYPE_ALLOW = 2; //响应结果类型-不执行业务，直接返回响应

    /**
     * 跨域共享资源数组
     * @var array
     */
    protected $_cors = [];
    /**
     * HTTP响应
     * @var \swoole_http_response
     */
    private static $_response = null;
    /**
     * 请求标识
     * @var bool true:外部请求 false:内部请求
     */
    private static $_reqTag = true;
    /**
     * 响应消息
     * @var string
     */
    private static $_rspMsg = '';
    /**
     * swoole请求头信息数组
     * @var array
     */
    private static $_reqHeaders = [];
    /**
     * swoole服务器信息数组
     * @var array
     */
    private static $_reqServers = [];
    /**
     * swoole task请求数据
     * @var string
     */
    private static $_reqTask = null;
    /**
     * @var \Tool\SyPack
     */
    private $_messagePack = null;
    /**
     * 最大签名缓存数量
     * @var int
     */
    private static $_sySignMaxNum = 0;
    /**
     * 当前签名缓存数量
     * @var int
     */
    private static $_sySignNowNum = 0;
    /**
     * 接口签名缓存列表
     * @var \swoole_table
     */
    private static $_sySigns = null;

    public function __construct(int $port,int $weight) {
        parent::__construct($port, $weight);
        define('SY_API', true);
        //设置静态文件访问，swoole1.9.17+才能使用
        $this->_configs['swoole']['document_root'] = SY_ROOT . '/static';
        $this->_configs['swoole']['enable_static_handler'] = true;
        $this->_cors = \Yaconf::get('cors');
        $this->_cors['allow']['headerStr'] = isset($this->_cors['allow']['headers']) ? implode(', ', $this->_cors['allow']['headers']) : '';
        $this->_cors['allow']['methodStr'] = isset($this->_cors['allow']['methods']) ? implode(', ', $this->_cors['allow']['methods']) : '';
        $this->_messagePack = new SyPack();
        self::$_sySignNowNum = 0;
        self::$_sySignMaxNum = (int)$this->_configs['server']['cachenum']['sign'];
    }

    private function __clone() {
    }

    /**
     * 添加签名缓存
     * @param string $sign 签名信息
     * @return bool
     */
    public static function addApiSign(string $sign) : bool {
        $needSign = substr($sign, 16);
        if (self::$_sySigns->exist($needSign)) {
            return false;
        } else if (self::$_sySignNowNum < self::$_sySignMaxNum) {
            self::$_sySigns->set($needSign, [
                'sign' => $needSign,
                'time' => time(),
            ]);
            self::$_sySignNowNum++;

            return true;
        } else {
            return true;
        }
    }

    /**
     * 清理签名缓存
     */
    private function clearApiSign() {
        $time = time() - 300;
        foreach (self::$_sySigns as $eSign) {
            if ($eSign['time'] <= $time) {
                self::$_sySigns->del($eSign['sign']);
            }
        }
        self::$_sySignNowNum = count(self::$_sySigns);
    }

    public function start() {
        $this->initStartBase();
        self::$_sySigns = new \swoole_table(self::$_sySignMaxNum);
        self::$_sySigns->column('sign', \swoole_table::TYPE_STRING, 32);
        self::$_sySigns->column('time', \swoole_table::TYPE_INT, 4);
        self::$_sySigns->create();

        //初始化swoole服务
        $this->_server = new \swoole_websocket_server($this->_host, $this->_port);
        $this->_server->set($this->_configs['swoole']);
        //注册方法
        $this->_server->on('start', [$this, 'onStart']);
        $this->_server->on('managerStart', [$this, 'onManagerStart']);
        $this->_server->on('workerStart', [$this, 'onWorkerStart']);
        $this->_server->on('workerStop', [$this, 'onWorkerStop']);
        $this->_server->on('workerError', [$this, 'onWorkerError']);
        $this->_server->on('shutdown', [$this, 'onShutdown']);
        $this->_server->on('request', [$this, 'onRequest']);
        $this->_server->on('task', [$this, 'onTask']);
        $this->_server->on('finish', [$this, 'onFinish']);
        $this->_server->on('handshake', [$this, 'onHandshake']);
        $this->_server->on('message', [$this, 'onMessage']);
        $this->_server->on('close', [$this, 'onClose']);

        echo "\e[1;36m start " . SY_MODULE . ":\e[0m \e[1;32m \t[success] \e[0m" . PHP_EOL;

        //启动服务
        $this->_server->start();
    }

    /**
     * 设置响应头信息
     * @param \swoole_http_response $response
     * @param array|bool $headers
     */
    private function setRspHeaders(\swoole_http_response $response, $headers) {
        if(is_array($headers)){
            if(!isset($headers['Content-Type'])){
                $response->header('Content-Type', 'application/json; charset=utf-8');
            }

            foreach ($headers as $headerName => $headerVal) {
                $response->header($headerName, $headerVal);
            }

            if(isset($headers['Location'])){
                $response->status(302);
            }
        } else {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Content-Type', 'application/json; charset=utf-8');
        }
    }

    /**
     * 设置响应cookie信息
     * @param \swoole_http_response $response
     * @param array|bool $cookies
     */
    private function setRspCookies(\swoole_http_response $response, $cookies) {
        if(is_array($cookies)){
            foreach ($cookies as $cookie) {
                if(is_array($cookie) && isset($cookie['key'])
                    && (is_string($cookie['key']) || is_numeric($cookie['key']))){
                    $cookieName = preg_replace('/[^0-9a-zA-Z\-\_]+/', '', $cookie['key']);
                    $value = Tool::getArrayVal($cookie, 'value', null);
                    $expires = Tool::getArrayVal($cookie, 'expires', 0);
                    $path = Tool::getArrayVal($cookie, 'path', '/');
                    $domain = Tool::getArrayVal($cookie, 'domain', '');
                    $secure = Tool::getArrayVal($cookie, 'secure', false);
                    $httpOnly = Tool::getArrayVal($cookie, 'httponly', false);
                    $response->cookie($cookieName, $value, $expires, $path, $domain, $secure, $httpOnly);
                }
            }
        }
    }

    /**
     * 生成web socket服务端签名
     * @param string $socketKey 客户端密钥
     * @return bool|string
     */
    public static function createSocketAccept(string $socketKey) {
        if (is_null($socketKey)) {
            return false;
        } else if(preg_match('/^[0-9a-zA-Z\+\/]{21}[AQgw]\={2}$/', $socketKey) == 0){
            return false;
        } else if(strlen(base64_decode($socketKey)) != 16){
            return false;
        }

        return base64_encode(sha1($socketKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',true));
    }

    /**
     * 校验服务端签名是否正确
     * @param string $socketKey 客户端密钥
     * @param string $socketAccept 服务端签名
     * @return bool
     * @throws \Exception\Swoole\HttpServerException
     */
    public static function checkSocketAccept(string $socketKey,string $socketAccept) : bool {
        if (is_null($socketAccept)) {
            throw new HttpServerException('服务端签名不能为空', ErrorCode::SWOOLE_SERVER_PARAM_ERROR);
        }

        $nowAccept = self::createSocketAccept($socketKey);
        if ($nowAccept === false) {
            return false;
        } else {
            return $nowAccept === $socketAccept;
        }
    }

    /**
     * 初始化公共数据
     * @param \swoole_http_request $request
     */
    private function initCommon(\swoole_http_request $request) {
        Registry::del(Server::REGISTRY_NAME_SERVICE_ERROR);
        $_POST = $request->post ?? [];
        $_SESSION = [];
        self::$_reqHeaders = $request->header ?? [];
        self::$_reqServers = $request->server ?? [];
        self::$_reqTag = isset(self::$_reqHeaders[Server::SERVER_HTTP_TAG_REQUEST_HEADER]) ? false : true;
        self::$_rspMsg = '';
        $this->createReqId();

        $taskData = $_POST[Server::SERVER_DATA_KEY_TASK] ?? '';
        self::$_reqTask = is_string($taskData) && (strlen($taskData) > 0) ? $taskData : null;

        $_SERVER = [];
        foreach (self::$_reqServers as $key => $val) {
            $_SERVER[strtoupper($key)] = $val;
        }
        foreach (self::$_reqHeaders as $key => $val) {
            $_SERVER[strtoupper($key)] = $val;
        }
        if(!isset($_SERVER['HTTP_HOST'])){
            $_SERVER['HTTP_HOST'] = $this->_host . ':' . $this->_port;
        }
        if(!isset($_SERVER['REQUEST_URI'])){
            $_SERVER['REQUEST_URI'] = '/';
        }
    }

    /**
     * 清理请求数据
     */
    private function clearRequest() {
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
        $_SERVER = [];
        $_SESSION = [];
        $GLOBALS['HTTP_RAW_POST_DATA'] = '';
        self::$_reqTag = true;
        self::$_reqTask = null;
        self::$_reqHeaders = [];
        self::$_reqServers = [];
        self::$_response = null;
        self::$_reqId = '';
        self::$_rspMsg = '';

        //清除yaf注册常量
        Registry::del(Server::REGISTRY_NAME_REQUEST_HEADER);
        Registry::del(Server::REGISTRY_NAME_REQUEST_SERVER);
        Registry::del(Server::REGISTRY_NAME_RESPONSE_HEADER);
        Registry::del(Server::REGISTRY_NAME_RESPONSE_COOKIE);

        self::$_syServer->set(self::$_serverToken, [
            'memory_usage' => memory_get_usage(),
        ]);
    }

    /**
     * 处理请求头
     * @param array $headers 响应头配置
     * @return int
     */
    private function handleReqHeader(&$headers) : int {
        $headers['Access-Control-Allow-Origin'] = '*';
        $headers['Access-Control-Allow-Credentials'] = 'true';

        if (isset($_SERVER['ACCESS-CONTROL-REQUEST-METHOD'])) { //校验请求方式
            $methodStr = ', ' . strtoupper(trim($_SERVER['ACCESS-CONTROL-REQUEST-METHOD']));
            if ( strpos(', ' . $this->_cors['allow']['methodStr'], $methodStr) === false) {
                return HttpServer::RESPONSE_RESULT_TYPE_FORBIDDEN;
            }
        }

        if (isset($_SERVER['ACCESS-CONTROL-REQUEST-HEADERS'])) { //校验请求头
            $checkRes = true;
            $controlReqHeaders = explode(',', strtolower($_SERVER['ACCESS-CONTROL-REQUEST-HEADERS']));
            foreach ($controlReqHeaders as $eHeader) {
                $headerName = trim($eHeader);
                if ((strlen($headerName) > 0) && (!in_array($headerName, $this->_cors['allow']['headers']))) {
                    $checkRes = false;
                    break;
                }
            }

            if (!$checkRes) {
                return HttpServer::RESPONSE_RESULT_TYPE_FORBIDDEN;
            }
        }

        $reqMethod = strtoupper(Tool::getArrayVal($_SERVER, 'REQUEST_METHOD', 'GET'));
        if ($reqMethod == 'OPTIONS') {
            //预请求OPTIONS的响应结果有效时间
            $headers['Access-Control-Max-Age'] = $this->_cors['options']['maxage'];
            $headers['Access-Control-Allow-Methods'] = $this->_cors['allow']['headerStr'];
            $headers['Access-Control-Allow-Headers'] = $this->_cors['allow']['methodStr'];
            return HttpServer::RESPONSE_RESULT_TYPE_ALLOW;
        }

        return HttpServer::RESPONSE_RESULT_TYPE_ACCEPT;
    }

    /**
     * 处理请求业务
     * @param array $initRspHeaders 初始化响应头
     * @return string
     */
    private function handleReqService(\swoole_http_request $request,array $initRspHeaders) : string {
        $uri = Tool::getArrayVal(self::$_reqServers, 'request_uri', '/');
        switch ($uri) {
            case '/syinfo' :
                self::$_syServer->incr(self::$_serverToken, 'request_times', 1);
                $result = Tool::jsonEncode($this->_server->stats());
                break;
            case '/phpinfo' :
                ob_start();
                phpinfo();
                $result = ob_get_contents();
                ob_end_clean();
                break;
            case '/serverctl' :
                $resArr = [
                    'code' => 0,
                ];
                $_GET = $request->get ?? [];
                if(!(isset($_GET['server_ip']) && is_string($_GET['server_ip']))){
                    $resArr['code'] = ErrorCode::COMMON_PARAM_ERROR;
                    $resArr['msg'] = '服务IP必须设置';
                } else if(!(isset($_GET['server_port']) && is_string($_GET['server_port']))){
                    $resArr['code'] = ErrorCode::COMMON_PARAM_ERROR;
                    $resArr['msg'] = '服务端口必须设置';
                } else if(!(isset($_GET['server_status']) && in_array($_GET['server_status'], [Server::SERVER_STATUS_CLOSE, Server::SERVER_STATUS_OPEN], true))){
                    $resArr['code'] = ErrorCode::COMMON_PARAM_ERROR;
                    $resArr['msg'] = '服务状态必须设置';
                } else {
                    $moduleKey = Etcd3Singleton::getInstance()->getPrefixModules() . hash('crc32b', $_GET['server_ip'] . ':' . $_GET['server_port']);
                    $configStr = Etcd3Singleton::getInstance()->get($moduleKey);
                    if($configStr === false){
                        $resArr['code'] = ErrorCode::COMMON_PARAM_ERROR;
                        $resArr['msg'] = '服务不存在';
                    } else {
                        $configArr = Tool::jsonDecode($configStr);
                        $configArr['status'] = $_GET['server_status'];
                        Etcd3Singleton::getInstance()->set($moduleKey, Tool::jsonEncode($configArr, JSON_UNESCAPED_UNICODE));

                        $resArr['data'] = [
                            'update_num' => 1,
                        ];
                    }
                }

                $result = Tool::jsonEncode($resArr, JSON_UNESCAPED_UNICODE);
                break;
            default:
                self::$_reqStartTime = microtime(true);
                self::$_syServer->incr(self::$_serverToken, 'request_times', 1);
                $healthTag = $this->sendReqHealthCheckTask($uri);
                $_GET = $request->get ?? [];
                $_FILES = $request->files ?? [];
                $_COOKIE = $request->cookie ?? [];
                $GLOBALS['HTTP_RAW_POST_DATA'] = $request->rawContent();
                $_POST[RequestSign::KEY_SIGN] = $_GET[RequestSign::KEY_SIGN] ?? '';
                unset($_GET[RequestSign::KEY_SIGN]);
                //注册全局信息
                Registry::set(Server::REGISTRY_NAME_REQUEST_HEADER, self::$_reqHeaders);
                Registry::set(Server::REGISTRY_NAME_REQUEST_SERVER, self::$_reqServers);
                Registry::set(Server::REGISTRY_NAME_RESPONSE_HEADER, $initRspHeaders);
                Registry::set(Server::REGISTRY_NAME_RESPONSE_COOKIE, []);

                try {
                    $result = $this->_app->bootstrap()->getDispatcher()->dispatch(new Http($uri))->getBody();
                } catch (\Exception $e){
                    SyResponseHttp::header('Content-Type', 'application/json; charset=utf-8');
                    if (!($e instanceof ValidatorException)) {
                        Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());
                    }

                    $error = new Result();
                    if(is_numeric($e->getCode())){
                        $error->setCodeMsg((int)$e->getCode(), $e->getMessage());
                    } else {
                        $error->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, '服务出错');
                    }

                    $result = $error->getJson();
                }
                $this->reportLongTimeReq($uri, array_merge($_GET, $_POST));
                self::$_syHealths->del($healthTag);

                break;
        }

        return $result;
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
            case Server::TASK_TYPE_CLEAR_API_SIGN_CACHE:
                $this->clearApiSign();
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

    public function onWorkerStart(\swoole_server $server, $workerId){
        $this->basicWorkStart($server, $workerId);
    }

    public function onWorkerStop(\swoole_server $server, int $workerId){
        $this->basicWorkStop($server, $workerId);
    }

    public function onWorkerError(\swoole_server $server, $workId, $workPid, $exitCode){
        $this->basicWorkError($server, $workId, $workPid, $exitCode);

        if (self::$_response) {
            $this->setRspCookies(self::$_response, Registry::get(Server::REGISTRY_NAME_RESPONSE_COOKIE));
            $this->setRspHeaders(self::$_response, Registry::get(Server::REGISTRY_NAME_RESPONSE_HEADER));

            $json = new Result();
            $json->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, ErrorCode::getMsg(ErrorCode::COMMON_SERVER_ERROR));
            if (self::$_reqTag) {
                self::$_response->end($json->getJson());
            } else {
                self::$_response->end($json->getJson() . Server::SERVER_HTTP_TAG_RESPONSE_EOF);
            }
        }
    }

    /**
     * web socket握手
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return bool
     */
    public function onHandshake(\swoole_http_request $request,\swoole_http_response $response) {
        $socketAccept = self::createSocketAccept(Tool::getArrayVal($request->header, 'sec-websocket-key', null));
        if ($socketAccept === false) {
            $response->end();
            return false;
        }

        $origin = isset($request->header['origin']) ? trim($request->header['origin']) : '';
        $origins = Tool::getArrayVal($this->_cors, 'allow.origins', [], true);
        if ((strlen($origin) > 0) && !empty($origins)) { //校验origin是否允许
            $checkRes = false;
            foreach ($origins as $eOrigin) {
                $startIndex = -1 * strlen($eOrigin);
                if (substr($origin, $startIndex) === $eOrigin) {
                    $checkRes = true;
                    break;
                }
            }

            if(!$checkRes){
                $response->end();
                return false;
            }
        }

        $response->header('Upgrade', 'websocket');
        $response->header('Connection', 'Upgrade');
        $response->header('Sec-WebSocket-Accept', $socketAccept);
        $response->header('Sec-WebSocket-Version', '13');
        $response->header('Keep-Alive', 'off');
        $response->status(101);
        $response->end();

        $fd = $request->fd;
        $server = $this->_server;
        $this->_server->defer(function() use ($fd, $server) {
            $server->push($fd, "hello, welcome", WEBSOCKET_OPCODE_TEXT);
        });

        return true;
    }

    /**
     * 接受socket消息
     * 消息格式：abcde
     * <pre>
     * 格式说明：
     *     a:消息头长度，值固定为16
     *     b:消息内容长度，无符号整数
     *     c:消息执行命令标识，4位字符串
     *     d:保留字段，值固定为0000
     *     e:消息内容，json格式
     * </pre>
     * @param \swoole_websocket_server $server
     * @param \swoole_websocket_frame $frame
     */
    public function onMessage(\swoole_websocket_server $server,\swoole_websocket_frame $frame) {
        $result = new Result();
        if ($frame->opcode != WEBSOCKET_OPCODE_BINARY) {
            $result->setCodeMsg(ErrorCode::COMMON_PARAM_ERROR, '只接受二进制数据');
            $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
            return;
        } else if (!$frame->finish) { //数据未发送完
            return;
        }

        $message = $this->_messagePack->unpackData($frame->data);
        $command = $this->_messagePack->getCommand();
        $commandData = $this->_messagePack->getData();
        $this->_messagePack->init();
        if ($message === false) {
            $result->setCodeMsg(ErrorCode::COMMON_PARAM_ERROR, '消息格式不正确');
            $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
            return;
        }

        switch ($command) {
            case SyPack::COMMAND_TYPE_SOCKET_CLIENT_CLOSE:
                $server->close($frame->fd);
                break;
            case SyPack::COMMAND_TYPE_SOCKET_CLIENT_CHECK_STATUS:
                $result->setData([
                    'status' => $server->exist($frame->fd) ? 1 : 0,
                    'detail' => $server->exist($frame->fd) ? $server->connection_info($frame->fd, null, true) : [],
                ]);
                $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
                break;
            case SyPack::COMMAND_TYPE_SOCKET_CLIENT_GET_SERVER:
                $result->setData([
                    'sy_version' => SY_VERSION,
                    'server_type' => 'swoole-http-server',
                    'swoole_version' => SWOOLE_VERSION,
                    'yaf_version' => \YAF\VERSION,
                ]);
                $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
                break;
            case SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_API_REQ:
                try {
                    if (($commandData['api_module'] == Server::MODULE_NAME_API) && ($commandData['api_method'] == 'GET')) {
                        $handleRes = SyModuleApi::getInstance()->sendGetReq($commandData['api_uri'], $commandData['api_params']);
                    } else if ($commandData['api_module'] == Server::MODULE_NAME_API) {
                        $handleRes = SyModuleApi::getInstance()->sendPostReq($commandData['api_uri'], $commandData['api_params']);
                    } else if ($commandData['api_module'] == Server::MODULE_NAME_ORDER) {
                        $handleRes = SyModuleOrder::getInstance()->sendApiReq($commandData['api_uri'], $commandData['api_params']);
                    } else if ($commandData['api_module'] == Server::MODULE_NAME_USER) {
                        $handleRes = SyModuleUser::getInstance()->sendApiReq($commandData['api_uri'], $commandData['api_params']);
                    } else if ($commandData['api_module'] == Server::MODULE_NAME_SERVICE) {
                        $handleRes = SyModuleService::getInstance()->sendApiReq($commandData['api_uri'], $commandData['api_params']);
                    } else {
                        $handleRes = false;
                    }

                    if ($handleRes === false) {
                        $result->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, '服务处理失败');
                    } else {
                        $result = $handleRes;
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

                    $result->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, '服务出错');
                } finally {
                    if ($result instanceof Result) {
                        $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
                    } else {
                        $server->push($frame->fd, $result, WEBSOCKET_OPCODE_TEXT, true);
                    }
                }
                break;
            case SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ:
                try {
                    if($commandData['task_module'] == Server::MODULE_NAME_ORDER){
                        $handleRes = SyModuleOrder::getInstance()->sendTaskReq($commandData['task_command'], $commandData['task_params']);
                    } else if($commandData['task_module'] == Server::MODULE_NAME_USER){
                        $handleRes = SyModuleUser::getInstance()->sendTaskReq($commandData['task_command'], $commandData['task_params']);
                    } else if($commandData['task_module'] == Server::MODULE_NAME_SERVICE){
                        $handleRes = SyModuleService::getInstance()->sendTaskReq($commandData['task_command'], $commandData['task_params']);
                    } else {
                        $handleRes = SyModuleApi::getInstance()->sendTaskReq($commandData['task_command'], $commandData['task_params']);
                    }

                    if ($handleRes === false) {
                        $result->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, '服务处理失败');
                    } else {
                        $result->setData([
                            'result' => 'send task success',
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

                    $result->setCodeMsg(ErrorCode::COMMON_SERVER_ERROR, '服务出错');
                } finally {
                    $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
                }
                break;
            default:
                $result->setCodeMsg(ErrorCode::COMMON_PARAM_ERROR, '命令不存在');
                $server->push($frame->fd, $result->getJson(), WEBSOCKET_OPCODE_TEXT, true);
                break;
        }
    }

    public function onTask(\swoole_server $server, int $taskId, int $fromId, string $data){
        $handleRes = $this->handleTaskBase($server, $taskId, $fromId, $data);
        if(is_string($handleRes)){
            return $handleRes;
        }

        $result = new Result();
        if(($handleRes['command'] == SyPack::COMMAND_TYPE_SOCKET_CLIENT_SEND_TASK_REQ) && $this->handleTaskClient($handleRes['params'])){
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
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     */
    public function onRequest(\swoole_http_request $request,\swoole_http_response $response){
        self::$_response = $response;
        $this->initCommon($request);
        $startTime = microtime(true);
        $inLog = 'http request start,host:' . $_SERVER['HTTP_HOST'] . ',uri:' . $_SERVER['REQUEST_URI'] . ',memory:' . memory_get_usage();
        \SeasLog::info($inLog);
        if(is_null(self::$_reqTask)){
            $rspHeaders = [];
            $handleHeaderRes = $this->handleReqHeader($rspHeaders);
            if($handleHeaderRes == HttpServer::RESPONSE_RESULT_TYPE_ACCEPT){
                self::$_rspMsg = $this->handleReqService($request, $rspHeaders);
                $this->setRspCookies($response, Registry::get(Server::REGISTRY_NAME_RESPONSE_COOKIE));
                $this->setRspHeaders($response, Registry::get(Server::REGISTRY_NAME_RESPONSE_HEADER));
            } else if($handleHeaderRes == HttpServer::RESPONSE_RESULT_TYPE_ALLOW){
                $rspHeaders['Content-Type'] = 'application/json; charset=utf-8';
                $this->setRspHeaders($response, $rspHeaders);
            } else {
                $rspHeaders['Content-Type'] = 'text/plain; charset=utf-8';
                $this->setRspHeaders($response, $rspHeaders);
            }
        } else {
            self::$_syServer->incr(self::$_serverToken, 'request_times', 1);
            $this->_server->task(self::$_reqTask, mt_rand(1, $this->_taskMaxId));
            $result = new Result();
            $result->setData([
                'msg' => 'task received',
            ]);
            self::$_rspMsg = $result->getJson();
        }

        if (self::$_reqTag) {
            $response->end(self::$_rspMsg);
        } else {
            $response->end(self::$_rspMsg . Server::SERVER_HTTP_TAG_RESPONSE_EOF);
        }

        $handleTime = microtime(true) - $startTime;
        $outLog = 'http request end,host:' . $_SERVER['HTTP_HOST'] . ',uri:' . $_SERVER['REQUEST_URI'] . ',memory:' . memory_get_usage() . ',time:' . $handleTime;
        \SeasLog::info($outLog);
        $this->clearRequest();
    }
}