<?php
/**
 * 控制器方法存在插件
 * User: 姜伟
 * Date: 2017/6/22 0022
 * Time: 9:42
 */
namespace SyFrame\Plugins;

use Constant\ErrorCode;
use Exception\Validator\ValidatorException;
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

class MethodExistPlugin extends Plugin_Abstract {
    /**
     * 控制器根目录
     * @var string
     */
    private $controllerPath = '';
    /**
     * 已存在的方法数组
     * @var array
     */
    private $existMethods = [];

    public function __construct() {
        $this->controllerPath = APP_PATH . '/application/controllers/';
    }

    private function __clone() {
    }

    public function routerShutdown(Request_Abstract $request,Response_Abstract $response) {
        $uriArr = explode('/', $request->getRequestUri());
        if(count($uriArr) != 4){
            throw new ValidatorException('路由格式错误', ErrorCode::COMMON_ROUTE_URI_FORMAT_ERROR);
        }

        $controllerTag = '\\' . strtolower($uriArr[2]);
        if(!isset($this->existMethods[$controllerTag])){
            $file = $this->controllerPath . $uriArr[2] . '.php';
            if(!file_exists($file)){
                throw new ValidatorException('控制器不存在', ErrorCode::COMMON_ROUTE_CONTROLLER_NOT_EXIST);
            }

            require_once $file;

            $this->existMethods[$controllerTag] = [];
        }

        $methodName = strtolower($uriArr[3]);
        if(!isset($this->existMethods[$controllerTag][$methodName])){
            if(method_exists('\\' . $uriArr[2] . 'Controller', $uriArr[3] . 'Action')){
                $this->existMethods[$controllerTag][$methodName] = 1;
            } else {
                throw new ValidatorException('方法不存在', ErrorCode::COMMON_ROUTE_ACTION_NOT_EXIST);
            }
        }
    }
}