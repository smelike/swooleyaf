<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午4:56
 */
class LoginController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 用户登录
     * @api {post} /Login/login 用户登录
     * @apiDescription 用户登录
     * @apiGroup UserLogin
     * @apiParam {string} login_type 登录类型,4位长度字符串
     * @apiUse UserLoginAccount
     * @apiUse UserLoginEmail
     * @apiUse UserLoginPhone
     * @apiUse UserLoginQQ
     * @apiUse UserLoginWxAuthBase
     * @apiUse UserLoginWxAuthUser
     * @apiUse UserLoginWxScan
     * @apiUse CommonSuccess
     * @apiUse CommonFail
     * @SyFilter-{"field": "login_type","explain": "登录类型","type": "string","rules": {"required": 1,"regex": "/^[0-9a-z]{4}$/"}}
     */
    public function loginAction() {

    }

    public function logoutAction() {

    }
}