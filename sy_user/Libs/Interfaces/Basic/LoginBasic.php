<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午3:44
 */
namespace Interfaces\Basic;

use Constant\ErrorCode;
use Interfaces\Containers\LoginContainer;
use Reflection\BaseReflect;
use Tool\InterfaceBasic;
use Traits\SimpleTrait;

class LoginBasic {
    use SimpleTrait;

    private static $loginValidators = [];
    /**
     * @var LoginContainer
     */
    private static $container = null;

    private static function init() {
        if (is_null(self::$container)) {
            self::$container = new LoginContainer();
        }
    }

    public static function handleLogin(string $loginType,array $data) : array {
        $resArr = [
            'code' => 0
        ];

        self::init();
        $loginService = self::$container->getObj($loginType);
        if ($loginService === null) {
            $resArr['code'] = ErrorCode::COMMON_PARAM_ERROR;
            $resArr['message'] = '登录类型不支持';
            return $resArr;
        }
        if (!isset(self::$loginValidators[$loginType])) {
            self::$loginValidators[$loginType] = BaseReflect::getValidatorAnnotations($loginService->className, 'verifyData');
        }
        InterfaceBasic::verify(self::$loginValidators[$loginType], $data['params']);

        $verifyRes = $loginService->verifyData($data['params']);
        if($verifyRes['code'] > 0) {
            $resArr['code'] = $verifyRes['code'];
            $resArr['message'] = $verifyRes['message'];
            return $resArr;
        }

        unset($data['params']);
        $handleData = array_merge($data, $verifyRes['data']);
        $handleRes = $loginService->handleLogin($handleData);
        if($handleRes['code'] > 0) {
            $resArr['code'] = $handleRes['code'];
            $resArr['message'] = $handleRes['message'];
            return $resArr;
        }

        $successRes = $loginService->successLogin($handleRes['data']);
        if($successRes['code'] > 0) {
            $resArr['code'] = $successRes['code'];
            $resArr['message'] = $successRes['message'];
        } else {
            $resArr['data'] = $successRes['data'];
        }

        return $resArr;
    }
}