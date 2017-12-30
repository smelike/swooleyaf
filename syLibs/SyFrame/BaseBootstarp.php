<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/4 0004
 * Time: 20:05
 */
namespace SyFrame;

use Constant\ErrorCode;
use Exception\Swoole\ServerException;
use Tool\Tool;
use Traits\SimpleTrait;
use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Registry;

class BaseBootstarp {
    use SimpleTrait;

    /**
     * APP配置数组
     * @var array
     */
    private static $appConfigs = [];
    /**
     * 允许的模块列表
     * @var array
     */
    private static $acceptModules = [];
    /**
     * 默认模块名
     * @var string
     */
    private static $defaultModule = '';
    /**
     * 默认控制器名
     * @var string
     */
    private static $defaultController = '';
    /**
     * 默认动作名
     * @var string
     */
    private static $defaultAction = '';

    /**
     * 基础设置
     * @param \Yaf\Dispatcher $dispatcher
     * @throws \Exception\Swoole\ServerException
     */
    public static function initBase(Dispatcher $dispatcher) {
        //设置应用配置
        $config = Application::app()->getConfig();
        if(empty($config->toArray())){
            throw new ServerException('APP配置不能为空', ErrorCode::SWOOLE_SERVER_PARAM_ERROR);
        }

        self::$appConfigs = $config->toArray();
        if (isset(self::$appConfigs['application']['modules'])) {
            $str1 = preg_replace([
                '/[^0-9a-zA-Z\,]+/',
                '/\,+/',
            ], [
                '',
                ' ',
            ], self::$appConfigs['application']['modules']);
            $str2 = trim($str1);
            if ($str2 !== '') {
                self::$acceptModules = explode(' ', ucwords($str2));
            }
        }
        if (empty(self::$acceptModules)) {
            throw new ServerException('允许的模块列表不能为空', ErrorCode::SWOOLE_SERVER_PARAM_ERROR);
        }

        Registry::set('config', $config);

        //设置默认模块
        if (isset(self::$appConfigs['application']['dispatcher']['defaultModule']) && (preg_match('/^[0-9a-zA-Z]+$/', self::$appConfigs['application']['dispatcher']['defaultModule']) > 0)) {
            self::$defaultModule = ucfirst(self::$appConfigs['application']['dispatcher']['defaultModule']);
        } else {
            throw new ServerException('默认模块名不存在或不合法', ErrorCode::SWOOLE_SERVER_PARAM_ERROR);
        }
        //设置默认控制器
        if (isset(self::$appConfigs['application']['dispatcher']['defaultController']) && (preg_match('/^[0-9a-zA-Z]+$/', self::$appConfigs['application']['dispatcher']['defaultController']) > 0)) {
            self::$defaultController = ucfirst(self::$appConfigs['application']['dispatcher']['defaultController']);
        } else {
            throw new ServerException('默认控制器名不存在或不合法', ErrorCode::SWOOLE_SERVER_PARAM_ERROR);
        }
        //设置默认方法
        if (isset(self::$appConfigs['application']['dispatcher']['defaultAction']) && (preg_match('/^[0-9a-zA-Z\_]+$/', self::$appConfigs['application']['dispatcher']['defaultAction']) > 0)) {
            self::$defaultAction = lcfirst(self::$appConfigs['application']['dispatcher']['defaultAction']);
        } else {
            throw new ServerException('默认方法名不存在或不合法', ErrorCode::SWOOLE_SERVER_PARAM_ERROR);
        }

        $dispatcher->setDefaultModule(self::$defaultModule)
            ->setDefaultController(self::$defaultController)
            ->setDefaultAction(self::$defaultAction);
    }

    /**
     * 获取APP配置
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function getAppConfigs(string $key=null, $default=null) {
        if ($key === null) {
            return self::$appConfigs;
        } else {
            return Tool::getArrayVal(self::$appConfigs, $key, $default, true);
        }
    }

    /**
     * @return array
     */
    public static function getAcceptModules() : array {
        return self::$acceptModules;
    }

    /**
     * @return string
     */
    public static function getDefaultModule() : string {
        return self::$defaultModule;
    }

    /**
     * @return string
     */
    public static function getDefaultController() : string {
        return self::$defaultController;
    }

    /**
     * @return string
     */
    public static function getDefaultAction() : string {
        return self::$defaultAction;
    }
}