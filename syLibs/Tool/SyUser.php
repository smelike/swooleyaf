<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/5/23 0023
 * Time: 12:16
 */
namespace Tool;

use Constant\ErrorCode;
use Exception\User\LoginException;
use Traits\SimpleTrait;

class SyUser {
    use SimpleTrait;

    private static $info = null;

    /**
     * 获取用户信息
     * @param bool $force 是否强制获取用户信息,true:是 false:否
     * @return array
     */
    public static function getUserInfo($force=false){
        if($force || is_null(self::$info)){
            self::$info = SySession::get(null, []);
        }

        return self::$info;
    }

    /**
     * 检查是否已登录
     * @throws \Exception\User\LoginException
     */
    public static function checkLogin(){
        if(empty(self::getUserInfo())){
            throw new LoginException('请先登录', ErrorCode::USER_NOT_LOGIN);
        }
    }

    /**
     * 获取用户ID
     * @return string
     */
    public static function getUid() : string {
        $userInfo = self::getUserInfo();
        return is_array($userInfo) ? Tool::getArrayVal($userInfo, 'user_id', '') : '';
    }

    /**
     * 获取用户openid
     * @return string
     */
    public static function getOpenId() : string {
        $userInfo = self::getUserInfo();
        return is_array($userInfo) ? Tool::getArrayVal($userInfo, 'user_openid', '') : '';
    }
}