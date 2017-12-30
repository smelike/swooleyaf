<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-3-30
 * Time: 上午7:46
 */
namespace Tool;

use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Request\SyRequest;
use SyServer\BaseServer;
use Traits\SimpleTrait;

class SySession {
    use SimpleTrait;

    /**
     * 获取session id
     * @param string $inToken 外部输入的token值
     * @return string
     */
    public static function getSessionId(string $inToken='') : string {
        if (strlen($inToken) > 0) {
            $token = $inToken . '';
        } else if (isset($_COOKIE[Server::SERVER_DATA_KEY_TOKEN]) && (strlen($_COOKIE[Server::SERVER_DATA_KEY_TOKEN]) > 0)) {
            $token = $_COOKIE[Server::SERVER_DATA_KEY_TOKEN];
        } else {
            $token = SyRequest::getParams(Server::SERVER_DATA_KEY_TOKEN, '');
        }

        if (strlen($token) != 16) {
            $token = Tool::createNonceStr(6) . time();
        }

        return $token;
    }

    /**
     * 设置session值
     * @param string|array $key hash键名
     * @param mixed $value hash键值
     * @param string $inToken 外部输入的token值
     * @return bool
     */
    public static function set($key, $value,string $inToken=''){
        $token = self::getSessionId($inToken);
        $redisKey = Server::REDIS_PREFIX_SESSION . $token;
        if (is_array($key)) {
            if (empty($key)) {
                return false;
            }

            //用于获取session信息时候的校验
            $key['session_id'] = $token;
            CacheSimpleFactory::getRedisInstance()->hMset($redisKey, $key);
            CacheSimpleFactory::getRedisInstance()->expire($redisKey, 1296000);
            return true;
        } else if (is_string($value) || is_numeric($value)) {
            CacheSimpleFactory::getRedisInstance()->hSet($redisKey, $key, $value);
            CacheSimpleFactory::getRedisInstance()->expire($redisKey, 1296000);
            return true;
        }

        return false;
    }

    /**
     * 获取session值
     * @param string|null $key hash键名
     * @param mixed|null $default 默认值
     * @param string $inToken 外部输入的token值
     * @return mixed
     */
    public static function get(string $key=null, $default=null,string $inToken=''){
        $refreshTag = false;
        $token = self::getSessionId($inToken);
        $cacheData = BaseServer::getLocalUserInfo($token);
        if(empty($cacheData)){
            $redisKey = Server::REDIS_PREFIX_SESSION . $token;
            $cacheData = CacheSimpleFactory::getRedisInstance()->hGetAll($redisKey);
            $refreshTag = true;
        }

        if (is_array($cacheData) && isset($cacheData['session_id']) && ($cacheData['session_id'] == $token)) {
            if($refreshTag){
                BaseServer::addLocalUserInfo($token, $cacheData);
            }

            if (is_null($key)) {
                return $cacheData;
            } else {
                return $cacheData[$key] ?? $default;
            }
        } else {
            return $default;
        }
    }

    /**
     * 删除session值
     * @param string $key
     * @param string $inToken
     * @return int
     */
    public static function del(string $key,string $inToken=''){
        $token = self::getSessionId($inToken);
        $redisKey = Server::REDIS_PREFIX_SESSION . $token;
        if($key === ''){
            return CacheSimpleFactory::getRedisInstance()->del($redisKey);
        } else {
            return CacheSimpleFactory::getRedisInstance()->hDel($redisKey, $key);
        }
    }
}