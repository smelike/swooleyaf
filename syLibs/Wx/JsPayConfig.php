<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-1
 * Time: 上午12:08
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class JsPayConfig {
    public function __construct(string $appId) {
        $this->appId = $appId;
        $this->signType = 'MD5';
        $this->nonceStr = WxUtil::createNonceStr();
    }

    /**
     * 公众号ID
     * @var string
     */
    private $appId = '';

    /**
     * 时间戳
     * @var string
     */
    private $timeStamp = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonceStr = '';

    /**
     * 预支付交易会话标识
     * @var string
     */
    private $package = '';

    /**
     * 签名类型
     * @var string
     */
    private $signType = '';

    /**
     * @param string $timeStamp
     * @throws \Exception\Wx\WxException
     */
    public function setTimeStamp(string $timeStamp) {
        if (preg_match('/^[1-9][0-9]{9}$/', $timeStamp . '') > 0) {
            $this->timeStamp = $timeStamp . '';
        } else {
            throw new WxException('时间戳不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $package
     * @throws \Exception\Wx\WxException
     */
    public function setPackage(string $package) {
        if (preg_match('/^[a-zA-Z0-9]{1,64}$/', $package) > 0) {
            $this->package = 'prepay_id=' . $package;
        } else {
            throw new WxException('预支付交易会话标识不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $resArr = [];
        $saveArr = get_object_vars($this);
        foreach ($saveArr as $key => $value) {
            if (strlen($value . '') > 0) {
                $resArr[$key] = $value;
            }
        }
        $resArr['paySign'] = WxUtil::createSign($resArr, $this->appId);

        return $resArr;
    }
}