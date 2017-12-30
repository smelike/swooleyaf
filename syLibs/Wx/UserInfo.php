<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 17:15
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class UserInfo {
    public function __construct() {
    }

    /**
     * 用户openid
     * @var string
     */
    private $openid = '';

    /**
     * @param string $openid
     * @throws \Exception\Wx\WxException
     */
    public function setOpenid(string $openid) {
        if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid . '') > 0) {
            $this->openid = $openid . '';
        } else {
            throw new WxException('用户openid不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getOpenid() : string {
        return $this->openid;
    }
}