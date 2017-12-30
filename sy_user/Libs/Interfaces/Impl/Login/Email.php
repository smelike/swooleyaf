<?php
/**
 * 邮箱登录
 * User: jw
 * Date: 17-4-19
 * Time: 下午3:49
 */
namespace Interfaces\Impl\Login;

use Constant\Project;
use Interfaces\Base\LoginBase;
use Interfaces\LoginService;

class Email extends LoginBase implements LoginService {
    public function __construct() {
        parent::__construct();
        $this->loginType = Project::LOGIN_TYPE_EMAIL;
        $this->className = '\\' . __CLASS__;
    }

    /**
     * 校验数据，user_type代表登录用户类型，1：用户 2：商家 3：联盟
     * @param array $data
     * @SyFilter-{"field": "user_type","explain": "用户类型","type": "int","rules": {"required": 1,"in": "1,2,3"}}
     * @SyFilter-{"field": "user_email","explain": "邮箱","type": "string","rules": {"required": 1,"email": 1}}
     * @SyFilter-{"field": "user_pwd","explain": "密码","type": "string","rules": {"required": 1,"min": 1}}
     * @return array
     */
    public function verifyData(array $data) : array {
        $resArr = [
            'code' => 0,
        ];

        return $resArr;
    }

    public function handleLogin(array $data) : array {
        $resArr = [
            'code' => 0,
        ];

        return $resArr;
    }

    public function successLogin(array $data) : array {
        $resArr = [
            'code' => 0,
        ];

        return $resArr;
    }
}